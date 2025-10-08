<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>About 703 Bakehouse</title>
        <meta name="description" content="703 Bakehouse offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="703 Bakehouse">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://703bakehouse.com/about">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.703bakehouse.com/about" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/about.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="about-us-body">
        <?php include("/home/bitnami/bakehouse/private/frontend/components/header.php"); ?>
        <div class="content">
            <?php
                foreach($_SESSION['about_page_sections'] as $index => $section){ ?>
                    <div class="<?= $index%2==0 ? "row row-reverse mb-4": "row mb-4"?>">
                        <div class="col-6">
                            <img src="<?=$section['imageURL']?>" class="caroline-image">
                        </div>
                        <div class="col-6 d-flex flex-column justify-content-center">
                            <h2><?=$section['headerText']?></h2>
                            <p><?=$section['bodyText']?></p>
                        </div>
                    </div>
            <?php } ?>
            <div class="row rob-card">
                <div class="col-3">
                    <img src="https://placehold.co/300" alt="Rob, Web Developer" class="rob-image">
                </div>
                <div class="col-9">
                    <h2>Meet Rob</h2>
                    <p>
                    Caroline’s brother Rob is the tech brain behind 703 Bakehouse’s online home. He designed and developed this site to give you a seamless ordering experience—so you can focus on enjoying the pastries, not the click-throughs.
                    </p>
                </div>
            </div>
        </div>
        <?php include("/home/bitnami/bakehouse/private/frontend/components/footer.php"); ?>
    </body>
</html>
