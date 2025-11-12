<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Successful</title>

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/return.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>

        <div class="row m-5">
            <div class="col-md-5 fade-in-right">
                <div class="card">
                    <div class="card-header">
                        <h2>Order Summary</h2>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($_SESSION['cart'] as $name => $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p>
                                    <strong><?=$item['quantity']." ".$name ?></strong>
                                </p>
                                <p><?= "$" . number_format($item['price'], 2) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-body">
                        <h5 class="card-title">Total: <?php echo "$" . number_format($_SESSION['cart_total'], 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-5 fade-in-left">
                <h2> Thanks for your order! </h2>
                <h4> A receipt has been sent to <?= $_SESSION['customer_email']; ?>. </h4>
            </div>
        </div>

        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>
