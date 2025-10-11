<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Checkout - 703 Bakehouse</title>
        <meta name="description" content="Complete your purchase of delicious baked goods from 703 Bakehouse. Enter shipping details, review your order, and place your order securely.">
        <meta property="og:title" content="Checkout - 703 Bakehouse">
        <meta property="og:description" content="Secure checkout for custom cakes, cookies, donuts, and more in Arlington, Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://703bakehouse.com/checkout">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.703bakehouse.com/checkout" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/checkout.css">
        <link rel="stylesheet" href="styles/stripe.css">

        <script src="https://js.stripe.com/basil/stripe.js"></script>
        <script src="js/stripe/checkout.js" defer></script>
        <script src="js/checkout.js" defer></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>

        <div class="row m-5">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h2>Order Summary</h2>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php $stripe_total = 0;
                        foreach ($_SESSION['line_items'] as $item): 
                        $stripe_total += $item['price_data']['unit_amount']/100; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?=$item['actual_quantity']." ".$item['price_data']['product_data']['name'] ?></strong>
                                </div>
                                <span><?= "$" . number_format($item['price_data']['unit_amount']/100, 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-body">
                        <h5 class="card-title">Total: <?php echo "$" . number_format($stripe_total, 2); ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-1"></div>
            <div class="col-md-5">
                <h4 class="m-0 p-0"> Email: </h4>
                <input type="email" id="email" class="m-0 p-0" style="width: 100%"></input>
                <div id="email-errors" class="mb-3"></div>

                <h4 class="m-0 p-0"> Order Method: </h4>
                <div class="row mb-3">
                    <button class="col-md-5 order-method-button selected" id="pickup-button"> Pickup </button>
                    <div class="col-1"></div>
                    <button class="col-md-5 order-method-button" id="delivery-button"> Delivery </button>
                </div>
                <div id="method-container"></div>

                <h4 class="m-0 p-0"> Payment Method: </h4>
                <div id="payment-element"></div>

                <button id="pay-button" class='btn-cookie btn-lg mt-3'>Pay</button>
                <div id="confirm-errors"></div>
            </div>
        </div>

        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>
