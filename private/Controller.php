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
			case strpos($uri, '/return') === 0:   // catches /return and any query string after
				$this->showReturn();
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
		$_SESSION['cart_total'] = $this->cart_total();
		$this->stripe->checkout();
		include(__DIR__ . "/frontend/pages/checkout.php");
	}
	public function getCheckoutAPI(){
		echo $this->stripe->create_stripe_checkout();
	}
	public function showAuthenticationPage(){
		include(__DIR__ . "/frontend/pages/authenticate.php");
	}
	public function showReturn(){
		if($this->stripe->did_checkout_succeed()){
			include(__DIR__ . "/frontend/pages/return.php");
			$_SESSION['cart'] = [];
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
			$_SESSION["desired_page"] = "/customize";
			include(__DIR__ . "/frontend/pages/authenticate.php");
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
			$_SESSION["desired_page"] = "/mail";
			include(__DIR__ . "/frontend/pages/authenticate.php");
		}
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

	public function customizeRemoveItem(){
		$this->s3->deleteImage($this->get_s3_image_name($_POST['partitionKeyValue']));
		$this->db->removeItem($_POST['tableName'], $_POST['partitionKeyValue']);
		$this->refresh_db_session($_POST['tableName']);
		include(__DIR__ . "/frontend/pages/customize.php");
	}

	public function customizeAddItem(){
		$filepath = $this->get_s3_image_name($_POST['partitionKeyValue']);
		$this->s3->uploadImage($filepath);

		$item = [];
		$item[$_POST['partitionKey']] = $_POST['partitionKeyValue'];
		$item['imageURL'] = 'https://703bakehouse.s3.us-east-1.amazonaws.com/'. $filepath;

		if(isset($_POST['headerText'])){
			$item['headerText'] = $_POST['headerText'];
		}
		if(isset($_POST['bodyText'])){
			$item['bodyText'] = $_POST['bodyText'];
		}
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

		$this->db->putItem($_POST['tableName'], $item);
		$this->refresh_db_session($_POST['tableName']);
		include(__DIR__ . "/frontend/pages/customize.php");
	}

	function get_s3_image_name($rootName){
		return $_POST['tableName'] . '/'. str_replace(' ', '_', $rootName) . '.jpg';
	}

	function get_products_from_database(){
		$results = $this->db->getTable('products');

		$_SESSION["products"] = [];
		foreach ($results as $product) {
			$product_name = $product['itemName'];
			$product_image = $product['imageURL'];
			$product_description = isset($product['description']) ? $product['description'] : "";

			$prices = $product['prices'];
			$price_array = [];
			foreach ($prices as $quantity => $price) {
				$price_array[$quantity] = $price;
			}
			ksort($price_array);
			
			$customizations = $product['customizations'];
			$customization_array = [];
			foreach ($customizations as $name => $price) {
				$customization_array[$name] = $price;
			}

			$_SESSION["products"][] = [
				'itemName' => $product_name,
				'description' => $product_description,
				'imageURL' => $product_image,
				'prices' => $price_array,
				'customizations' => $customization_array
			];
		}
	}

	function get_page_sections_from_database($pageName){
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

	function refresh_db_session($tableName){
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
	function add_to_cart($name, $qty, $price) {
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
	function cart_total() {
		$total = 0;
		foreach ($_SESSION['cart'] as $item) {
			$total += $item['price'];
		}
		return $total;
	}
}