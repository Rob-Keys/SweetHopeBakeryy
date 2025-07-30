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
        <script src="js/custom.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("/home/bitnami/bakehouse/private/components/header.php"); ?>

        <?php
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;

            require '/home/bitnami/vendor/autoload.php';

            $mail = new PHPMailer(true);

            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Use your SMTP provider
                $mail->SMTPAuth   = true;
                $mail->Username   = '703bakehouse@gmail.com'; // Your SMTP username
                $mail->Password   = 'what';   // Use an app password, NOT Gmail password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                // Email settings
                $mail->setFrom('703bakehouse@gmail.com', 'Rob');
                $mail->addAddress('703bakehouse@gmail.com');

                $mail->isHTML(true);
                $mail->Subject = 'Hello from AWS Lightsail';
                $mail->Body    = 'This is a test email sent from a PHP app on Lightsail!';

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        ?>
        <main class="container py-5">
            <h1 class="mb-4 checkout-title">Checkout</h1>
            <div class="row">
                <div class="col-md-7">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h2>Shipping & Contact Information</h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="/process_order">
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="full_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" required>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="zip" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control" id="zip" name="zip" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">Payment Method</label>
                                    <select class="form-select" id="paymentMethod" name="payment_method" required>
                                        <option value="paypal">PayPal</option>
                                        <option value="venmo">Venmo</option>
                                    </select>
                                </div>
                                <div id="cardDetails" class="payment-details">
                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" name="card_number">
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="expiry" class="form-label">Expiration Date</label>
                                            <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cvc" class="form-label">CVC</label>
                                            <input type="text" class="form-control" id="cvc" name="cvc">
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="price" value=<?= $_SESSION['cart_total'] ?>/>
                                <button type="submit" class="btn btn-success btn-lg mt-3">Place Order</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h2>Order Summary</h2>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?=$item['quantity']." ".$item['name'] ?></strong>
                                    </div>
                                    <span><?= "$" . number_format($item['price'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="card-body">
                            <h5 class="card-title">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("/home/bitnami/bakehouse/private/components/footer.php"); ?>

        <script>
            // Toggle card details based on payment method
            document.getElementById('paymentMethod').addEventListener('change', function() {
                const cardSection = document.getElementById('cardDetails');
                if (this.value === 'credit_card') {
                    cardSection.style.display = 'block';
                } else {
                    cardSection.style.display = 'none';
                }
            });
            document.getElementById('paymentMethod').dispatchEvent(new Event('change'));
        </script>
    </body>
</html>
