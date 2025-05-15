<?php
    $id = isset($_SESSION["products"][$i]['id']) ? $_SESSION["products"][$i]['id'] : "";
    $name = isset($_SESSION["products"][$i]['name']) ? $_SESSION["products"][$i]['name'] : "";
    $prices = isset($_SESSION["products"][$i]['prices']) ? $_SESSION["products"][$i]['prices'] : "";
    $image = isset($_SESSION["products"][$i]['image']) ? $_SESSION["products"][$i]['image'] : "";
    $customizations = isset($_SESSION["products"][$i]['customizations']) ? $_SESSION["products"][$i]['customizations'] : "";
?>
<div class="product-card mb-4 p-3 rounded">
    <h3 class="product-name"><?=$name?></h3>
    <img src="<?=$image?>" class="product-image mb-2">
    <form class="add-to-cart-form" method="POST" action="/order">
        <input type="hidden" name="id" value="<?=$id?>">
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
	    <input type="hidden" name="action" value="add">
        <button type="submit" class="btn btn-lg btn-cookie">Add to Cart</button>
    </form>
</div>
