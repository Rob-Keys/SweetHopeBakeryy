<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sweet Hope Bakery</title>
        <meta name="description" content="Sweet Hope Bakery offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="Sweet Hope Bakery">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://sweethopebakeryy.com">
        <link rel="icon" type="image/x-icon" href="/images/sweethopebakeryy.ico">
        <link rel="apple-touch-icon" href="/images/sweethopebakeryy.ico">
        <link rel="canonical" href="https://www.sweethopebakeryy.com/" />

        <base href="/">
        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/home.css">

        <script src="js/shared.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <main>
            <div class="d-flex justify-content-center front-holder">
                <div class="front-page text-center">
                    <div class="bg-third"></div>
                    <h1 class="pb-3 welcome mt-5 fade-in-up">Sweet Hope Bakery</h1>
                    <p class="mb-5 subwelcome fade-in-up">Made-To-Order Baking in Arlington, VA</p>
                </div>
            </div>
            <div class="mt-5 mb-5">
                <div class="justify-content-center text-center">
                    <h2 class="rhombus text-center">World Class Baking Since 2002</h2>
                    <a href="/menu" class="btn btn-lg btn-cookie fade-in-up mobile-button">See Our Menu</a>
                </div>
            </div>
            <?php
                foreach($_SESSION['home_page_sections'] as $index => $section){ ?>
                    <div class="<?= $index%2==0 ? "row mb-5" : "row row-reverse mb-5"?>">
                        <div class="col-5 <?= $index%2==0 ? "fade-in-right": "fade-in-left"?>">
                            <img style="width: 100%;" src=<?= $section['imageURL'] ?>>
                        </div>
                        <div class="col-6 d-flex align-items-center <?= $index%2==0 ? "fade-in-left": "fade-in-right"?>">
                            <div class="image-caption d-flex flex-column align-items-center">
                                <h3><?= $section['bodyText'] ?></h3>
                                <a href="/menu" class="btn btn-lg btn-cookie mt-5">See Our Menu</a>
                            </div>
                        </div>
                    </div>
            <?php } ?>
        </main>

        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>    