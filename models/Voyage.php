<?php


class Voyage
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function add(int $isActive): string
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare(
            "INSERT INTO ro_voyage (is_active) 
                    VALUES (:is_active)");

        $stm->bindValue('is_active', $isActive);

        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $id, int $isActive)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            UPDATE ro_voyage v  SET v.is_active = :is_active WHERE v.id = :id;
        ");

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('is_active', $isActive);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("SELECT * FROM ro_voyage v;");

        $stm->execute();
        return $stm->fetch();
    }
}
