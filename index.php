<?php
    session_start();
    include_once 'connection/db_connectie.php';
    include_once 'user_classes.php';
    $user = new gebruiker($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('login/login.php');
    }

    if (isset($_SESSION['flash'])){
        $error = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    // rolID van de gebruike ophalen
    $rolID = $gebruiker['RolID'];

    // gebruikersrol ophalen doormiddel van een functie
    $rol = $user->gebruikers_rol($rolID);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="styling/footer.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">PietenOnTour <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
            </ul>
        </div>
    </nav>

    <a href="login/uitloggen.php">Uitloggen</a>
    <Br>
    <a href="gebruikers/aanmaken.php">Gebruiker aanmaken</a> <br>
    <a href="gebruikers/bekijken.php?id=4">Gebruikers bekijken</a>
    <br>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type'];?> ">
            <?php echo $error['message'];?>
        </div>
    <?php } ?>
    <br>
    <?php
        echo $gebruiker['Voornaam'];
        echo !is_null($gebruiker['Tussenvoegsel']) ? ' ' . $gebruiker['Tussenvoegsel'] . ' ' : ' ';
        echo $gebruiker['Achternaam'];
    ?>
    <div class="footer">
        <p>Footer</p>
    </div>
</div>
</body>
<script>
    setTimeout(function() {
        $('.alert').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
</script>
</html>