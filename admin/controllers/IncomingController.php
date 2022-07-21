<?php

require('../models/Incoming.php');

class IncomingController extends DefaultController
{
    private $incoming;
    private $media;

    public function __construct()
    {
        $this -> media = new Media();
        $this -> incoming = new Incoming($this->media);
    }

    public function getIncoming()
    {
        $this -> verifyConnection('ROLE_ADMIN');

        $incoming = $this -> incoming -> findAll();

        // Si $profile est défini -> édition, sinon -> création
        $id          = $incoming ? $incoming['id'] : false;
        $picture     = $incoming ? $incoming['picture'] : '';

        $oldPicture = $id ? $incoming['picture'] : null;

        if(isset($_POST['title'])) {

            $title = (isset($_POST['title'])) ? trim($_POST['title']) : '';
            $startAt = (isset($_POST['start_at'])) ? trim($_POST['start_at']) : '';
            $rower = (isset($_POST['rower'])) ? trim($_POST['rower']) : 0;
            $seatLeft = (isset($_POST['seat_left'])) ? trim($_POST['seat_left']) : 0;
            $isActive = (isset($_POST['is_active'])) ? 1 : 0;
            $description = (isset($_POST['description'])) ? trim($_POST['description']) : '';
            $alt = (isset($_POST['alt'])) ? trim($_POST['alt']) : '';
            $oldPicture = (isset($_POST['oldPicture'])) ? trim($_POST['oldPicture']) : null;

            if (empty($title))
                $errors['title'] = 'Ce champ est obligatoire';
            if (empty($startAt))
                $errors['start_at'] = 'Ce champ est obligatoire';
            if ($rower < 0)
                $errors['rower'] = 'Le champ doit être au moins supérieur à 0';
            if ($seatLeft < 0)
                $errors['seat_left'] = 'Le champ doit être au moins supérieur à 0';
            if (empty($description))
                $errors['description'] = 'Ce champ est obligatoire';
            if (empty($alt))
                $errors['alt'] = 'Ce champ est obligatoire';

            /** Upload du fichier et gestion d'erreur */
            try {
                $picture = $this -> uploadFile('picture', 'incoming');
            } catch (DomainException $e) {
                $errors['picture'] = $e -> getMessage();
            }

            if (empty($errors)) {

                $picture = $this -> keepOrReplacePicture($picture, $oldPicture, 'incoming');

                if (!$id) {
                    $this -> incoming -> add($picture, $alt, $title, $startAt, $rower, $seatLeft , $description, $isActive);
                    $this ->addFlashBag("La prochaine traversée a bien été ajoutée", 'success');
                } else {
                    $this -> incoming -> update($id, $picture, $alt, $title, $startAt, $rower, $seatLeft , $description, $isActive);
                    $this -> addFlashBag("La prochaine traversée a bien été mise à jour", 'success');
                }
                header('Location:index.php?name=incoming');
            }
        }

        $this -> getLayout('incoming/incoming', 'Prochaine Traversée', $this->getFlashBag(), [
            'incoming'    => $incoming,
            'id'          => $incoming ? $incoming['id'] : false,
            'picture'     => $incoming ? $incoming['picture'] : '',
            'alt'         => $incoming ? $incoming['alt'] : '',
            'title'       => $incoming ? $incoming['title'] : '',
            'start_at'    => $incoming ? $incoming['start_at'] : '',
            'rower'       => $incoming ? $incoming['rower'] : 0,
            'seat_left'   => $incoming ? $incoming['seat_left'] : 0,
            'is_active'   => $incoming ? $incoming['is_active'] : true,
            'description' => $incoming ? $incoming['description'] : '',
            'errors'      => $errors ?? [],
            'oldPicture'  => $oldPicture ?? null,
        ]);
    }

}
