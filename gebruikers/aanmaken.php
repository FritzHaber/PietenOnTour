<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    if ($gebruiker['RolID'] != 3) {
        $user->redirect('../index.php');
    }

    $error = null;
    if (isset($_POST['opslaan'])) {
        if (!empty($_POST['mail']) && !empty($_POST['voornaam']) && !empty($_POST['achternaam']) &&
            !empty($_POST['geb_datum']) && !empty($_POST['woonplaats']) && !empty($_POST['telefoon'])
        ) {
            if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $gebruiker = $user->nieuwe_gebruiker($_POST);
                $error = 'Gebruiker ' . $_POST['voornaam'] . ' is toevgevoegd!';
            } else {
                $error = 'E-mail adres is niet geldig!';
            }
        } else {
            $error = 'Niet alle velden zijn geldig ingevuld!';
        }
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Document</title>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
    </script>
</head>
<body>
<?php echo $error; ?>
<div class="container">
    <h1>Gebruiker aanmaken</h1>
    <div class="container">
        <form method="POST" action="aanmaken.php">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group row">
                        <label for="voornaam" class="col-sm-2 col-form-label">Voornaam</label>
                        <div class="col-sm-10">
                            <input name="voornaam" type="text" class="form-control" id="voornaam">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tussenvoegsel" class="col-sm-2 col-form-label">Tussenvoegsel</label>
                        <div class="col-sm-10">
                            <input name="tussenvoegsel" type="text" class="form-control" id="tussenvoegsel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="achternaam" class="col-sm-2 col-form-label">Achternaam</label>
                        <div class="col-sm-10">
                            <input name="achternaam" type="text" class="form-control" id="achternaam">
                        </div>
                    </div>
                </div>
                <div class="col-sm">

                </div>
            </div>
        </form>
    </div>
</div>
<form method="POST" action="aanmaken.php">
    <label for="">Voornaam</label>
    <input required name="voornaam" type="text">
    <br>
    <br>
    <label for="">Tussenvoegsel</label>
    <input name="tussenvoegsel" type="text">
    <br>
    <br>
    <label for="">Achternaam</label>
    <input required name="achternaam" type="text">
    <br>
    <br>
    <label for="">Geboortedatum</label>
    <input required name="geb_datum" type="text">
    <br>
    <br>
    <label for="">Maat</label>
    <select name="maat">
        <option value="S">S</option>
        <option value="M">M</option>
        <option value="L">L</option>
        <option value="XL">XL</option>
    </select>
    <br>
    <br>
    <label for="">Woonplaats</label>
    <input required name="woonplaats" type="text">
    <br>
    <br>
    <label for="">E-mail</label>
    <input required name="mail" type="text">
    <br>
    <br>
    <label for="">Telefoonnummer</label>
    <input required name="telefoon" type="text">
    <br>
    <br>
    <label for="">Functie</label>
    <select name="functie">
        <option value="1">Admin</option>
        <option value="2">Beheerder</option>
        <option value="3">Vrijwilliger</option>
    </select>
    <br>
    <br>
    <button name="opslaan">Aanmaken</button>
</form>
</body>
</html>