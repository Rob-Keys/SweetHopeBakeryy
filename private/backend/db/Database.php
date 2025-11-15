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
	private $products;
    private $home_page;
	private $about_page;
	private $contact_page;

    public function __construct() {
		$this->products = $this->getTable("products");
		$this->home_page = $this->getTable("home_page");
		$this->about_page = $this->getTable("about_page");
        $this->contact_page = $this->getTable("contact_page");
	}

	public function getTable($tableName, $partitionKeyValue = null) {
        try {
			$jsonData = file_get_contents(__DIR__ . "/data/" . $tableName . ".json");
			$data = json_decode($jsonData, true);
			if (!is_array($data)) {
				$data = [];
			}

			// If the second optional parameter is set, return the specific item from the table
			if (!is_null($partitionKeyValue)) {
				if($tableName == "products"){
					$partitionKey = 'itemName';
				} else {
					$partitionKey = 'sectionIndex';
				}
				foreach ($data as $item) {
					if (isset($item[$partitionKey]) && $item[$partitionKey] === $partitionKeyValue) {
						return $item;
					}
				}
				return null;  // Not found
			}

			return $data;
        } catch (Exception $e) {
            return [];
        }
    }

	public function putItem($tableName, $item) {
		// Ensure the table exists and is an array
		if (!isset($this->{$tableName}) || !is_array($this->{$tableName})) {
			$this->{$tableName} = [];
		}

		$table =& $this->{$tableName};

		// If the item specifies a sectionIndex, insert at that index and bump others
		if (isset($item['sectionIndex'])) {
			$newIndex = (int)$item['sectionIndex'];

			// Normalize existing items: ensure we can compare sectionIndex
			usort($table, function($a, $b) {
				$ai = isset($a['sectionIndex']) ? (int)$a['sectionIndex'] : PHP_INT_MAX;
				$bi = isset($b['sectionIndex']) ? (int)$b['sectionIndex'] : PHP_INT_MAX;
				return $ai - $bi;
			});

			// Bump items with sectionIndex >= $newIndex (iterate from end to avoid double bumps)
			for ($i = count($table) - 1; $i >= 0; $i--) {
				if (!isset($table[$i]['sectionIndex'])) continue;
				if ((int)$table[$i]['sectionIndex'] >= $newIndex) {
					$table[$i]['sectionIndex'] = (int)$table[$i]['sectionIndex'] + 1;
				}
			}

			// Add the new item
			$table[] = $item;

			// Sort by sectionIndex so ordering is preserved
			usort($table, function($a, $b) {
				$ai = isset($a['sectionIndex']) ? (int)$a['sectionIndex'] : PHP_INT_MAX;
				$bi = isset($b['sectionIndex']) ? (int)$b['sectionIndex'] : PHP_INT_MAX;
				return $ai - $bi;
			});

			// Reindex numeric keys
			$table = array_values($table);
		} else {
			// Default behavior: append to the table (for products or items without sectionIndex)
			$table[] = $item;
		}

		$this->writeTableToFile($tableName);
	}

	public function removeItem($tableName, $key) {
		$keyKey = ($tableName == "products") ? "itemName" : "sectionIndex";

		if (!isset($this->{$tableName}) || !is_array($this->{$tableName})) {
			return;
		}

		// Work on a reference so changes persist
		$table =& $this->{$tableName};

		// If the table is an associative map where the outer keys are the item keys,
		// try removing directly by key. Capture removed sectionIndex to close gaps.
		$removedSectionIndex = null;
		if (array_key_exists($key, $table)) {
			if (is_array($table[$key]) && isset($table[$key]['sectionIndex'])) {
				$removedSectionIndex = (int)$table[$key]['sectionIndex'];
			}
			unset($table[$key]);
		} else {
			// Otherwise iterate with index to find the matching item
			foreach ($table as $index => $item) {
				if (is_array($item) && isset($item[$keyKey]) && $item[$keyKey] == $key) {
					if (isset($item['sectionIndex'])) {
						$removedSectionIndex = (int)$item['sectionIndex'];
					}
					unset($table[$index]);
					break;
				}
			}
		}

		// Reindex only if the array uses numeric keys so we don't break associative maps
		$allNumeric = true;
		foreach (array_keys($table) as $k) {
			if (!is_int($k)) { $allNumeric = false; break; }
		}
		if ($allNumeric) {
			$table = array_values($table);
		}

		// If we removed an item that had a sectionIndex, decrement sectionIndex for
		// all items with sectionIndex greater than the removed one so indexes close up.
		if (!is_null($removedSectionIndex)) {
			foreach ($table as $idx => $itm) {
				if (is_array($itm) && isset($itm['sectionIndex']) && (int)$itm['sectionIndex'] > $removedSectionIndex) {
					$table[$idx]['sectionIndex'] = (int)$itm['sectionIndex'] - 1;
				}
			}
			// Ensure ordering remains stable
			usort($table, function($a, $b) {
				$ai = isset($a['sectionIndex']) ? (int)$a['sectionIndex'] : PHP_INT_MAX;
				$bi = isset($b['sectionIndex']) ? (int)$b['sectionIndex'] : PHP_INT_MAX;
				return $ai - $bi;
			});
			if ($allNumeric) {
				$table = array_values($table);
			}
		}

		$this->writeTableToFile($tableName);
	}

	private function writeTableToFile($tableName){
		$jsonData = json_encode($this->{$tableName}, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		file_put_contents(__DIR__ . "/data/" . $tableName . ".json", $jsonData);
	}
}

?>