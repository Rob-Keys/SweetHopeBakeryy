<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>703 Bakehouse</title>

        <link rel="stylesheet" href="styles/custom.css">
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
                    $products = [
                        ['id' => 'cupcake', 'name' => 'Cupcakes', 'prices' => [1 => 2.50, 3 => 7.00, 6 => 13.00, 12 => 24.00], 'image'=>'cupcakes.jpg',' customizations' => ['frosting' => ['yellow','red','swirled']]],
                        ['id' => 'donut', 'name' => 'Donuts', 'prices' => [1 => 1.75, 3 => 5.00, 6 => 9.50, 12 => 18.00], 'image'=>'oreo.jpg', 'customizations' => ['glaze' => ['yes','no']]],
                        ['id' => 'cookie', 'name' => 'Cookies', 'prices' => [1 => 1.25, 3 => 3.50, 6 => 6.50, 12 => 12.00], 'image'=>'smore_bar.jpg', 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                        ['id' => 'brownie', 'name' => 'Gourmet Brownies', 'prices' => [1 => 3.00, 3 => 8.00, 6 => 15.00, 12 => 28.00], 'image'=>'cupcakes.jpg', 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                        ['id' => 'macaron', 'name' => 'French Macarons', 'prices' => [3 => 9.00, 6 => 17.00, 12 => 32.00], 'image'=>'cupcakes.jpg', 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                    ];
                    for($i=0; $i<sizeof($products)-1; $i++) { ?>
                    <div class='row'>
                        <div class='col-6'>
                            <?php include("/home/bitnami/bakehouse/private/components/print_product_i.php"); ?>
                        </div>
                        <?php $i++; ?>
                        <div class='col-6'>
                            <?php include("/home/bitnami/bakehouse/private/components/print_product_i.php"); ?>
                        </div>
                    </div>
                    <?php }
                    if(sizeof($products)%2===1){
                        echo "<div class='row'><div class='col-3'></div><div class='col-6'>";
                        include("/home/bitnami/bakehouse/private/components/print_product_i.php");
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