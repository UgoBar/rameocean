<?php

require('lib/bdd.php');

require('models/Banner.php');
require('models/Contact.php');
require('models/Gallery.php');
require('models/Incoming.php');
require('models/Media.php');
require('models/Partner.php');
require('models/Profile.php');
require('models/Trip.php');

require('models/Voyage.php');
require('models/Rower.php');
require('models/Newsletter.php');
require('models/Coordinate.php');

$mediaRepo      = new Media();
$bannerRepo     = new Banner($mediaRepo);
$galleryRepo    = new Gallery($mediaRepo);
$incomingRepo   = new Incoming($mediaRepo);
$partnerRepo    = new Partner($mediaRepo);
$profileRepo    = new Profile($mediaRepo);
$tripRepo       = new Trip($mediaRepo);
$voyageRepo     = new Voyage();
$rowerRepo      = new Rower($mediaRepo);
$newsRepo       = new Newsletter();
$coordinateRepo = new Coordinate();

$banners     = $bannerRepo->findAll();
$gallery     = $galleryRepo->findAll();
$incoming    = $incomingRepo->findAll();
$partners    = $partnerRepo->findAll();
$profile     = $profileRepo->findAll();
$trips       = $tripRepo->findAll();
$voyage      = $voyageRepo->findAll();
$rowers      = $rowerRepo->findAll();
$newsletter  = $newsRepo->findAll();
$coordinates = $coordinateRepo->findAll();

$mainPartners          = $partnerRepo->findByCategory($partnerRepo::PARTNER_MAIN);
$officialPartners      = $partnerRepo->findByCategory($partnerRepo::PARTNER_OFFICIAL);
$institutionalPartners = $partnerRepo->findByCategory($partnerRepo::PARTNER_INSTITUTIONAL);
$technicPartners       = $partnerRepo->findByCategory($partnerRepo::PARTNER_TECHNIC);

$partnerCategories = $partnerRepo->getPartnerCategories();

$isIncoming = $incoming ? $incoming['is_active'] : false;
$isCurrentVoyage = $voyage ? $voyage['is_active'] : false;
setlocale(LC_TIME, "fr_FR", "French");

$errors = [];
$data = [];

$firstname = '';
$lastname  = '';
$phone     = '';
$email     = '';
$demand    = '';
$content   = '';

$createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));

if(isset($_POST['email'])) {

    $firstname = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
    $lastname  = (isset($_POST['lastname'])) ? trim($_POST['lastname']) : '';
    $phone     = (isset($_POST['phone'])) ? trim($_POST['phone']) : '';
    $email     = trim($_POST['email']);
    $demand    = (isset($_POST['demand'])) ? trim($_POST['demand'])  :'';
    $content   = (isset($_POST['content'])) ? trim($_POST['content'])  :'';

    if (!$firstname || strlen($firstname) < 3 )
        $errors['firstname'] = true;

    if (!$lastname || strlen($lastname) < 3)
        $errors['lastname'] = true;

    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
        $errors['email'] = true;

    if(!$phone)
        $errors['phone'] = true;

    if($demand === '1')
        $errors['demand'] = true;

    if(!$content)
        $errors['content'] = true;

    if(!$errors) {

        // No errors - send mail
        $mailTo  = 'pfavre92@icloud.com';

        $subject = $demand . ' - ' . $firstname . ' ' . $lastname . ' (' . $phone . ')';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: Patrick Favre <contact@rameocean.fr>'."\r\n";
        $headers .= 'Reply-To: '.$email."\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();

        mail($mailTo, $subject, $content, $headers);

        // Ajout dans la base de données
        $contactModel = new Contact;
        $contactModel->add($firstname, $lastname, $phone, $email, $demand, $content, $createdAt);

        header('Location:/rameocean/mailsent.html');
    }
}

require('front/index.phtml');
