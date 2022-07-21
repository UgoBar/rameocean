<?php

const BD_DSN = 'mysql:host=localhost;dbname=rameocean;charset=utf8';
const BD_USER = 'root';
const BD_PASS = '';

function dbConnexion(): PDO
{

    $dbh = new PDO(BD_DSN, BD_USER, BD_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $dbh;
}
