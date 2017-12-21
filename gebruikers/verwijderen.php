<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);
    $ingelogde_gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rol_id = $ingelogde_gebruiker['rol_id'];

    if ($rol_id !== 3) {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om een gebruiker te verwijderen!'
        );
        $user->redirect('../pakken/pietenpakken.php?pagina=1');
    }

    $gebruiker_id = $_GET['id'];
    if (empty($gebruiker_id)){
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Deze gebruiker kan niet worden gevonden!'
        );
        $user->redirect('../gebruikers/overzicht.php');
    }

    if ($ingelogde_gebruiker['rol_id'] === $gebruiker_id){
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je bent ingelogd met dit account en deze kan dus niet worden verwijderd!'
        );
        $user->redirect('../gebruikers/overzicht.php');
    }

    $gebruiker = $user->gebruiker_ophalen_id($gebruiker_id);

    $user->gebruiker_verwijderen($gebruiker);

    $user->redirect('../gebruikers/overzicht.php?pagina=1');
?>