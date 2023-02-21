<?php

class Coordinate
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add($date, string $hour, $lat, $lon, ?string $picture = null, ?string $alt = null)
    {

        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_coordinate (date, hour, latitude, longitude, media_id)
                    VALUES (:date, :hour, :lat, :lon, :media_id)"
        );

        $stm->bindValue('date', $date);
        $stm->bindValue('hour', $hour);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);

        if($picture !== null) {
            $lastInsertMediaId = $this->media->add($picture, $alt);
            $stm->bindValue('media_id', $lastInsertMediaId);
        } else {
            $stm->bindValue('media_id', null);
        }

        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $coordinateId, $date, string $hour, $lat, $lon, ?string $picture = null, ?string $alt = null)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $queryPicture = "UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId;";
        $queryNews = "UPDATE ro_coordinate c SET c.date = :date, c.hour = :hour, c.latitude = :lat, c.longitude = :lon WHERE c.id = :coordinateId;";

        $stm = $this->dbh->prepare($picture !== null ? $queryPicture . $queryNews : $queryNews);

        if($picture !== null) {
            $mediaId = $this->findMediaId($coordinateId);
            $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);
            $stm->bindValue('picture', $picture);
            $stm->bindValue('alt', $alt);
        }

        $stm->bindValue('coordinateId', $coordinateId, PDO::PARAM_INT);
        $stm->bindValue('date', $date);
        $stm->bindValue('hour', $hour);
        $stm->bindValue('lat', $lat);
        $stm->bindValue('lon', $lon);
        $stm->execute();

       return $this->dbh->lastInsertId();
    }

    public function delete(int $coordinateId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($coordinateId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_coordinate WHERE id = :coordinateId;
            DELETE FROM ro_media where id = :mediaId;
        ");
        $stm->bindValue('coordinateId', $coordinateId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT c.id, c.media_id, c.date, c.hour, c.latitude, c.longitude, m.picture, m.alt
            FROM ro_coordinate c
            LEFT JOIN ro_media m ON c.media_id = m.id
            ORDER BY c.date DESC
        ");

        $stm->execute();
        return $stm->fetchAll();
    }

    public function findLastOne()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT c.date, c.hour, c.latitude, c.longitude, m.picture, m.alt
            FROM ro_coordinate c
            LEFT JOIN ro_media m ON c.media_id = m.id
            ORDER BY c.date DESC
            LIMIT 1;
        ");

        $stm->execute();
        return $stm->fetch();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT c.media_id, c.date, c.hour, c.latitude, c.longitude, m.picture, m.alt
            FROM ro_coordinate c
            LEFT JOIN ro_media m ON c.media_id = m.id
            WHERE c.id = :id;
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $newsId)
    {
        $currentNews = $this->findById($newsId);
        return $currentNews['media_id'];
    }
}
