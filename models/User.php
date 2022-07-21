<?php

class User
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = dbConnexion();
    }

    /**
     * Renvoie un utilisateur à partir de son email
     * @param string $email
     *
     */
    public function findByEmail(string $email)
    {
        /** ........................MAIL IN BASE......................... */
        $stm = $this->dbh->prepare('SELECT * FROM ro_user WHERE email = :checkMail');
        $stm->bindValue('checkMail',$email);
        $stm->execute();
        return $stm->fetch();
    }

    /** Renvoie tous les utilisateurs
     * @return array Tous les utilisateurs
     */
    public function findAll(): array
    {
        $stm = $this->dbh->prepare('SELECT * FROM ro_user');
        $stm->execute();
        return $stm->fetchAll();
    }


    /**
     * Insertion d'utilisateur
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param string $role
     * @return string
     */
    public function add(string $firstname, string $lastname, string $email, string $password, string $role = 'ROLE_USER'): string
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        //cryptage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        /** ......................... CREATE USER ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $stm = $this->dbh->prepare("INSERT INTO ro_user 
            (firstname, lastname, email, password, role) 
            VALUES (:firstname, :lastname, :email, :password, :role)");
        $stm->bindValue('firstname', $firstname);
        $stm->bindValue('lastname', $lastname);
        $stm->bindValue('email', $email);
        $stm->bindValue('password', $passwordHash);
        $stm->bindValue('role', $role);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    /**
     * Modification d'utilisateur
     * @param int $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param string $role
     * @return string
     */
    public function update(int $id, string $firstname, string $lastname, string $email, string $password, string $role = 'ROLE_USER'): string
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $userInBase = $this->findByEmail($email);
        // Si le mot de passe est différent alors on le hash sinon non
        $hash = substr( $userInBase['password'], 0, 60 );
        $newPassword = password_verify($password, $hash) ? $userInBase['password'] : password_hash($password, PASSWORD_DEFAULT);

        /** ......................... UPDATE USER ......................... */
        $stm = $this->dbh->prepare("UPDATE ro_user 
            SET firstname = :firstname, lastname = :lastname, email = :email, password = :newPassword, role = :role 
            WHERE id=:id");
        $stm->bindValue('id',$id, PDO::PARAM_INT);
        $stm->bindValue('firstname',$firstname);
        $stm->bindValue('lastname',$lastname);
        $stm->bindValue('email',$email);
        $stm->bindValue('newPassword',$newPassword);
        $stm->bindValue('role',$role);
        /** 4. Exécuter notre requête */
        $stm->execute();

        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /**
     * Modification du mot de passe
     * @param string $email
     * @param string $password
     * @return string
     */
    public function updatePassword(string $email, string $password): string
    {

        //cryptage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        /** ......................... UPDATE PASSWORD ......................... */
        $stm = $this->dbh->prepare("UPDATE ro_user SET password = :password WHERE email = :email");
        $stm->bindValue('email',$email);
        $stm->bindValue('password',$passwordHash);
        $stm->execute();

        return $this->dbh->lastInsertId();
    }

    public function delete(int $id)
    {
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $stm = $this->dbh->prepare("DELETE FROM ro_user WHERE id = :id");
        $stm->bindValue('id', $id);
        $stm->execute();
    }

    /**
     * Recherche d'un utilisateur
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $stm = $this->dbh->prepare("SELECT * FROM ro_user WHERE id = $id");
        $stm->execute();
        return $stm->fetch();
    }
}
