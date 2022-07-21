<?php

class Profile
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add(string $picture, string $alt, string $title, string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_profile (media_id, title, description)
                    VALUES (:media_id, :title, :description)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('title', $title);
        $stm->bindValue('description', $description);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $profileId, string $picture, string $alt, string $title, string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($profileId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId; 
            UPDATE ro_profile p SET p.title = :title, p.description = :description WHERE p.id = :profileId;
        ");

        $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);
        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);

        $stm->bindValue('profileId', $profileId, PDO::PARAM_INT);
        $stm->bindValue('title', $title);
        $stm->bindValue('description', $description);

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $profileId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($profileId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_profile WHERE profile.id = :profileId;
            DELETE FROM ro_media where media.id = :mediaId
        ");
        $stm->bindValue('profileId', $profileId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT p.id, p.title, p.description, p.media_id, m.picture, m.alt
            FROM ro_profile p LEFT JOIN ro_media m ON p.media_id = m.id
        ");

        $stm->execute();
        return $stm->fetch();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT p.id, p.title, p.description, p.media_id, m.picture, m.alt
            FROM ro_profile p LEFT JOIN ro_media m ON p.media_id = m.id
            WHERE p.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $profileId)
    {
        $currentProfile = $this->findById($profileId);
        return $currentProfile['media_id'];
    }
}
