<?php

require_once '/home/bitnami/vendor/autoload.php';

Class Stripe{
    private $config;
    private $client;

    public function __construct() {
		$this->config = include('/home/bitnami/bakehouse/private/backend/config.php');
		$this->client = new \Stripe\StripeClient($this->config['stripe_secret']);
	}

    public function checkout(){
        $this->create_checkout_session();
        $this->create_stripe_checkout();
    }

    public function create_checkout_session(){
        $_SESSION['line_items'] = [];
        foreach($_SESSION['cart'] as $name => $item){
            $_SESSION['line_items'][] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $name],
                    'unit_amount' => $item['price'] * 100, /* Price per unit in cents */
                ],
                'quantity' => 1,
                'actual_quantity' => $item['quantity']
            ];
        }
    }

    public function create_stripe_checkout(){
        $stripe_line_items = [];
        foreach($_SESSION['cart'] as $name => $item){
            $stripe_line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $name],
                    'unit_amount' => $item['price'] * 100, /* Price per unit in cents */
                ],
                'quantity' => 1
            ];
        }
        $checkout_session = $this->client->checkout->sessions->create([
            'line_items' => $stripe_line_items,
            'mode' => 'payment',
            'ui_mode' => 'custom',
            'return_url' => 'https://703bakehouse.com/return?session_id={CHECKOUT_SESSION_ID}'
        ]);

        return json_encode(array('clientSecret' => $checkout_session->client_secret));
    }

    public function did_checkout_succeed(){
        $session = $this->client->checkout->sessions->retrieve($_GET['session_id']);
        if ($session->payment_status === 'paid') {
            return true;
        } else {
            return false;
        }
    }
}