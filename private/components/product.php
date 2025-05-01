<?php
    $id = isset($_SESSION["id"]) ? $_SESSION["id"] : "";
    $name = isset($_SESSION["name"]) ? $_SESSION["name"] : "";
    $prices = isset($_SESSION["prices"]) ? $_SESSION["prices"] : "";
?>
<div class="product-card mb-4 p-3 border rounded">
    <h3><?=$name?></h3>
    <form method="POST" action="/order">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="name" value="<?=$name?>">
        <div class="mb-2"><label>Quantity Pack:</label> 
            <select name="opt_qty" class="form-select d-inline-block w-auto">;
                <?php    
                    foreach ($prices as $qty => $price) {
                        printf("<option value='%d' style='font-family: sans-serif'>%d for \$%.2f</option>", $qty, $qty, $price);
                    }
                ?>
            </select>
        </div>
        <?php $firstPrice = reset($prices); ?>
        <input type="hidden" name="price" value="<?=$firstPrice?>">
        <button type="submit" name="action" value="add" class="btn btn-cookie">Add to Cart</button>
    </form>
</div>
