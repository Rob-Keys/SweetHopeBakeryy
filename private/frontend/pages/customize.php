<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customize</title>
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/customize.css">

        <script src="js/customize.js"></script>
        <script src="js/shared.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <div class="wrapper">
            <div class="m-5">
                <h2 class="subtitle">Edit Menu</h2>
                <p> "Prices" will create a dropdown menu of those quantities at those prices. So "6:10, 12:15", would mean 6 for $10, or 12 for $15. </p>
                <p> "Customizations" currently dont do anything and you can just leave that field blank for now. </p>
                <p> I recommend uploading images that are of filetype .avif, as theyre most efficient size-wise and will make your site load faster. To convert a .png or .jpg to .avif you can use a site like: <a href="https://cloudinary.com/tools/jpg-to-avif" target="_blank">JPEG-To-Avif Converter</a></p>
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
                    <div class='col-2 right-align'>
                        <h5> Actions </h5>
                    </div>
                </div>
                <?php
                    for($i=0; $i<sizeof($_SESSION["products"]); $i++) { 
                        $pricesStr = "";
                        foreach($_SESSION["products"][$i]["prices"] as $quantity => $price){
                            $pricesStr .= $quantity . ":" . $price . ", ";
                        }
                        $pricesStr = rtrim($pricesStr, ", ");
                        
                        $customizationsStr = "";
                        foreach($_SESSION["products"][$i]["customizations"] as $customization => $price){
                            $customizationsStr .= $customization . ":" . $price . ", ";
                        }
                        $customizationsStr = rtrim($customizationsStr, ", ");
                        ?>
                        <div class='row menu-row' data-item-index="<?= $i ?>">
                            <!-- View Mode -->
                            <div class='col-2 view-mode'>
                                <p> <?= $_SESSION["products"][$i]["itemName"] ?> </p>
                            </div>
                            <div class='col-2 view-mode'>
                                <p> <?= $_SESSION["products"][$i]["description"] ?> </p>
                            </div>
                            <div class='col-2 view-mode'>
                                <p> <?= $pricesStr ?> </p>
                            </div>
                            <div class='col-2 view-mode'>
                                <p> <?= $customizationsStr ?> </p>
                            </div>
                            
                            <!-- Edit Mode (hidden by default) -->
                            <div class='col-2 edit-mode' style='display: none;'>
                                <textarea class="form-control edit-itemName" rows="4"><?= $_SESSION["products"][$i]["itemName"] ?></textarea>
                                <input type="hidden" class="original-itemName" value="<?= $_SESSION["products"][$i]["itemName"] ?>">
                            </div>
                            <div class='col-2 edit-mode' style='display: none;'>
                                <textarea class="form-control edit-description" rows="4"><?= $_SESSION["products"][$i]["description"] ?></textarea>
                            </div>
                            <div class='col-2 edit-mode' style='display: none;'>
                                <textarea class="form-control edit-prices" rows="4"><?= $pricesStr ?></textarea>
                            </div>
                            <div class='col-2 edit-mode' style='display: none;'>
                                <textarea class="form-control edit-customizations" rows="4"><?= $customizationsStr ?></textarea>
                            </div>
                            
                            <div class='col-2'>
                                <div class="slider-container view-mode">
                                    <div class="slider-wrapper">
                                        <?php foreach($_SESSION["products"][$i]["imageURLs"] as $url){
                                            echo '<div class="slide"><img src="'.$url.'" alt="'.$_SESSION["products"][$i]["itemName"].' picture" class="product-image"></div>';
                                        }
                                        ?>
                                    </div>
                                    <?php if (sizeof($_SESSION["products"][$i]["imageURLs"]) > 1){
                                        echo '<button class="arrow left">‹</button>';
                                        echo '<button class="arrow right">›</button>';
                                    }
                                    ?>
                                </div>
                                <!-- Edit Mode Images -->
                                <div class="edit-mode" style='display: none;'>
                                    <div class="edit-images-container">
                                        <?php foreach($_SESSION["products"][$i]["imageURLs"] as $imgIndex => $url){ ?>
                                            <div class="edit-image-item" data-image-url="<?= $url ?>">
                                                <img src="<?= $url ?>" alt="Product image" class="edit-product-image" style="width: 80px; height: 80px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-danger remove-image-btn">×</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <input type="file" class="form-control mt-2 add-images-input" multiple accept="image/*">
                                    <small class="text-muted">Add new images or remove existing ones</small>
                                </div>
                            </div>
                            <div class='col-2 right-align'>
                                <button type="button" class="btn btn-cookie button-2 mt-2 edit-item-btn">Edit Item</button>
                                <button type="button" class="btn btn-success button-2 mt-2 save-edits-btn" style='display: none;'>Save Edits</button>
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
                        <textarea rows="4" name="partitionKeyValue" placeholder="Product Name" required></textarea>
                    </div>
                    <div class='col-2'>
                        <textarea rows="4" name="description" placeholder="Product description"></textarea>
                    </div>
                    <div class='col-2'>
                        <textarea rows="4" name="csvPrices" placeholder="qnty: price, qnty: price, ..." required></textarea>
                    </div>
                    <div class='col-2'>
                        <textarea rows="4" name="csvCustomizations" placeholder="cstm: price, cstm: price, ..."></textarea>
                    </div>
                    <div class='col-2'>
                        <input type="file" name="images[]" multiple accept="image/*" required></input>
                    </div>
                    <div class='col-2 right-align'>
                        <input type="hidden" name="tableName" value="products"></input>
                        <input type="hidden" name="partitionKey" value="itemName"></input>
                        <button type="submit" class="btn btn-cookie button-2 mt-2">Add Item</button>
                    </div>
                </form>
            </div>
            <?php
                $pageName = 'home_page';
                include(__DIR__ . "/../components/customize_page.php");
                $pageName = 'about_page';
                include(__DIR__ . "/../components/customize_page.php");
                $pageName = 'contact_page';
                include(__DIR__ . "/../components/customize_page.php");
            ?>
        </div>
        <div class="confirm-delete-form">
            <!-- TODO: Confirm Delete Item page -->
        </div>
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>