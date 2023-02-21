<?php

class Newsletter
{
    private $dbh;
    private $media;

    public function __construct($media)
    {
        $this->dbh = dbConnexion();
        $this->media = $media;
    }

    public function add($date, string $description, ?string $picture = null, ?string $alt = null)
    {

        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_newsletter (date, description, media_id)
                    VALUES (:date, :description, :media_id)"
        );

        $stm->bindValue('date', $date);
        $stm->bindValue('description', $description);

        if($picture !== null) {
            $lastInsertMediaId = $this->media->add($picture, $alt);
            $stm->bindValue('media_id', $lastInsertMediaId);
        } else {
            $stm->bindValue('media_id', null);
        }

        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $newsId, $date, string $description, ?string $picture = null, ?string $alt = null)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $queryPicture = "UPDATE ro_media m SET m.picture = :picture, m.alt = :alt WHERE m.id = :mediaId;";
        $queryNews = "UPDATE ro_newsletter n SET n.date = :date, n.description = :description WHERE n.id = :newsId;";

        $stm = $this->dbh->prepare($picture !== null ? $queryPicture . $queryNews : $queryNews);

        if($picture !== null) {
            $mediaId = $this->findMediaId($newsId);
            $stm->bindValue('mediaId', $mediaId, PDO::PARAM_INT);
            $stm->bindValue('picture', $picture);
            $stm->bindValue('alt', $alt);
        }

        $stm->bindValue('newsId', $newsId, PDO::PARAM_INT);
        $stm->bindValue('date', $date);
        $stm->bindValue('description', $description);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function delete(int $newsId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $mediaId = $this->findMediaId($newsId);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_newsletter WHERE id = :newsId;
            DELETE FROM ro_media where id = :mediaId;
        ");
        $stm->bindValue('newsId', $newsId);
        $stm->bindValue('mediaId', $mediaId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT n.id, n.date, n.description, n.media_id, m.picture, m.alt
            FROM ro_newsletter n
            LEFT JOIN ro_media m ON n.media_id = m.id
            ORDER BY n.date DESC;
        ");

        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT n.id, n.date, n.description, n.media_id, m.picture, m.alt
            FROM ro_newsletter n
            LEFT JOIN ro_media m ON n.media_id = m.id
            WHERE n.id = :id
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
