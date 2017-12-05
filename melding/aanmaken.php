<!DOCTYPE html>
<?php
/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/
session_start();
require_once '../connection/db_connectie.php';
require_once '../user_classes.php';
$user = new gebruiker($dbh);
// TODO: haal sessievariabelen op
/* Deze code hieronder verwijderen als de sessievariabelen bekend zijn */
$_SESSION['foto_id'] = "";
$_SESSION['pak_id'] = 1;
$_SESSION['maat'] = 40;
$_SESSION['kleur'] = "rood";
$_SESSION['geslacht'] = "M";
/* Deze code hierboven verwijderen als de sessievariabelen bekend zijn */
$gebruiker_id = $_SESSION['user_session']; 
$pak_id = $_SESSION['pak_id'];

// Controleert of er op de 'Opslaan' knop is gedrukt, maakt een nieuwe melding aan
if (isset($_POST['opslaan'])) {
    $user->staat_pak($_POST['staat']); 
    $user->aanmaken_melding();
}
?>
<html>
    <head>
        <title>Melding aanmaken</title>
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
        <form name="aanmaken" method="POST" action="aanmaken.php">
            <!-- Navigatiebar -->
            <div class="topnav">
                <a class="active" href="pakken/pietenpakken.php">Pietenpakken</a>
                <a href="pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
                    <a href="pakken/beschadigd.php">Beschadigd</a>
                    <?php if ($rol_id == 3) { ?>
                    <a href="gebruikers/gebruikers.php">Gebruikers</a>
                <?php } ?>
            </div>

            <!-- Informatie over het pak -->                             
                <div class="row" style="padding-left:20px;">
                    <img src="<?php print($_SESSION['foto_id']) ?>" class="img-responsive" width="200" height="250">
                    <p style="padding:0px 0px 150px 10px;">PakID: <?php print $_SESSION['pak_id'] ?><br>
                                                           Maat: <?php print $_SESSION['maat'] ?><br>                                                            
                                                           Kleur: <?php print $_SESSION['kleur'] ?><br>
                                                           Geslacht: <?php print $_SESSION['geslacht'] ?></p>
                </div>

            <!-- Selecteer de status van de melding -->
            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Status</div>
                <div class="col-lg-1">
                    <select name="status_id">
                        <option value=1>Nieuw</option>
                        <option value=2>In behandeling</option>
                        <option value=3>Afgerond</option>
                        <option value=4>Afgewezen</option>
                    </select>
                </div>
            </div>

            <!-- Selecteer de staat van het pak -->
            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Staat</div>
                <div class="col-lg-1">
                    <input type="checkbox" name="staat"> Beschadigd
                </div>
            </div>

            <!-- Geef aan wat de schade aan het pak is -->
            <div class="row" style="padding:5px;">
                <div class="col-lg-1">Schademelding</div>
                <div class="col-lg-4">
                    <textarea name="schademelding" rows="5" cols="50"></textarea>
                </div>
                <!-- Selecteer een foto om te uploaden -->
                Selecteer een foto om te uploaden:
                <div class="col-lg-1">
                    <input type="file" name="foto"> 
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