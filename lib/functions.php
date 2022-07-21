<?php

const VALIDE_EXTENSION_PICTURE = ['jpg','gif','png','webp', 'jpeg', 'PNG', 'JPG'];
const VALIDE_EXTENSION_FILE = ['pdf','ods','docx'];
const UPLOADS_DIR = 'C:/wamp64/www/rameocean/uploads/';
const UPLOADS_URL = 'http://localhost/rameocean/uploads/';
const URL = 'http://localhost/rameocean/admin/';

function verifyConnection(string $role = 'ROLE_USER')
{

    if (!$_SESSION || (isset($_SESSION['connected']) && !$_SESSION['connected'])) {
        header('Location:login.php');
    }

    if ($_SESSION['user']['role'] !== 'ROLE_ADMIN' && $_SESSION['user']['role'] !== $role) {
        addFlashBag("<h5>Désolé vous n'aviez <b>pas les droits</b> d'accès à la page précédente</h5>", 'dark');
        header('Location:index.php');
    }
}

function deconnexion()
{
    $_SESSION['connected'] = false;
    header('Location:login.php');
}

/**
 * @param string $inputName le nom du champ dans le formulaire
 * @param string $subDirectory le sous dossier ou sera placé le fichier dans le dossier UPLOADS
 * @param array $valideExtensions un tableau contenant les extensions de fichiers acceptés
 * @param string $uploadDirectory le dossier d'upload
 *
 * @return string|null une erreur ou null se pas d'erreur
 */
function uploadFile(string $inputName, string $imageFolder = '', array $valideExtensions = VALIDE_EXTENSION_PICTURE, string $uploadDirectory = UPLOADS_DIR): ?string
{
    if (array_key_exists($inputName, $_FILES) && !empty($_FILES[$inputName]['name'] != '')) {

        /** On vérifie qu'il n'y ai pas d'erreur d'upload */
        switch ($_FILES[$inputName]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new DomainException('Pas de fichier ou erreur sur le fichier');
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new DomainException('Fichier trop grand');
                break;
            default:
                throw new DomainException('Erreur inconnue lors du chargement du fichier !');
        }

        /** Si on a pas d'erreur d'upload on va déplacer l'image dans notre dossier uploads */


        $info = new SplFileInfo($_FILES[$inputName]['name']);
        $extension = $info->getExtension();

        if (in_array($extension, VALIDE_EXTENSION_PICTURE)) {

            $picture = bin2hex(random_bytes(2)) . basename($_FILES[$inputName]['name']);

            /** On déplace le fichier temporaire vers sa nouvelle destination */
            if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDirectory . $imageFolder . '/' . $picture))
                throw new DomainException ('Erreur de répertoire');

            return $picture;
        } else {
            throw new DomainException ('Ce type de fichier n\'est pas autorisé');
        }
    }
    return null;
}

/**
 * @param string $pictureName le nom de l'image a supprimer
 * @param string $subDirectory le sous dossier ou se trouve fichier dans le dossier d'UPLOADS
 */
function deleteFile(string $pictureName, string $subDirectory = '')
{
    $tmpFilePath = UPLOADS_DIR . (($subDirectory != '') ? $subDirectory . '/' : '') . $pictureName;

    /** Si le fichier existe sur le disque */
    if (file_exists($tmpFilePath))
        unlink($tmpFilePath);
}

/** function addFlashBag
 * Ajoute une valeur au flashbag
 * @param string $texte le message a afficher
 * @param string $level le niveau du message (correspond au type d'info bulle boostrap : success - warning - danger ...)
 * @return void
 */
function addFlashBag(string $texte, string $level = 'success')
{
    if (!isset($_SESSION['flashbag']) || !is_array($_SESSION['flashbag']))
        $_SESSION['flashbag'] = [];

    $_SESSION['flashbag'][] = ['message' => $texte, 'level' => $level];
}

/** function getFlashBag
 * Ajoute une valeur au flashbag
 * @param void
 * @return array flashbag le tableau contenant tous les messages a afficher
 */
function getFlashBag()
{
    if (isset($_SESSION['flashbag']) && is_array($_SESSION['flashbag'])) {
        $flashbag = $_SESSION['flashbag'];
        unset($_SESSION['flashbag']);
        return $flashbag;
    }
    return false;
}
