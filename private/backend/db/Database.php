<?php

/*
TABLE SCHEMA DEFINITION:

products.json:
itemName: {
    itemName: S,
    imageUrl: S,
    prices: {
	    quantity: price,
        ....
    },
    customizations: {
	    name: price,
        ...
    }
},
...


*/

Class Database {
	private $config;
	private $products;
    private $home_page;
	private $about_page;
	private $contact_page;
    private $orders;

    public function __construct() {
		$this->config = include('/home/bitnami/bakehouse/private/backend/config.php');
		$this->products = $this->getTable("products");
		$this->home_page = $this->getTable("home_page");
		$this->about_page = $this->getTable("about_page");
        $this->contact_page = $this->getTable("contact_page");
        $this->orders = $this->getTable("orders");
	}

	public function getTable($tableName) {
        try {
			$jsonData = file_get_contents("/home/bitnami/bakehouse/private/backend/db/data/".$tableName.".json");
			$data = json_decode($jsonData, true);
			if (!is_array($data)) {
				$data = [];
			}
			return $data;
        } catch (Exception $e) {
            return [];
        }
    }

	public function putItem($tableName, $item) {
		$this->{$tableName}[] = $item;
		$this->writeTableToFile($tableName);
	}

	public function removeItem($tableName, $key) {
		$keyKey = ($tableName == "products") ? "itemName" : "sectionIndex";
		foreach ($this->{$tableName} as $index => $item) {
			if (isset($item[$keyKey]) && $item[$keyKey] === $key) {
				unset($this->{$tableName}[$index]);
				break;
			}
		}
		// Reindex to keep keys sequential
		$this->{$tableName} = array_values($this->{$tableName});
		
		$this->writeTableToFile($tableName);
	}

	private function writeTableToFile($tableName){
		$jsonData = json_encode($this->{$tableName}, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		file_put_contents("/home/bitnami/bakehouse/private/backend/db/data/".$tableName.".json", $jsonData);
	}
}

?>