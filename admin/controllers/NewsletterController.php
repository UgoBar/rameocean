<?php

require('../models/Newsletter.php');

class NewsletterController extends DefaultController
{
    private $newsletter;

    public function __construct()
    {
        $this -> newsletter = new Newsletter();
    }

    public function getNews()
    {
        $newsletters = $this -> newsletter -> findAll();
        setlocale(LC_TIME, "fr_FR");

        foreach ($newsletters as $newsletter) {
            if (isset($_POST['delete-newsletter-' . $newsletter['id']])) {
                $id      = (int)$_POST['delete-newsletter-' . $newsletter['id']];
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

        $this -> verifyConnection('ROLE_ROWER');

        $pageTitle = 'Ajouter une news';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $date = date("Y-m-d");

        // EDITION MODE
        if($id) {

            $newsletter = $this -> newsletter -> findById($id);

            $date         = $newsletter['date'];
            $description  = $newsletter['description'];
            $errors       = [];

            $pageTitle = "Modification de la news du $date";
        }

        if (isset($_POST['description'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $date        = (isset($_POST['date'])) ? trim($_POST['date']) : '';
            $description = (isset($_POST['description'])) ? trim($_POST['description']) : '';

            // Gestion des erreurs
            if (empty($date))
                $errors['date'] = 'Ce champ est obligatoire';
            if (empty($description))
                $errors['description'] = 'Ce champ est obligatoire';

            if (empty($errors)) {

                if (!$id) {
                    $this -> newsletter -> add($date, $description);
                    $this -> addFlashBag("La news a bien été ajoutée", 'success');
                } else {
                    $this -> newsletter -> update($id, $date, $description);
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
            'errors'      => $errors ?? [],
        ]);
    }
}
