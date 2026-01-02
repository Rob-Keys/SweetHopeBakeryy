<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About Sweet Hope Bakery</title>
        <meta name="description" content="Sweet Hope Bakery offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="Sweet Hope Bakery">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://sweethopebakeryy.com/about">
        <link rel="icon" type="image/x-icon" href="/images/sweethopebakeryy.ico">
        <link rel="apple-touch-icon" href="/images/sweethopebakeryy.ico">
        <link rel="canonical" href="https://www.sweethopebakeryy.com/about" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/about.css">

        <script src="js/shared.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="about-us-body">
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <div class="content">
            <?php
                foreach($_SESSION['about_page_sections'] as $index => $section){ ?>
                    <div class="<?= $index%2==0 ? "row row-reverse mb-4": "row mb-4"?>">
                        <div class="col-6 <?= $index%2==0 ? "fade-in-left": "fade-in-right"?>">
                            <img src="<?=$section['imageURL']?>" class="caroline-image">
                        </div>
                        <div class="col-6 d-flex flex-column justify-content-center <?= $index%2==0 ? "fade-in-right": "fade-in-left"?>">
                            <h2><?=$section['headerText']?></h2>
                            <h5><?=$section['bodyText']?></h5>
                        </div>
                        <hr class="mobile-divider mt-3">
                    </div>
            <?php } ?>
            <!-- Make the anchor the card itself so the whole card is clickable and CSS can target the anchor directly -->
            <div class="fade-in-up">
                <a href="https://www.linkedin.com/in/rob-keys/" class="row rob-card" target="_blank" rel="noopener noreferrer" aria-label="Rob — LinkedIn profile">
                    <div class="col-3">
                        <img src="/images/about/rob.avif" alt="Rob, Web Developer" class="rob-image">
                    </div>
                    <div class="col-9">
                        <h2>Meet Rob</h2>
                        <h4>
                        Caroline’s brother Rob is the tech brain behind Sweet Hope Bakery’s online home. He designed and developed this site to give you a seamless ordering experience—so you can focus on enjoying the pastries, not the click-throughs.
                        </h4>
                    </div>
                </a>
            </div>
        </div>
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>
