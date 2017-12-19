<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rolID = $gebruiker['rol_id'];

    if (empty($gebruiker)) {
        $user->redirect('../index.php');
    }

    if (isset($_SESSION['gebruiker_aangemaakt'])) {
        $error = $_SESSION['gebruiker_aangemaakt'];
        unset($_SESSION['gebruiker_aangemaakt']);
    }

    if (isset($_POST['wachtwoord-opslaan'])) {
        $error = $user->wachtwoord_veranderen($_POST, $gebruiker['gebruiker_id']);
    }

    if (isset($_POST['opslaan'])) {
        if (!empty($_POST['mail']) && !empty($_POST['voornaam']) && !empty($_POST['achternaam']) &&
            !empty($_POST['geb_datum']) && !empty($_POST['woonplaats']) && !empty($_POST['telefoon'])
        ) {
            if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $gebruiker = $user->gebruiker_bewerken($_POST, $gebruiker['gebruiker_id']);
                if (isset($gebruiker['type'])) {
                    $error = $gebruiker;
                    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
                } else {
                    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
                    $error = array(
                        'type' => 'success',
                        'message' => 'Je account is opgeslagen!'
                    );
                }
            } else {
                $error = array(
                    'type' => 'danger',
                    'message' => 'E-mail adres is niet geldig!'
                );
            }
        } else {
            $error = array(
                'type' => 'danger',
                'message' => 'Niet alle velden zijn geldig ingevuld!'
            );
        }
    }
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
    <script src="../scripts/script.js"></script>
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Pak bewerken</title>
</head>
<body>
<div class="topnav">
    <a href="../pakken/pietenpakken.php?pagina=1">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php?pagina=1">Sinterklaaspakken</a>
    <?php if ($rolID == '3') { ?>
        <a href="../pakken/beschadigd.php?pagina=1">Beschadigd</a>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>

<div class="container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
               aria-selected="true">Account gegevens</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
               aria-selected="false">Wachtwoord</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <h1>
                Account bewerken
            </h1>
            <hr>
            <?php if (!empty($error)) { ?>
                <div class="alert alert-<?php echo $error['type']; ?> ">
                    <?php echo $error['message']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="mijn-account.php">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="voornaam" class="col-sm-3 col-form-label">Voornaam</label>
                            <div class="col-sm-9">
                                <input name="voornaam" required type="text" class="form-control" id="voornaam"
                                       value="<?php echo isset($gebruiker["voornaam"]) ? $gebruiker["voornaam"] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tussenvoegsel" class="col-sm-3 col-form-label">Tussenvoegsel</label>
                            <div class="col-sm-9">
                                <input name="tussenvoegsel" type="text" class="form-control" id="tussenvoegsel"
                                       value="<?php echo isset($gebruiker["tussenvoegsel"]) ? $gebruiker["tussenvoegsel"] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="achternaam" class="col-sm-3 col-form-label">Achternaam</label>
                            <div class="col-sm-9">
                                <input name="achternaam" required type="text" class="form-control" id="achternaam"
                                       value="<?php echo isset($gebruiker["achternaam"]) ? $gebruiker["achternaam"] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="woonplaats" class="col-sm-3 col-form-label">Woonplaats</label>
                            <div class="col-sm-9">
                                <input name="woonplaats" required type="text" class="form-control" id="woonplaats"
                                       value="<?php echo isset($gebruiker["woonplaats"]) ? $gebruiker["woonplaats"] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mail" class="col-sm-3 col-form-label">E-mail</label>
                            <div class="col-sm-9">
                                <input name="mail" required type="email" class="form-control" id="mail"
                                       value="<?php echo isset($gebruiker["email"]) ? $gebruiker["email"] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="functie" class="col-sm-3 col-form-label">Functie</label>
                            <select name="functie" class="form-control col-sm-9" id="functie">
                                <option <?php if ($gebruiker['rol_id'] == "1") {
                                    echo 'selected="selected"';
                                } elseif ($gebruiker['rol_id'] == "2") { ?>
                                    <option<?php if ($gebruiker['rol_id'] == "2") {
                                        echo 'selected="selected"';
                                    } ?> value="2">Beheerder
                                    </option> <?php
                                }
                                    else{ ?>
                                <option<?php
                                    echo 'selected="selected"';
                                    } ?> value="3">Admin
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="geb_datum" class="col-sm-3 col-form-label">Geboortedatum</label>
                            <div class="col-sm-9">
                                <input name="geb_datum"
                                       value="<?php echo isset($gebruiker["geb_datum"]) ? $gebruiker["geb_datum"] : ''; ?>"
                                       required
                                       type="date" class="form-control" id="geb_datum">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="maat" class="col-sm-3 col-form-label">Maat</label>
                            <select name="maat" class="form-control col-sm-9" id="maat">
                                <option <?php if ($gebruiker['maat'] == "s") {
                                    echo 'selected="selected"';
                                } ?> value="s">S
                                </option>
                                <option <?php if ($gebruiker['maat'] == "m") {
                                    echo 'selected="selected"';
                                } ?> value="m">M
                                </option>
                                <option <?php if ($gebruiker['maat'] == "x") {
                                    echo 'selected="selected"';
                                } ?> value="x">X
                                </option>
                                <option <?php if ($gebruiker['maat'] == "xl") {
                                    echo 'selected="selected"';
                                } ?> value="xl">XL
                                </option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="telefoon" class="col-sm-3 col-form-label">Telefoonnummer</label>
                            <div class="col-sm-9">
                                <input name="telefoon"
                                       value="<?php echo isset($gebruiker["telefoonnummer"]) ? $gebruiker["telefoonnummer"] : ''; ?>"
                                       required
                                       type="number" class="form-control" id="telefoon">
                            </div>
                        </div>
                    </div>
                </div>
                <button name="opslaan" class="btn btn-primary">Accountgegevens opslaan</button>
            </form>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <h1>
                Wachtwoord veranderen
            </h1>
            <hr>
            <?php if (!empty($error)) { ?>
                <div class="alert alert-<?php echo $error['type']; ?> ">
                    <?php echo $error['message']; ?>
                </div>
            <?php } ?>
            <form method="POST" action="mijn-account.php">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="voornaam" class="col-sm-3 col-form-label">Oude wachtwoord</label>
                            <div class="col-sm-9">
                                <input name="oud-wachtwoord" required type="password" class="form-control" id="voornaam"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="voornaam" class="col-sm-3 col-form-label">Wachtwoord</label>
                            <div class="col-sm-9">
                                <input name="wachtwoord" required type="password" class="form-control" id="voornaam"
                                       value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tussenvoegsel" class="col-sm-3 col-form-label">Wachtwoord herhalen</label>
                            <div class="col-sm-9">
                                <input name="her-wachtwoord" type="password" class="form-control" id="tussenvoegsel"
                                       value="">
                            </div>
                        </div>
                    </div>
                </div>
                <button name="wachtwoord-opslaan" class="btn btn-primary">Wachtwoord opslaan</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <div class="left">
            <a href="../gebruikers/aanmaken.php">Gebruiker aanmaken</a>
        </div>
        <div class="right">
            <a href="mijn-account.php">Account</a>
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</div>
</body>
</html>