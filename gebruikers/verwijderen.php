<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);
    $ingelogde_gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);
    $rol_id = $ingelogde_gebruiker['rol_id'];

    if ($rol_id !== '3') {
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Je hebt geen rechten om een gebruiker te verwijderen!'
        );
        $user->redirect('../index.php');
    }

    $gebruiker_id = $_GET['id'];
    if (empty($gebruiker_id)){
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Pak niet gevonden!'
        );
        $user->redirect('../gebruikers/overzicht.php');
    }

    $gebruiker = $user->gebruiker_ophalen_id($gebruiker_id);

    $user->gebruiker_verwijderen($gebruiker);

    $user->redirect('../gebruikers/overzicht.php?pagina=1');
?>