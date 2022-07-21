<?php

class Media
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function add(string $picture, string $alt): string
    {

        $stm = $this->dbh->prepare(
    "INSERT INTO ro_media (picture, alt) 
            VALUES (:picture, :alt)"
        );
        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);

        $stm->execute();
        $lastId =  $this->dbh->lastInsertId();
        return $lastId;
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("SELECT * FROM ro_media WHERE id = :id");
        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }
}
