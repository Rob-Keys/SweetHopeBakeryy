<?php
return [
    'aws_db_key' => getenv('AWS_KEY'),
    'aws_db_secret' => getenv('AWS_SECRET_KEY'),
    'customize_pw' => getenv('ADMIN_PASSWORD'),
    'stripe_secret' => getenv('STRIPE_SECRET_KEY')
];
?>