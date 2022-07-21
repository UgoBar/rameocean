<?php

class Partner
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
            "INSERT INTO ro_partner (media_id, title, position)
                    VALUES (:media_id, :title, :position)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('position', $position);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $partnerId, string $picture, string $alt, string $title, int $position)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($partnerId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :media_id ;
            UPDATE ro_partner p SET p.title = :title, p.position = :position WHERE p.id = :partner_id;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('media_id', $mediaId, PDO::PARAM_INT);

        $stm->bindValue('title', $title);
        $stm->bindValue('position', $position);
        $stm->bindValue('partner_id', $partnerId, PDO::PARAM_INT);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $partnerId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($partnerId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_partner WHERE id = :partnerId;
            DELETE FROM ro_media WHERE id = :mediaId
        ");
        $stm->bindValue('partnerId', $partnerId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll(): array
    {
        $stm = $this->dbh->prepare('
            SELECT p.id, p.title, p.position, p.media_id, m.picture, m.alt
            FROM ro_partner p LEFT JOIN ro_media m ON p.media_id = m.id
            ORDER BY p.position
        ');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT p.id, p.title, p.position, p.media_id, m.picture, m.alt
            FROM ro_partner p LEFT JOIN ro_media m ON p.media_id = m.id
            WHERE p.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function count()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $sth = $this->dbh->prepare('SELECT COUNT(*) as partner_count FROM ro_partner');
        $sth->execute();
        return $sth->fetch();
    }

    public function findMediaId(int $partnerId)
    {
        $currentPartner = $this->findById($partnerId);
        return $currentPartner['media_id'];
    }
}
