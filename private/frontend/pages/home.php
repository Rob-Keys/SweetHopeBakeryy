<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>703 Bakehouse</title>
        <meta name="description" content="703 Bakehouse offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="703 Bakehouse">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://703bakehouse.com">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.703bakehouse.com/" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/home.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("/home/bitnami/bakehouse/private/frontend/components/header.php"); ?>
        <main>
            <div class="d-flex justify-content-center front-holder">
                <div class="front-page text-center">
                    <h1 class="pb-3 welcome mt-5">The 703 Bakehouse</h1>
                    <p class="mb-5 subwelcome">Made-To-Order Baking in Arlington, VA</p>
                    <a href="/order" class="btn btn-lg btn-cookie">Order Now</a>
                </div>
            </div>
            <div class="mt-5 mb-5">
                <div class="justify-content-center text-center">
                    <h3 class="rhombus text-center">World Class Baking Since 2002</h2>
                </div>
            </div>
            <?php
                foreach($_SESSION['home_page_sections'] as $section){ ?>
                    <div class="row mb-5">
                        <div class="col-5">
                            <img style="width: 100%;" src=<?= $section['imageURL'] ?>>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <h3 class="ms-5 me-5 pe-5 image-caption"><?= $section['bodyText'] ?></p>
                            <a href="/order" class="btn btn-lg btn-cookie float-end button-1">Order Now</a>
                        </div>
                    </div>
            <?php } ?>
        </main>

        <?php include("/home/bitnami/bakehouse/private/frontend/components/footer.php"); ?>
    </body>
</html>    