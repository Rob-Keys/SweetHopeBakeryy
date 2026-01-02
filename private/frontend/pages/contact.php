<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact the Sweet Hope Bakery</title>
        <meta name="description" content="Sweet Hope Bakery offers custom cakes, cookies, donuts, and more baked goods in Arlington, Virginia. Gluten-free, dairy-free, and other allergy restrictions can all be incorporated. Order your treats today!">
        <meta property="og:title" content="Contact the Sweet Hope Bakery">
        <meta property="og:description" content="Custom cakes and cookies in Northern Virginia.">
        <meta property="og:image" content="https://example.com/your-cake-photo.jpg">
        <meta property="og:url" content="https://sweethopebakeryy.com/contact">
        <link rel="icon" type="image/x-icon" href="/images/bakehouselogo.ico">
        <link rel="apple-touch-icon" href="/images/bakehouselogo.ico">
        <link rel="canonical" href="https://www.sweethopebakeryy.com/contact" />

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/contact.css">

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
            <div class="content mt-5 mb-5 fade-in-up">
                <h2 class="subtitle"><?= $_SESSION['contact_page_sections'][0]['headerText']?></h2>
                <h5 class="description"><?= $_SESSION['contact_page_sections'][0]['bodyText']?></h5>
                <div class="contact-info">
                    <p class="contact-info-email"><strong><?= $_SESSION['contact_page_sections'][1]['headerText']?>:</strong> <a href="mailto:<?= $_SESSION['contact_page_sections'][1]['bodyText']?>" class="contact-page-link"><?= $_SESSION['contact_page_sections'][1]['bodyText']?></a></p>
                    <p class="contact-info-instagram"><strong><?= $_SESSION['contact_page_sections'][2]['headerText']?>:</strong> <a href="https://www.instagram.com/sweethopebakeryy/" target="_blank" class="contact-page-link"><?= $_SESSION['contact_page_sections'][2]['bodyText']?> <i class="fa-brands fa-instagram"></i></a></p>
                </div>
            </div>
        </div>
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>