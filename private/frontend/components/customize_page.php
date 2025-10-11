<div class="m-5">
    <h2 class="subtitle">Edit <?= ucfirst(str_replace("_"," ",$pageName)) ?> Content</h2>
    <div class='row menu-row'>
        <div class='col-2'>
            <h5> Section Index </h5>
        </div>
        <div class='col-2'>
            <h5> Header Text </h5>
        </div>
        <div class='col-4'>
            <h5> Body Text </h5>
        </div>
        <div class='col-2'>
            <h5> Image URL </h5>
        </div>
        <div class='col-2'>
            <h5> Actions </h5>
        </div>
    </div>
    <?php
        for($i=0; $i<sizeof($_SESSION[$pageName."_sections"]); $i++) { ?>
            <div class='row menu-row'>
                <div class='col-2'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?> </p>
                </div>
                <div class='col-2'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["headerText"] ?> </p>
                </div>
                <div class='col-4'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["bodyText"] ?> </p>
                </div>
                <div class='col-2'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["imageURL"] ?> </p>
                </div>
                <div class='col-2'>
                    <form method="post" action="/customize_remove_item">
                        <input type="hidden" name="tableName" value="<?=$pageName?>"></input>
                        <input type="hidden" name="partitionKey" value="sectionIndex"></input>
                        <input type="hidden" name="partitionKeyValue" value="<?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?>"></input>
                        <button type="submit" class="btn btn-cookie button-2 mt-2">Remove Item</button>
                    </form>
                </div>
            </div>
    <?php } ?>
    <form method="post" action="/customize_add_item" class='row menu-row' enctype="multipart/form-data">
        <div class='col-2'>
            <input type="text" name="partitionKeyValue" placeholder="Section Index"></input>
        </div>
        <div class='col-2'>
            <input type="text" name="headerText" placeholder="Header text"></input>
        </div>
        <div class='col-4'>
            <input type="text" name="bodyText" placeholder="Body text"></input>
        </div>
        <div class='col-2'>
            <input type="file" name="image"></input>
        </div>
        <div class='col-2'>
            <input type="hidden" name="tableName" value="<?=$pageName?>"></input>
            <input type="hidden" name="partitionKey" value="sectionIndex"></input>
            <button type="submit" class="btn btn-cookie button-2 mt-2">Add Item</button>
        </div>
    </form>
</div>