<?php
class Controller {
	private $db;

	public function __construct() {
		session_start();
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = [];
		}
		//TODO: Connect to database
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
			case "/home":	
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
	public function showOrder(){
		// Handle form submissions
		if (isset($_POST['action'])) {
			if($_POST['action']==='add'){
				$quant_price = explode("_",$_POST['quantity']);
				$quantity = $quant_price[0];
				$price = $quant_price[1];
				echo json_encode([
					    'id' => $_POST['id'],
					    'name' => $_POST['name'],
					    'quantity' => $quantity,
					    'price'=> $price
				]);
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
		$_SESSION['cart_total']= $this->cart_total();
		include("/home/bitnami/bakehouse/private/pages/order.php");
	}
	
	/**
	 * Add item to cart
	 * @param string $id
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
}
