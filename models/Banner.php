<?php

class Banner
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add(string $picture, string $alt, string $title, int $position)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_banner (media_id, title, position)
                    VALUES (:media_id, :title, :position)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('position', $position);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $bannerId, string $picture, string $alt, string $title, int $position)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($bannerId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId; 
            UPDATE ro_banner b SET b.title = :title, b.position = :position WHERE b.id = :bannerId;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);

        $stm->bindValue('title', $title);
        $stm->bindValue('position', $position);
        $stm->bindValue('bannerId', $bannerId, PDO::PARAM_INT);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $bannerId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($bannerId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_banner WHERE id = :bannerId;
            DELETE FROM ro_media where id = :mediaId
        ");
        $stm->bindValue('bannerId', $bannerId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll(): array
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare('SELECT b.id, b.title, b.position, b.media_id, m.picture, m.alt FROM ro_banner b LEFT JOIN ro_media m ON b.media_id = m.id ORDER BY b.position');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("SELECT b.id, b.title, b.position, b.media_id, m.picture, m.alt FROM ro_banner b LEFT JOIN ro_media m ON b.media_id = m.id WHERE b.id = :id");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function count()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as ban_count FROM ro_banner');
        $sth->execute();
        return $sth->fetch();
    }

    public function findMediaId(int $bannerId) {
        $currentBanner = $this->findById($bannerId);
        return $currentBanner['media_id'];
    }
}
