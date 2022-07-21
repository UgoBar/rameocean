<?php

require ('../models/User.php');

class UserController extends DefaultController
{

    private $user;

    public function __construct()
    {
        $this -> user = new User();
    }

    public function getUsers()
    {
        $users = $this -> user ->findAll();

        foreach ($users as $user) {
            if (isset($_POST['delete-user-' . $user['id']])) {

                $id      = (int)$_POST['delete-user-' . $user['id']];
                $name   = $_POST['name-' . $user['id']];

                $this -> user -> delete($id);
                $this -> addFlashBag("L'utilisateur $name a bien été supprimé");
                header('Location:index.php?name=users');
            }
        }

        $this -> getLayout('user/users', 'Utilisateurs', $this->getFlashBag(), [
            'users' => $users,
        ]);
    }

    public function addUser()
    {
        $this -> verifyConnection('ROLE_ADMIN');
        $pageTitle = 'Ajouter un utilisateur';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;

        // EDITION MODE
        if($id) {
            $user = $this -> user -> findById($id);

            $firstname = $user['firstname'];
            $lastname = $user['lastname'];
            $email = $user['email'];
            $password = $user['password'];
            $role = $user['role'];

            $pageTitle = "Modification de l'utilisateur $firstname";
        }

        if(isset($_POST['email'])) {
            $firstname          = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
            $lastname           = (isset($_POST['lastname'])) ? trim($_POST['lastname'])  :'';
            $email              = trim($_POST['email']);
            $password           = (isset($_POST['password'])) ? $_POST['password'] : '';
            $confirmPassword    = (isset($_POST['confirmPassword'])) ? $_POST['confirmPassword'] : '';
            $role               = $_POST['role'];

            // Vérification des erreurs du champ "prénom"
            if(empty($firstname) || strlen($_POST['firstname']) < 3)
                $errors['firstname'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';
            // Vérification des erreurs du champ "nom de famille"
            if(empty($lastname) || strlen($_POST['lastname']) < 2)
                $errors['lastname'] = 'Ce champ est obligatoire et doit avoir au moins 2 caractères';

            // Vérification du format de l'email et si il est déjà en base
            if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $errors['email'] = 'Mauvais format de mail';
            } else {
                $isMailInBase = $this->user->findByEmail($email);
                // EDITION
                if($id) {
                    if($email !== $user['email'] && $isMailInBase)
                        $errors['email'] = 'Cet email est déjà pris !';
                } else {
                    if($isMailInBase)
                        $errors['email'] = 'Cet email est déjà pris !';
                }
            }

            // Vérification des erreurs du champ "mot de passe"
            if(empty($password) || strlen($password) < 4)
                $errors['password'] = 'Votre mot de passe doit avoir au 5 caractères !';

            // Vérification des erreurs du champ "confirmer votre mot de passe"
            if(empty($confirmPassword) || $password != $confirmPassword)
                $errors['confirmPassword'] = 'Les 2 mots de passe ne sont pas les mêmes';

            if(empty($errors)) {
                if(!$id) {
                    $this->user->add($firstname, $lastname, $email, $password, $role);
                    $this -> addFlashBag("Utilisateur ajouté avec succès", 'success');
                } else {
                    $this->user->update($id, $firstname, $lastname, $email, $password, $role);
                    $this -> addFlashBag("Utilisateur ajouté avec succès", 'success');
                }
                header('Location:index.php?name=users');
            }
        }

        $this -> getLayout('user/addUser', $pageTitle, $this->getFlashBag(), [
            'id' => $id ?? null,
            'firstname' => $firstname ?? '',
            'lastname' => $lastname ?? '',
            'email' => $email ?? '',
            'password' => $password ?? '',
            'confirmPassword' => $confirmPassword ?? '',
            'role' => $role ?? 'ROLE_USER',
            'errors' => $errors ?? [],
        ]);
    }

    public function disconnect()
    {
        $_SESSION['connected'] = false;
        header('Location:login.php');
        exit();
    }
}
