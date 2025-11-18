<div class="m-5">
    <h2 class="subtitle">Edit <?= ucfirst(str_replace("_"," ",$pageName)) ?> Content</h2>
    <div class='row menu-row'>
        <div class='col-2'>
            <h5> Section Index </h5>
        </div>
        <?php if($pageName !== 'home_page'){ ?>
            <div class='col-2'>
                <h5> Header Text </h5>
            </div>
        <?php } ?>
        <div class='<?= $pageName === 'about_page' ? 'col-4' : 'col-6' ?>'>
            <h5> Body Text </h5>
        </div>
        <?php if($pageName !== 'contact_page'){ ?>
            <div class='col-2'>
                <h5> Image URL </h5>
            </div>
        <?php } ?>
        <div class='col-2 right-align'>
            <h5> Actions </h5>
        </div>
    </div>
    <?php
        for($i=0; $i<sizeof($_SESSION[$pageName."_sections"]); $i++) { ?>
            <div class='row menu-row' data-section-index="<?= $i ?>" data-page-name="<?= $pageName ?>">
                <!-- View Mode -->
                <div class='col-2 view-mode'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?> </p>
                </div>
                <?php if($pageName !== 'home_page'){ ?>
                    <div class='col-2 view-mode'>
                        <p> <?= $_SESSION[$pageName."_sections"][$i]["headerText"] ?> </p>
                    </div>
                <?php } ?>
                <div class='<?= $pageName === 'about_page' ? 'col-4' : 'col-6' ?> view-mode'>
                    <p> <?= $_SESSION[$pageName."_sections"][$i]["bodyText"] ?> </p>
                </div>
                
                <!-- Edit Mode -->
                <div class='col-2 edit-mode' style='display: none;'>
                    <textarea class="form-control edit-sectionIndex" rows="2" disabled><?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?></textarea>
                    <input type="hidden" class="original-sectionIndex" value="<?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?>">
                </div>
                <?php if($pageName !== 'home_page'){ ?>
                    <div class='col-2 edit-mode' style='display: none;'>
                        <textarea class="form-control edit-headerText" rows="2"><?= $_SESSION[$pageName."_sections"][$i]["headerText"] ?></textarea>
                    </div>
                <?php } ?>
                <div class='<?= $pageName === 'home_page' ? 'col-4' : 'col-6' ?> edit-mode' style='display: none;'>
                    <textarea class="form-control edit-bodyText" rows="4"><?= $_SESSION[$pageName."_sections"][$i]["bodyText"] ?></textarea>
                </div>
                
                <?php if($pageName !== 'contact_page'){ ?>
                    <div class='col-2'>
                        <div class="view-mode">
                            <img class='demo-photo' src="<?= $_SESSION[$pageName."_sections"][$i]["imageURL"] ?>" alt="Section's associated image">
                        </div>
                        <!-- Edit Mode Image -->
                        <div class="edit-mode" style='display: none;'>
                            <div class="edit-images-container">
                                <div class="edit-image-item" data-image-url="<?= $_SESSION[$pageName."_sections"][$i]["imageURL"] ?>">
                                    <img src="<?= $_SESSION[$pageName."_sections"][$i]["imageURL"] ?>" alt="Section image" class="edit-product-image" style="width: 80px; height: 80px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger remove-image-btn">×</button>
                                </div>
                            </div>
                            <input type="file" class="form-control mt-2 add-images-input" accept="image/*">
                            <small class="text-muted">Replace image</small>
                        </div>
                    </div>
                <?php } ?>
                <div class='col-2 right-align'>
                    <button type="button" class="btn btn-cookie button-2 mt-2 edit-section-btn">Edit Section</button>
                    <button type="button" class="btn btn-success button-2 mt-2 save-section-btn" style='display: none;'>Save Edits</button>
                    <form method="post" action="/customize_remove_item" class="remove-section-form">
                        <input type="hidden" name="tableName" value="<?=$pageName?>">
                        <input type="hidden" name="partitionKey" value="sectionIndex">
                        <input type="hidden" name="partitionKeyValue" value="<?= $_SESSION[$pageName."_sections"][$i]["sectionIndex"] ?>">
                        <button type="submit" class="btn btn-danger button-2 mt-2">Remove Section</button>
                    </form>
                </div>
            </div>
    <?php } ?>
    <form method="post" action="/customize_add_item" class='row menu-row' enctype="multipart/form-data">
        <div class='col-2'>
            <input type="text" name="partitionKeyValue" placeholder="Section Index"></input>
        </div>
        <?php if($pageName !== 'home_page'){ ?>
            <div class='col-2'>
                <textarea rows="2" name="headerText" placeholder="Header text"></textarea>
            </div>
        <?php } ?>
        <div class='<?= $pageName === 'about_page' ? 'col-4' : 'col-6' ?>'>
            <textarea rows="4" name="bodyText" placeholder="Body text"></textarea>
        </div>
        <?php if($pageName !== 'contact_page'){ ?>
            <div class='col-2'>
                <input type="file" name="images[]"></input>
            </div>
        <?php } ?>
        <div class='col-2 right-align'>
            <input type="hidden" name="tableName" value="<?=$pageName?>"></input>
            <input type="hidden" name="partitionKey" value="sectionIndex"></input>
            <button type="submit" class="btn btn-cookie button-2 mt-2">Add Section</button>
        </div>
    </form>
</div>
