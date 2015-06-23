$(document).on('page:load page:restore', function () {
    if ($('.menu-page').length) {
        $('.header__logo__restaurant-name').append($('.hero-menu__info__headline').text());
    }

    changeHeader();
    if ($('body').hasClass('home-page')) {
        $('.header').removeClass('header--mobile');
    }

    $(window).on('resize', function() {
        changeHeader();
        //only on menu page change logo to restaurant name
        if ($('.menu-page').length) {
            changeLogoToRestaurantName();
        }
    });

    $(window).on('scroll', function () {
        //add white header when scrolled down
        if (pageScrolledDownForHeaderChange()) {
            $('.header').addClass('header--white');
        } else {
            //remove white header but not when header is header-small
            if ($('.header').hasClass('header--white') && !$('.header').hasClass('header-small')) {
                $('.header').removeClass('header--white');
            }
            //remove white header on homepage
            if ($('.header').hasClass('header--white') && $('body').hasClass('home-page')) {
                $('.header').removeClass('header--white');
            }
        }
        //only on menu page change logo to restaurant name
        if ($('.menu-page').length) {
            changeLogoToRestaurantName();
        }
    });

});

function pageScrolledDownForHeaderChange () {
    var distanceY = window.pageYOffset || document.documentElement.scrollTop,
        shrinkOn = 1;
    if(distanceY > shrinkOn) {
        return true;
    } else {
        return false;
    }
}

//change Header depending on Screen width and Scroll height
function changeHeader() {
    //change header to mobile when screen smaller that 800
    if ($(window).width() <= 800) {
        $('.header').addClass('header--white header--mobile header-small');
        //Don't show the white header on home page
        if ($('body').hasClass('home-page') && !pageScrolledDownForHeaderChange()) {
            $('.header').removeClass('header--white');
        }
        //on homepage remove header--mobile
        if ($('body').hasClass('home-page')) {
            $('.header').removeClass('header--mobile');
        }
    //change header to Desktop when screen bigger that 800
    } else if ($(window).width() >= 800 && $('.header').hasClass('header--mobile')) {
        //on checkout remove only header mobile
        if ($('body').hasClass('checkout-page') || $('body').hasClass('general-error-page') || $('body').hasClass('profile-page')) {
            $('.header').removeClass('header--mobile');
        } else {
            //if scrolled down don't remove the white header
            if (pageScrolledDownForHeaderChange()) {
                $('.header').removeClass('header--mobile header-small');
            } else {
                $('.header').removeClass('header--white header--mobile header-small');
            }
        }
    } else {
        //if screen is bigger than 800 remove header logo change
        $('.header').removeClass('header--logo-change');
    }
}

function changeLogoToRestaurantName() {
    var positionOfHeadline = $('.hero-menu__info__headline').offset().top + $('.hero-menu__info__headline').height()  - $('.header').height();
    //if the Restaurant name is under the header switch the logo to resteraunt name
    if ((positionOfHeadline <= $(document).scrollTop()) && ($(window).width() <= 800 && $('.menu-page').length)) {
        $('.header').addClass('header--logo-change');
    } else {
        $('.header').removeClass('header--logo-change');
    }
}
