'use strict';

/** HEADER **/
let headerHeight    = document.querySelector('#desktopHeader').offsetHeight,
    fixedHeader     = document.querySelector('.fixedHeader'),
    arrowUp         = document.querySelector('.arrow-up'),
    showMainHeader  = true,
    showFixedHeader = false;

// When refresh show the navbar if we're under the main header
if(document.documentElement.scrollTop > headerHeight) {

    fixedHeader.style.opacity = '1';
    fixedHeader.style.removeProperty('pointer-events');

    arrowUp.style.opacity = '1';
    arrowUp.style.removeProperty('pointer-events');

    showFixedHeader = true;
    showMainHeader = false;
}

// On scroll, detect the top of the window and show or not the Fixed Header
window.onscroll = () => {
    let scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;

    if(scrollPosition > headerHeight) {
        if(!showFixedHeader) {
            fadeIn(fixedHeader);
            fadeIn(arrowUp);
            showMainHeader = false;
        }
    } else {
        if(!showMainHeader) {
            fadeOut(fixedHeader);
            fadeOut(arrowUp);
            showFixedHeader = false;
        }
    }
};

const fadeIn = (elem) => {
    elem.classList.add('fast-fade-in');
    elem.classList.remove('fast-fade-out');
    elem.style.opacity = '1';
    elem.style.removeProperty('pointer-events');
}

const fadeOut = (elem) => {
    elem.classList.add('fast-fade-out');
    elem.classList.remove('fast-fade-in');
    elem.style.opacity = '0';
    elem.style.pointerEvents = 'none';
}
/** END HEADER **/

/** BURGER MENU **/
let menuWrapper = document.querySelector('.menu-wrapper'),
    menu = document.querySelector('#mobileHeader');

menuWrapper.addEventListener('click', function(e){
    menuWrapper.classList.toggle('open');
    menu.classList.toggle('openMenu');
});

document.querySelector('main').addEventListener('click', hideNavbar);
const navItem = document.querySelectorAll('#mobileHeader nav a');

navItem.forEach(elem => {
    elem.addEventListener('click', hideNavbar);
})

function hideNavbar() {
    menuWrapper.classList.remove('open');
    menu.classList.remove('openMenu')
}
/** END BURGER MENU **/

/** TIMELINE **/
let dots = $('.dot');
let paras = $('.description-flex-container').find('article');
dots.click(function(){
    let t = $(this),
        index = $(this).index(),
        matchedPara = paras.eq(index);

    if(matchedPara.find('div.media').length === 0) {
        console.log('pas de photo')
        matchedPara.find('div.text').css({'width':'100%'})
    }
    // t.add(matchedPara).addClass('active fade-in');
    matchedPara.addClass('active fade-in');
    t.addClass('active');
    dots.not(t).add(paras.not(matchedPara)).removeClass('active');
});
/** END TIMELINE **/

/*** FORM ***/

// Toast
function dismissToast(selector) {
    document.querySelector(selector).classList.remove('active');
}

// FORMULAIRE
const inputFirstname = document.querySelector("#firstname"),
    inputLastname = document.querySelector("#lastname"),
    inputPhone = document.querySelector("#phone"),
    inputEmail = document.querySelector("#email"),
    inputDemand = document.querySelector("#demand"),
    inputContent = document.querySelector("#content");

const formInputs = [ inputFirstname, inputLastname, inputPhone, inputEmail, inputDemand, inputContent ]

// On change
const inputs = document.querySelectorAll('input');
inputs.forEach(elem => {
    elem.addEventListener('change', (e) => e.target.classList.remove('input-error'));
})

function removeError (input) {
    input.classList.remove('input-error')
}

const checkValidity = (input) => {

    input.addEventListener('invalid', (e) => {
        e.preventDefault()
        if (!e.target.validity.valid) {
            e.target.parentElement.classList.add('error')
        }
    })

    input.addEventListener('input', (e) => {
        if (e.target.validity.valid) {
            e.target.parentElement.classList.remove('error')
        }
    })
}

// On submit
document.querySelector('form').addEventListener( 'submit', (event) => {

    formInputs.forEach(elem => {

        if(!elem.value || (elem === inputDemand && elem.value == 1)) {
            // add error
            event.preventDefault();
            elem.classList.add('input-error');
        }
    });
});
/*** END FORM ***/
