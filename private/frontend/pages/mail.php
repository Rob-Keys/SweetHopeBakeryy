<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Customize</title>
        <link rel="icon" type="image/x-icon" href="/images/sweethopebakeryy.ico">
        <link rel="apple-touch-icon" href="/images/sweethopebakeryy.ico">

        <link rel="stylesheet" href="styles/shared.css">
        <link rel="stylesheet" href="styles/customize.css">

        <script src="js/mail.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tagesschrift&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/21730f7c7c.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include(__DIR__ . "/../components/header.php"); ?>
        <h2> Inbox: </h2>
        <?php // Sort the inbox by date, newest first
        usort($_SESSION['inbox'], function($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        ?>
        <?php foreach ($_SESSION['inbox'] as $mail){
            echo "<div class='bg-light m-3'>";
                echo "<div>"."from: ".$mail["from"]."</div>";
                echo "<div>"."to: ".$mail["to"]."</div>";
                echo "<div>"."date: ".$mail["date"]."</div>";
                echo "<div>"."subject: ".$mail["subject"]."</div>";
                echo "<div>"."body: ".$mail["body"]."</div>";
            echo "</div>";
        }
        ?>

        <?php // Sort the outbox by date, newest first
        usort($_SESSION['outbox'], function($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        ?>
        <h2> Sent: </h2>
        <?php foreach ($_SESSION['outbox'] as $mail){
            echo "<div class='bg-light m-3'>";
                echo "<div>"."from: ".$mail["from"]."</div>";
                echo "<div>"."to: ".$mail["to"]."</div>";
                echo "<div>"."date: ".$mail["date"]."</div>";
                echo "<div>"."subject: ".$mail["subject"]."</div>";
                echo "<div>"."body: ".$mail["body"]."</div>";
            echo "</div>";
        }
        ?>

        <h2> Send a new email: </h2>
        <div class="row">
            <form method="post" action="/mail" class="col-md-6 m-3 d-flex flex-column">
                <input type="hidden" name="send-mail" value="true"></input>
                <input type="text" name="sender" placeholder="From (full email address)" required></input>
                <input type="text" name="recipients" placeholder="Comma seperated Recipient email addresses" required></input>
                <input type="text" name="subject" placeholder="Subject" required></input>
                <input type="text" name="body" id="body-input" placeholder="Body (HTML)" required></input>
                <button type="submit" class="btn btn-cookie button-2 mt-2">Send Email</button>
            </form>
            <div class="col-md-5">
                <div id="body-preview"></div>
            </div>
        </div>
        <?php include(__DIR__ . "/../components/footer.php"); ?>
    </body>
</html>