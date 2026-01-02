<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order from Sweet Hope Bakery</title>
        <meta name="description" content="Sweet Hope Bakery offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="Order from Sweet Hope Bakery">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://sweethopebakeryy.com/order">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.sweethopebakeryy.com/order" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/order.css">
        <script src="js/order.js"></script>
        <script src="js/shared.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <div>
            <div>
                <h2 class="col-md-8 order-title">Order Your Sweet Treats</h2>
            </div>
            <div class="order-content">
                <div class="col-8 border rounded bg-light products mb-3">
                    <?php
                    for($i=0; $i<sizeof($_SESSION["products"])-1; $i++) { ?>
                        <div class='row'>
                            <div class='col-6'>
                                <?php include(__DIR__ . "/../components/product.php"); ?>
                            </div>
                            <?php $i++; ?>
                            <div class='col-6'>
                                <?php include(__DIR__ . "/../components/product.php"); ?>
                            </div>
                        </div>
                    <?php }
                    if(sizeof($_SESSION["products"])%2===1){
                        echo "<div class='row'><div class='col-3'></div><div class='col-6'>";
                        include(__DIR__ . "/../components/product.php");
                        echo "</div></div>";
                    }
                    ?>
                </div>
                <div class="col-1"></div>
                <div class="col-3"></div>
                <div class="d-flex justify-content-end me-2 cart-container-wrapper">
                    <div id="normal-cart" class="col-md-3 cart-container rounded fade-in-left">
                        <h3 id="your-cart">Your Cart</h3>
                        <ul class="list-group mb-3" id="cart-list">
                            <?php foreach ($_SESSION['cart'] as $name => $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <p><?= $name . " : (".$item['quantity'].")"?></p>
                                    <div class="d-flex justify-content-end align-items-center price-container">
                                        <p class="me-3 price">
                                            <?php echo "\$" . number_format($item['price'], 2); ?>
                                        </p>
                                        <p>
                                            <form method="post" action="/order">
                                                <input type="hidden" name="removed_name" value="<?= $name ?>">
                                                <button type="submit" name="action" value="remove" style="color: red; background: none; border: none;"><p>X</p></button>
                                            </form>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            <?= sizeof($_SESSION['cart']) == 0 ? "<p id='empty-cart' class='text-center mt-2 text-muted'>Currently Empty</p>" : "" ?>
                        </ul>
                        <div class="float-end btn-container">
                            <h4 id="total-price">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h4>
                            <form class="d-flex justify-content-end" method="post" action="/checkout">
                                <button type="submit" name="action" value="checkout" class="btn btn-lg btn-primary mt-2">Checkout</button>
                            </form>
                            <form class="d-flex justify-content-end" method="post" action="/order">
                                <button type="submit" name="action" value="clear" class="btn btn-lg btn-danger mt-2">Clear Cart</button>
                            </form>
                        </div>
                        <div class="mobile-container d-flex justify-content-between align-items-end">
                            <h4 id="mobile-total-price">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h4>
                            <form class="d-flex justify-content-end" method="post" action="/checkout">
                                <button type="submit" name="action" value="checkout" class="btn btn-lg btn-primary mt-2">Checkout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>