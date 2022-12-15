<?php

require('../models/Coordinate.php');

class CoordinateController extends DefaultController
{
    private $coordinate;

    public function __construct()
    {
        $this -> coordinate = new Coordinate();
    }

    public function getCoordinates()
    {
        $coordinates = $this -> coordinate -> findAll();

        foreach ($coordinates as $coordinate) {
            if (isset($_POST['delete-coordinate-' . $coordinate['id']])) {
                $id      = (int)$_POST['delete-coordinate-' . $coordinate['id']];
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

        // EDITION MODE
        if($id) {

            $coordinate = $this -> coordinate -> findById($id);

            $date         = $coordinate['date'];
            $hour         = $coordinate['hour'];
            $latitude     = $coordinate['latitude'];
            $longitude    = $coordinate['longitude'];
            $errors       = [];

            $pageTitle = "Modification de la coordonnées du $date";
        }

        if (isset($_POST['latitude'])) {

            // On hydrate les variables avec les données reçues du formulaire
            $date        = (isset($_POST['date'])) ? trim($_POST['date']) : $date;
            $description = (isset($_POST['hour'])) ? trim($_POST['hour']) : '18h30';
            $latitude    = (isset($_POST['latitude'])) ? trim($_POST['latitude']) : 0;
            $longitude   = (isset($_POST['longitude'])) ? trim($_POST['longitude']) : 0;

            // Gestion des erreurs
            if (empty($date))
                $errors['date'] = 'Ce champ est obligatoire';
            if (empty($description))
                $errors['description'] = 'Ce champ est obligatoire';
            if (empty($latitude))
                $errors['latitude'] = 'Ce champ est obligatoire';
            if (empty($longitude))
                $errors['longitude'] = 'Ce champ est obligatoire';

            if (empty($errors)) {

                if (!$id) {
                    $this -> coordinate -> add($date, $description, $latitude, $longitude);
                    $this -> addFlashBag("La coordonnée a bien été ajoutée", 'success');
                } else {
                    $this -> coordinate -> update($id, $date, $description, $latitude, $longitude);
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
            'errors'      => $errors ?? [],
        ]);
    }
}
