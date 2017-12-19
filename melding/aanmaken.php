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
    require_once '../pak_classes.php';
    $user = new gebruiker($dbh);
    $costume = new pak($dbh);

    // gebruiker ophalen uit de sessie
    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    // Rol van de gebruiker ophalen
    $rol_id = $gebruiker['rol_id'];

    // PakId ophalen
    $pak_id = $_GET['pakid'];

    $pak = $costume->pak_ophalen_pakid($pak_id);

    if (empty($pak)) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Het pak kan niet worden gevonden!'
        );

        return $user->redirect('../pakken/pietenpakken.php?pagina=1');
    }
    if (isset($_POST['opslaan'])) {
        $check = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION); // Haalt de bestandsextensie op
        if (($check == "png" || $check == "jpg" || $check == "jpeg") && file_exists($_FILES["foto"]["tmp_name"]) &&
            is_uploaded_file($_FILES["foto"]["tmp_name"])
        ) {
            $costume->aanmaken_melding($pak, $gebruiker['gebruiker_id'], $_POST);
        } else {
            $error = array(
                'type' => 'danger',
                'message' => 'Upload een afbeelding van het formaat .png, .jpg of .jpeg!'
            );
        }
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
    <link rel="stylesheet" href="../styling/base.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        img {
            max-height: 200px;
            max-width: 200px;
        }
    </style>
</head>
<body>
<!-- Navigatiebar -->
<div class="topnav">
    <a class="active" href="../pakken/pietenpakken.php">Pietenpakken</a>
    <a href="../pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
    <a href="../pakken/beschadigd.php">Beschadigd</a>
    <?php if ($rol_id == 3) { ?>
        <a href="../gebruikers/overzicht.php">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>
    <h1>Melding aanmaken</h1>
    <hr>
    <form name="aanmaken" method="POST" action="aanmaken.php?pakid=<?php echo $pak_id ?>" enctype="multipart/form-data">
        <!-- Informatie over het pak -->
        <div class="row">
            <img src="<?php print($pak['foto_id']) ?>" class="img-responsive" width="200" height="250">
            <p style="padding:0px 0px 150px 10px;">PakID: <?php print $pak['pak_id'] ?><br>
                Maat: <?php print $pak['maat'] ?><br>
                Kleur: <?php print $pak['kleur'] ?><br>
                Geslacht: <?php print $pak['geslacht'] ?></p>
        </div>

        <!-- Selecteer de staat van het pak -->
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Schademelding</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="schademelding" rows="5" cols="50"
                                  required><?php if (isset($_POST['opslaan'])) {
                                print($_POST['schademelding']);
                            } ?></textarea>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="voornaam" class="col-sm-3 col-form-label">Foto van de schade</label>
                    <div class="col-sm-9">
                        <div class="col-lg-1">
                            <input id="profiel_foto" type="file" name="foto" accept=".png, .jpg, .jpeg" required>
                            <img id="foto_schade" src="http://via.placeholder.com/100x100" alt="your image" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" name="opslaan" value="Opslaan">
        <a href="../pakken/pietenpakken.php?pagina=1" class="btn btn-primary" role="button">Annuleren</a>
        <!-- Opmaak onderkant pagina -->
        <div class="footer">
            <div class="right">
                <a href="../login/uitloggen.php">Uitloggen</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#foto_schade').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#profiel_foto").change(function () {
        readURL(this);
    });
</script>