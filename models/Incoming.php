<?php

class Incoming
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function  add(string $picture, string $alt, string $title, string $startAt, int $rower, int $seatLeft, string $description, int $isActive)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_incoming (media_id, title, start_at, rower, seat_left, description, is_active)
                    VALUES (:media_id, :title, :start_at, :rower, :seat_left, :description, :is_active)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('start_at', $startAt);
        $stm->bindValue('rower', $rower);
        $stm->bindValue('seat_left', $seatLeft);
        $stm->bindValue('description', $description);
        $stm->bindValue('is_active', $isActive);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $incomingId, string $picture, string $alt, string $title, string $startAt, int $rower, int $seatLeft, string $description, int $isActive)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($incomingId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId; 
            UPDATE ro_incoming i SET i.title = :title, i.start_at = :start_at, i.rower = :rower, i.seat_left = :seat_left, i.description = :description, i.is_active = :is_active WHERE i.id = :incomingId;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);

        $stm->bindValue('incomingId', $incomingId, PDO::PARAM_INT);
        $stm->bindValue('title', $title);
        $stm->bindValue('start_at', $startAt);
        $stm->bindValue('rower', $rower);
        $stm->bindValue('seat_left', $seatLeft);
        $stm->bindValue('description', $description);
        $stm->bindValue('is_active', $isActive);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $incomingId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($incomingId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_incoming WHERE incoming.id = :incomingId;
            DELETE FROM ro_media where media.id = :mediaId
        ");
        $stm->bindValue('incomingId', $incomingId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT i.id, i.title, i.start_at, i.rower, i.seat_left, i.description, i.is_active, i.media_id, m.picture, m.alt
            FROM ro_incoming i
            LEFT JOIN ro_media m ON i.media_id = m.id
        ");

        $stm->execute();
        return $stm->fetch();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT i.id, i.title, i.start_at, i.rower, i.seat_left, i.description, i.is_active, i.media_id, m.picture, m.alt
            FROM ro_incoming i LEFT JOIN ro_media m ON i.media_id = m.id
            WHERE i.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $incomingId)
    {
        $currentIncoming = $this->findById($incomingId);
        return $currentIncoming['media_id'];
    }
}
