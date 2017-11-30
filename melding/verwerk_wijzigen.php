<!DOCTYPE html>
<html>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require 'connection/db_connectie.php';
$pak_id = $_SESSION['PakID'];

// Controleer of de checkbox is aangevinkt
if (isset($_POST["staat"])) {
    $staat = 1;
} else {
    $staat = 0;
}

// Controleer of er op de 'Opslaan' knop is gedrukt, update de database  
if (isset($_POST["opslaan"])) {
    $pak_id = $_SESSION['PakID'];
    $status = $_POST['status'];
    $schademelding = $_POST['schademelding'];
    $oplossing = $_POST['oplossing'];
    $kosten = $_POST['kosten'];
    
    $stmt = $dbh->prepare("UPDATE melding_pak SET 'Status melding' = '$status', Kosten = $kosten, Oplossing = '$oplossing', Schademelding = '$schademelding' WHERE PakID = '$pak_id'");
    $stmt->execute();
    $stmt = $dbh->prepare("UPDATE status_pak SET statusID = $staat WHERE PakID = '$pak_id'");
    $stmt->execute();
}
?> 
    <a href="Melding_wijzigen.php">Ga terug</a>
</html>