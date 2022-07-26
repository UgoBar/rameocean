<?php

require('../models/Partner.php');

class PartnerController extends DefaultController
{
    private $partner;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> partner = new Partner($this->media);
    }

    public function getPartners()
    {
        $partners = $this -> partner -> findAll();
        $categories = $this->partner->getPartnerCategories();

        foreach ($partners as $partner) {
            if (isset($_POST['delete-partner-' . $partner['id']])) {
                $id      = (int)$_POST['delete-partner-' . $partner['id']];
                $title   = $_POST['title-' . $partner['id']];
                $picture = $_POST['picture-' . $partner['id']];

                $this -> deleteFile($picture, 'partner');
                $this -> partner -> delete($id);
                header('Location:index.php?name=partners');
            }
        }

        if(isset($_POST['categories'])) {
            foreach ($partners as $partner) {
                $id       = (int)$partner['id'];
                $picture  = $partner['picture'];
                $alt      = $partner['alt'];
                $title    = $partner['title'];
                $category = (int)$_POST['category-'.$partner['id'].''];

                $this -> partner -> update($id, $picture, $alt, $title, $category);
                $this -> addFlashBag("Les categories des partenaires ont bien été modifiés !", 'success');
                header('Location:index.php?name=partners');
            }
        }

        $this -> getLayout('partner/partners', 'Partenaires', $this->getFlashBag(), [
            'partners' => $partners,
            'categories' => $categories,
        ]);
    }

    public function addPartner()
    {
        $this -> verifyConnection('ROLE_ADMIN');

        $pageTitle = 'Ajouter un partenaire';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $picture = '';
        $count = $this -> partner -> count();
        $categories = $this->partner->getPartnerCategories();

        // EDITION MODE
        if($id) {

            $partner = $this -> partner -> findById($id);

            $title      = $partner['title'];
            $category   = $partner['category'];
            $alt        = $partner['alt'];
            $oldPicture = $partner['picture'];

            $pageTitle = "Modification du partenaire \"$title\"";
        }

        if (isset($_POST['title'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $title = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $category = (int)$_POST['category'];
            $alt = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            // Gestion des erreurs
            if (empty($title))
                $errors['title'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'partner');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'partner');

                if (!$id) {
                    $this -> partner -> add($picture, $alt, $title, $category);
                    $this -> addFlashBag("Le partenaire a bien été ajouté", 'success');
                } else {
                    $this->partner->update($id, $picture, $alt, $title, $category);
                    $this -> addFlashBag("Le partenaire a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=partners');
            }
        }

        $this -> getLayout('partner/addPartner', $pageTitle, $this->getFlashBag(), [
            'partner'    => $partner ?? null,
            'id'         => $id ?: null,
            'picture'    => $picture,
            'alt'        => $alt ?? '',
            'title'      => $title ?? '',
            'category'   => $category ?? 10,
            'categories' => $categories,
            'errors'     => $errors ?? [],
            'oldPicture' => $oldPicture ?? null,
            'count'      => $count
        ]);
    }
}
