<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    if ($gebruiker['RolID'] != 3) {
        $user->redirect('../index.php');
    }

    $error = array();
    if (isset($_POST['opslaan'])) {
        if (!empty($_POST['mail']) && !empty($_POST['voornaam']) && !empty($_POST['achternaam']) &&
            !empty($_POST['geb_datum']) && !empty($_POST['woonplaats']) && !empty($_POST['telefoon'])
        ) {
            if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $gebruiker = $user->nieuwe_gebruiker($_POST);
                if (!empty($gebruiker)){
                    $error = $gebruiker;
                } else{
                    $error = array(
                        'type' => 'success',
                        'message' => 'Er is een mail gestuurd naar ' . $_POST['mail'] . '!'
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Document</title>
    <script type="text/javascript">
            $('#geb_datum').datepicker({
                format: 'yyyy-mm-dd'
            });
    </script>
</head>
<body>
<div class="container">
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type'];?> ">
            <?php echo $error['message'];?>
        </div>
    <?php } ?>
    <a href="../index.php">Home</a>
    <h1>Gebruiker aanmaken</h1>
    <hr>
    <form method="POST" action="aanmaken.php">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Voornaam</label>
                    <div class="col-sm-9">
                        <input name="voornaam" required type="text" class="form-control" id="voornaam"
                               value="<?php echo isset($_POST["voornaam"]) ? $_POST["voornaam"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tussenvoegsel" class="col-sm-3 col-form-label">Tussenvoegsel</label>
                    <div class="col-sm-9">
                        <input name="tussenvoegsel" type="text" class="form-control" id="tussenvoegsel"
                               value="<?php echo isset($_POST["tussenvoegsel"]) ? $_POST["tussenvoegsel"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="achternaam" class="col-sm-3 col-form-label">Achternaam</label>
                    <div class="col-sm-9">
                        <input name="achternaam" required type="text" class="form-control" id="achternaam"
                               value="<?php echo isset($_POST["achternaam"]) ? $_POST["achternaam"] : ''; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="woonplaats" class="col-sm-3 col-form-label">Woonplaats</label>
                    <div class="col-sm-9">
                        <input name="woonplaats" required type="text" class="form-control" id="woonplaats"
                               value="<?php echo isset($_POST["woonplaats"]) ? $_POST["woonplaats"] : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mail" class="col-sm-3 col-form-label">E-mail</label>
                    <div class="col-sm-9">
                        <input name="mail" required type="email" class="form-control" id="mail"
                               value="<?php echo isset($_POST["mail"]) ? $_POST["mail"] : ''; ?>">
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
                               value="<?php echo isset($_POST["geb_datum"]) ? $_POST["geb_datum"] : ''; ?>" required
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
                               value="<?php echo isset($_POST["telefoon"]) ? $_POST["telefoon"] : ''; ?>" required
                               type="number" class="form-control" id="telefoon">
                    </div>
                </div>
            </div>
        </div>
        <button name="opslaan" class="btn btn-primary">Gebruiker aanmaken</button>
    </form>
</div>
<!---->
<!--<form method="POST" action="aanmaken.php">-->
<!--    <label for="">Voornaam</label>-->
<!--    <input required name="voornaam" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Tussenvoegsel</label>-->
<!--    <input name="tussenvoegsel" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Achternaam</label>-->
<!--    <input required name="achternaam" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Geboortedatum</label>-->
<!--    <input required name="geb_datum" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Maat</label>-->
<!--    <select name="maat">-->
<!--        <option value="S">S</option>-->
<!--        <option value="M">M</option>-->
<!--        <option value="L">L</option>-->
<!--        <option value="XL">XL</option>-->
<!--    </select>-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Woonplaats</label>-->
<!--    <input required name="woonplaats" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">E-mail</label>-->
<!--    <input required name="mail" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Telefoonnummer</label>-->
<!--    <input required name="telefoon" type="text">-->
<!--    <br>-->
<!--    <br>-->
<!--    <label for="">Functie</label>-->
<!--    <select name="functie">-->
<!--        <option value="1">Admin</option>-->
<!--        <option value="2">Beheerder</option>-->
<!--        <option value="3">Vrijwilliger</option>-->
<!--    </select>-->
<!--    <br>-->
<!--    <br>-->
<!--    <button name="opslaan">Aanmaken</button>-->
<!--</form>-->
</body>
</html>