<?php

require('../models/Banner.php');

class BannerController extends DefaultController
{
    private $banner;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> banner = new Banner($this->media);
    }

    public function getBanners()
    {
        $banners = $this -> banner -> findAll();

        foreach ($banners as $banner) {
            if (isset($_POST['delete-picture-' . $banner['id']])) {
                $id      = (int)$_POST['delete-picture-' . $banner['id']];
                $title   = $_POST['title-' . $banner['id']];
                $picture = $_POST['picture-' . $banner['id']];

                $this -> deleteFile($picture, 'banner');
                $this -> banner -> delete($id);
                header('Location:index.php?name=banners');
            }
        }

        if(isset($_POST['order'])) {
            foreach ($banners as $banner) {
                $id       = (int)$banner['id'];
                $picture  = $banner['picture'];
                $alt      = $banner['alt'];
                $title    = $banner['title'];
                $position = (int)$_POST['position-'.$banner['id'].''];

                $this -> banner -> update($id, $picture, $alt, $title, $position);
                $this -> addFlashBag("L'ordre des photos a bien été modifié !", 'success');
                header('Location:index.php?name=banners');
            }
        }

        $this -> getLayout('banner/banners', 'Bannières', $this->getFlashBag(), [
            'banners' => $banners,
        ]);
    }

    public function addBanner()
    {
        $this -> verifyConnection('ROLE_ADMIN');
        $pageTitle = 'Ajouter une photo de bannière';

        $banner  = null;

        $title    = '';
        $alt      = '';
        $picture  = null;
        $position = null;
        $errors   = [];

        /** EDIT MODE */
        $id          = null;
        $oldPicture  = null;

        $count = $this -> banner -> count();

        /** EDITION : Si l'on est en mode édition la page reçoit un ID en chaine de requête */
        if(array_key_exists('id',$_GET))
        {
            $banner = $this -> banner -> findById((int)$_GET['id']);

            // Changement du titre de la page
            $pageTitle = 'Edition de la photo : '. $banner['title'];

            /** On rempli le formulaire avec les données existantes, prêtes à être modifiées */
            $id         = $banner['id'];
            $title      = $banner['title'];
            $oldPicture = $banner['picture'];
            $position   = $banner['position'];
            $alt        = $banner['alt'];
        }

        if(isset($_POST['title'])) {

            $title    = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $position = $_POST['position'];
            $alt      = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';

            if(empty($title))
                $errors['title'] = 'Ce champ est obligatoire';
            if(empty($alt))
                $errors['alt'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try
            {
                $picture = $this -> uploadFile('picture', 'banner');
            }
            catch(DomainException $e)
            {
                $errors['picture'] = $e -> getMessage();
            }

            if(empty($errors))
            {
                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'banner');

                if(!$id) {
                    $this -> banner -> add($picture, $alt, $title, $position);
                    $this -> addFlashBag("La photo <b>$title</b> a bien été ajoutée !", 'success');
                } else {
                    $this -> banner -> update($id, $picture, $alt, $title, $position);
                    $this -> addFlashBag("La photo <b>$title</b> a bien été modifée !", 'success');
                }
                header('Location:index.php?name=banners');
            }
        }

        $this -> getLayout('banner/addBanner', $pageTitle, false, [
            'banner'        => $banner,
            'title'         => $title,
            'picture'       => $picture,
            'position'      => $position,
            'alt'           => $alt,
            'errors'        => $errors,
            'id'            => $id,
            'count'         => $count,
            'oldPicture'    => $oldPicture,
        ]);
    }

}
