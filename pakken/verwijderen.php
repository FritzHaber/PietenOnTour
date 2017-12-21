<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    include_once '../pak_classes.php';
    $user = new gebruiker($dbh);
    $costume = new pak($dbh);
    $ingelogde_gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rol_id = $ingelogde_gebruiker['rol_id'];
    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    if ($rol_id !== 3) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om een gebruiker te verwijderen!'
        );
        $user->redirect('../pakken/pietenpakken.php?pagina=1');
    } elseif (empty($pak_id)){
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Pak niet gevonden!'
        );
        $user->redirect('../pakken/pietenpakken.php?pagina=1');
    } else {
        $pak = $costume->pak_ophalen_pakid($pak_id);

        $costume->pak_verwijderen($pak);

        $user->redirect('../pakken/pietenpakken.php?pagina=1');
    }


?>