<!DOCTYPE html>
<?php
/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/
session_start();
require '../connection/db_connectie.php';
/* Deze code hieronder verwijderen als de sessievariabelen bekend zijn */ 
$_SESSION['FotoID'] = "";
$_SESSION['PakID'] = 1;
$_SESSION['Maat'] = 40;
$_SESSION['Kleur'] = "rood";
$_SESSION['Geslacht'] = "M";
/* Deze code hierboven verwijderen als de sessievariabelen bekend zijn */
?>
<html>
    <head>
        <title>Melding aanmaken</title>
    </head>
    <body>
        <form name="aanmaken" method="POST" action="verwerk_aanmaken.php">
            <!-- Informatie over het pak -->
            <img src="<?php print $_SESSION['FotoID']?>"> 
            PakID: <?php print $_SESSION['PakID'] ?><br> 
            Maat: <?php print $_SESSION['Maat'] ?><br>
            Kleur: <?php print $_SESSION['Kleur'] ?><br>
            Geslacht: <?php print $_SESSION['Geslacht'] ?><br>
            
            <!-- Selecteer de status van de melding -->
            Status <select name="status">
                <option value=1>Nieuw</option>
                <option value=2>In behandeling</option>
                <option value=3>Afgerond</option>
                <option value=4>Afgewezen</option>
            </select><br>
            
            <!-- Selecteer de staat van het pak -->
            Staat <input type="checkbox" name="staat">Beschadigd<br>
            
            <!-- Geef aan wat de schade aan het pak is -->
            Schademelding <textarea name="schademelding"></textarea><br>
                                    
            <!-- Sla de gegevens op of annuleer -->
            <input type="submit" name="opslaan" value="Opslaan">
            <input type="submit" name="annuleren" value="Annuleren"><br>
            
            <!-- Selecteer een foto om te uploaden -->
            Selecteer een foto om te uploaden:
            <input type="file" name="foto">     
        </form>
    </body>
</html>