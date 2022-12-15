<?php

class Coordinate
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function add($date, string $hour, $lat, $lon)
    {

        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_coordinate (date, hour, latitude, longitude)
                    VALUES (:date, :hour, :lat, :lon)"
        );

        $stm->bindValue('date', $date);
        $stm->bindValue('hour', $hour);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $coordinateId, $date, string $hour, $lat, $lon)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            UPDATE ro_coordinate c SET c.date = :date, c.hour = :hour, c.latitude = :lat, c.longitude = :lon WHERE c.id = :coordinateId;
        ");

        $stm->bindValue('coordinateId', $coordinateId, PDO::PARAM_INT);
        $stm->bindValue('date', $date);
        $stm->bindValue('hour', $hour);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);
        $stm->execute();

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $coordinateId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_coordinate WHERE id = :coordinateId;
        ");
        $stm->bindValue('coordinateId', $coordinateId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT *
            FROM ro_coordinate c
            ORDER BY c.date DESC;
        ");

        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT *
            FROM ro_coordinate c
            WHERE c.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }
}
