<?php
    session_start();
    include_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    include_once '../booking_classes.php';
    $user = new gebruiker($dbh);
    $timestamp = new booking($dbh);

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rolID = $gebruiker['rol_id'];

    if (empty($gebruiker)) {
        $user->redirect('../pakken/pietenpakken.php');
    }

    if ($rolID == 1) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om deze pagina te bezoeken!'
        );
        $user->redirect('../pakken/pietenpakken.php');
    }

    if (isset($_SESSION['flash'])) {
        $error = $_SESSION['flash'];
        unset($_SESSION);
    }
    
    if (isset($_POST['opslaan'])) {
        $timestamp->tijdblok_aanmaken($_POST, $gebruiker);
        $url = '../pakken/pietenpakken.php';
        $user->redirect($url);
    }
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
    <link rel="stylesheet" href="../styling/footer.css">
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="icon" href="../plaatjes/favicon.png" type="image/gif" sizes="16x16">
    <title>Tijdsblok</title>
</head>
<body>
<div class="topnav">
    <a href="../pakken/pietenpakken.php?pagina=1">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php?pagina=1">Sinterklaaspakken</a>
    <?php if ($rolID > 1) { ?>
        <a href="../pakken/beschadigd.php?pagina=1">Beschadigd</a>
    <?php } ?>
    <?php if ($rolID == 3) { ?>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <h1>Tijdsblok aanmaken</h1>
    <hr>
    <form action="tijdsblok.php" method="POST">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="tijdsblok_naam" class="col-sm-3 col-form-label">Naam</label>
                    <div class="col-sm-9">
                        <input name="tijdsblok_naam" required type="text" class="form-control" id="tijdsblok_naam"
                               value="<?php echo isset($_POST["tijdsblok_naam"]) ? $_POST["tijdsblok_naam"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="begin_datum" class="col-sm-3 col-form-label">Begin datum</label>
                    <div class="col-sm-9">
                        <input name="begin_datum"
                               value="<?php echo isset($_POST["begin_datum"]) ? $_POST["begin_datum"] : ''; ?>" required
                               type="datetime-local" class="form-control" id="begin_datum">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="eind_datum" class="col-sm-3 col-form-label">Eind datum</label>
                    <div class="col-sm-9">
                        <input name="eind_datum"
                               value="<?php echo isset($_POST["eind_datum"]) ? $_POST["eind_datum"] : ''; ?>" required
                               type="datetime-local" class="form-control" id="eind_datum">
                    </div>
                </div>
            </div>
        </div>
        <button name="opslaan" class="btn btn-primary">Opslaan</button>
    </form>
    <div class="footer">
        <div class="left">
            <a href="../gebruikers/aanmaken.php">Gebruiker aanmaken</a>
        </div>
        <div class="right">
            <a href="../gebruikers/mijn-account.php">Account</a>
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</div>
</body>
</html>