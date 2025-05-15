<?php
class Controller {
	private $db;

	public function __construct() {
		session_start();
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = [];
		}
		$config = include('/home/bitnami/bakehouse/private/config.php');
		$host = $config['db_host'];
		$dbname = $config['db_name'];
		$user = $config['db_user'];
		$password = $config['db_password'];
		try {
			// Create a PDO instance
			$this->db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
			// Set the PDO error mode to exception
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
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
			case "/caroline-customize":
				$this->showCustomize();
				break;
			case "/dev-db-init":
				$this->initializeDatabase();
				break;
			case "/dev-db-clear":
				$this->clearDatabase();
				break;		
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

		//Populate the session variable from the database
		$_SESSION["products"] = [];

		$stmt = $this->db->query("SELECT * FROM products");
		$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($products as $product) {
			$product_name = $product['name'];
			$product_image = $product['url'];
		
			$stmt_prices = $this->db->prepare("SELECT quantity, price FROM prices WHERE product_id = (SELECT id FROM products WHERE name = ?)");
			$stmt_prices->execute([$product_name]);
			$prices = $stmt_prices->fetchAll(PDO::FETCH_ASSOC);
		
			$price_array = [];
			foreach ($prices as $price) {
				$price_array[$price['quantity']] = $price['price'];
			}

			$stmt_customizations = $this->db->prepare("SELECT name FROM customizations WHERE product_id = (SELECT id FROM products WHERE name = ?)");
			$stmt_customizations->execute([$product_name]);
			$customizations = $stmt_customizations->fetchAll(PDO::FETCH_ASSOC);

			$customization_array = [];
			foreach ($customizations as $customization) {
				$customization_name = $customization['name'];
				list($customization_type, $customization_value) = explode(": ", $customization_name);
				$customization_array[$customization_type][] = $customization_value;
			}

			$_SESSION["products"][] = [
				'id' => $product['id'],
				'name' => $product_name,
				'prices' => $price_array,
				'image' => $product_image,
				'customizations' => $customization_array
			];
		}
		include("/home/bitnami/bakehouse/private/pages/order.php");
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

	function initializeDatabase() {
		try {
			$this->db->exec("
				CREATE TABLE IF NOT EXISTS products (
					id SERIAL PRIMARY KEY,
					name TEXT NOT NULL,
					url TEXT NOT NULL
				);
				CREATE TABLE IF NOT EXISTS prices (
					product_id INTEGER NOT NULL,
					quantity INT NOT NULL,
					price DECIMAL(5,2) NOT NULL,
                	FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
				);
				CREATE TABLE IF NOT EXISTS customizations (
					product_id INTEGER NOT NULL,
					name TEXT NOT NULL,
					cost DECIMAL(5,2),
					FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
				);
				CREATE TABLE IF NOT EXISTS home (
					url TEXT NOT NULL,
					description TEXT NOT NULL
				);
				CREATE TABLE IF NOT EXISTS caroline (
					username TEXT NOT NULL,
					pass_hash TEXT NOT NULL
				);
			");
	
			$stmt = $this->db->prepare("
				INSERT INTO products (name, url) VALUES
					('Cupcakes', 'https://703bakehouse.s3.us-east-1.amazonaws.com/cupcakes.jpg'),
					('Donuts', 'https://703bakehouse.s3.us-east-1.amazonaws.com/oreo.jpg'),
					('Smores Bars', 'https://703bakehouse.s3.us-east-1.amazonaws.com/smore_bar.jpg'),
					('Gourmet Brownies', 'https://703bakehouse.s3.us-east-1.amazonaws.com/cupcakes.jpg'),
					('Frosted Cookies', 'https://703bakehouse.s3.us-east-1.amazonaws.com/cupcakes.jpg');
			");
			$stmt->execute();
			$stmt = $this->db->prepare("
				INSERT INTO prices (product_id, quantity, price) VALUES
					((SELECT id FROM products WHERE name = 'Cupcakes'), 1, 2.50),
					((SELECT id FROM products WHERE name = 'Cupcakes'), 3, 7.00),
					((SELECT id FROM products WHERE name = 'Cupcakes'), 6, 13.00),
					((SELECT id FROM products WHERE name = 'Cupcakes'), 12, 24.00),
					((SELECT id FROM products WHERE name = 'Donuts'), 1, 1.75),
					((SELECT id FROM products WHERE name = 'Donuts'), 3, 5.00),
					((SELECT id FROM products WHERE name = 'Donuts'), 6, 9.50),
					((SELECT id FROM products WHERE name = 'Donuts'), 12, 18.00),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 1, 1.25),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 3, 3.50),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 6, 6.50),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 12, 12.00),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 1, 3.00),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 3, 8.00),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 6, 15.00),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 12, 28.00),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 3, 9.00),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 6, 17.00),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 12, 32.00);
			");
			$stmt->execute();
			$stmt = $this->db->prepare("
				INSERT INTO customizations (product_id, name, cost) VALUES
					((SELECT id FROM products WHERE name = 'Cupcakes'), 'frosting: yellow', NULL),
					((SELECT id FROM products WHERE name = 'Cupcakes'), 'frosting: red', NULL),
					((SELECT id FROM products WHERE name = 'Cupcakes'), 'frosting: swirled', NULL),
					((SELECT id FROM products WHERE name = 'Donuts'), 'glaze: yes', NULL),
					((SELECT id FROM products WHERE name = 'Donuts'), 'glaze: no', NULL),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'gluten-free: yes', 1.00),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'gluten-free: no', NULL),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'chocolate: semi-sweet', NULL),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'chocolate: dark', NULL),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'chocolate: white', NULL),
					((SELECT id FROM products WHERE name = 'Smores Bars'), 'chocolate: peppermint', NULL),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'gluten-free: yes', 2.50),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'gluten-free: no', NULL),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'chocolate: semi-sweet', NULL),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'chocolate: dark', NULL),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'chocolate: white', NULL),
					((SELECT id FROM products WHERE name = 'Gourmet Brownies'), 'chocolate: peppermint', NULL),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'gluten-free: yes', 0.50),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'gluten-free: no', NULL),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'chocolate: semi-sweet', NULL),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'chocolate: dark', NULL),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'chocolate: white', NULL),
					((SELECT id FROM products WHERE name = 'Frosted Cookies'), 'chocolate: peppermint', 2.00);
			");
			$stmt->execute();
	
			echo "Database initialized successfully.<br>";
		} catch (PDOException $e) {
			echo "Initialization failed: " . $e->getMessage() . "<br>";
		}
	}
	
	function clearDatabase() {
		try {
			$this->db->exec("DROP TABLE IF EXISTS products CASCADE;");
			echo "Database cleared successfully.<br>";
		} catch (PDOException $e) {
			echo "Clearing failed: " . $e->getMessage() . "<br>";
		}
		session_destroy();
	}
	
}
