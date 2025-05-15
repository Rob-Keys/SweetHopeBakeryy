<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order from 703 Bakehouse</title>
        <meta name="description" content="703 Bakehouse offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="Order from 703 Bakehouse">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://703bakehouse.com/order">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.703bakehouse.com/order" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/order.css">
        <script src="js/custom.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("/home/bitnami/bakehouse/private/components/header.php"); ?>
        <main class="container py-5">
            <h1 class="mb-4 order-title">Order Your Sweet Treats</h1>
            <div class="row order-content">
                <div class="col-md-8 border rounded bg-light">
                    <?php
                    for($i=0; $i<sizeof($_SESSION["products"])-1; $i++) { ?>
                        <div class='row'>
                            <div class='col-6'>
                                <?php include("/home/bitnami/bakehouse/private/components/product.php"); ?>
                            </div>
                            <?php $i++; ?>
                            <div class='col-6'>
                                <?php include("/home/bitnami/bakehouse/private/components/product.php"); ?>
                            </div>
                        </div>
                    <?php }
                    if(sizeof($_SESSION["products"])%2===1){
                        echo "<div class='row'><div class='col-3'></div><div class='col-6'>";
                        include("/home/bitnami/bakehouse/private/components/product.php");
                        echo "</div></div>";
                    }
                    ?>
                </div>
                <div class="col-1"></div>
                <div id="normal-cart" class="col-md-3 cart-container border rounded bg-light">
                    <h2>Your Cart</h2>
                    <ul class="list-group mb-3" id="cart-list">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $item['name'].": (".$item['quantity'].")"?>
                                <div class="d-flex justify-content-end align-items-center price-container">
                                    <span class="me-3 price">
                                        <?php echo "\$" . number_format($item['price'], 2); ?>
                                    </span>
                                    <span>
                                        <form method="post" action="/order">
                                            <input type="hidden" name="item_id" value="<?= $item['id']  ?>">
                                            <button type="submit" name="action" value="remove" style="color: red; background: none; border: none;">X</button>
                                        </form>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?= sizeof($_SESSION['cart']) == 0 ? "<p id='empty-cart' class='text-center mt-2 text-muted'>Currently Empty</p>" : "" ?>
                        </ul>
                        <div class="float-end">
                            <h5 id="total-price">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h5>
                            <form class="d-flex justify-content-end" method="post" action="">
                                <button type="submit" name="action" value="checkout" class="btn btn-lg btn-primary mt-2">Checkout</button>
                            </form>
                            <form class="d-flex justify-content-end" method="post" action="/order">
                                <button type="submit" name="action" value="clear" class="btn btn-lg btn-danger mt-2">Clear Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <div class="total-background"></div>
        <div class="dropdown">
            <button id="dropdownButton"><i class="fa-solid fa-cart-shopping"></i></button>
            <div id="dropdownContent" class="dropdown-content">
                <div class="cart-container border rounded bg-light">
                        <h2>Your Cart</h2>
                        <ul class="list-group mb-3" id="cart-list">
                            <?php if($_SESSION['cart']): foreach ($_SESSION['cart'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= $item['name'].": (".$item['quantity'].")"?>
                                    <div class="d-flex justify-content-end align-items-center price-container">
                                        <span class="me-3 price">
                                            <?php echo "\$" . number_format($item['price'], 2); ?>
                                        </span>
                                        <span>
                                            <form method="post" action="/order">
                                                <input type="hidden" name="item_id" value="<?= $item['id']  ?>">
                                                <button type="submit" name="action" value="remove" style="color: red; background: none; border: none;">X</button>
                                            </form>
                                        </span>
                                    </div>
                                </li>
                                <?php endforeach; endif; ?>
                            </ul>
                            <div class="float-end">
                                <h5 id="total-price">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h5>
                                <form class="d-flex justify-content-end" method="post" action="">
                                    <button type="submit" name="action" value="checkout" class="btn btn-lg btn-primary mt-2">Checkout</button>
                                </form>
                                <form class="d-flex justify-content-end" method="post" action="/order">
                                    <button type="submit" name="action" value="clear" class="btn btn-lg btn-danger mt-2">Clear Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("/home/bitnami/bakehouse/private/components/footer.php"); ?>
    </body>
</html>