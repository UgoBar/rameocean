// Gallery image hover
$( ".img-wrapper" ).hover(
    function() {
        $(this).find(".img-overlay").animate({opacity: 1}, 400);
    }, function() {
        $(this).find(".img-overlay").animate({opacity: 0}, 400);
    }
);

// Lightbox
let $overlay    = $('<div id="overlay"></div>');
let $image      = $("<img>");
let $title      = $('<h3 class="title-overlay">');
let $credit     = $('<div class="credit-overlay">Crédit : <span class="credit"></span></div>')
let $prevButton = $('<div id="prevButton"><i class="icon-arrow-left"></i></div>');
let $nextButton = $('<div id="nextButton"><i class="icon-arrow-right"></i></div>');
let $exitButton = $('<div id="exitButton"><i class="icon-cross"></i></div>');
let burgerMenu  = $('.burger');
let $images = $("#image-gallery .img-wrapper");

// Add overlay
$overlay.append($image).append($title).append($credit).prepend($prevButton).append($nextButton).append($exitButton);
$("#gallery").append($overlay);

// Hide overlay on default
$overlay.hide();

// When an image is clicked
$(".img-overlay").click(function(event) {
    // Prevents default behavior
    event.preventDefault();
    // Adds image source attribute to variable
    let imageLocation = $(this).prev().attr("src");
    // Add image title to overlay
    $title.text($(this).children('h3').data('title') ? $(this).children('h3').data('title') : '');
    // Add image credit to overlay
    let credit = $(this).children('h3').data('credit') ? $(this).children('h3').data('credit') : 'Rame Océan';
    $('.credit').text(credit);
    // Add the image src to $image
    $image.attr("src", imageLocation);
    // Fade in the overlay
    $overlay.fadeIn(400);
    burgerMenu.fadeOut(500)
    burgerMenu.hide();
});

// When the overlay is clicked
$overlay.click(function() {
    // Fade out the overlay
    $(this).fadeOut(400);
    burgerMenu.fadeIn(500)
    burgerMenu.show()
});

// When next button is clicked
$nextButton.click(function(event) {
    // Hide the current image
    $("#overlay img").hide();
    // Overlay image location
    let $currentImgSrc = $("#overlay img").attr("src");
    // Image with matching location of the overlay image
    let $currentImg = $('#image-gallery img[src="' + $currentImgSrc + '"]');
    // Finds the next image
    let $nextImg = $($currentImg.closest(".image").next().find("img"));

    // Finds next title
    let $nextTitle = $currentImg.closest(".image").next().find("h3").data('title');
    let nextTitle  = $nextTitle ? $nextTitle : ''; // Set empty if there is no title
    // Finds next credit
    let $nextCredit = $currentImg.closest(".image").next().find("h3").data('credit');
    let nextCredit = $nextCredit ? $nextCredit : 'Rame Océan';

    // If there is a next image
    if ($nextImg.length > 0) {
        // Fade in the next image
        $("#overlay img").attr("src", $nextImg.attr("src")).fadeIn(400);
        $("#overlay h3").text($nextTitle).fadeIn(400);
        $("#overlay .credit").text(nextCredit).fadeIn(400);
    } else {
        // Otherwise fade in the first image
        $("#overlay img").attr("src", $($images[0]).children('img').attr("src")).fadeIn(400);
        // Load the first title if it's defined, otherwise set empty
        let firstTitle = $($images[0]).children('figcaption').children('h3').data('title') ? $($images[0]).children('figcaption').children('h3').data('title') : '';
        $title.text(firstTitle);
        // Load the first credit if it's defined, otherwise set 'Rame Ocean'
        let firstCredit = $($images[0]).children('figcaption').children('h3').data('credit') ? $($images[0]).children('figcaption').children('h3').data('credit') : 'Rame Océan';
        $('.credit').text(firstCredit);
    }
    // Prevents overlay from being hidden
    event.stopPropagation();
});

// When previous button is clicked
$prevButton.click(function(event) {
    // Hide the current image
    $("#overlay img").hide();
    // Overlay image location
    let $currentImgSrc = $("#overlay img").attr("src");
    // Image with matching location of the overlay image
    let $currentImg = $('#image-gallery img[src="' + $currentImgSrc + '"]');
    // Finds the next image
    let $prevImg = $($currentImg.closest(".image").prev().find("img"));

    // Finds next title, set empty if it's not defined
    let $prevTitle = $currentImg.closest(".image").prev().find("h3").data('title');
    let prevTitle = $prevTitle ? $prevTitle : '';
    // Finds next credit, set 'Rame Océan' if it's not defined
    let $prevCredit = $currentImg.closest(".image").prev().find("h3").data('credit');
    let prevCredit = $prevCredit ? $prevCredit : 'Rame Océan';

    // Fade in the previous image
    if($prevImg.length > 0) {
        $("#overlay img").attr("src", $prevImg.attr("src")).fadeIn(400);
        $("#overlay h3").text($prevTitle)
        $("#overlay .credit").text(prevCredit)
    } else {
        console.log('LOAD LAST IMAGE', $images.length-1)
        // Otherwise fade in the last image
        $("#overlay img").attr("src", $($images[$images.length-1]).children('img').attr("src")).fadeIn(400);
        // Load the last title if it's defined, otherwise set empty
        let lastTitle = $($images[$images.length-1]).children('figcaption').children('h3').data('title') ? $($images[$images.length-1]).children('figcaption').children('h3').data('title') : '';
        $title.text(lastTitle);
        // Load the last credit if it's defined, otherwise set 'Rame Ocean'
        let lastCredit = $($images[$images.length-1]).children('figcaption').children('h3').data('credit') ? $($images[$images.length-1]).children('figcaption').children('h3').data('credit') : 'Rame Océan';
        $('.credit').text(lastCredit);
    }
    // Prevents overlay from being hidden
    event.stopPropagation();
});

// When the exit button is clicked
$exitButton.click(function() {
    // Fade out the overlay
    $("#overlay").fadeOut("slow");
    burgerMenu.fadeIn(500)
    burgerMenu.show()
});
