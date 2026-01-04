<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Selection Summary</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Submit your order request for delicious baked goods from Sweet Hope Bakery. Enter pickup details and submit your order request. Payment is due at in-person pickup.">
        <meta property="og:title" content="Order Request Form - Sweet Hope Bakery">
        <meta property="og:description" content="Order request for custom cakes, cookies, donuts, and more in Arlington, Virginia. Payment at pickup only.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://sweethopebakeryy.com/checkout">
        <link rel="icon" type="image/x-icon" href="/images/sweethopebakeryy.ico">
        <link rel="apple-touch-icon" href="/images/sweethopebakeryy.ico">
        <link rel="canonical" href="https://www.sweethopebakeryy.com/checkout" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/checkout.css">

        <script src="/js/shared.js"></script>

        <!-- STRIPE INTEGRATION COMMENTED OUT - Virginia cottage law compliance -->
        <!-- <script src="https://js.stripe.com/basil/stripe.js"></script> -->
        <!-- <script src="js/stripe/checkout.js" defer></script> -->
        <script src="js/checkout.js" defer></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>

        <div class="row mx-2 my-4">
            <div class="col-md-5 left-side fade-in-right">
                <div class="card">
                    <div class="card-header">
                        <h2>Selection Summary</h2>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php $stripe_total = 0;
                        foreach ($_SESSION['line_items'] as $item): 
                        $stripe_total += $item['price_data']['unit_amount']/100; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p>
                                    <strong><?=$item['actual_quantity']." ".$item['price_data']['product_data']['name'] ?></strong>
                                </p>
                                <p><?= "$" . number_format($item['price_data']['unit_amount']/100, 2); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-body">
                        <h5 class="card-title">Total: <?php echo "$" . number_format($stripe_total, 2); ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-1"></div>
            <div class="col-md-5 fade-in-left">
                <form method="post" action="/return">
                    <h4 class="m-0 p-0"> Name: </h4>
                    <input type="text" name="customer_name" id="name" class="m-0 p-0 mb-2" style="width: 100%" required>

                    <h4 class="m-0 p-0"> Email: </h4>
                    <input type="email" name="customer_email" id="email" class="m-0 p-0 mb-2" style="width: 100%" required>

                    <h4 class="m-0 p-0"> Phone Number: </h4>
                    <input type="tel" name="customer_phone" id="phone" class="m-0 p-0 mb-4" style="width: 100%" required>

                    <h4 class="m-0 p-0"> Pickup Details: </h4>
                    <div class="pickup-details-container">
                        <!--
                        <div class="row mb-3">
                            <button class="col-12 order-method-button selected" id="pickup-button"> In-Person Pickup</button>
                            <div class="col-1"></div>
                            <button class="col-md-5 order-method-button" id="delivery-button"> Delivery </button>
                        </div>
                        <div id="method-container"></div>
                        -->
                        <div class="row">
                            <p class="col-md-6">Pickup Location:</p>
                            <h5 class="col-md-6"><strong>Arlington, VA, 22207</strong> </h5>
                        </div>
                        
                        <div class="row">
                            <p class="col-md-6">Pickup Date:</p>
                            <input type="date" name="acquisition_date" id="pickup-date" class="col-md-6" required>
                        </div>
                        <div class="row">
                            <div class="col-6 m-0 p-0"></div>
                            <p class="col-6 m-0 p-0 small-text mt-1" style="text-align: end"> * Final pickup time and address to be coordinated through direct communication post-order. </p>
                        </div>
                    </div>

                    <input type="hidden" name="acquisition_method" value="pickup">

                    <!-- STRIPE PAYMENT COMMENTED OUT - Virginia cottage law compliance -->
                    <!-- <h4 class="m-0 p-0"> Payment Method: </h4> -->
                    <!-- <div id="payment-element"></div> -->

                    <div class="alert alert-warning mt-3" role="alert">
                        <strong>Important:</strong> Submitting this form does not constitute a sale. Payment is due at in-person pickup only.
                    </div>

                    <button type="submit" id="pay-button" class='btn-cookie btn-lg mt-3'>Submit Item and Date Selection</button>
                    <div id="confirm-errors"></div>
                </form>
            </div>
        </div>

        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>
