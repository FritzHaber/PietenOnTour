<?php
    session_start();
    session_destroy();
    include_once '../user_classes.php';
    $user = new gebruiker($dbh);

    $user->redirect('login.php')
?>