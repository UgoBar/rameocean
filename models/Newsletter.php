<?php

class Newsletter
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function add($date, string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare (
            "INSERT INTO ro_newsletter (date, description)
                    VALUES (:date, :description)"
        );

        $stm->bindValue('date', $date);
        $stm->bindValue('description', $description);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function update(int $newsId, $date, string $description)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            UPDATE ro_newsletter n SET n.date = :date, n.description = :description WHERE n.id = :newsId;
        ");

        $stm->bindValue('newsId', $newsId, PDO::PARAM_INT);
        $stm->bindValue('date', $date);
        $stm->bindValue('description', $description);
        $stm->execute();

        $stm->execute();
        return $this->dbh->lastInsertId();
    }

    public function delete(int $newsId)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("
            DELETE FROM ro_newsletter WHERE id = :newsId;
        ");
        $stm->bindValue('newsId', $newsId);

        $stm->execute();
    }

    public function findAll()
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare("
            SELECT *
            FROM ro_newsletter n
            ORDER BY n.date DESC;
        ");

        $stm->execute();
        return $stm->fetchAll();
    }

    public function findById(int $id)
    {
        $stm = $this->dbh->prepare("
            SELECT *
            FROM ro_newsletter n
            WHERE n.id = :id
        ");

        $stm->bindValue('id', $id);
        $stm->execute();
        return $stm->fetch();
    }
}
