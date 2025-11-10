<?php
    $name = $_SESSION["products"][$i]['itemName'];
    $images = $_SESSION["products"][$i]['imageURLs'];
    $prices = $_SESSION["products"][$i]['prices'];
    $description = $_SESSION["products"][$i]['description'];
    $customizations = $_SESSION["products"][$i]['customizations'];
?>
<div class="product-card mb-4 p-3 rounded">
    <h3 class="product-name"><?=$name?></h3>
    <div class="slider-container">
        <div class="slider-wrapper">
            <?php foreach($images as $url){
                echo '<div class="slide"><img src="'.$url.'" alt="'.$name.' picture" class="product-image"></div>';
            }
            ?>
        </div>
        <?php if (sizeof($images) > 1){
            echo '<button class="arrow left">‹</button>';
            echo '<button class="arrow right">›</button>';
        }
        ?>
    </div>
    <?php 
    if($description != ""){
        echo "<h5>".$description."</h5>";
    }
    ?>
    <form class="add-to-cart-form" method="POST" action="/order">
        <input type="hidden" name="name" value="<?=$name?>">
        <div class="mb-2 product-quantity"><label>Quantity:</label> 
            <select name="quantity" class="form-select d-inline-block w-auto product-select">;
                <?php
                    foreach ($prices as $qty => $price) {
                        echo "<option value='".$qty."_".$price."' style='font-family: sans-serif'>".$qty." for $".number_format($price,2)."</option>";
                    }
                ?>
            </select>
        </div>
    <!--
        <div class="mb-2">Customizations:
            <ul class="customizationsList form-select d-inline-block w-auto product-select">
                <li>--</li>
                <?php
                    foreach ($customizations as $customization => $price) {
                        echo "<li value='".$customization."_".$price."' class='customization' style='font-family: sans-serif'>".$customization.": +$".number_format($price,2)."</li>";
                    }
                ?>
            </ul>
        </div>
    -->
	    <input type="hidden" name="action" value="add">
        <button type="submit" class="btn btn-lg btn-cookie">Add to Cart</button>
    </form>
</div>
