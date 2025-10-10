<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/S3.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

Class SES {
    private $config;
    private $client;
    private $s3;

    public function __construct() {
        $this->config = include(__DIR__ . '/../config.php');
		$this->client = new SesClient([
			'region' => 'us-east-1',
			'version' => 'latest',
			'credentials' => [
				'key' => $this->config['aws_db_key'],
				'secret' => $this->config['aws_db_secret']
			]
		]);
        $this->s3 = new Bucket();
	}

    public function sendEmail($mail) {   
        $sender_email = $mail["from"];
        $recipient_emails = $mail["to"];
        $subject = $mail["subject"];
        $html_body = $mail["body"];

        try {
            $result = $this->client->sendEmail([
                'Source' => $sender_email,
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => $subject,
                        'Charset' => 'UTF-8',
                    ],
                    'Body' => [
                        'Html' => [
                            'Data' => $html_body,
                            'Charset' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            $this->s3->saveEmail($mail);
        } catch (AwsException $e) {
            echo "The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n";
        }
    }

}
?>