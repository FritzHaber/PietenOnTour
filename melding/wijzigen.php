<!DOCTYPE html>
<?php
    session_start();
    require_once '../connection/db_connectie.php';
    require_once '../user_classes.php';
    require_once '../pak_classes.php';
    $user = new gebruiker($dbh);
    $costume = new pak($dbh);
    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    // Rol van de gebruiker ophalen
    $rol_id = $gebruiker['rol_id'];
    // cheken of de gebruiker rechten heeft
    if ($gebruiker['rol_id'] != 3) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om een schademelding te bewerken!'
        );
        $user->redirect('../pakken/pietenpakken.php');
    }
    $melding = $costume->ophalen_melding_pak($_GET['id']);
    // Controleert of er op de 'Opslaan' knop is gedrukt, updatet de melding
    if (isset($_POST["opslaan"])) {
        $costume->wijzigen_melding($melding, $melding['pak_id'], $gebruiker);
    }
    $status_id = $melding['status_id'];

?>
<html>
<head>
    <title>Melding wijzigen</title>
    <!-- Opmaak -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styling/footer.css">
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <link rel="stylesheet" href="../styling/base.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>


<!-- Navigatiebar -->
<div class="topnav">
    <a href="../pakken/pietenpakken.php">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
    <?php if ($rol_id == 3) { ?>
        <a class="active" href="../pakken/beschadigd.php">Beschadigd</a>
        <a href="../gebruikers/overzicht.php">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <form name="melding" method="POST" action="wijzigen.php?id=<?php echo $melding['melding_id']; ?>">
        <div class="row">
            <img src="<?php print($melding['13']) ?>" class="img-responsive" width="200" height="250">
            <p class="pak-info">PakID: <?php print $melding['pak_id'] ?><br>
                Maat: <?php print $melding['maat'] ?><br>
                Kleur: <?php print $melding['kleur'] ?><br>
                Geslacht: <?php print $melding['geslacht'] ?></p>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <br>
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Schademelding</label>
                    <div class="col-sm-9">
                        <textarea disabled class="form-control" name="schademelding" rows="5" cols="50"
                                  required><?php echo $melding['bericht']; ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Oplossing</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="oplossing" rows="5" cols="50"><?php print($melding['oplossing']) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Reperatiekosten</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="number" name="kosten" value="<?php print($melding['kosten']) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="kleur" class="col-sm-3 col-form-label">Status</label>
                    <select class='custom-select' name='status_id'>
                        <option <?php echo $status_id == 1 ? 'selected' : '' ?> value=1>Nieuw</option>
                        <option <?php echo $status_id == 2 ? 'selected' : '' ?> value=2>In behandeling</option>
                        <option <?php echo $status_id == 3 ? 'selected' : '' ?> value=3>Afgerond</option>
                        <option <?php echo $status_id == 4 ? 'selected' : '' ?> value=4>Afgewezen</option>
                    </select>
                </div>
                <img width="200" height="270" src="<?php echo $melding['16']; ?>" alt="">
            </div>
        </div>
        <!-- Sla de gegevens op of annuleer -->
        <input class="btn btn-primary" type="submit" name="opslaan" value="Opslaan">
        <a href="../pakken/pietenpakken.php?pagina=1" class="btn btn-primary" role="button">Annuleren</a>
        <!-- Opmaak onderkant pagina -->
        <div class="footer">
            <div class="right">
                <a href="../gebruikers/mijn-account.php">Account</a>
                <a href="../login/uitloggen.php">Uitloggen</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>