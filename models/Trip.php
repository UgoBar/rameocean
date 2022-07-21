<?php

class Trip
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add(string $picture, string $alt, int $date, int $isVideo, string $videoUrl, string $description, string $title, string $subtitle)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_trip (media_id, date, is_video, video_url, description, title, subtitle)
                    VALUES (:media_id, :date, :is_video, :video_url, :description, :title, :subtitle)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('date', $date);
        $stm->bindValue('is_video', $isVideo);
        $stm->bindValue('video_url', $videoUrl);
        $stm->bindValue('description', $description);
        $stm->bindValue('title', $title);
        $stm->bindValue('subtitle', $subtitle);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $tripId, string $picture, string $alt, int $date, int $isVideo, string $videoUrl, string $description, string $title, string $subtitle)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($tripId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId; 
            UPDATE ro_trip t SET t.date = :date, t.is_video = :is_video, t.video_url = :video_url, t.description = :description, t.title = :title, t.subtitle = :subtitle WHERE t.id = :tripId;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);

        $stm->bindValue('tripId', $tripId, PDO::PARAM_INT);
        $stm->bindValue('date', $date);
        $stm->bindValue('is_video', $isVideo);
        $stm->bindValue('video_url', $videoUrl);
        $stm->bindValue('description', $description);
        $stm->bindValue('title', $title);
        $stm->bindValue('subtitle', $subtitle);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $tripId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($tripId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_trip WHERE ro_trip.id = :tripId;
            DELETE FROM ro_media where ro_media.id = :mediaId
        ");
        $stm->bindValue('tripId', $tripId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll(): array
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare('
            SELECT t.id, t.date, t.is_video, t.video_url, t.description, t.title, t.subtitle, t.media_id, m.picture, m.alt
            FROM ro_trip t
            LEFT JOIN ro_media m ON t.media_id = m.id
            ORDER BY t.date DESC');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT t.date, t.is_video, t.video_url, t.description, t.title, t.subtitle, t.media_id, m.picture, m.alt
            FROM ro_trip t
            LEFT JOIN ro_media m ON t.media_id = m.id
            WHERE t.id = :id");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $tripId)
    {
        $currentTrip = $this->findById($tripId);
        return $currentTrip['media_id'];
    }
}
