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

    $pak_id = $_GET['id'];
    $pak = $costume->pak_ophalen_pakid($pak_id);
    $pak_events = $costume->pak_event_ophalen($pak_id);
    $rolID = $gebruiker['rol_id'];
    // checken of de gebruiker is ingelogd
    if (!$user->is_ingelogd()) {
        $user->redirect('login/login.php');
    }

    if (isset($_SESSION['flash'])) {
        $error = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    $vaste_onderdelen = $costume->vaste_onderdelen_ophalen($pak_id);
    $losse_onderdelen = $costume->losse_onderdelen_ophalen($pak_id);

    if (isset($_POST['opslaan_details'])) {
        if (!empty($_POST['pakid'])) {      // && !empty($_POST['profiel_foto'])
            if (is_uploaded_file($_FILES["profiel_foto"]["tmp_name"])) {
                $target_file = $target_dir .
                    basename($_FILES["profiel_foto"]["name"]);//waarom staat dit hier? omdat er eerst een foto moet zijn voordat dit gebruikt kan worden
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);//waarom staat dit hier? omdat er eerst een foto moet zijn voordat dit gebruikt kan worden
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, het bestand dat je hebt geupload is geen JPG, JPEG of PNG bestand.'
                    );
                    $user->redirect('bewerken.php?id=' . $pak_id);
                    //                exit;
                } // Check if file already exists
                elseif (file_exists($target_file)) {
                    $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, de naam van je foto bestaat al.'
                    );
                    $user->redirect('bewerken.php?id=' . $pak_id);
                    //                exit;
                } // Check file size
                elseif ($_FILES["profiel_foto"]["size"] > 500000) {
                    $_SESSION['flash'] = array(
                        'type' => 'danger',
                        'message' => 'Sorry, je foto is te groot.'
                    );
                    $user->redirect('bewerken.php?id=' . $pak_id);
                    //                exit;
                } //de foto voldoet aan de eisen en mag geupload worden
                else {
                    $pak = $costume->pak_bewerken($_POST, $pak_id, $foto = true, $gebruiker);
                    $pak = $costume->pak_ophalen_pakid($pak_id);
                    if (move_uploaded_file($_FILES["profiel_foto"]["tmp_name"], $target_file)) {// verplaatst de foto naar uploads           !empty($pak) &&
                        if ($_POST["type"] == "piet") {
                            $_SESSION['flash'] = array(
                                'type' => 'success',
                                'message' => 'Het pietenpak is succesvol toegevoegd! Je kunt nu onderdelen toevoegen.'
                            );
                            $_SESSION['volgende'] = 'piet';
                            $url = 'bewerken.php?id=' . $_POST['pakid'];
                            $user->redirect($url);
                        } else {
                            $_SESSION['flash'] = array(
                                'type' => 'success',
                                'message' => 'Het sinterklaaspak is succesvol toegevoegd! Je kunt nu onderdelen toevoegen.'
                            );
                            $_SESSION['volgende'] = 'sinterklaas';
                            $url = 'bewerken.php?id=' . $_POST['pakid'];
                            $user->redirect($url);
                        }
                    } else {
                        $_SESSION['flash'] = array(
                            'type' => 'danger',
                            'message' => 'Sorry, er is iets mis gegaan.'
                        );
                        $user->redirect('bewerken.php?id=' . $pak_id);
                    }
                }
            } else {
                $pak = $costume->pak_bewerken($_POST, $pak_id, $foto = false, $gebruiker);
                $pak = $costume->pak_ophalen_pakid($pak_id);
                $_SESSION['flash'] = array(
                    'type' => 'success',
                    'message' => 'Je pak is succesvol bijgewerkt!'
                );
                $user->redirect('bewerken.php?id=' . $pak_id);
            }
        } else {
            $error = array(
                'type' => 'danger',
                'message' => 'Niet alle velden zijn ingevuld!'
            );
        }
    }
//    if (isset($_POST["opslaan_onderdelen"])) {
//        $pak = $costume->nieuw_pak_onderdelen($_POST, $pak_id);
//        if (isset($pak['pak_id'])) {
//            $_SESSION['flash'] = array(
//                'type' => 'success',
//                'message' => 'De onderdelen zijn succesvol toegevoegd.'
//            );
//            $_SESSION['volgende'] = 'piet';
//            $url = 'bewerken.php?id=' . $_POST['pakid'];
//            $user->redirect($url);
//        }
//    }
?>
<html>
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pak '<?php echo $pak_id ?>' bewerken</title>
    <style>
        img {
            max-height: 200px;
            max-width: 200px;
        }
    </style>
</head>
<body>
<div class="topnav">
    <a href="pietenpakken.php">Pietenpakken</a>
    <a href="sinterklaaspakken.php">Sinterklaaspakken</a>
    <?php if ($rolID == '3') { ?>
        <a href="beschadigd.php">Beschadigd</a>
        <a href="../gebruikers/overzicht.php?pagina=1">Gebruikers</a>
    <?php } ?>
</div>
<div class="container">
    <h1>Pak '<?php
            echo $pak['pak_id'];
        ?>' bewerken
    </h1>
    <?php if (!empty($error)) { ?>
        <div class="alert alert-<?php echo $error['type']; ?> ">
            <?php echo $error['message']; ?>
        </div>
    <?php } ?>

    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'Details')" id="<?php if (empty($_SESSION['volgende'])) {
            echo 'defaultOpen';
        } ?>">Details
        </button>
        <button class="tablinks" onclick="openCity(event, 'Onderdelen')" id="<?php if (!empty($_SESSION['volgende'])) {
            echo 'defaultOpen';
        } ?>">Onderdelen
        </button>
        <button class="tablinks" onclick="openCity(event, 'Log')">Log</button>
        <?php $_SESSION['volgende'] = ''; ?>
    </div>
    <!--        <h1>Pak bewerken</h1>-->
    <hr>
    <div id="Details" class="tabcontent">
        <form action="bewerken.php?id=<?php echo $pak_id ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="pakid" class="col-sm-3 col-form-label">PakID</label>
                        <div class="col-sm-9">
                            <input name="pakid" required type="number" class="form-control" id="pakid"
                                   value="<?php echo isset($pak["pak_id"]) ? $pak["pak_id"] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="maat" class="col-sm-3 col-form-label">Maat</label>
                        <select name="maat" class="form-control col-sm-9" id="maat">
                            <option value="s"<?php if ($pak["maat"] == "s") {
                                echo 'selected="selected"';
                            } ?>>S
                            </option>
                            <option value="m"<?php if ($pak["maat"] == "m") {
                                echo 'selected="selected"';
                            } ?>>M
                            </option>
                            <option value="x"<?php if ($pak["maat"] == "x") {
                                echo 'selected="selected"';
                            } ?>>X
                            </option>
                            <option value="xl"<?php if ($pak["maat"] == "xl") {
                                echo 'selected="selected"';
                            } ?>>XL
                            </option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="kleur" class="col-sm-3 col-form-label">Kleur</label>
                        <select name="kleur" class="form-control col-sm-9" id="kleur">
                            <option value="Rood/Zwart"<?php if ($pak["kleur"] == "Rood/Zwart") {
                                echo 'selected="selected"';
                            } ?>>Rood/Zwart
                            </option>
                            <option value="Paars/Zwart"<?php if ($pak["kleur"] == "Paars/Zwart") {
                                echo 'selected="selected"';
                            } ?>>Paars/Zwart
                            </option>
                            <option value="Groen/Zwart"<?php if ($pak["kleur"] == "Groen/Zwart") {
                                echo 'selected="selected"';
                            } ?>>Groen/Zwart
                            </option>
                            <option value="Geel/Zwart"<?php if ($pak["kleur"] == "Geel/Zwart") {
                                echo 'selected="selected"';
                            } ?>>Geel/Zwart
                            </option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="geslacht" class="col-sm-3 col-form-label">Geslacht</label>
                        <select name="geslacht" class="form-control col-sm-9" id="geslacht">
                            <option value="Man"<?php if ($pak["geslacht"] == "Man") {
                                echo 'selected="selected"';
                            } ?>>Man
                            </option>
                            <option value="Vrouw"<?php if ($pak["geslacht"] == "Vrouw") {
                                echo 'selected="selected"';
                            } ?>>Vrouw
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="beschadigd" class="col-sm-3 col-form-label">Beschadigd</label>
                        <div class="col-sm-9">
                            <input name="beschadigd" value="beschadigd" <?php if ($pak["omschrijving"] ==
                                "beschadigd"
                            ) {
                                echo 'checked="checked"';
                            } ?> type="checkbox" class="form-control" id="beschadigd">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-3 col-form-label">Type</label>
                        <select name="type" class="form-control col-sm-9" id="type">
                            <option value="piet"<?php if ($pak["type"] == "piet") {
                                echo 'selected="selected"';
                            } ?>>Piet
                            </option>
                            <option value="sinterklaas"<?php if ($pak["type"] == "sinterklaas") {
                                echo 'selected="selected"';
                            } ?>>Sinterklaas
                            </option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="profiel_foto" class="col-sm-3 col-form-label">Profiel foto</label>
                        <div class="col-sm-9">
                            <input name="profiel_foto" type="file" class="form-control" id="profiel_foto" value="
                            <?php 
                            if (!empty($pak["foto_id"])){
                                echo $pak["foto_id"];
                            } else {
                                echo "";
                            }
                            ?>">
                            <img class="img-thumbnail" id="blah" src="http://<?php echo $pak["foto_id"] ?>"
                                 alt="your image"/>
                        </div>
                    </div>
                </div>
            </div>
            <button name="opslaan_details" class="btn btn-primary">Opslaan</button>
            <a href="pietenpakken.php?pagina=1" class="btn btn-primary" role="button">Annuleren</a>
        </form>
    </div>

    <div id="Onderdelen" class="tabcontent">
        <form action="bewerken.php?id=<?php echo $pak_id ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-6">
                    <?php foreach ($vaste_onderdelen as $key => $onderdeel) { ?>
                        <div class="form-group row">
                            <label for="vastonderdeel1" class="col-sm-3 col-form-label">Vastonderdeel <?php echo $key +
                                    1 ?></label>
                            <div class="col-sm-9">
                                <input disabled name="<?php echo $key; ?>" required type="text" class="form-control"
                                       id="vastonderdeel1"
                                       value="<?php echo $onderdeel['onderdeel']; ?>">
                            </div>
                        </div>
                    <?php } ?>

                    <?php foreach ($losse_onderdelen as $key => $onderdeel) { ?>
                        <div class="form-group row">
                            <label for="onderdeel12" class="col-sm-3 col-form-label">Onderdeel <?php echo $key +
                                    1 ?></label>
                            <div class="col-sm-9">
                                <input name="<?php echo $onderdeel['onderdeel_id'] ?>" type="text" class="form-control"
                                       id="<?php echo $onderdeel['onderdeel_id'] ?>"
                                       value="<?php echo $onderdeel['onderdeel'] ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <button name="opslaan_onderdelen" class="btn btn-primary">Opslaan</button>
            <a href="pietenpakken.php?pagina=1" class="btn btn-primary" role="button">Annuleren</a>
        </form>
    </div>
    <div id="Log" class="tabcontent">
        <table class="table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Actie</th>
                    <th>Gebruiker</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pak_events AS $pak_event){ ?>
                    <tr>
                        <td><?php echo isset($pak_event["datum"]) ? $pak_event["datum"] : ''; ?></td>
                        <td><?php echo isset($pak_event["actie"]) ? $pak_event["actie"] : ''; ?></td>
                        <td>
                            <?php if (isset($pak_event["tussenvoegsel"])){
                                        echo $pak_event["voornaam"] ." ". $pak_event["tussenvoegsel"] ." ". $pak_event["achternaam"];                                                         } else { 
                                        echo $pak_event["voornaam"] ." ". $pak_event["achternaam"];
                            } ?>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
</div>
<div class="footer">
    <div class="left">
        <a href="../melding/aanmaken.php?pakid=<?php echo $pak_id ?>">Melding aanmaken</a>
        <a href="toevoegen.php">Pak toevoegen</a>
    </div>
    <div class="right">
        <a href="../gebruikers/mijn-account.php">Account</a>
        <a href="../login/uitloggen.php">Uitloggen</a>
    </div>
</div>
</body>
<script>
    setTimeout(function () {
        $('.alert').fadeOut('fast');
    }, 5000); // <-- time in milliseconds

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#profiel_foto").change(function () {
        readURL(this);
    });

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    //    for (isset($_POST['opslaan'])){
    document.getElementById("defaultOpen").click();
    
    //foto ophalen
    document.querySelector('.preview-image-instruction')
    .addEventListener('drop', (ev) => {
        ev.preventDefault();
        document.querySelector('$pak["foto_id"]').files = ev.dataTransfer.files;
    });
</script>
</html>
