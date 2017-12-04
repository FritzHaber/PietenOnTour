<!DOCTYPE html>
<?php
session_start();
require '../connection/db_connectie.php';
/* Deze code hieronder verwijderen als de sessievariabelen bekend zijn */
$_SESSION['foto_id'] = "";
$_SESSION['pak_id'] = 1;
$_SESSION['maat'] = 40;
$_SESSION['kleur'] = "rood";
$_SESSION['geslacht'] = "M";
/* Deze code hierboven verwijderen als de sessievariabelen bekend zijn */

/* Deze code hieronder verwijderen als de sessievariabelen bekend zijn */
$_SESSION['gebruiker_id'] = 1;
$_SESSION['pak_id'] = 1;
/* Deze code hierboven verwijderen als de sessievariabelen bekend zijn */
$gebruiker_id = $_SESSION['gebruiker_id'];
$pak_id = $_SESSION['pak_id'];

// Controleer of the checkbox is aangevinkt
if (isset($_POST['staat'])) {
    $staat = 1;
} else {
    $staat = 0;
}

// Controleer of er op de 'Opslaan' knop is gedrukt, maak een nieuwe melding aan
if (isset($_POST['opslaan'])) {
    $pak_id = $_SESSION['pak_id'];
    $status_id = $_POST['status_id'];
    $schademelding = $_POST['schademelding'];

    // Voegt de nieuwe melding toe aan melding_pak
    $stmt = $dbh->prepare("INSERT INTO melding_pak(pak_id, gebruiker_id, status_id, bericht) VALUES ($pak_id, $gebruiker_id, $status_id, '$schademelding')");
    $stmt->execute();
    
    // Haalt de MeldingID op van de nieuw aangemaakte melding
    $stmt = $dbh->prepare("SELECT MAX(melding_id) AS melding_id FROM melding_pak WHERE gebruiker_id = $gebruiker_id AND pak_id = $pak_id");
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        $melding_id = $row['melding_id'];
    }    
    
    // Verplaatst de geüploade foto naar de doelmap
    // $_FILES["form input name"]["bestandsnaam"]
    $doelmap = "uploads/" . basename($_FILES["foto"]["name"]); 
    move_uploaded_file($_FILES["foto"]["tmp_name"], $doelmap);
    $datum_upload = date("y-m-d H:i:s");
    
    // Voegt de foto van de schade toe 
    $stmt = $dbh->prepare("INSERT INTO foto_melding VALUES ('$doelmap', $melding_id, '$datum_upload')");
    $stmt->execute();
    
    header("Location: wijzigen.php");
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
                <?php if ($rol_id == '3') { ?>
                    <a href="pakken/beschadigd.php">Beschadigd</a>
                    <a href="gebruikers/gebuikers.php">Gebruikers</a>
                <?php } ?>
            </div>

            <!-- Informatie over het pak -->                             
                <div class="row" style="padding-left:20px;">
                    <?php // print $_SESSION['FotoID'] ?>
                    <img src="../xxl.jpg" class="img-responsive" width="200" height="250">
                    <p style="padding:0px 0px 150px 10px;">PakID: <?php print $_SESSION['PakID'] ?><br>
                                                           Maat: <?php print $_SESSION['Maat'] ?><br>                                                            
                                                           Kleur: <?php print $_SESSION['Kleur'] ?><br>
                                                           Geslacht: <?php print $_SESSION['Geslacht'] ?></p>
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