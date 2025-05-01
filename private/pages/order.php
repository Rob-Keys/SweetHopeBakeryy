<?php include("/home/bitnami/bakehouse/private/components/header.php"); ?>
<body>
    <main class="container py-5">
        <h1 class="mb-4">Order Your Treats</h1>
        <div class="row">
            <div class="col-md-8">
                <?php
                // Define products
                $products = [
                    ['id' => 'cupcake', 'name' => 'Cupcakes', 'prices' => [1 => 2.50, 3 => 7.00, 6 => 13.00, 12 => 24.00], 'customizations' => ['frosting' => ['yellow','red','swirled']]],
                    ['id' => 'donut', 'name' => 'Donuts', 'prices' => [1 => 1.75, 3 => 5.00, 6 => 9.50, 12 => 18.00], 'customizations' => ['glaze' => ['yes','no']]],
                    ['id' => 'cookie', 'name' => 'Cookies', 'prices' => [1 => 1.25, 3 => 3.50, 6 => 6.50, 12 => 12.00], 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                    ['id' => 'brownie', 'name' => 'Gourmet Brownies', 'prices' => [1 => 3.00, 3 => 8.00, 6 => 15.00, 12 => 28.00], 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                    ['id' => 'macaron', 'name' => 'French Macarons', 'prices' => [3 => 9.00, 6 => 17.00, 12 => 32.00], 'customizations' => ['gluten-free' => ['yes','no'],'chocolate'=>['semi-sweet','dark','white','peppermint']]],
                ];
                // Render product forms
                for($i=0; $i<sizeof($products)-1; $i++) {
                    echo "<div class='row'>";
                    echo "<div class='col-6'>";
                    $_SESSION['id']=$products[$i]['id'];
                    $_SESSION['name']=$products[$i]['name'];
                    $_SESSION['prices']=$products[$i]['prices'];
                    include("/home/bitnami/bakehouse/private/components/product.php");
                    echo "</div>";
                    $i++;
                    echo "<div class='col-6'>";
                    $_SESSION['id']=$products[$i]['id'];
                    $_SESSION['name']=$products[$i]['name'];
                    $_SESSION['prices']=$products[$i]['prices'];
                    include("/home/bitnami/bakehouse/private/components/product.php");
                    echo "</div>";
                    echo "</div>";
                }
                if(sizeof($products)%2===1){
                    echo "<div class='row'><div class='col-3'></div><div class='col-6'>";
                    $_SESSION['id']=$products[$i]['id'];
                    $_SESSION['name']=$products[$i]['name'];
                    $_SESSION['prices']=$products[$i]['prices'];
                    include("/home/bitnami/bakehouse/private/components/product.php");
                    echo "</div></div>";
                }
                ?>
            </div>
            <div class="col-md-4">
                <h2>Your Cart</h2>
                <ul class="list-group mb-3">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $item['name'].": (".$item['option_qty'].")"?>
                            <span><?php echo "\$" . number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="float-end">
                    <h5>Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h5>
                    <form class="d-flex justify-content-end" method="post" action="">
                        <button type="submit" name="action" value="checkout" class="btn btn-primary mt-2">Checkout</button>
                    </form>
                    <form class="d-flex justify-content-end" method="post" action="/order">
                        <button type="submit" name="action" value="clear" class="btn btn-danger mt-2">Clear Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php include("/home/bitnami/bakehouse/private/components/footer.php"); ?>
