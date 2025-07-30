<?php
require '/home/bitnami/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\Sdk;


/*
TABLE SCHEMA DEFINITION:

bakehouse_menu:
itemName: S,
url: S,
prices: {
	quantity: price,
},
customizations: {
	name: price,
}

bakehouse_front_page:
sectionIndex: N,
text: S,
image: S

bakehouse_about:
sectionIndex: N,
text: S,
image: S

bakehouse_contact:
sectionIndex: N,
text: S,
image: S

*/

Class Database {
	private $config;
	private $client;

    public function __construct() {
		$this->config = include('/home/bitnami/bakehouse/private/config.php');
		$this->client = new DynamoDbClient([
			'region' => 'us-east-1',
			'version' => 'latest',
			'credentials' => [
				'key' => $this->config['aws_db_key'],
				'secret' => $this->config['aws_db_secret']
			]
		]);
	}

	public function getClient() {
		return $this->client;
	}

	public function putItem($tableName, $item) {
		$formattedItem = $this->convertItemToDDB($item);
		$this->client->putItem([
			'TableName' => $tableName,
			'Item' => $formattedItem
		]);
	}

	public function getItem($tableName, $partitionKey) {
		$formattedPartitionKey = $this->convertItemToDDB($partitionKey);
		$result = $this->client->getItem([
			'TableName' => $tableName,
			'Key' => $formattedPartitionKey
		]);
		return $this->convertDDBToItem($result['Item']);
	}

	public function removeItem($tableName, $partitionKey) {
		try{
			$formattedPartitionKey = $this->convertItemToDDB($partitionKey);
			$this->client->deleteItem([
				'TableName' => $tableName,
				'Key' => $formattedPartitionKey
			]);
		} catch (Exception $e) {
			echo $e;
		}
	}

	public function scanTable($tableName) {
        try {
			$items = [];
            $result = $this->client->scan(['TableName' => $tableName])['Items'];
			foreach($result as $item){
				$items[] = $this->convertDDBToItem($item);
			}
			return $items;
        } catch (AwsException $e) {
            return [];
        }
    }

	private function convertItemToDDB($item) {
		$formattedItem = [];

		foreach ($item as $key => $value) {
			$formattedItem[$key] = $this->convertValueToDDB($value);
		}

		return $formattedItem;
	}

	private function convertValueToDDB($value) {
		if (is_string($value)) {
			return ['S' => $value];
		} elseif (is_int($value) || is_float($value)) {
			return ['N' => (string)$value];  // DynamoDB expects numbers as strings
		} elseif (is_bool($value)) {
			return ['BOOL' => $value];
		} elseif (is_null($value)) {
			return ['NULL' => true];
		} elseif (is_array($value)) {
			if (array_keys($value) === range(0, count($value) - 1)) {
				// Indexed array → List
				return ['L' => array_map([$this, 'convertValueToDDB'], $value)];
			} else {
				// Associative array → Map
				$map = [];
				foreach ($value as $k => $v) {
					$map[$k] = $this->convertValueToDDB($v);
				}
				return ['M' => $map];
			}
		} else {
			throw new InvalidArgumentException("Unsupported type for value: " . gettype($value));
		}
	}


	private function convertDDBToItem($item) {
		$formattedItem = [];

		foreach ($item as $key => $ddbValue) {
			$formattedItem[$key] = $this->convertValueFromDDB($ddbValue);
		}

		return $formattedItem;
	}

	private function convertValueFromDDB($ddbValue) {
		if (isset($ddbValue['S'])) {
			return $ddbValue['S'];
		} elseif (isset($ddbValue['N'])) {
			return is_numeric($ddbValue['N']) ? +$ddbValue['N'] : $ddbValue['N']; // auto-cast
		} elseif (isset($ddbValue['BOOL'])) {
			return $ddbValue['BOOL'];
		} elseif (isset($ddbValue['NULL'])) {
			return null;
		} elseif (isset($ddbValue['L'])) {
			return array_map([$this, 'convertValueFromDDB'], $ddbValue['L']);
		} elseif (isset($ddbValue['M'])) {
			$map = [];
			foreach ($ddbValue['M'] as $k => $v) {
				$map[$k] = $this->convertValueFromDDB($v);
			}
			return $map;
		} else {
			throw new InvalidArgumentException("Unknown DynamoDB attribute type in: " . json_encode($ddbValue));
		}
	}

}

?>