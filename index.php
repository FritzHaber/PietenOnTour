<?php
    session_start();
    include_once 'connection/db_connectie.php';
    include_once 'user_classes.php';
    $user = new gebruiker($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('login/login.php');
    }

    if (isset($_SESSION['flash'])) {
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
    <link rel="stylesheet" href="styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="topnav">
    <a class="active" href="pakken/pietenpakken.php">Pietenpakken</a>
    <a href="pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
    <?php if ($rolID == '3') { ?>
        <a href="pakken/beschadigd.php">Beschadigd</a>
        <a href="gebruikers/gebuikers.php">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <h2>Overzicht pietenpakken</h2>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <table class="table">
        <thead>
        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Email</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><img src="http://via.placeholder.com/100x100" alt="..." class="img-thumbnail"></td>
            <td>Doe</td>
            <td>john@example.com</td>
        </tr>
        <tr>
            <td><img src="http://via.placeholder.com/100x100" alt="..." class="img-thumbnail"></td>
            <td>Moe</td>
            <td>mary@example.com</td>
        </tr>
        <tr>
            <td><img src="http://via.placeholder.com/100x100" alt="..." class="img-thumbnail"></td>
            <td>Dooley</td>
            <td>july@example.com</td>
        </tr>
        </tbody>
    </table>
    <Br>
    <a href="gebruikers/aanmaken.php">Gebruiker aanmaken</a> <br>
    <a href="gebruikers/bekijken.php?id=4">Gebruikers bekijken</a>
<!--    <br>-->
<!--    <br>-->
<!--    --><?php
//        echo $gebruiker['Voornaam'];
//        echo !is_null($gebruiker['Tussenvoegsel']) ? ' ' . $gebruiker['Tussenvoegsel'] . ' ' : ' ';
//        echo $gebruiker['Achternaam'];
//    ?>
    <div class="footer">
        <div class="left">
            <a href="login/uitloggen.php">Pak toevoegen</a>
        </div>
        <div class="right">
            <a href="#">Uitloggen</a>
        </div>
    </div>
</div>
</body>
<script>
    setTimeout(function () {
        $('.alert').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
</script>
</html>