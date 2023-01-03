<?php

require('../models/Newsletter.php');

class NewsletterController extends DefaultController
{
    private $newsletter;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> newsletter = new Newsletter($this->media);
    }

    public function getNews()
    {
        $newsletters = $this -> newsletter -> findAll();

        foreach ($newsletters as $newsletter) {
            if (isset($_POST['delete-newsletter-' . $newsletter['id']])) {
                $id      = (int)$_POST['delete-newsletter-' . $newsletter['id']];
                $picture = $_POST['picture-' . $newsletter['id']];
                $this -> deleteFile($picture, 'newsletter');
                $this -> newsletter -> delete($id);
                header('Location:index.php?name=news');
            }
        }

        $this -> getLayout('news/news', 'Rameurs', $this->getFlashBag(), [
            'newsletters' => $newsletters,
        ]);
    }

    public function addNews()
    {

        $pageTitle = 'Ajouter une news';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $picture = '';
        $date = date("Y-m-d");

        // EDITION MODE
        if($id) {

            $newsletter = $this -> newsletter -> findById($id);

            $date         = $newsletter['date'];
            $description  = $newsletter['description'];
            $alt          = $newsletter['alt'];
            $oldPicture   = $newsletter['picture'];
            $errors       = [];

            $pageTitle = "Modification de la news du $date";
        }

        if (isset($_POST['description'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $date        = (isset($_POST['date'])) ? trim($_POST['date']) : '';
            $description = (isset($_POST['description'])) ? trim($_POST['description']) : '';
            $alt         = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture  = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            // Gestion des erreurs
            if (empty($date))
                $errors['date'] = 'Ce champ est obligatoire';
            if (empty($description))
                $errors['description'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'newsletter');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'newsletter');

                if (!$id) {
                    $this -> newsletter -> add($date, $description, $picture, $alt);
                    $this -> addFlashBag("La news a bien été ajoutée", 'success');
                } else {
                    $this -> newsletter -> update($id, $date, $description, $picture, $alt);
                    $this -> addFlashBag("La news a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=news');
            }
        }

        $this -> getLayout('news/addNews', $pageTitle, $this->getFlashBag(), [
            'newsletter'  => $newsletter ?? null,
            'id'          => $id ?: null,
            'date'        => $date ?? '',
            'description' => $description ?? '',
            'picture'     => $picture,
            'alt'         => $alt ?? '',
            'oldPicture'  => $oldPicture ?? null,
            'errors'      => $errors ?? [],
        ]);
    }
}
