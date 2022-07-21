<?php

declare(strict_types=1);
session_start();

require('../config/bdd.php');
require('../models/User.php');

/******* Varirables globales ******/

$errors              = false;
$mailError           = false;
$displayConfirmation = false;
$tokenError          = false;

$token = '2749ec84c3c252bae7539ea0be33c21e9d86550d';
$_SESSION['token'] = $token;

$password = '';
$confirmPassword = '';
$token = $_SESSION['token'];
$_SESSION['userMail'] = 'ugo17190@gmail.com';

if(isset($_POST['update']))
{
    $password        = (isset($_POST['password'])) ? $_POST['password'] : '';
    $confirmPassword = (isset($_POST['confirmPassword'])) ? $_POST['confirmPassword'] : '';

    // Le champ mail a t-il un format correct ?
    if(!empty($_POST['email']))
    {
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $email = $_POST['email'];
        else
            $errors['email'] = 'L\'email renseigné n\'est pas valide';
    }
    else
        $errors['email'] = 'Email non renseigné';

    // Vérification des erreurs du champ "confirmer votre mot de passe"
    if(empty($confirmPassword) || $password != $confirmPassword)
        $errors['confirmPassword'] = 'Les 2 mots de passe ne sont pas les mêmes';

    // Vérification du token : on compare si le token de la session et de l'url sont les mêmes
    if($_GET['token'] !== $token)
    {
        $errors['token'] = true;
        $tokenError = true;
    }

    /**** Aucunes erreurs ? Alors on modifie le mot de passe de l'utilisateur dans la BDD ****/
    if(empty($errors) && $tokenError == false)
    {
        /* Récupération de l'utilisateur */
        $userModel = new User();
        //$user = $userModel->getByEmail($email);

        $userModel->updatePassword($email, $password);
        $displayConfirmation = true;
    }


}


require('views/updatePassword.phtml');
