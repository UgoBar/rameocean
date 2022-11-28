<?php

class Rower
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add(string $picture, string $alt, string $firstname, string $lastname, int $age, string $job ,string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $lastInsertMediaId = $this->media->add($picture, $alt);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_rower (media_id, firstname, lastname, age, job, description)
                    VALUES (:media_id, :firstname, :lastname, :age, :job, :description)"
        );
        $stm->bindValue('media_id', $lastInsertMediaId);
        $stm->bindValue('firstname', $firstname);
        $stm->bindValue('lastname', $lastname);
        $stm->bindValue('age', $age);
        $stm->bindValue('job', $job);
        $stm->bindValue('description', $description);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $rowerId, string $picture, string $alt, string $firstname, string $lastname, int $age, string $job ,string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($rowerId);

        $stm = $this->dbh->prepare("
            UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId; 
            UPDATE ro_rower r SET r.firstname = :firstname, r.lastname = :lastname, r.age = :age, r.job = :job, r.description = :description WHERE r.id = :rowerId;
        ");

        $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);
        $stm->bindValue('picture', $picture);
        $stm->bindValue('alt', $alt);

        $stm->bindValue('rowerId', $rowerId, PDO::PARAM_INT);
        $stm->bindValue('firstname', $firstname);
        $stm->bindValue('lastname', $lastname);
        $stm->bindValue('age', $age);
        $stm->bindValue('job', $job);
        $stm->bindValue('description', $description);
        $stm->execute();

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $rowerId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($rowerId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_rower WHERE id = :rowerId;
            DELETE FROM ro_media where id = :mediaId
        ");
        $stm->bindValue('rowerId', $rowerId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll(): array
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT r.id, r.firstname, r.lastname, r.age, r.job, r.description, r.media_id, m.picture, m.alt
            FROM ro_rower r LEFT JOIN ro_media m ON r.media_id = m.id
        ");

        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT r.id, r.firstname, r.lastname, r.age, r.job, r.description, r.media_id, m.picture, m.alt
            FROM ro_rower r LEFT JOIN ro_media m ON r.media_id = m.id
            WHERE r.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }

    public function findMediaId(int $rowerId)
    {
        $currentRower = $this->findById($rowerId);
        return $currentRower['media_id'];
    }
}
