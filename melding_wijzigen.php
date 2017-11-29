<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
require 'connection/db_connectie.php';
$_SESSION['PakID'] = 1;
$_SESSION['Maat'] = 40;
$_SESSION['Kleur'] = "rood";
$_SESSION['Geslacht'] = "M";
$_SESSION['Type'] = "piet";
$_SESSION['FotoIDS'] = "";

$pak_id = $_SESSION['PakID'];
$stmt = $dbh->prepare("SELECT 'Status melding', Staat, Bericht, Oplossing, Kosten FROM melding_pak WHERE PakID = '$pak_id'");
$stmt->execute();

while ($row = $stmt->fetch()) {
    $status = $row['Status melding'];
    $schademelding = $row['Schademelding'];
    $oplossing = $row['Oplossing'];
    $kosten = $row['Kosten'];
}

$stmt = $dbh->prepare("SELECT statusID FROM status_pak WHERE PakID = '$pak_id'");
$stmt->execute();

while ($row = $stmt->fetch()) {
    $staat = $row['statusID'];
}
?>
<html>
    <head>
        <title>Melding wijzigen</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <form name="melding" method="POST" action="verwerk_wijzigen.php">
            <!-- TODO: oplossing per e-mail verzenden aan de gebruiker als de melding afgerond of afgewezen is -->
            <img src="<?php print $_SESSION['FotoIDS']?>">
            PakID: <?php print $_SESSION['PakID'] ?><br>
            Maat: <?php print $_SESSION['Maat'] ?><br>
            Kleur: <?php print $_SESSION['Kleur'] ?><br>
            Geslacht: <?php print $_SESSION['Geslacht'] ?><br>
            Type: <?php print $_SESSION['Type'] ?><br>
            
            Status 
            <?php
            print("<select name='status'>");
//            print("<option>" . $_SESSION['Status'] . "</option>");
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

            Staat <input type="checkbox" name="staat" <?php if ($staat) print("checked") ?>>Beschadigd<br>
            Schademelding <textarea name="schademelding"><?php print($schademelding) ?></textarea><br> 
            Oplossing <textarea name="oplossing"><?php print($oplossing) ?></textarea><br>
            Reparatiekosten € <input type="number" name="kosten" value="<?php print($kosten) ?>"><br>
            <input type="submit" name="opslaan" value="Opslaan">
            <input type="submit" name="annuleren" value="Annuleren">
        </form>
    </body>
</html>