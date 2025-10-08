<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Customize</title>
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/customize.css">

        <script src="js/customize.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("/home/bitnami/bakehouse/private/frontend/components/header.php"); ?>
        <div class="wrapper">
            <div class="m-5">
                <h2 class="subtitle">Edit Menu</h2>
                <p> "Prices" will create a dropdown menu of those quantities at those prices. So "6:10, 12:15", would mean 6 for $10, or 12 for $15. </p>
                <div class='row menu-row'>
                    <div class='col-2'>
                        <h5> Product Name </h5>
                    </div>
                    <div class='col-2'>
                        <h5> Description </h5>
                    </div>
                    <div class='col-2'>
                        <h5> Prices </h5>
                    </div>
                    <div class='col-2'>
                        <h5> Customizations </h5>
                    </div>
                    <div class='col-2'>
                        <h5> Image </h5>
                    </div>
                    <div class='col-1'>
                    </div>
                </div>
                <?php
                    for($i=0; $i<sizeof($_SESSION["products"]); $i++) { ?>
                        <div class='row menu-row'>
                            <div class='col-2'>
                                <p> <?= $_SESSION["products"][$i]["itemName"] ?> </p>
                            </div>
                            <div class='col-2'>
                                <p> <?= $_SESSION["products"][$i]["description"] ?> </p>
                            </div>
                            <div class='col-2'>
                                <p> <?php foreach($_SESSION["products"][$i]["prices"] as $quantity => $price){ echo $quantity . ": " . $price . ", "; } ?> </p>
                            </div>
                            <div class='col-2'>
                                <p> <?php foreach($_SESSION["products"][$i]["customizations"] as $customization => $quantity){ echo $customization . ": " . $price . ", "; } ?> </p>
                            </div>
                            <div class='col-2'>
                                <img class='demo-photo' src="<?= $_SESSION["products"][$i]["imageURL"] ?>" alt="S3 image"></img>
                            </div>
                            <div class='col-1'>
                                <!-- <button type="button" class="btn btn-cookie button-2 mt-2 edit-item-btn">Edit Item</button> -->
                                <form method="post" action="/customize_remove_item" class="remove-item-form">
                                    <input type="hidden" name="tableName" value="products"></input>
                                    <input type="hidden" name="partitionKey" value="itemName"></input>
                                    <input type="hidden" name="partitionKeyValue" value="<?= $_SESSION["products"][$i]["itemName"] ?>"></input>
                                    <button type="submit" class="btn btn-danger button-2 mt-2">Remove Item</button>
                                </form>
                            </div>
                        </div>
                <?php } ?>
                <form method="post" action="/customize_add_item" class='row menu-row' enctype="multipart/form-data">
                    <div class='col-2'>
                        <input type="text" name="partitionKeyValue" placeholder="Product Name"></input>
                    </div>
                    <div class='col-2'>
                        <input type="text" name="description" placeholder="Product description"></input>
                    </div>
                    <div class='col-2'>
                        <input type="text" name="csvPrices" placeholder="qnty: price, qnty: price, ..."></input>
                    </div>
                    <div class='col-2'>
                        <input type="text" name="csvCustomizations" placeholder="cstm: price, cstm: price, ..."></input>
                    </div>
                    <div class='col-2'>
                        <input type="file" name="image"></input>
                    </div>
                    <div class='col-1'>
                        <input type="hidden" name="tableName" value="products"></input>
                        <input type="hidden" name="partitionKey" value="itemName"></input>
                        <button type="submit" class="btn btn-cookie button-2 mt-2">Add Item</button>
                    </div>
                </form>
            </div>
            <?php
                $pageName = 'home_page';
                include("/home/bitnami/bakehouse/private/frontend/components/customize_page.php");
                $pageName = 'about_page';
                include("/home/bitnami/bakehouse/private/frontend/components/customize_page.php");
                $pageName = 'contact_page';
                include("/home/bitnami/bakehouse/private/frontend/components/customize_page.php");
            ?>
        </div>
        <div class="confirm-delete-form">
            <!-- TODO: Confirm Delete Item page -->
        </div>
        <?php include("/home/bitnami/bakehouse/private/frontend/components/footer.php"); ?>
    </body>
</html>