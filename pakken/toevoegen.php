<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
        <?php
        session_start();
        require_once '../connection/db_connectie.php';
        include_once '../user_classes.php';
        include_once '../pak_classes.php';
        $user = new gebruiker($dbh);
        $costume = new pak($dbh);
        $target_dir = "../uploads/";
        
        $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

        if ($gebruiker['rol_id'] != 3) {
            $_SESSION['flash'] = array(
                'type' => 'danger',
                'message' => 'Je hebt geen rechten om een gebruiker aan te maken!'
            );
            $user->redirect('../index.php');
        }
        $rolID = $gebruiker['rol_id'];
        
        // checken of de gebruiker is ingelogd
        if (!$user->is_ingelogd()) {
            $user->redirect('login/login.php');
        }
        
        if (isset($_SESSION['flash'])){
            $error = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        if (isset($_POST['volgende'])) {
            if (!empty($_POST['pakid'])) {      // && !empty($_POST['profiel_foto'])
                if (is_uploaded_file($_FILES["profiel_foto"]["tmp_name"])) {
                    $target_file = $target_dir . basename($_FILES["profiel_foto"]["name"]);//waarom staat dit hier? omdat er eerst een foto moet zijn voordat dit gebruikt kan worden
                    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);//waarom staat dit hier? omdat er eerst een foto moet zijn voordat dit gebruikt kan worden
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, het bestand dat je hebt geupload is geen JPG, JPEG of PNG bestand.'
                        );
                        $user->redirect('toevoegen.php');
        //                exit;
                    }
                    // Check if file already exists
                    elseif (file_exists($target_file)) {
                        $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, de naam van je foto bestaat al.'
                        );
                        $user->redirect('toevoegen.php');
        //                exit;
                    }
                    // Check file size
                    elseif ($_FILES["profiel_foto"]["size"] > 500000) {
                        $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, je foto is te groot.'
                        );
                        $user->redirect('toevoegen.php');
        //                exit;
                    } 
                    //de foto voldoet aan de eisen en mag geupload worden
                    else {
                        $pak = $costume->nieuw_pak_details($_POST);
                        if (move_uploaded_file($_FILES["profiel_foto"]["tmp_name"], $target_file)){// verplaatst de foto naar uploads           !empty($pak) && 
                            if ($_POST["type"]=="piet") {
                                $_SESSION['flash'] = array(
                                'type' => 'success',
                                'message' => 'Het pietenpak is succesvol toegevoegd! Je kunt nu onderdelen toevoegen.'
                            );
                                $_SESSION['volgende'] = 'piet';
                            $url = 'bewerken.php?id='. $_POST['pakid'];
                            $user->redirect($url);
                            } else {
                                $_SESSION['flash'] = array(
                                'type' => 'success',
                                'message' => 'Het sinterklaaspak is succesvol toegevoegd! Je kunt nu onderdelen toevoegen.'
                            );
                                $_SESSION['volgende'] = 'sinterklaas';
                            $url = 'bewerken.php?id='. $_POST['pakid'];
                            $user->redirect($url);
                            }
                        } else {$_SESSION['flash'] = array(
                                'type' => 'danger',
                                'message' => 'Sorry, er is iets mis gegaan.'
                            );
                            $user->redirect('toevoegen.php');
                        }
                    }
                } else {
                    $_SESSION['flash'] = array(
                    'type' => 'danger',
                    'message' => 'Upload een foto alstublieft.'
                    );
                    $user->redirect('toevoegen.php');
                    //echo nl2br("Please upload an Image.\n");
                }
            } else {
                $error = array(
                    'type' => 'danger',
                    'message' => 'Niet alle velden zijn ingevuld!'
                );
            }
        }
        ?>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
          integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link href="../styling/base.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../styling/footer.css">
    <link rel="stylesheet" href="../styling/nav-bar.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Pak toevoegen</title>
    <style>
        img {
        max-height: 200px;
        max-width: 200px;
}
    </style>
    </head>
    <body>
        <div class="topnav">
            <a class="active" href="pakken/pietenpakken.php">Pietenpakken</a>
            <a href="pakken/sinterklaaspakken.php">Sinterklaaspakken</a>
            <?php if ($rolID == '3') { ?>
                <a href="pakken/beschadigd.php">Beschadigd</a>
                <a href="gebruikers/gebuikers.php">Gebruikers</a>
            <?php } ?>
        </div>
        <div class="container">
        <?php if (!empty($error)) { ?>
            <div class="alert alert-<?php echo $error['type'];?> ">
                <?php echo $error['message'];?>
            </div>
        <?php } ?>
        <h1>Pak Toevoegen</h1>
        <hr>
            <form action="toevoegen.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="pakid" class="col-sm-3 col-form-label">PakID</label>
                            <div class="col-sm-9">
                                <input name="pakid" required type="number" class="form-control" id="pakid"
                                       value="<?php echo isset($_POST["pakid"]) ? $_POST["pakid"] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="maat" class="col-sm-3 col-form-label">Maat</label>
                            <select name="maat" class="form-control col-sm-9" id="maat">
                                <option value="s">S</option>
                                <option value="m">M</option>
                                <option value="x">X</option>
                                <option value="xl">XL</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="kleur" class="col-sm-3 col-form-label">Kleur</label>
                            <select name="kleur" class="form-control col-sm-9" id="kleur">
                                <option value="Rood/Zwart">Rood/Zwart</option>
                                <option value="Paars/Zwart">Paars/Zwart</option>
                                <option value="Groen/Zwart">Groen/Zwart</option>
                                <option value="Geel/Zwart">Geel/Zwart</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="geslacht" class="col-sm-3 col-form-label">Geslacht</label>
                            <select name="geslacht" class="form-control col-sm-9" id="geslacht">
                                <option value="Man">Man</option>
                                <option value="Vrouw">Vrouw</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="beschadigd" class="col-sm-3 col-form-label">Beschadigd</label>
                            <div class="col-sm-9">
                                <input name="beschadigd" value="2" type="hidden" class="form-control" id="beschadigd">
                                <input name="beschadigd" value="1" type="checkbox" class="form-control" id="beschadigd">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-sm-3 col-form-label">Type</label>
                            <select name="type" class="form-control col-sm-9" id="type">
                                <option value="piet">Piet</option>
                                <option value="sinterklaas">Sinterklaas</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="profiel_foto" class="col-sm-3 col-form-label">Profiel foto</label>
                            <div class="col-sm-9">
                                <input name="profiel_foto" type="file" class="form-control" id="profiel_foto">
                                <img class="img-thumbnail" id="blah" src="http://via.placeholder.com/100x100" alt="your image" />
                            </div>
                        </div>
                    </div>
                </div>
                <button name="volgende" class="btn btn-primary">Volgende</button>
                <a href="http://localhost/KBS_login/index.php" class="btn btn-primary" role="button">Annuleren</a>
            </form>
        </div>
        <div class="footer">
            <div class="right">
                <a href="../login/uitloggen.php">Uitloggen</a>
            </div>
        </div>
        <script>        
        function readURL(input) {

          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
          }
        }

        $("#profiel_foto").change(function() {
          readURL(this);
        });
        
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        </script>
    </body>
</html>
