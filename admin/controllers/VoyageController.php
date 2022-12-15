<?php

require('../models/Voyage.php');

class VoyageController extends DefaultController
{
    private $voyage;

    public function __construct()
    {
        $this -> voyage = new Voyage();
    }

    public function getVoyage()
    {
        $this -> verifyConnection('ROLE_ADMIN');
        $voyage = $this->voyage->findAll();

        // Si $voyage est défini -> édition, sinon -> création
        $id        = $voyage ? $voyage['id'] : false;
        $isActive  = $voyage ? $voyage['is_active'] : '';
        $latitude  = $voyage ? $voyage['latitude'] : 28.418746;
        $longitude = $voyage ? $voyage['longitude'] : -16.549302;

        if(isset($_POST['submit'])) {

            $isActive  = (isset($_POST['is_active'])) ? 1 : 0;
            $latitude  = (isset($_POST['latitude'])) ? trim($_POST['latitude']) : 28.418746;
            $longitude = (isset($_POST['longitude'])) ? trim($_POST['longitude']) : -16.549302;

            if(empty($errors)) {

                if(!$id) {
                    $this -> voyage -> add($isActive, $latitude, $longitude);
                    $this -> addFlashBag("La traversée est à jour", 'success');
                } else {
                    $this -> voyage -> update($id, $isActive, $latitude, $longitude);
                    $this -> addFlashBag("La traversée est à jour", 'success');
                }
                header('Location:index.php?name=voyage');
            }
        }

        $this -> getLayout('voyage/voyage', 'Traversée en cours', $this->getFlashBag(), [
            'voyage'      => $voyage,
            'id'          => $id,
            'latitude'    => $latitude,
            'longitude'  => $longitude,
            'is_active'   => $isActive,
        ]);
    }

}
