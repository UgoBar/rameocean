<?php

class Gallery
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add(string $picture, string $alt, string $title, string $credit = '')
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare("
            INSERT INTO ro_gallery (media_id, title, credit)
            VALUES (:media_id, :title, :credit)
        ");

        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('credit', $credit);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $galleryId, string $picture, string $alt, string $title, string $credit = '')
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($galleryId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media SET picture = :picture, alt = :alt WHERE id = :mediaId;
            UPDATE ro_gallery SET title = :title, credit = :credit WHERE id = :galleryId;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('mediaId', $mediaId);

        $stm->bindValue('title', $title);
        $stm->bindValue('credit', $credit);
        $stm->bindValue('galleryId', $galleryId);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $galleryId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($galleryId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_gallery WHERE id = :galleryId;
            DELETE FROM ro_media WHERE id = :mediaId
        ");
        $stm->bindValue('galleryId', $galleryId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll(): array
    {
        $stm = $this->dbh->prepare('
            SELECT g.id, g.title, g.credit, g.media_id, m.picture, m.alt
            FROM ro_gallery g LEFT JOIN ro_media m ON g.media_id = m.id');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT g.id, g.title, g.credit, g.media_id, m.picture, m.alt
            FROM ro_gallery g LEFT JOIN ro_media m ON g.media_id = m.id
            WHERE g.id = :id");
        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $galleryId) {
        $currentGallery = $this->findById($galleryId);
        return $currentGallery['media_id'];
    }
}
