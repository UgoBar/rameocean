<?php

class Contact
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    public function getAll(): array
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stm = $this->dbh->prepare('SELECT * FROM ro_contact');
        $stm->execute();
        return $stm->fetchAll();
    }

    public function add(string $firstname, string $lastname, string $phone, string $mail, string $demand, string $content, Datetime $createdAt): string
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare(
            "INSERT INTO ro_contact (firstname, lastname, phone, mail, demand, content, created_at) 
                    VALUES (:firstname, :lastname, :phone, :mail, :demand, :content, :created_at)");

        $stm->bindValue('firstname', $firstname);
        $stm->bindValue('lastname', $lastname);
        $stm->bindValue('phone', $phone);
        $stm->bindValue('mail', $mail);
        $stm->bindValue('demand',$demand);
        $stm->bindValue('content',$content);
        $stm->bindValue('created_at',$createdAt->format('Y-m-d H:i:s'));

        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function countOfContacts()
    {
        $stm = $this->dbh->prepare('SELECT COUNT(*) as count FROM ro_contact');
        $stm->execute();
        return $stm->fetch();
    }
}
