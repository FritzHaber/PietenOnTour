<!DOCTYPE html>
<?php
session_start();
require '../connection/db_connectie.php';
require '../user_classes.php';
$user = new gebruiker($dbh);

// Controleert of er op de 'Opslaan' knop is gedrukt, update de melding
if (isset($_POST["opslaan"])) {
    $user->staat_pak($_POST['staat']);
    $user->wijzigen_melding();
}

// Haalt de melding op
$row = $user->ophalen_melding();
$status_id = $row['status_id'];
$schademelding = $row['bericht'];
$oplossing = $row['oplossing'];
$kosten = $row['kosten'];
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    <body>
        <form name="melding" method="POST" action="wijzigen.php">
            <!-- TODO: oplossing per e-mail verzenden aan de gebruiker als de melding afgerond of afgewezen is -->
            <!-- Navigatiebar -->
            <div class="topnav">
                <a href="pakken/pietenpakken.php">Pietenpakken</a>
                <a href="pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
                <a class="active" href="pakken/beschadigd.php">Beschadigd</a>
                <?php if ($rol_id == 3) { ?>
                    <a href="gebruikers/gebruikers.php">Gebruikers</a>
                <?php } ?>
            </div>

            <!-- Informatie over het pak -->                             
            <div class="row" style="padding-left:20px;">
                <img src="../xxl.jpg" class="img-responsive" width="200" height="250">
                <p style="padding:0px 0px 150px 10px;">PakID: <?php print $_SESSION['pak_id'] ?><br>
                                                       Maat: <?php print $_SESSION['maat'] ?><br>                                                            
                                                       Kleur: <?php print $_SESSION['kleur'] ?><br>
                                                       Geslacht: <?php print $_SESSION['geslacht'] ?></p>
            </div>

            <!-- Selecteer de status van de melding -->
            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Status</div>
                <div class="col-lg-1"> 
                    <?php
                    print("<select name='status_id'>");
                    if ($status == 1) {
                        print("<option value=1>Nieuw</option>");
                        print("<option value=2>In behandeling</option>");
                        print("<option value=3>Afgerond</option>");
                        print("<option value=4>Afgewezen</option>");
                    } elseif ($status == 2) {
                        print("<option value=2>In behandeling</option>");
                        print("<option value=1>Nieuw</option>");
                        print("<option value=3>Afgerond</option>");
                        print("<option value=4>Afgewezen</option>");
                    } elseif ($status == 3) {
                        print("<option value=3>Afgerond</option>");
                        print("<option value=1>Nieuw</option>");
                        print("<option value=2>In behandeling</option>");
                        print("<option value=4>Afgewezen</option>");
                    } else {
                        print("<option value=4>Afgewezen</option>");
                        print("<option value=1>Nieuw</option>");
                        print("<option value=2>In behandeling</option>");
                        print("<option value=3>Afgerond</option>");
                    }
                    print("</select><br>");
                    ?>
                </div>
            </div>        

            <!-- Selecteer de staat van het pak -->
            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Staat</div>
                <div class="col-lg-1">
                    <input type="checkbox" name="staat" <?php // if ($staat) print("checked")   ?>> Beschadigd
                </div>
            </div>

            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Schademelding</div>
                <div class="col-lg-4">
                    <textarea name="schademelding" disabled><?php print($schademelding) ?></textarea>
                </div>
            </div>

            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Oplossing</div>
                <div class="col-lg-4">
                    <textarea name="oplossing"><?php print($oplossing) ?></textarea>
                </div>
            </div>

            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Reparatiekosten</div>
                <div class="col-lg-4">
                    <input type="number" name="kosten" value="<?php print($kosten) ?>">
                </div>
            </div>

            <!-- Sla de gegevens op of annuleer -->
            <div class="row">
                <div style="padding:20px;">
                    <input type="submit" name="opslaan" value="Opslaan">
                </div>
                <div style="padding:20px 20px 20px 0px;">
                    <input type="submit" name="annuleren" value="Annuleren">
                </div>
            </div>

            <!-- Opmaak onderkant pagina -->
            <div class="footer">
                <div class="right">
                    <a href="../login/uitloggen.php">Uitloggen</a>
                </div>
            </div>
        </form>
    </body>
</html>