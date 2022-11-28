<?php

declare(strict_types=1);
session_start();

require('../lib/bdd.php');

require('../models/Media.php');

require('controllers/DefaultController.php');
require('controllers/BannerController.php');
require('controllers/ProfileController.php');
require('controllers/IncomingController.php');
require('controllers/TripController.php');
require('controllers/PartnerController.php');
require('controllers/GalleryController.php');
require('controllers/UserController.php');
require('controllers/VoyageController.php');
require('controllers/RowerController.php');
require('controllers/NewsletterController.php');

$defaultController  = new DefaultController();
$defaultController -> verifyConnection();
$userController        = new UserController();

if(isset($_GET['name'])) {

    switch($_GET['name']) {

        case 'logout':
            $userController -> disconnect();
            break;

// ---------- VOYAGE ---------- \\
        case 'voyage':
            $voyageController      = new VoyageController();
            $voyageController -> getVoyage();
            break;

// ---------- RAMEURS ---------- \\
        case 'rowers':
            $rowerController = new RowerController();
            $rowerController -> getRowers();
            break;
        case 'addRower':
            $rowerController = new RowerController();
            $rowerController -> addRower();
            break;

// ---------- RAMEURS ---------- \\
        case 'news':
            $newsletterController = new NewsletterController();
            $newsletterController -> getNews();
            break;
        case 'addNews':
            $newsletterController = new NewsletterController();
            $newsletterController -> addNews();
            break;

// ---------- BANNER ---------- \\
        case 'banners':
            $bannerController      = new BannerController();
            $bannerController -> getBanners();
            break;

        case 'addBanner':
            $bannerController      = new BannerController();
            $bannerController -> addBanner();
            break;

// ---------- PROFILE ---------- \\
        case 'profile':
            $profileController     = new ProfileController();
            $profileController -> getProfile();
            break;

// ---------- INCOMING ---------- \\
        case 'incoming':
            $incomingController    = new IncomingController();
            $incomingController -> getIncoming();
            break;

// ---------- TRIPS ---------- \\
        case 'trips':
            $tripController        = new TripController();
            $tripController -> getTrips();
            break;

        case 'addTrip':
            $tripController        = new TripController();
            $tripController -> addTrip();
            break;

// ---------- PARTNERS ---------- \\
        case 'partners':
            $partnerController     = new PartnerController();
            $partnerController -> getPartners();
            break;

        case 'addPartner':
            $partnerController     = new PartnerController();
            $partnerController -> addPartner();
            break;

// ---------- GALLERY ---------- \\
        case 'gallery':
            $galleryController = new GalleryController();
            $galleryController -> getGallery();
            break;

        case 'addGallery':
            $galleryController = new GalleryController();
            $galleryController -> addGallery();
            break;

// ---------- USERS ---------- \\
        case 'users':
            $userController -> getUsers();
            break;

        case 'addUser':
            $userController -> addUser();
            break;
    }

} else {
    $flashBag = $defaultController -> getFlashBag();
    $defaultController -> getLayout('index', 'Dashboard', $flashBag);
}
