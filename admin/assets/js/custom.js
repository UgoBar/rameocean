$(function() {
    "use strict";

    $(".preloader").fadeOut();
    // this is for close icon when navigation open in mobile view
    $(".nav-toggler").on('click', function() {
        $("#main-wrapper").toggleClass("show-sidebar");
        $(".nav-toggler i").toggleClass("ti-menu");
    });
    $(".search-box a, .search-box .app-search .srh-btn").on('click', function() {
        $(".app-search").toggle(200);
        $(".app-search input").focus();
    });

    // ==============================================================
    // Resize all elements
    // ==============================================================
    $("body, .page-wrapper").trigger("resize");
    $(".page-wrapper").delay(20).show();

    //****************************
    /* This is for the mini-sidebar if width is less then 1170*/
    //****************************
    var setsidebartype = function() {
        var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
        if (width < 1170) {
            $("#main-wrapper").attr("data-sidebartype", "mini-sidebar");
        } else {
            $("#main-wrapper").attr("data-sidebartype", "full");
        }
    };
    $(window).ready(setsidebartype);
    $(window).on("resize", setsidebartype);

});


// RANGE INPUT
if(document.querySelector('#rower') !== null) {
    document.querySelector('#rower').innerHTML = document.querySelector('input[name="rower"]').value;
    document.querySelector('#seat_left').innerHTML = document.querySelector('input[name="seat_left"]').value;
}
if(document.querySelector('#addPartnerForm #position') !== null) {
    document.querySelector('#position').innerHTML = document.querySelector('input[name="position"]').value;
}
const rangeValue = (elem, idName) => {
    let newValue = elem.value;
    let target = document.querySelector(idName);
    target.innerHTML = newValue;
}

// elem.addEventListener("input", rangeValue);

function displayTextarea() {
    description.classList.toggle('hidden');
}



