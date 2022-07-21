<?php

require('../models/Gallery.php');

class GalleryController extends DefaultController
{
    private $gallery;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> gallery = new Gallery($this->media);
    }

    public function getGallery()
    {
        $pictures = $this -> gallery -> findAll();

        foreach ($pictures as $picture) {
            if (isset($_POST['delete-picture-' . $picture['id']])) {
                $id      = (int)$_POST['delete-picture-' . $picture['id']];
                $title   = $_POST['title-' . $picture['id']];
                $picture = $_POST['picture-' . $picture['id']];

                $this -> deleteFile($picture, 'gallery');
                $this -> gallery -> delete($id);
                header('Location:index.php?name=gallery');
            }
        }

        $this -> getLayout('gallery/gallery', 'Galerie', $this->getFlashBag(), [
            'gallery' => $pictures,
        ]);
    }

    public function addGallery()
    {
        $this -> verifyConnection('ROLE_ADMIN');

        $pageTitle = 'Ajouter une photo à la galerie';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $picture = '';

        // EDITION MODE
        if($id) {
            $trip = $this -> gallery -> findById($id);

            $title       = $trip['title'];
            $credit      = $trip['credit'];
            $alt         = $trip['alt'];
            $oldPicture  = $trip['picture'];

            $pageTitle = "Modification de la photo $title";
        }

        if (isset($_POST['title'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $title = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $credit = (isset($_POST['credit'])) ? trim($_POST['credit']) : '';
            $alt = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            // Gestion des erreurs
            if (empty($alt))
                $errors['alt'] = 'Ce champ est obligatoire';
            if (empty($title))
                $errors['title'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'gallery');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'gallery');

                if (!$id) {
                    $this -> gallery -> add($picture, $alt, $title, $credit);
                    $this -> addFlashBag("La photo a bien été ajoutée", 'success');
                } else {
                    $this -> gallery -> update($id, $picture, $alt, $title, $credit);
                    $this -> addFlashBag("La photo a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=gallery');
            }
        }

        $this -> getLayout('gallery/addGallery', $pageTitle, $this->getFlashBag(), [
            'gallery'     => $gallery ?? null,
            'id'          => $id ?: null,
            'picture'     => $picture,
            'alt'         => $alt ?? '',
            'title'       => $title ?? '',
            'credit'      => $credit ?? '',
            'errors'      => $errors ?? [],
            'oldPicture'  => $oldPicture ?? null,
        ]);
    }
}
