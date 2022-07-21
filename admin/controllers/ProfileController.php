<?php

require('../models/Profile.php');

class ProfileController extends DefaultController
{
    private $profile;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> profile = new Profile($this->media);
    }


    public function getProfile()
    {
        $this -> verifyConnection('ROLE_ADMIN');
        $profile = $this->profile->findAll();

        // Si $profile est défini -> édition, sinon -> création
        $id          = $profile ? $profile['id'] : false;
        $description = $profile ? $profile['description'] : '';
        $title       = $profile ? $profile['title'] : '';
        $picture     = $profile ? $profile['picture'] : '';
        $alt         = $profile ? $profile['alt'] : '';

        $errors = [];

        $oldPicture = $id ? $profile['picture'] : null;

        if(isset($_POST['title'])) {

            $description = (isset($_POST['description'])) ? trim($_POST['description'])  : '';
            $title       = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $alt         = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture     = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null;

            if(empty($description))
                $errors['description'] = 'Ce champ est obligatoire';
            if(empty($title))
                $errors['title'] = 'Ce champ est obligatoire';
            if(empty($alt))
                $errors['alt'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try
            {
                $picture = $this -> uploadFile('picture', 'profile');
            }
            catch(DomainException $e)
            {
                $errors['picture'] = $e -> getMessage();
            }

            if(empty($errors)) {

                $picture = $this -> keepOrReplacePicture($picture, $oldPicture, 'profile');

                if(!$id) {
                    $this -> profile -> add($picture, $alt, $title, $description);
                    $this -> addFlashBag("Le profil est à jour", 'success');
                } else {
                    $this -> profile -> update($id, $picture, $alt, $title, $description);
                    $this -> addFlashBag("Le profil est à jour", 'success');
                }
                header('Location:index.php?name=profile');
            }
        }

        $this -> getLayout('profile/profile', 'Biographie', $this->getFlashBag(), [
            'profile'     => $profile,
            'id'          => $id,
            'picture'     => $picture,
            'alt'         => $alt,
            'title'       => $title,
            'description' => $description,
            'errors'      => $errors,
            'oldPicture'  => $oldPicture,
        ]);
    }

}
