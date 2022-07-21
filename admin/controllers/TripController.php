<?php

require('../models/Trip.php');

class TripController extends DefaultController
{
    private $trip;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> trip = new Trip($this->media);
    }

    public function getTrips()
    {
        $trips = $this -> trip -> findAll();

        foreach ($trips as $trip) {
            if (isset($_POST['delete-trip-' . $trip['id']])) {
                $id      = (int)$_POST['delete-trip-' . $trip['id']];
                $title   = $_POST['title-' . $trip['id']];
                $picture = $_POST['picture-' . $trip['id']];
                $this -> deleteFile($picture, 'trip');
                $this -> trip -> delete($id);
                header('Location:index.php?name=trips');
            }
        }

        $this -> getLayout('trip/trips', 'Traversées réalisées', $this->getFlashBag(), [
            'trips' => $trips,
        ]);
    }

    public function addTrip()
    {
        $this -> verifyConnection('ROLE_ADMIN');

        $pageTitle = 'Ajouter une traversée';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $picture = '';

        // EDITION MODE
        if($id) {
            $trip = $this -> trip -> findById($id);

            $date        = $trip['date'];
            $isVideo     = $trip['is_video'];
            $videoUrl    = $trip['video_url'];
            $description = $trip['description'];
            $title       = $trip['title'];
            $subtitle    = $trip['subtitle'];
            $alt         = $trip['alt'];
            $oldPicture  = $trip['picture'];

            $pageTitle = "Modification de la traversée $title";
        }

        if (isset($_POST['title'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $date        = (isset($_POST['date'])) ? trim($_POST['date'])  : '';
            $isVideo     = (isset($_POST['is_video'])) ? 1 : 0;
            $videoUrl    = (isset($_POST['video_url'])) ? trim($_POST['video_url'])  : '';
            $description = (isset($_POST['description'])) ? trim($_POST['description'])  : '';
            $title       = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $subtitle    = (isset($_POST['subtitle'])) ? trim($_POST['subtitle']) : '';
            $alt         = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture  = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null;

            // Gestion des erreurs
            if(empty($date))
                $errors['date'] = 'Ce champ est obligatoire';
            if(empty($description))
                $errors['description'] = 'Ce champ est obligatoire';
            if(empty($title))
                $errors['title'] = 'Ce champ est obligatoire';


            if(!$isVideo) {
                /** Upload du fichier et gestion d'erreur */
                try {
                    $picture = $this -> uploadFile('picture', 'trip');
                } catch (DomainException $e) {
                    $errors['picture'] = $e -> getMessage();
                }
            }

            if (empty($errors)) {

                if(!$isVideo) {
                    $picture = $this -> keepOrReplacePicture($picture, $oldPicture, 'trip');
                }

                if (!$id) {
                    $this -> trip -> add($picture ?? '', $alt, $date, $isVideo, $videoUrl, $description, $title, $subtitle);
                    $this -> addFlashBag("La traversée a bien été ajoutée", 'success');
                } else {
                    $this -> trip -> update($id, $picture ?? '', $alt, $date, $isVideo, $videoUrl, $description, $title, $subtitle);
                    $this -> addFlashBag("La traversée a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=trips');
            }
        }

        $this -> getLayout('trip/addTrip', $pageTitle, $this->getFlashBag(), [
            'trip'        => $trip ?? null,
            'id'          => $id ?: null,
            'picture'     => $picture,
            'alt'         => $alt ?? '',
            'date'        => $date ?? '',
            'is_video'    => $isVideo ?? 0,
            'video_url'   => $videoUrl ?? '',
            'title'       => $title ?? '',
            'subtitle'    => $subtitle ?? '',
            'description' => $description ?? '',
            'errors'      => $errors ?? [],
            'oldPicture'  => $oldPicture ?? null,
        ]);
    }

}
