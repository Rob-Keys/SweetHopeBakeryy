<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Authenticate</title>
        <link rel="icon" type="image/x-icon" href="/images/sweethopebakeryy.ico">
        <link rel="apple-touch-icon" href="/images/sweethopebakeryy.ico">

        <link rel="stylesheet" href="styles/shared.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <div class="wrapper">
            <div class="content ms-5 mt-5 mb-5">
                <h2 class="subtitle">Provide your password to access this page</h2>
                <form method="post" action="<?=isset($_SESSION["desired_page"]) ? $_SESSION["desired_page"] : "/mail" ?>">
                    <!-- Dummy username field -->    
                    <input type="text" name="username" autocomplete="username" style="display:none">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required></input>
                    <button type="submit" class="btn btn-cookie button-2 mt-2">Submit Password</button>
                </form>
            </div>
        </div>
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>