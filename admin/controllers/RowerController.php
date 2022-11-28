<?php

require('../models/Rower.php');

class RowerController extends DefaultController
{
    private $rower;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> rower = new Rower($this->media);
    }

    public function getRowers()
    {
        $rowers = $this -> rower -> findAll();


        foreach ($rowers as $rower) {
            if (isset($_POST['delete-rower-' . $rower['id']])) {
                $id      = (int)$_POST['delete-rower-' . $rower['id']];
                $picture = $_POST['picture-' . $rower['id']];

                $this -> deleteFile($picture, 'rower');
                $this -> rower -> delete($id);
                header('Location:index.php?name=rowers');
            }
        }

        $this -> getLayout('rower/rowers', 'Rameurs', $this->getFlashBag(), [
            'rowers' => $rowers,
        ]);
    }

    public function addRower()
    {
        $this -> verifyConnection('ROLE_ROWER');

        $pageTitle = 'Ajouter un rameur';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $picture = '';

        // EDITION MODE
        if($id) {

            $rower = $this -> rower -> findById($id);

            $firstname   = $rower['firstname'];
            $lastname    = $rower['lastname'];
            $age         = $rower['age'];
            $job         = $rower['job'];
            $description = $rower['job'];
            $alt         = $rower['alt'];
            $oldPicture  = $rower['picture'];

            $pageTitle = "Modification du rameur \"$firstname $lastname\"";
        }

        if (isset($_POST['firstname'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $firstname   = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
            $lastname    = (isset($_POST['lastname'])) ? trim($_POST['lastname']) : '';
            $age         = (isset($_POST['age'])) ? trim($_POST['age']) : '';
            $job         = (isset($_POST['job'])) ? trim($_POST['job']) : '';
            $description = (isset($_POST['description'])) ? trim($_POST['description']) : '';
            $alt         = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture  = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            // Gestion des erreurs
            if (empty($firstname))
                $errors['firstname'] = 'Ce champ est obligatoire';
            if (empty($lastname))
                $errors['lastname'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'rower');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'rower');

                if (!$id) {
                    $this -> rower -> add($picture, $alt, $firstname, $lastname, $age, $job, $description);
                    $this -> addFlashBag("Le rameur a bien été ajouté", 'success');
                } else {
                    $this -> rower -> update($id, $picture, $alt, $firstname, $lastname, $age, $job, $description);
                    $this -> addFlashBag("Le rameur a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=rowers');
            }
        }

        $this -> getLayout('rower/addRower', $pageTitle, $this->getFlashBag(), [
            'rower'       => $rower ?? null,
            'id'          => $id ?: null,
            'picture'     => $picture,
            'alt'         => $alt ?? '',
            'firstname'   => $firstname ?? '',
            'lastname'    => $lastname ?? '',
            'age'         => $age ?? '',
            'job'         => $job ?? '',
            'description' => $description ?? '',
            'errors'      => $errors ?? [],
            'oldPicture'  => $oldPicture ?? null,
        ]);
    }
}
