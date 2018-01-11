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
        $booking->aanmaken_bezoek($_POST);
        $url = 'bewerken_bezoek.php?id=' . $dbh->lastInsertId();
        $user->redirect($url);
    }

    $tijdblokken = $booking->ophalen_tijdblokken();
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
    <title>Aanmaken | bezoek</title>
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
    <h1>Bezoek aanmaken</h1>
    <hr>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <form method="POST" action="aanmaken_bezoek.php">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="type" class="col-sm-3 col-form-label">Type bezoek</label>
                    <select name="type" class="form-control col-sm-9" id="type">
                        <option value="piet">Piet
                        </option>
                        <option value="sint">Sint
                        </option>
                        <option value="klop">Klop
                        </option>
                    </select>
                </div>
                <div class="form-group row">
                    <label for="begin" class="col-sm-3 col-form-label">Begin</label>
                    <div class="col-sm-9">
                        <input name="begin" required type="datetime-local" class="form-control" id="begin">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="eind" class="col-sm-3 col-form-label">Einde</label>
                    <div class="col-sm-9">
                        <input name="eind" required type="datetime-local" class="form-control" id="eind">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="naam" class="col-sm-3 col-form-label">Naam bezoeker</label>
                    <div class="col-sm-9">
                        <input name="naam" required type="text" class="form-control" id="naam"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="adres" class="col-sm-3 col-form-label">Adres</label>
                    <div class="col-sm-9">
                        <input name="adres" required type="text" class="form-control" id="adres"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="aantal_kinderen" class="col-sm-3 col-form-label">Aantal kinderen</label>
                    <div class="col-sm-9">
                        <input name="aantal_kinderen" required type="number" class="form-control" id="aantal_kinderen"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="allergie" class="col-sm-3 col-form-label">Allergie</label>
                    <div class="col-sm-9">
                        <input name="allergie" type="text" class="form-control" id="allergie"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telefoon" class="col-sm-3 col-form-label">Telefoon</label>
                    <div class="col-sm-9">
                        <input name="telefoon" required type="text" class="form-control" id="telefoon"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label">E-mail</label>
                    <div class="col-sm-9">
                        <input name="email" required type="email" class="form-control" id="email"
                               value="">
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="verhaaltjes_binnen" class="col-sm-3 col-form-label">Verhaaltjes binnen</label>
                    <div class="col-sm-9">
                        <input name="verhaaltjes_binnen" type="checkbox" class="form-control" id="verhaaltjes_binnen">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="opmerking" class="col-sm-3 col-form-label">Opmerking</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="opmerking" id="" cols="10" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="betaald" class="col-sm-3 col-form-label">Betaald</label>
                    <div class="col-sm-9">
                        <input name="betaald" type="checkbox" class="form-control" id="betaald">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="aantal_pieten" class="col-sm-3 col-form-label">Aantal pieten</label>
                    <div class="col-sm-9">
                        <input name="aantal_pieten" required type="text" class="form-control" id="aantal_pieten"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="aantal_sinterklazen" class="col-sm-3 col-form-label">Aantal sinterklazen</label>
                    <div class="col-sm-9">
                        <input name="aantal_sinterklazen" required type="text" class="form-control" id="aantal_sinterklazen"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="aantal_schminkers" class="col-sm-3 col-form-label">Aantal schminkers</label>
                    <div class="col-sm-9">
                        <input name="aantal_schminkers" required type="text" class="form-control" id="aantal_schminkers"
                               value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tijdblok" class="col-sm-3 col-form-label">Tijdblok</label>
                    <select name="tijdblok" class="form-control col-sm-9" id="tijdblok">
                        <?php foreach ($tijdblokken as $tijdblok){ ?>
                        <option value="<?php echo $tijdblok['tijdblok_id']; ?>"><?php echo date('j-M-Y H:i', strtotime($tijdblok['begin_tijd'])) . ' - ' . date('j-M-Y H:i', strtotime($tijdblok['eind_tijd'])) ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <button name="opslaan" class="btn btn-primary">Bezoek aanmaken</button>
    </form>
    <div class="footer">
        <div class="right">
            <a href="../gebruikers/mijn-account.php">Account</a>
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</body>
</html>