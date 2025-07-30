<?php
require '/home/bitnami/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Sdk;

Class Bucket {
    private $config;
    private $client;
    private $bucketName;

    public function __construct() {
		$this->config = include('/home/bitnami/bakehouse/private/config.php');
		$this->client = new S3Client([
			'region' => 'us-east-1',
			'version' => 'latest',
			'credentials' => [
				'key' => $this->config['aws_db_key'],
				'secret' => $this->config['aws_db_secret']
			]
		]);
        $this->bucketName = '703bakehouse';
	}

    public function uploadImage($filename) {
        $tmpFilePath = $_FILES['image']['tmp_name'];
        $this->client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $filename,
            'Body' => fopen($tmpFilePath, 'rb'),
            'ContentType' => mime_content_type($tmpFilePath)
        ]);
    }

    public function deleteImage($filename) {
        $this->client->deleteObject([
            'Bucket' => $this->bucketName,
            'Key' => $filename
        ]);
    }
}
?>