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
        $id          = $voyage ? $voyage['id'] : false;
        $isActive    = $voyage ? $voyage['is_active'] : '';

        if(isset($_POST['is_active'])) {

            $isActive = (isset($_POST['is_active'])) ? 1 : 0;

            if(empty($errors)) {

                if(!$id) {
                    $this -> voyage -> add($isActive);
                    $this -> addFlashBag("La traversée est à jour", 'success');
                } else {
                    $this -> voyage -> update($id, $isActive);
                    $this -> addFlashBag("La traversée est à jour", 'success');
                }
                header('Location:index.php?name=voyage');
            }
        }

        $this -> getLayout('voyage/voyage', 'Traversée en cours', $this->getFlashBag(), [
            'voyage'      => $voyage,
            'id'          => $id,
            'is_active'   => $isActive,
        ]);
    }

}
