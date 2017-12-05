<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);        

    $dbh = $dbh->prepare("SELECT * FROM pak JOIN status_pak ON pak.PakID=status_pak.PakID JOIN foto_pak ON pak.PakID=foto_pak.PakID WHERE Type='piet' ORDER BY pak.PakID");
    $dbh->execute();
    $pakken = $dbh->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="../styling/footer.css">
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        img { 
            height: 125px;
            width: 125px; 
        } 
</style>
</head>
<body>
<div class="topnav">
    <a class="active" href="pietenpakken.php">Pietenpakken</a>
    <a href="sinterklaaspakken.php">Sinterklaaspakken</a>
    <a href="beschadigd.php">Beschadigd</a>
    <a href="gebruikers/bekijken.php">Gebruikers bekijken</a>


</div>
<div class="container">
    <h2>Overzicht Pietenpakken</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Paknummer</th>
                <th>Maat</th>
                <th>Geslacht</th>
                <th>Kleur</th>
                <th>Beschadigd</th>
            </tr>
           
        </thead>
        <tbody>
        <?php foreach($pakken as $pak): ?>
                <tr>
                    <td><img src="<?php echo $pak['FotoID']; ?>" alt="..." class="img-thumbnail"></td>
                    <td><?php print_r($pak['PakID']); ?></td>
                    <td><?php print_r($pak['Maat']); ?></td>
                    <td><?php print_r($pak['Geslacht']); ?></td>
                    <td><?php print_r($pak['Kleur']); ?></td>
                    <td><?php print_r($pak['Status']); ?></td>
                </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
    <Br>
    <a href="gebruikers/aanmaken.php">Gebruiker aanmaken</a> <br>

<!--    <br>-->
<!--    <br>-->

    
</div>
    <div class="footer">
        <div class="left">
            <a href="login/uitloggen.php">Pak toevoegen</a>
        </div>
        <div class="right">
            <a href="#">Uitloggen</a>
        </div>
    </div>
</body>
<script>
    setTimeout(function () {
        $('.alert').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
</script>
</html>