<?php

function dbConnexion(): PDO
{
    $db_host = 'ugobarldatabase.mysql.db';
    $db_name = 'ugobarldatabase';
    $db_user = 'ugobarldatabase';
    $db_pass = 'PinkFloyd17';

    try {

        $dbh = new PDO("mysql:dbname=$db_name;host=$db_host", $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $dbh;

    } catch (PDOException $e) {

        echo 'Connexion échouée : ' . $e->getMessage();

    }
}
