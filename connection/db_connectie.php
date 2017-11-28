<?php
    $hostname='localhost';
    $username='root';
    $password='';
    $databasename = 'mydb';

    try {
        $dbh = new PDO("mysql:host=$hostname;dbname=$databasename",$username,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }
?>