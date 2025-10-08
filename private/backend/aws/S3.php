<?php
require_once '/home/bitnami/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Sdk;

Class Bucket {
    private $config;
    private $client;
    private $imageBucketName;
    private $mailBucketName;

    public function __construct() {
		$this->config = include('/home/bitnami/bakehouse/private/backend/config.php');
		$this->client = new S3Client([
			'region' => 'us-east-1',
			'version' => 'latest',
			'credentials' => [
				'key' => $this->config['aws_db_key'],
				'secret' => $this->config['aws_db_secret']
			]
		]);
        $this->imageBucketName = '703bakehouse';
        $this->mailBucketName = 'bakehouse-emails';
	}

    public function uploadImage($filename) {
        try{
            $tmpFilePath = $_FILES['image']['tmp_name'];
            $this->client->putObject([
                'Bucket' => $this->imageBucketName,
                'Key' => $filename,
                'Body' => fopen($tmpFilePath, 'rb'),
                'ContentType' => mime_content_type($tmpFilePath)
            ]);
        } catch(ValueError $e){
            return;
        }
    }

    public function deleteImage($filename) {
        $this->client->deleteObject([
            'Bucket' => $this->imageBucketName,
            'Key' => $filename
        ]);
    }

    public function getInbox() {
        $mail = [];
        try {
            $all_mail = $this->client->listObjectsV2([
                'Bucket' => $this->mailBucketName,
                'Prefix' => 'inbox/'
            ]);

            foreach ($all_mail['Contents'] as $object) {
                $key = $object['Key'];

                // Get object content
                $result = $this->client->getObject([
                    'Bucket' => $this->mailBucketName,
                    'Key'    => $key,
                ]);

                $raw = (string) $result['Body'];

                // --- Step 1: Split headers and body ---
                $parts = preg_split("/\r?\n\r?\n/", $raw, 2);
                $headerText = $parts[0] ?? '';
                $bodyText   = $parts[1] ?? '';

                // --- Step 2: Parse headers ---
                $headers = [];
                $lines = preg_split("/\r?\n/", $headerText);
                $currentHeader = null;
                foreach ($lines as $line) {
                    if (preg_match('/^\s+/', $line) && $currentHeader) {
                        // Handle folded headers (multi-line)
                        $headers[$currentHeader] .= ' ' . trim($line);
                    } elseif (strpos($line, ':') !== false) {
                        list($name, $value) = explode(':', $line, 2);
                        $currentHeader = trim($name);
                        $headers[$currentHeader] = trim($value);
                    }
                }

                // --- Step 3: Parse body, html and plaintext (ai-gen) ---
                $parsedBody = "couldnt read";
                if (preg_match('/Content-Type: multipart\/.*; boundary="(.+?)"/', $raw, $m)) {
                    $boundary = $m[1];
                    $sections = preg_split('/--' . preg_quote($boundary, '/') . '/', $bodyText);
                    
                    $plain = $html = '';
                    foreach ($sections as $section) {
                        if (preg_match('/Content-Type: text\/plain;.*?\r?\n\r?\n(.*)/s', $section, $matches)) {
                            $plain = trim($matches[1]);
                        }
                        if (preg_match('/Content-Type: text\/html;.*?\r?\n\r?\n(.*)/s', $section, $matches)) {
                            $html = trim($matches[1]);
                        }
                    }

                    // Use HTML if available, otherwise plain text
                    $parsedBody = $html ?: $plain;
                } else {
                    // Simple email: take everything after headers
                    $parsedBody = trim($bodyText);
                }

                // --- Step 4: Collect structured mail data ---
                $mail[] = [
                    'subject' => $headers['Subject'] ?? null,
                    'from'    => $headers['From'] ?? null,
                    'to'      => $headers['To'] ?? null,
                    'date'    => $headers['Date'] ?? null,
                    'body'    => $parsedBody,
                ];
            }

            return $mail;
        } catch (AwsException $e) {
            return $mail;
        }
    }

    public function getOutbox(){
        $mail = [];
        try {
            $all_mail = $this->client->listObjectsV2([
                'Bucket' => $this->mailBucketName,
                'Prefix' => 'outbox/'
            ]);

            foreach ($all_mail['Contents'] as $object) {
                $key = $object['Key'];
                $result = $this->client->getObject([
                    'Bucket' => $this->mailBucketName,
                    'Key'    => $key,
                ]);
                $body = (string) $result['Body'];
                $mail_array = json_decode($body, true);

                $recipients = "";
                foreach($mail_array['to'] as $recipient) {
                    $recipients = $recipients . $recipient . ", ";
                }

                $mail[] = [
                    'subject' => $mail_array['subject'] ?? null,
                    'from'    => $mail_array['from'] ?? null,
                    'to'      => $recipients,
                    'date'    => $mail_array['date'],
                    'body'    => $mail_array['body'],
                ];
            }

            return $mail;
        } catch (AwsException $e) {
            return $mail;
        }
    }

    public function saveEmail($mail){
        $this->client->putObject([
                'Bucket' => $this->mailBucketName,
                'Key' => 'outbox/' . time() . ".json",
                'Body' => json_encode($mail),
                'ContentType' => 'application/json'
            ]);
    }

}
?>