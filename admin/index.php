<?php

declare(strict_types=1);
session_start();

require('../config/bdd.php');

require('../models/Media.php');

require('controllers/DefaultController.php');
require('controllers/BannerController.php');
require('controllers/ProfileController.php');
require('controllers/IncomingController.php');
require('controllers/TripController.php');
require('controllers/PartnerController.php');
require('controllers/GalleryController.php');
require('controllers/UserController.php');

$defaultController  = new DefaultController();
$defaultController -> verifyConnection();

$bannerController   = new BannerController();
$profileController  = new ProfileController();
$incomingController = new IncomingController();
$tripController     = new TripController();
$partnerController  = new PartnerController();
$galleryController  = new GalleryController();
$userController     = new UserController();

if(isset($_GET['name'])) {

    switch($_GET['name']) {

        case 'logout':
            $userController -> disconnect();
            break;

// ---------- BANNER ---------- \\
        case 'banners':
            $bannerController -> getBanners();
            break;

        case 'addBanner':
            $bannerController -> addBanner();
            break;

// ---------- PROFILE ---------- \\
        case 'profile':
            $profileController -> getProfile();
            break;

// ---------- INCOMING ---------- \\
        case 'incoming':
            $incomingController -> getIncoming();
            break;

// ---------- TRIPS ---------- \\
        case 'trips':
            $tripController -> getTrips();
            break;

        case 'addTrip':
            $tripController -> addTrip();
            break;

// ---------- PARTNERS ---------- \\
        case 'partners':
            $partnerController -> getPartners();
            break;

        case 'addPartner':
            $partnerController -> addPartner();
            break;

// ---------- GALLERY ---------- \\
        case 'gallery':
            $galleryController -> getGallery();
            break;

        case 'addGallery':
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
