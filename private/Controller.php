<?php
require_once __DIR__ . '/backend/db/Database.php';
require_once __DIR__ . '/backend/aws/S3.php';
require_once __DIR__ . '/backend/aws/SES.php';
require_once __DIR__ . '/backend/stripe/stripe.php';

class Controller {
	private $db;
	private $s3;
	private $ses;
	private $stripe;
	private $config;

	public function __construct() {
		set_exception_handler(function($e) {
			include(__DIR__ . '/frontend/pages/error.php');
			exit;
		});
		session_start();
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = [];
		}
		$this->db = new Database();
		$this->s3 = new Bucket();
		$this->ses = new SES();
		$this->stripe = new Stripe();
		$this->config = include(__DIR__ . '/backend/config.php');
	}

	public function run(){
		$command = "/home";
		if(isset($_SERVER['REQUEST_URI'])){
			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		}
		switch($uri){
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
			case "/checkoutAPI":
				$this->getCheckoutAPI();
				break;
			case "/customize":
				$this->showCustomize();
				break;
			case "/customize_remove_item":
				$this->customizeRemoveItem();
				break;
			case "/customize_add_item":
				$this->customizeAddItem();
				break;
			case "/mail":
				$this->showMail();
				break;
			case strpos($uri, '/return') === 0:   // catches /return and any query string after, preserves Stripe session id
				$this->showReturn();
				break;
			case "/log_customer_info_api":
				$this->log_customer_info_api();
				break;
			case "/get_stripe_public_key":
				echo $this->config['stripe_public_key'];
				break;
			case "/dev_clear_session": // remove in production
				session_destroy();
			case "/":
			default:
				$this->showHome();
				break;	
		}
	}

	public function showHome(){
		if(!isset($_SESSION['home_page_sections'])){ $this->get_page_sections_from_database('home_page'); }
		include(__DIR__ . "/frontend/pages/home.php");
	}
	public function showAbout(){
		if(!isset($_SESSION['about_sections']))		{ $this->get_page_sections_from_database('about_page'); }
		include(__DIR__ . "/frontend/pages/about.php");
	}
	public function showContact(){
		if(!isset($_SESSION['contact_sections'])){ $this->get_page_sections_from_database('contact_page'); }
		include(__DIR__ . "/frontend/pages/contact.php");
	}
	public function showCheckout(){
		if(!isset($_SESSION['cart']) || sizeof($_SESSION['cart'])==0){
			header("Location: /order", true, 303);
			exit;
		}
		$_SESSION['cart_total'] = $this->cart_total();
		$this->stripe->checkout();
		if(!isset($_SESSION['line_items']) || sizeof($_SESSION['line_items'])==0){
			header("Location: /order", true, 303);
			exit;
		}
		include(__DIR__ . "/frontend/pages/checkout.php");
	}
	public function getCheckoutAPI(){
		if(!isset($_SESSION['cart'])){
			header("Location: /order", true, 303);
			exit;
		}
		echo $this->stripe->create_stripe_checkout();
	}
	public function showAuthenticationPage($desiredPage){
		$_SESSION["desired_page"] = $desiredPage;
		include(__DIR__ . "/frontend/pages/authenticate.php");
	}
	public function showReturn(){
		if(!isset($_SESSION['cart']) || !isset($_SESSION['cart_total'])){
			header("Location: /order", true, 303);
			exit;
		}
		if($this->stripe->did_checkout_succeed()){
			include(__DIR__ . "/frontend/pages/return.php");
			$this->send_email_receipt();
			$_SESSION['cart'] = [];
			unset($_SESSION['line_items']);
		} else {
			header("Location: /checkout", true, 303);
		}
	}

	private function isAuthenticated(){
		if(isset($_POST['password'])){
			$_SESSION['customize_pw'] = hash('sha256', $_POST['password']);
		}
		if(isset($_SESSION['customize_pw'])){
			if($_SESSION['customize_pw'] == hash('sha256', $this->config['customize_pw'])){
				return true;
			} else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function showCustomize(){
		if($this->isAuthenticated()){
			if(!isset($_SESSION['products']))					{ $this->get_products_from_database(); }
			if(!isset($_SESSION['home_page_sections']))			{ $this->get_page_sections_from_database('home_page'); }
			if(!isset($_SESSION['about_page_sections']))		{ $this->get_page_sections_from_database('about_page'); }
			if(!isset($_SESSION['contact_page_sections']))		{ $this->get_page_sections_from_database('contact_page'); }
			include(__DIR__ . "/frontend/pages/customize.php");
		}
		else {
			$this->showAuthenticationPage("/customize");
		}
	}

	public function showMail(){
		if(isset($_POST['send-mail'])){
			$email = [
				"from" => $_POST["sender"],
				"to" => [$_POST["recipients"]],
				"subject" => $_POST["subject"],
				"body" => $_POST["body"],
				"date" => time()
			];
			$this->ses->sendEmail($email);
			unset($_POST['send-mail']);
			header("Location: /mail", true, 303);
			exit;
		}

		else if($this->isAuthenticated()){
			$_SESSION['inbox'] = $this->s3->getInbox();
			$_SESSION['outbox'] = $this->s3->getOutbox(); 
			include(__DIR__ . "/frontend/pages/mail.php");
		}
		else {
			$this->showAuthenticationPage("/mail");
		}
	}

	public function showOrder(){
		if(!isset($_SESSION['cart'])){ $_SESSION['cart'] = []; }
		$_SESSION['cart_total']= $this->cart_total();
		
		//if its a POST request
		if (isset($_POST['action'])) {
			if($_POST['action']==='add'){
				$quant_price = explode("_",$_POST['quantity']);
				$quantity = $quant_price[0];
				$price = $quant_price[1];
				// For javascript to display
				echo json_encode([
					    'name' => $_POST['name'],
					    'quantity' => $quantity,
					    'price'=> $price
				]);
				// To actually upate cart
				$this->add_to_cart($_POST['name'],$quantity,$price);
				exit;
			}
			else if($_POST['action']==='clear'){
				$_SESSION['cart']=[];
			}
			else if($_POST['action']==='remove'){
				unset($_SESSION['cart'][$_POST['removed_name']]);
			}
			
			$_SESSION['cart_total']= $this->cart_total();
			header("Location: /order");
			return;
		}
		// If this is not the first GET of the session, simplest case
		if (isset($_SESSION["products"])) {
			if(sizeof($_SESSION["products"])!=0){
				include(__DIR__ . "/frontend/pages/order.php");
				exit;
			}
		}

		//Otherwise, this is the first GET of the session
		//Populate the session variable from the database
		$this->get_products_from_database();
		
		include(__DIR__ . "/frontend/pages/order.php");
	}

	function log_customer_info_api(){
		// Support both traditional form-encoded POSTs (in $_POST)
		// and JSON POST bodies (Content-Type: application/json).
		$data = $_POST;

		if (empty($data)) {
			$raw = file_get_contents('php://input');
			$decoded = json_decode($raw, true);
			if (is_array($decoded)) {
				$data = $decoded;
			}
		}

		if (isset($data['acquisition_method'])) {
			$_SESSION['acquisition_method'] = $data['acquisition_method'];
		}
		if (isset($data['acquisition_date'])) {
			$_SESSION['acquisition_date'] = $data['acquisition_date'];
		}
		if (isset($data['delivery_address'])) {
			$_SESSION['delivery_address'] = $data['delivery_address'];
		}
		if (isset($data['customer_phone'])) {
			$_SESSION['customer_phone'] = $data['customer_phone'];
		}
		if (isset($data['customer_name'])) {
			$_SESSION['customer_name'] = $data['customer_name'];
		}
		exit;
	}

	public function customizeRemoveItem(){
		$this->s3->deleteImages($this->get_image_keys_for_deletion($_POST['partitionKeyValue']));
		$this->db->removeItem($_POST['tableName'], $_POST['partitionKeyValue']);
		$this->refresh_db_session($_POST['tableName']);
		header("Location: /customize", true, 303);
	}

	public function customizeAddItem(){
		// Ensure required fields are set
		if(!isset($_POST['partitionKeyValue']) || $_POST['partitionKeyValue'] == "" || !isset($_FILES['images'])){
			header("Location: /customize", true, 303);
			exit;
		}

		$item = [];

		// Set the item name or page section index
		$item[$_POST['partitionKey']] = $_POST['partitionKeyValue'];

		// Upload images to s3 and get their URLs
		$filepaths = $this->get_s3_image_names($_POST['partitionKeyValue']);
		$this->s3->uploadImages($filepaths);

		foreach($filepaths as $filepath){
			$item['imageURLs'][] = 'https://703bakehouse.s3.us-east-1.amazonaws.com/'. $filepath;
		}

		if(isset($_POST['description'])){
			$item['description'] = $_POST['description'];
		}
		if(isset($_POST['headerText'])){
			$item['headerText'] = $_POST['headerText'];
		}
		if(isset($_POST['bodyText'])){
			$item['bodyText'] = $_POST['bodyText'];
		}

		// Parse and set Prices
		if(isset($_POST['csvPrices'])){
			$pairs = explode(",",$_POST['csvPrices']);
			$prices = [];
			foreach($pairs as $pair){
				$split = explode(":",$pair);
				$prices[trim($split[0])] = (int)trim($split[1]);
			}
			$item['prices'] = $prices;
		}

		// Parse and set customizations
		if(isset($_POST['csvCustomizations']) && $_POST['csvCustomizations'] != ""){
			$pairs = explode(",",$_POST['csvCustomizations']);
			$customizations = [];
			foreach($pairs as $pair){
				$split = explode(":",$pair);
				$customizations[trim($split[0])] = trim($split[1]);
			}
			$item['customizations'] = $customizations;
		}

		$this->db->putItem($_POST['tableName'], $item);
		$this->refresh_db_session($_POST['tableName']);
		header("Location: /customize", true, 303);
	}

	// Helper functions only below

	private function get_s3_image_names($partitionKeyValue){
		$names = [];
		$delimiter = bin2hex(random_bytes(4));  // Random 8 character string
		foreach ($_FILES['images']['name'] as $imageIndex => $fileName) {
			$names[] = $_POST['tableName'] . '/'. str_replace(' ', '_', $partitionKeyValue) . '_' . $imageIndex . '_' . $delimiter . '.jpg';
		}
		return $names;
	}

	private function get_image_keys_for_deletion($partitionKeyValue){
		$results = $this->db->getTable('products', $partitionKeyValue);
		$imageURLs = $results['imageURLs'];
		$names = [];

		foreach($imageURLs as $url){
			$parsed = parse_url($url);
			$names[] = ltrim($parsed['path'], '/'); // Remove leading slash to get S3 object key
		}

		return $names;
	}

	private function get_products_from_database(){
		$results = $this->db->getTable('products');

		$_SESSION["products"] = [];
		foreach ($results as $product) {
			$product_name = $product['itemName'];
			$product_images = $product['imageURLs'];
			$product_description = isset($product['description']) ? $product['description'] : "";

			$prices = $product['prices'];
			$price_array = [];
			foreach ($prices as $quantity => $price) {
				$price_array[$quantity] = $price;
			}
			ksort($price_array);
			
			$customization_array = [];
			if(isset($product['customizations'])){
				$customizations = $product['customizations'];
				$customization_array = [];
				foreach ($customizations as $name => $price) {
					$customization_array[$name] = $price;
				}
			}

			$_SESSION["products"][] = [
				'itemName' => $product_name,
				'description' => $product_description,
				'imageURLs' => $product_images,
				'prices' => $price_array,
				'customizations' => $customization_array
			];
		}
	}

	private function get_page_sections_from_database($pageName){
		$_SESSION[$pageName.'_sections'] = [];
		$results = $this->db->getTable($pageName);
		ksort($results);
		
		foreach($results as $section){
			$_SESSION[$pageName.'_sections'][] = [
				"sectionIndex" => $section['sectionIndex'],
				"headerText" => $section['headerText'],
				"bodyText" => $section['bodyText'],
				"imageURL" => $section['imageURL']
			];
		}
	}

	private function refresh_db_session($tableName){
		$_POST = [];
		switch($tableName){
			case "products":
				$this->get_products_from_database();
				break;
			case "home_page":
				$this->get_page_sections_from_database('home_page');
				break;
			case "about_page":
				$this->get_page_sections_from_database('about_page');
				break;
			case "contact_page":
				$this->get_page_sections_from_database('contact_page');
				break;
		}
	}
	
	/**
	 * Add item to cart
	 * @param string $name
	 * @param int $qty
	 * @param float $price
	 */
	private function add_to_cart($name, $qty, $price) {
		// Get the menu item real data
		$menuItem = $this->db->getTable('products', $name);
		
		// Validate the price matches expected price
		if ($price != $menuItem['prices'][$qty]) {
			throw new Exception("Price mismatch detected");
		}
		
		// Price is valid, add to cart
		if (isset($_SESSION['cart'][$name])) {
			$_SESSION['cart'][$name]['quantity'] += $qty;
			$_SESSION['cart'][$name]['price'] += $price;
		} else {
			$_SESSION['cart'][$name] = [
				'quantity' => $qty,
				'price' => $price
			];
		}
	}
	
	/**
	 * Calculate cart total
	 */
	private function cart_total() {
		$total = 0;
		foreach ($_SESSION['cart'] as $item) {
			$total += $item['price'];
		}
		return $total;
	}

	/**
	 * Format an ISO date (YYYY-MM-DD or any parsable date) to MM-DD-YYYY for display
	 * Returns original value if it cannot be parsed.
	 * @param string $isoDate
	 * @return string
	 */
	private function formatDateForDisplay($isoDate){
		if(empty($isoDate)) return '';
		$dt = date_create($isoDate);
		if($dt === false) return htmlspecialchars($isoDate);
		return $dt->format('m-d-Y');
	}

	private function send_email_receipt(){
		$emailBody = "<h3>Thank you for your order!</h3>\n\n";
		if($_SESSION['acquisition_method'] === "delivery"){
			$emailBody .= "<h4>Delivery Details:</h4>";
			$emailBody .= "<p>Delivery Address: " . htmlspecialchars($_SESSION['delivery_address']) . "</p>";
			$emailBody .= "<p>Delivery on " . $this->formatDateForDisplay($_SESSION['acquisition_date']) . "</p>";
		} else {
			$emailBody .= "<h4>Pickup Details:</h4>";
			$emailBody .= "<p>" . htmlspecialchars($this->config['pickup_address']) . "</p>";
			$emailBody .= "<p>" . $this->formatDateForDisplay($_SESSION['acquisition_date']) . "</p>";
			$emailBody .= "<p> Coordinate a pickup time on your chosen day by texting 703-996-9846.</p>";
		}
		$emailBody .= "<h4>Order Summary:</h4>";
		foreach ($_SESSION['cart'] as $name => $item) {
			$emailBody .= "<p>" . $item['quantity'] . " x " . $name . ": $" . number_format($item['price'], 2) . "</p>";
		}
		$emailBody .= "<p>Total: $" . number_format($this->cart_total(), 2) . "</p>";
		$emailBody .= "<hr><p>We appreciate your business!</p>";
		$emailBody .= "<p>For any questions, please contact support@703bakehouse.com</p>";
		$emailBody .= "<img src='https://703bakehouse.s3.us-east-1.amazonaws.com/header/bakehouse_pfp.jpg' alt='703 Bakehouse Logo' style='width:200px;height:auto;'/>";

		$email = [
			"from" => "support@703bakehouse.com",
			"to" => [$_SESSION['customer_email']],
			"subject" => "Your 703 Bakehouse Receipt",
			"body" => $emailBody,
			"date" => time()
		];
		$this->ses->sendEmail($email);

		$caroline_email_body = "<h3>New order received with the following details:</h3>\n\n";
		$caroline_email_body .= "<h4>Customer Contact Info:</h4>";
		$caroline_email_body .= "<p>Name: " . htmlspecialchars($_SESSION['customer_name']) . "</p>";
		$caroline_email_body .= "<p>Email: " . htmlspecialchars($_SESSION['customer_email']) . "</p>";
		$caroline_email_body .= "<p>Phone: " . htmlspecialchars($_SESSION['customer_phone']) . "</p>";
		$caroline_email_body .= $emailBody;
		$caroline_email = [
			"from" => "support@703bakehouse.com",
			"to" => [$this->config['caroline_email_address']],
			"subject" => "New Order: " . $_SESSION['acquisition_method'] . ": " . $this->formatDateForDisplay($_SESSION['acquisition_date']),
			"body" => $caroline_email_body,
			"date" => time()
		];
		$this->ses->sendEmail($caroline_email);

		$documentation_email = [
			"from" => "support@703bakehouse.com",
			"to" => ["703bakehouse@gmail.com"],
			"subject" => "New Order Documentation: " . $_SESSION['acquisition_method'] . ": " . $this->formatDateForDisplay($_SESSION['acquisition_date']),
			"body" => $caroline_email_body,
			"date" => time()
		];
		$this->ses->sendEmail($documentation_email);
	}
}