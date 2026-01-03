<?php
// Load environment variables in one place to show developers what's available
return [
    'app_env' => getenv('APP_ENV'),
    'aws_db_key' => getenv('AWS_KEY'),
    'aws_db_secret' => getenv('AWS_SECRET_KEY'),
    'customize_pw' => getenv('ADMIN_PASSWORD'),
    'stripe_secret' => getenv('STRIPE_SECRET_KEY'),
    'stripe_public_key' => getenv('STRIPE_PUBLIC_KEY'),
    'caroline_email_address' => getenv('CAROLINE_EMAIL_ADDRESS'),
    'developer_email_address' => getenv('DEVELOPER_EMAIL_ADDRESS'),
    'pickup_address' => getenv('PICKUP_ADDRESS'),
];
?>