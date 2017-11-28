<?php
    session_start();
    include_once 'connection/db_connectie.php';
    include_once 'user_classes.php';
    $user = new gebruiker($dbh);

    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('login/login.php');
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<a href="login/uitloggen.php">Uitloggen</a>
<Br>
<a href="gebruikers/aanmaken.php">Gebruiker aanmaken</a>
<br>
<br>
<?php
    echo $gebruiker['Voornaam'];
    echo !is_null($gebruiker['Tussenvoegsel']) ? ' ' . $gebruiker['Tussenvoegsel'] . ' ' : ' ';
    echo $gebruiker['Achternaam'];
?>
</body>
</html>