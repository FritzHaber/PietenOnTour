<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    if (!$user->gebruikers_rol(3)) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om een gebruiker aan te maken!'
        );
        $user->redirect('../index.php');
    }

    $gebruikerId = $_GET['id'];
    $gebruiker = $user->gebruiker_ophalen_id($gebruikerId);
    $rolID = $gebruiker['RolID'];
    
    if (empty($gebruiker)) {
        $user->redirect('../index.php');
    }

    if (isset($_POST['opslaan'])) {
        if (!empty($_POST['mail']) && !empty($_POST['voornaam']) && !empty($_POST['achternaam']) &&
            !empty($_POST['geb_datum']) && !empty($_POST['woonplaats']) && !empty($_POST['telefoon'])
        ) {
            if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $gebruiker = $user->gebruiker_bewerken($_POST, $gebruikerId);
                $gebruiker = $user->gebruiker_ophalen_id($gebruikerId);
                $error = array(
                    'type' => 'success',
                    'message' => 'Gebruiker succesvol bijgewerkt!'
                );
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
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
    <h1>Gebruiker '<?php
            echo $gebruiker['Voornaam'];
            echo !is_null($gebruiker['Tussenvoegsel']) ? ' ' . $gebruiker['Tussenvoegsel'] . ' ' : ' ';
            echo $gebruiker['Achternaam'];
        ?>' bewerken
    </h1>
    <hr>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <form method="POST" action="bekijken.php?id=<?php echo $gebruikerId ?>">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Voornaam</label>
                    <div class="col-sm-9">
                        <input name="voornaam" required type="text" class="form-control" id="voornaam"
                               value="<?php echo isset($gebruiker["Voornaam"]) ? $gebruiker["Voornaam"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tussenvoegsel" class="col-sm-3 col-form-label">Tussenvoegsel</label>
                    <div class="col-sm-9">
                        <input name="tussenvoegsel" type="text" class="form-control" id="tussenvoegsel"
                               value="<?php echo isset($gebruiker["Tussenvoegsel"]) ? $gebruiker["Tussenvoegsel"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="achternaam" class="col-sm-3 col-form-label">Achternaam</label>
                    <div class="col-sm-9">
                        <input name="achternaam" required type="text" class="form-control" id="achternaam"
                               value="<?php echo isset($gebruiker["Achternaam"]) ? $gebruiker["Achternaam"] : ''; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="woonplaats" class="col-sm-3 col-form-label">Woonplaats</label>
                    <div class="col-sm-9">
                        <input name="woonplaats" required type="text" class="form-control" id="woonplaats"
                               value="<?php echo isset($gebruiker["Woonplaats"]) ? $gebruiker["Woonplaats"] : ''; ?>">
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
                        <option value="1">Vrijwilliger
                        </option>
                        <option value="2">Beheerder
                        </option>
                        <option value="3">Admin
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
                        <option value="s">S
                        </option>
                        <option value="m">M
                        </option>
                        <option value="x">X
                        </option>
                        <option value="xl">XL
                        </option>
                    </select>
                </div>
                <div class="form-group row">
                    <label for="telefoon" class="col-sm-3 col-form-label">Telefoonnummer</label>
                    <div class="col-sm-9">
                        <input name="telefoon"
                               value="<?php echo isset($gebruiker["Telefoonnummer"]) ? $gebruiker["Telefoonnummer"] : ''; ?>"
                               required
                               type="number" class="form-control" id="telefoon">
                    </div>
                </div>
            </div>
        </div>
        <button name="opslaan" class="btn btn-primary">Gebruiker bewerken</button>
    </form>
    <div class="footer">
        <div class="left">
            <a href="../gebruikers/aanmaken.php">Gebruiker aanmaken</a>
        </div>
        <div class="right">
            <a href="../login/uitloggen.php">Uitloggen</a>
        </div>
    </div>
</div>
</body>
</html>