<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    include_once '../booking_classes.php';
    $user = new gebruiker($dbh);
    $booking = new booking($dbh);

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rolID = $gebruiker['rol_id'];

    if (empty($gebruiker)) {
        $user->redirect('../pakken/pietenpakken.php');
    }

    if ($rolID != 3) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om inschrijvingen te bevestigen!'
        );
        $user->redirect('../pakken/pietenpakken.php');
    }

    if (isset($_SESSION['flash'])) {
        $error = $_SESSION['flash'];
        unset($_SESSION);
    }

    if (isset($_POST['bevestigen'])) {
        $tijdblok_id = $_POST['tijdblok_id'];
        $booking->inschrijvingen_bevestigen($_POST, $tijdblok_id);
    }

    $inschrijvingen = $booking->tijdblokken_inschrijvingen();
    $tijdsblokken = $booking->ophalen_tijdblokken();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styling/footer.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"
            integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
            integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styling/base.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../scripts/script.js"></script>
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="icon" href="../plaatjes/favicon.png" type="image/gif" sizes="16x16">
    <title>Inschrijvingen | bevestigen</title>
</head>
<body>
<div class="topnav">
    <a href="../pakken/pietenpakken.php?pagina=1">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php?pagina=1">Sinterklaaspakken</a>
    <?php if ($rolID == '3' || $rolID == '2') { ?>
        <a href="../pakken/beschadigd.php?pagina=1">Beschadigd</a>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <h1>Inschrijvingen</h1>
    <hr>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <div id="accordion" role="tablist">
        <?php if (empty($tijdsblokken)) { ?>
            Er zijn nog geen tijdblokken beschikbaar
        <?php } else { ?>
            <?php foreach ($tijdsblokken as $key => $tijdsblok) { ?>
                <div class="card">
                    <div class="card-header" role="tab" id="heading<?php echo $key; ?>">
                        <h5 class="mb-0">
                            <a class="collapsed" data-toggle="collapse"
                               href="#collapse<?php echo $key; ?>"
                               aria-expanded="false" aria-controls="collapse<?php echo $key; ?>">
                                <?php echo date('d-m-Y', strtotime($tijdsblok['begin_tijd'])); ?>
                            </a>
                        </h5>
                    </div>
                    <div id="collapse<?php echo $key; ?>" class="collapse" role="tabpanel"
                         aria-labelledby="heading<?php echo $key; ?>" data-parent="#accordion">
                        <form action="bevestigen_inschrijvingen.php" method="POST">
                            <div class="card-body">
                                <input name="tijdblok_id" type="hidden"
                                       value="<?php echo $tijdsblok['tijdblok_id']; ?>">
                                <h3><?php echo $tijdsblok['tijdblok_naam']; ?></h3>
                                <?php foreach ($inschrijvingen as $inschrijving) { ?>
                                    <p>
                                        <?php if ($tijdsblok['tijdblok_id'] == $inschrijving['tijdsblok_id']) { ?>
                                            <input name="<?php echo $inschrijving['inschrijving_id'] ?>"
                                                   value="<?php echo $inschrijving['inschrijving_id'] ?>"
                                                <?php echo $inschrijving['bevestigd'] == 1 ? 'checked' : ''; ?>
                                                   type="checkbox" class="form-control" id="beschadigd">
                                            <?php
                                            echo $inschrijving['voornaam'];
                                            echo !is_null($inschrijving['tussenvoegsel']) ? ' ' .
                                                $inschrijving['tussenvoegsel'] . ' ' : ' ';
                                            echo $inschrijving['achternaam'];
                                            ?>
                                        <?php } ?>
                                    </p>
                                <?php } ?>
                                <button name="bevestigen" class="btn btn-primary">Inschrijvingen bevestigen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    </div>
    <div class="footer">
        <div class="right">
            <a href="../gebruikers/mijn-account.php">Account</a>
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</body>
</html>