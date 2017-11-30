<!DOCTYPE html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* De nieuw aangemaakte melding wordt opgeslagen in de database */
session_start();
require 'connection/db_connectie.php';
/* Deze code hieronder verwijderen als de sessievariabelen bekend zijn */
$_SESSION['GebruikerID'] = 1;
$_SESSION['PakID'] = 1;
/* Deze code hierboven verwijderen als de sessievariabelen bekend zijn */
$gebruiker_id = $_SESSION['GebruikerID'];
$pak_id = $_SESSION['PakID'] = 1;

// Controleer of the checkbox is aangevinkt
if (isset($_POST['staat'])) {
    $staat = 1;
} else {
    $staat = 0;
}

// Controleer of de 'Opslaan' knop is ingedrukt
if (isset($_POST['opslaan'])) {
    $pak_id = $_SESSION['PakID'];
    $status = $_POST['status'];
    $schademelding = $_POST['schademelding'];

    // Voegt de nieuwe melding toe aan melding_pak
    $stmt = $dbh->prepare("INSERT INTO melding_pak(PakID, 'Status melding', GebruikerID, Bericht) VALUES ('$pak_id', '$status', '$gebruiker_id', '$schademelding')");
    $stmt->execute();
    $stmt = $dbh->prepare("UPDATE status_pak SET statusID = $staat WHERE PakID = '$pak_id'");
    $stmt->execute();
    
    // Haalt de MeldingID op van de nieuw aangemaakte melding
    $stmt = $dbh->prepare("SELECT MAX(MeldingID) FROM melding_pak WHERE GebruikerID = '$gebruiker_id' AND PakID = '$pak_id'");
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        $melding_id = $row['MeldingID'];
    }    
    
    // Verplaatst de geüploade foto naar de doelmap
    // $_FILES["form input name"]["bestandsnaam"]
    $doelmap = "uploads/" . basename($_FILES["foto"]["name"]); 
    move_uploaded_file($_FILES["foto"]["tmp_name"], $doelmap);
    $datum_upload = date("y-m-d H:i:s");
    
    // Voegt de foto van de schade toe 
    $stmt = $dbh->prepare("INSERT INTO foto_melding VALUES ('$doelmap', '$melding_id', '$datum_upload')");
    $stmt->execute();
}
?>
<!-- Deze code hieronder verwijderen als deze samengevoegd is met de andere pagina's --> 
<html>
    <a href="Melding_aanmaken.php">Ga terug</a>
</html>
<!-- Deze code hierboven verwijderen als deze samengevoegd is met de andere pagina's --> 