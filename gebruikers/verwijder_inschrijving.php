<?php
    session_start();
    require_once '../connection/db_connectie.php';
    include_once '../user_classes.php';
    include_once '../booking_classes.php';
    $user = new gebruiker($dbh);
    $booking = new booking($dbh);
    $gebruiker = $user->gebruiker_ophalen_id($_SESSION['user_session']);

    $inschrijving_id = $_GET['id'];
    if (empty($inschrijving_id)){
        $_SESSION['flash'] = array(
            'type' => 'danger',
            'message' => 'Deze inschrijving kan niet worden gevonden!'
        );
        $user->redirect('../gebruikers/mijn-account.php');
    }
    $booking->inschrijvingen_verwijderen($inschrijving_id, $gebruiker['gebruiker_id']);

    $user->redirect('../gebruikers/mijn-account.php');
?>