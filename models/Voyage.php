<?php


class Voyage
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function add(int $isActive, $lat, $lon): string
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare(
            "INSERT INTO ro_voyage (is_active, latitude, longitude) 
                    VALUES (:is_active, :lat, :lon)");

        $stm->bindValue('is_active', $isActive);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);

        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $id, int $isActive, $lat, $lon)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            UPDATE ro_voyage v  SET v.is_active = :is_active, latitude = :lat, longitude = :lon WHERE v.id = :id;
        ");

        $stm->bindValue('id', $id, PDO::PARAM_INT);
        $stm->bindValue('is_active', $isActive);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);

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
