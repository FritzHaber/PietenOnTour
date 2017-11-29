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
/* Deze code hieronder verwijderen als deze pagina gecombineerd is met de andere pagina's */ 
$_SESSION['GebruikerID'] = 1;
$gebruikerID = $_SESSION['GebruikerID'];
/* Deze code hierboven verwijderen als deze pagina samengevoegd is met de andere pagina's */    

// Controleer of the checkbox is aangevinkt
if (isset($_POST['staat'])) {
    $staat = 1;
} else {
    $staat = 0;
}

$pak_id = $_SESSION['PakID'];
$status = $_POST['status'];
$schademelding = $_POST['schademelding'];

$stmt = $dbh->prepare("INSERT INTO melding_pak(PakID, status, GebruikerID, Bericht) VALUES ('$pak_id', '$status', '$gebruikerID', '$schademelding')");
$stmt->execute();
$stmt = $dbh->prepare("UPDATE status_pak SET statusID = $staat WHERE PakID = '$pak_id'");
$stmt->execute();
?>
<!-- Deze code hieronder verwijderen als deze samengevoegd is met de andere pagina's --> 
<html>
    <a href="Melding_aanmaken.php">Ga terug</a>
</html>
<!-- Deze code hierboven verwijderen als deze samengevoegd is met de andere pagina's --> 