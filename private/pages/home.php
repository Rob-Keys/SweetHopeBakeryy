<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>703 Bakehouse</title>

        <link rel="stylesheet" href="styles/custom.css">
        <script src="js/custom.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("/home/bitnami/bakehouse/private/components/header.php"); ?>
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
                    <h1 class="rhombus text-center">World Class Baking Since 2002</h2>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-5">
                    <img style="width: 100%;" src="/images/allergy.jpg">
                </div>
                <div class="col-7">
                    <p class="ms-5 me-5 pe-5 image-caption">Indulge without compromise! Our gluten-free and dairy-free snickerdoodles are soft, chewy, and packed with that classic cinnamon-sugar flavor you love — all without the common allergens.</p>
                    <a href="/order" class="btn btn-lg btn-cookie float-end button-1">Order Now</a>
                </div>
            </div>
            <div class="row mb-5 mt-5">
                <div class="col-5">
                    <img src="/images/cupcakes.jpg" style="width: 100%">
                </div>
                <div class="col-7">
                    <p class="ms-5 me-5 pe-5 image-caption">Sweet, fluffy, and topped with just the right swirl of frosting — our cupcakes are little bites of joy baked fresh in every batch. Whether you're celebrating something special or just craving a midweek pick-me-up, these charming treats bring homemade warmth and irresistible flavor to any moment.</p>
                    <a href="/order" class="ms-5 btn btn-cookie btn-lg button-2">Order Now</a>
                </div>
            </div>
        </main>

        <?php include("/home/bitnami/bakehouse/private/components/footer.php"); ?>
    </body>
</html>    