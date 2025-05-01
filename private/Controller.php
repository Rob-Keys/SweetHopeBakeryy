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
	public function showOrder(){
		// Handle form submissions
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
			if($_POST['action']==='add'){
				$id = $_POST['id'];
				$name = $_POST['name'];
				$opt_qty = intval($_POST['opt_qty']);
				$pricePerPack = floatval($_POST['price']);
				$this->add_to_cart($id, $name, $opt_qty, $pricePerPack);
			}
			else if($_POST['action']==='clear'){
				$_SESSION['cart']=[];
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
		$key = $id . "_" . $qty;
		if (isset($_SESSION['cart'][$key])) {
			$_SESSION['cart'][$key]['quantity'] += 1;
		} else {
			$_SESSION['cart'][$key] = [
				'id' => $id,
				'name' => $name,
				'quantity' => 1,
				'option_qty' => $qty,
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
			$total += $item['price'] * $item['quantity'];
		}
		return $total;
	}
}
