<?php
require_once '/home/bitnami/bakehouse/private/aws/DDB.php';
require_once '/home/bitnami/bakehouse/private/aws/S3.php';

class Controller {
	private $db;
	private $s3;
	private $config;

	public function __construct() {
		session_start();
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = [];
		}
		$this->db = new Database();
		$this->s3 = new Bucket();
		$this->config = include('/home/bitnami/bakehouse/private/config.php');
	}

	public function run(){
		$command = "/home";
		if(isset($_SERVER['REQUEST_URI'])){
			$command = $_SERVER['REQUEST_URI'];
		}
		switch($command){
			case "/about":
				$this->showAbout();
				break;
			case "/order":
				$this->showOrder();
				break;
			case "/contact":
				$this->showContact();
				break;
			case "/checkout":
				$this->showCheckout();
				break;	
			case "/process_order":
				$this->sendBakehouseEmailAndRedirect();
				break;
			case "/customize":
				$this->authenticate();
				break;
			case "/customize_remove_item":
				$this->customizeRemoveItem();
				break;
			case "/customize_add_item":
				$this->customizeAddItem();
				break;
			case "/dev_clear_session":
				session_destroy();
			case "/":
			default:
				$this->showHome();
				break;	
		}
	}

	public function showHome(){
		include("/home/bitnami/bakehouse/private/pages/home.php");
	}
	public function showAbout(){
		include("/home/bitnami/bakehouse/private/pages/about.php");
	}
	public function showContact(){
		include("/home/bitnami/bakehouse/private/pages/contact.php");
	}
	public function showCheckout(){
		$_SESSION['cart_total'] = $this->cart_total();
		include("/home/bitnami/bakehouse/private/pages/checkout.php");
	}
	public function showAuthenticationPage(){
		include("/home/bitnami/bakehouse/private/pages/authenticate.php");
	}

	public function authenticate(){
		if(isset($_POST['password'])){
			$_SESSION['customize_pw'] = hash('sha256', $_POST['password']);
		}
		if(isset($_SESSION['customize_pw'])){
			if($_SESSION['customize_pw'] == hash('sha256', $this->config['customize_pw'])){
				$this->showCustomize();
			} else {
				echo "Wrong password";
				$this->showAuthenticationPage();
			}
		}
		else {
			echo "No session token;";
			$this->showAuthenticationPage();
		}
	}
	public function showCustomize(){
		if(!isset($_SESSION['products'])){ $this->get_products_from_database(); }
		include("/home/bitnami/bakehouse/private/pages/customize.php");
	}

	public function showOrder(){
		$_SESSION['cart_total']= $this->cart_total();
		
		//if its a POST request
		if (isset($_POST['action'])) {
			if($_POST['action']==='add'){
				$quant_price = explode("_",$_POST['quantity']);
				$quantity = $quant_price[0];
				$price = $quant_price[1];
				// For javascript to display
				echo json_encode([
					    'id' => $_POST['id'],
					    'name' => $_POST['name'],
					    'quantity' => $quantity,
					    'price'=> $price
				]);
				// To actually upate cart
				$this->add_to_cart($_POST['id'],$_POST['name'],$quantity,$price);
				exit;
			}
			else if($_POST['action']==='clear'){
				$_SESSION['cart']=[];
			}
			else if($_POST['action']==='remove'){
				unset($_SESSION['cart'][$_SESSION['item_id']]);
			}
			
			$_SESSION['cart_total']= $this->cart_total();
			header("Location: /order");
			return;
		}
		// If this is not the first GET of the session, simplest case
		if (isset($_SESSION["products"])) {
			if(sizeof($_SESSION["products"])!=0){
				include("/home/bitnami/bakehouse/private/pages/order.php");
				exit;
			}
		}

		//Otherwise, this is the first GET of the session
		//Populate the session variable from the database
		$this->get_products_from_database();
		
		include("/home/bitnami/bakehouse/private/pages/order.php");
	}

	public function customizeRemoveItem(){
		$this->s3->deleteImage($this->get_s3_image_name($_POST['partitionKeyValue']));
		$this->db->removeItem($_POST['tableName'], [$_POST['partitionKey'] => $_POST['partitionKeyValue']]);
		$this->get_products_from_database();
		include("/home/bitnami/bakehouse/private/pages/customize.php");
	}

	public function customizeAddItem(){
		$filepath = $this->get_s3_image_name($_POST['partitionKeyValue']);
		$this->s3->uploadImage($filepath);

		$item = [];
		$item[$_POST['partitionKey']] = $_POST['partitionKeyValue'];
		if(isset($_POST['csvPrices'])){
			$pairs = explode(",",$_POST['csvPrices']);
			$prices = [];
			foreach($pairs as $pair){
				$split = explode(":",$pair);
				$prices[trim($split[0])] = trim($split[1]);
			}
			$item['prices'] = $prices;
		}
		if(isset($_POST['csvCustomizations'])){
			$pairs = explode(",",$_POST['csvCustomizations']);
			$customizations = [];
			foreach($pairs as $pair){
				$split = explode(":",$pair);
				$customizations[trim($split[0])] = trim($split[1]);
			}
			$item['customizations'] = $customizations;
		}
		$item['imageURL'] = 'https://703bakehouse.s3.us-east-1.amazonaws.com/'. $filepath;

		$this->db->putItem($_POST['tableName'], $item);
		$this->get_products_from_database();
		include("/home/bitnami/bakehouse/private/pages/customize.php");
	}

	function get_s3_image_name($rootName){
		return str_replace(' ', '_', $rootName) . '.jpg';
	}

	function get_products_from_database(){
		$results = $this->db->scanTable('bakehouse_menu');

		$_SESSION["products"] = [];
		foreach ($results as $product) {
			$product_name = $product['itemName'];
			$product_image = $product['imageURL'];

			$prices = $product['prices'];
			$price_array = [];
			foreach ($prices as $quantity => $price) {
				$price_array[$quantity] = $price;
			}

			$customizations = $product['customizations'];
			$customization_array = [];
			foreach ($customizations as $name => $price) {
				$customization_array[$name] = $price;
			}

			$_SESSION["products"][] = [
				'name' => $product_name,
				'prices' => $price_array,
				'image' => $product_image,
				'customizations' => $customization_array
			];
		}
	}
	
	/**
	 * Add item to cart
	 * @param int $id
	 * @param string $name
	 * @param int $qty
	 * @param float $price
	 */
	function add_to_cart($id, $name, $qty, $price) {
		if (isset($_SESSION['cart'][$id])) {
			$_SESSION['cart'][$id]['quantity'] += $qty;
			$_SESSION['cart'][$id]['price'] += $price;
		} else {
			$_SESSION['cart'][$id] = [
				'id' => $id,
				'name' => $name,
				'quantity' => $qty,
				'price' => $price
			];
		}
	}
	
	/**
	 * Calculate cart total
	 */
	function cart_total() {
		$total = 0;
		foreach ($_SESSION['cart'] as $item) {
			$total += $item['price'];
		}
		return $total;
	}

	/**
	 * Send an email notification, then redirect to Venmo or PayPal to collect payment.
	 *
	 * @param string $customerEmail   Email address of the customer (will appear as “From”).
	 * @param string $paymentMethod   'venmo' or 'paypal'.
	 * @param float  $amount          Amount to charge.
	 * @param string $recipientId     Venmo username or PayPal merchant ID/email.
	 */
	function sendBakehouseEmailAndRedirect() {
		$customerEmail = $_POST['email'];
		$paymentMethod = $_POST['payment_method'];
		$amount = $_POST['price'];
		$recipientId = "RobKeys";
		$headers = [];
		$headers[] = 'From: 703bakehouse@gmail.com';
		// MIME and content-type for HTML email
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=UTF-8';

		// Send email
		$subject = "New Order";
		$body = "Order from ".$customerEmail.". Order:\n";
		foreach($_SESSION['cart'] as $item){
			$body .= $item['name'] . ": ". $item['quantity'] ."\n";
		}
		if (!mail('703bakehouse@gmail.com', $subject, $body, implode("\r\n", $headers))) {
			// If mail fails, you might want to log or show an error
			echo "Failed to send email";
			$err = error_get_last();
    		echo 'Mail failed: ' . print_r($err, true);
			error_log('Failed to send email to Bakehouse.');
			exit;
		} else {
			echo "sent email";
		}

		// 2. Build the payment redirect URL
		switch (strtolower($paymentMethod)) {
			case 'venmo':
				// Venmo deep link (mobile) or web link fallback
				// recipients: Venmo username; amount in USD
				$venmoLink = sprintf(
					'https://venmo.com/%s?txn=pay&amount=%.2f',
					urlencode($recipientId),
					$amount
				);
				$redirectUrl = $venmoLink;
				break;

			case 'paypal':
				// PayPal payment link (checkout for fixed amount)
				// Replace CLIENT_ID and RETURN_URL with your PayPal settings if using PayPal Checkout SDK.
				// Here’s the easiest non-SDK approach: a PayPal.Me link:
				$paypalMeLink = sprintf(
					'https://www.paypal.me/%s/%.2f',
					urlencode($recipientId),
					$amount
				);
				$redirectUrl = $paypalMeLink;
				break;

			default:
				// Unknown payment method
				throw new InvalidArgumentException('Unsupported payment method: ' . $paymentMethod);
		}

		// Redirect to the payment provider
		header('Location: ' . $redirectUrl);
		exit;
	}
}
