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

// DYNAMIC NEWSLETTER & ROWERS VARS
const newsletter  = document.querySelector('.newsletter');
const newsBtn     = document.querySelector('.left-side .btn');
const rowerInfo   = document.querySelector('.rower-info');
const rowerHeader = document.querySelector('.rower-header');
const avatars     = document.querySelectorAll('.avatar');

// --------------------------------------------------- MODAL FOR IMAGES
// all images inside the image modal content class
const lightboxImages = document.querySelectorAll('.backdrop-overlay');
const body = document.querySelector('body');
const modalPopup = document.querySelector('.image-modal-popup');

// closes modal on clicking anywhere and adds overflow back
const closeModal = () => {
    body.style.overflow = 'auto';
    modalPopup.style.display = 'none';
};
modalPopup.querySelector('span').addEventListener('click', () => {
    closeModal();
})
document.addEventListener('click', (event) => {
    if(event.target === modalPopup) {
        closeModal();
    }
});

// loops over each modal content img and adds click event functionality
lightboxImages.forEach(backdrop => {
    backdrop.addEventListener('click', e => {
        body.style.overflow = 'hidden';
        modalPopup.style.display = 'block';
        document.querySelector(`.image-modal-popup img`).src = backdrop.dataset.src;
    });
});
// --------------------------------------------------- END MODAL FOR IMAGES

let isRowerDisplayed = false;
// Display Rower and hide newsletter
const displayRower = (name, age, job, node) => {

    let description = node.dataset.description

    if(!isRowerDisplayed) {
        // NEWSLETTER CSS
        newsletter.classList.add('slide-left');
        newsletter.classList.remove('active');
        setTimeout(() => {
            newsletter.style.display = 'none';
        }, 500);
        newsBtn.classList.remove('active');

        // ROWER CONTENT
        setRowerInfo(node.dataset.image, name, age, job, description)

        // ROWER CSS
        rowerInfo.classList.remove('slide-right');
        rowerInfo.classList.add('active');
        // Avatar active
        node.classList.add('active');

        isRowerDisplayed = true;
    } else {

        avatars.forEach(avatar => {
            avatar.classList.remove('active')
        })
        node.classList.add('active');

        rowerInfo.classList.remove('active');
        rowerInfo.classList.add('slide-left');
        setTimeout(() => {
            setRowerInfo(node.dataset.image, name, age, job, description)
            rowerInfo.classList.remove('slide-left');
            rowerInfo.classList.add('active');
        }, 350);
    }
};

const displayNews = () => {

    isRowerDisplayed = false;

    // ROWER CSS
    rowerInfo.classList.remove('active');
    rowerInfo.classList.add('slide-right');
    avatars.forEach(avatar => {
        avatar.classList.remove('active')
    })

    // NEWSLETTER CSS
    newsletter.style.display = 'block';
    setTimeout(() => {
        newsletter.classList.add('active');
        newsletter.classList.remove('slide-left');
    }, 200);
    newsBtn.classList.add('active')
};

function setRowerInfo(img, name, age, job, description) {
    rowerHeader.querySelector('img').src = 'uploads/rower/' + img;
    rowerHeader.querySelector('.backdrop-overlay').dataset.src = 'uploads/rower/' + img;
    rowerHeader.querySelector('h2').innerHTML = name;
    rowerHeader.querySelector('.age').innerHTML = age;
    rowerHeader.querySelector('.job').innerHTML = job;
    rowerInfo.querySelector('.rower-description').innerHTML = description;
}

function displayPartners(partnerCategory, btn) {
    // Récupérer la liste active
    let activePartners = document.querySelector('.category-partner.active');
    let activeBtn = document.querySelector('.blue-btn.active')

    let selectedPartners =  document.querySelector('#' + partnerCategory);
    let selectedBtn = btn;

    if(activePartners !== selectedPartners) {
        // BTN
        activeBtn.classList.remove('active');
        btn.classList.add('active');
        // PARTNER WRAPPER

            activePartners.classList.add('slide-left')
            activePartners.classList.remove('active')

            selectedPartners.classList.add('active');
            selectedPartners.classList.remove('slide-left');

    }
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
