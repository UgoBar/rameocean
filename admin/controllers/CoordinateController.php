<?php

require('../models/Coordinate.php');

class CoordinateController extends DefaultController
{
    private $coordinate;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> coordinate = new Coordinate($this->media);
    }

    public function getCoordinates()
    {
        $coordinates = $this -> coordinate -> findAll();

        foreach ($coordinates as $coordinate) {
            if (isset($_POST['delete-coordinate-' . $coordinate['id']])) {
                $id      = (int)$_POST['delete-coordinate-' . $coordinate['id']];
                $picture = $_POST['picture-' . $coordinate['id']];
                $this -> deleteFile($picture, 'coordinate');
                $this -> coordinate -> delete($id);
                header('Location:index.php?name=coordinates');
            }
        }

        $this -> getLayout('coordinate/coordinates', 'Rameurs', $this->getFlashBag(), [
            'coordinates' => $coordinates,
        ]);
    }

    public function addCoordinate()
    {

        $pageTitle = 'Ajouter une coordonnées';
        $id = array_key_exists('id',$_GET) ? (int)$_GET['id'] : null;
        $date = date("Y-m-d");
        date_default_timezone_set('Europe/Paris');
        $hour = date('H:i');
        $hour = str_replace(":", "h", $hour);
        $picture = '';

        // EDITION MODE
        if($id) {

            $coordinate = $this -> coordinate -> findById($id);

            $date         = $coordinate['date'];
            $hour         = $coordinate['hour'];
            $latitude     = $coordinate['latitude'];
            $longitude    = $coordinate['longitude'];
            $oldPicture   = $coordinate['picture'];
            $errors       = [];

            $pageTitle = "Modification de la coordonnées du $date";
        }

        if (isset($_POST['latitude'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $date        = (isset($_POST['date'])) ? trim($_POST['date']) : $date;
            $hour        = (isset($_POST['hour'])) ? trim($_POST['hour']) : '18h30';
            $latitude    = (isset($_POST['latitude'])) ? trim($_POST['latitude']) : 0;
            $longitude   = (isset($_POST['longitude'])) ? trim($_POST['longitude']) : 0;
            $alt         = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture  = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            // Gestion des erreurs
            if (empty($date))
                $errors['date'] = 'Ce champ est obligatoire';
            if (empty($hour))
                $errors['description'] = 'Ce champ est obligatoire';
            if (empty($latitude))
                $errors['latitude'] = 'Ce champ est obligatoire';
            if (empty($longitude))
                $errors['longitude'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'coordinate');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this->keepOrReplacePicture($picture, $oldPicture, 'coordinate');

                if (!$id) {
                    $this -> coordinate -> add($date, $hour, $latitude, $longitude, $picture, $alt);
                    $this -> addFlashBag("La coordonnée a bien été ajoutée", 'success');
                } else {
                    $this -> coordinate -> update($id, $date, $hour, $latitude, $longitude, $picture, $alt);
                    $this -> addFlashBag("La coordonnée a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=coordinates');
            }
        }

        $this -> getLayout('coordinate/addCoordinate', $pageTitle, $this->getFlashBag(), [
            'coordinate'  => $coordinate ?? null,
            'id'          => $id ?: null,
            'date'        => $date ?? '',
            'hour'        => $hour ?? '',
            'latitude'    => $latitude ?? '',
            'longitude'   => $longitude ?? '',
            'picture'     => $picture,
            'alt'         => $alt ?? '',
            'oldPicture'  => $oldPicture ?? null,
            'errors'      => $errors ?? [],
        ]);
    }
}
