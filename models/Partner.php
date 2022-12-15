<?php

class Partner
{
    private $dbh;
    private $media;

    const PARTNER_MAIN = 10;
    const PARTNER_OFFICIAL = 20;
    const PARTNER_INSTITUTIONAL = 30;
    const PARTNER_TECHNIC = 40;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public static function getPartnerCategories($category = null)
    {
        $categories = [
            self::PARTNER_MAIN => 'Principaux',
            self::PARTNER_OFFICIAL => 'Officiels',
            self::PARTNER_INSTITUTIONAL => 'Institutionnels',
            self::PARTNER_TECHNIC => 'Techniques'
        ];

        if($category !== null){
            return $categories[$category];
        }

        return $categories;
    }

    public function add(string $picture, string $alt, string $title, int $category)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_partner (media_id, title, category)
                    VALUES (:media_id, :title, :category)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('category', $category);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $partnerId, string $picture, string $alt, string $title, int $category)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($partnerId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :media_id ;
            UPDATE ro_partner p SET p.title = :title, p.category = :category WHERE p.id = :partner_id;
        ");

        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);
        $stm->bindValue('media_id', $mediaId, PDO::PARAM_INT);

        $stm->bindValue('title', $title);
        $stm->bindValue('category', $category);
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
            SELECT p.id, p.title, p.category, p.media_id, m.picture, m.alt
            FROM ro_partner p LEFT JOIN ro_media m ON p.media_id = m.id
            ORDER BY category
        ');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findByCategory(int $category): array
    {
        $stm = $this->dbh->prepare('
            SELECT p.id, p.title, p.category, p.media_id, m.picture, m.alt
            FROM ro_partner p LEFT JOIN ro_media m ON p.media_id = m.id
            WHERE p.category = :category
        ');

        $stm->bindValue('category', $category);
        $stm->execute();

        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT p.id, p.title, p.category, p.media_id, m.picture, m.alt
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
