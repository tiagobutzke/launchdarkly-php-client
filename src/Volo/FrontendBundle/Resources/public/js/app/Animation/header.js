var VOLO = VOLO || {};
VOLO.HeaderAnimations = (function() {
    'use strict';

    function HeaderAnimations(options) {
        this.$window = options.$window;
        this.$document = options.$document;
        _.bindAll(this);
    }

    HeaderAnimations.prototype.init = function () {
        this.$document.off('page:load page:restore', this.registerEvents).on('page:load page:restore', this.registerEvents);
        this.$document.off('page:before-unload', this.unbind).on('page:before-unload', this.unbind);
        this.registerEvents();
    };

    HeaderAnimations.prototype.registerEvents = function() {
        if ($('.menu').length) {
            $('.header__logo__restaurant-name').text($('.hero-menu__info__headline').text());
            //only on menu page change logo to restaurant name
            this.$window.off('resize', this._changeLogoToRestaurantName).on('resize', this._changeLogoToRestaurantName);
            this.$window.off('scroll', this._changeLogoToRestaurantName).on('scroll', this._changeLogoToRestaurantName);
        }
        this.$window.off('resize', this._changeHeader).on('resize', this._changeHeader);
        this.$window.off('scroll', this._changeHeaderBackground).on('scroll', this._changeHeaderBackground);
        this._changeHeader();
        this._changeHeaderBackground();
        if ($('body').hasClass('home')) {
            $('.header').removeClass('header--mobile');
        }
    };

    HeaderAnimations.prototype.unbind = function() {
        this.$window.off('resize', this._changeLogoToRestaurantName);
        this.$window.off('scroll', this._changeLogoToRestaurantName);
        this.$window.off('resize', this._changeHeader);
        this.$window.off('scroll', this._changeHeaderBackground);
    };

    HeaderAnimations.prototype._changeHeaderBackground = function () {
        var $header = $('.header'),
            headerHasClassWhite;

        //add white header when scrolled down
        if (this.pageScrolledDownForHeaderChange()) {
            $header.addClass('header--white');
        } else {
            headerHasClassWhite = $header.hasClass('header--white');
            //remove white header but not when header is header-small
            if (headerHasClassWhite && !$header.hasClass('header-small')) {
                $header.removeClass('header--white');
            }
            //remove white header on homepage
            if (headerHasClassWhite && $('body').hasClass('home')) {
                $header.removeClass('header--white');
            }
        }
    };

    //change Header depending on Screen width and Scroll height
    HeaderAnimations.prototype._changeHeader = function() {
        var $header = $('.header'),
            bodyHasClassHome,
            windowWidth = this.$window.width(),
            $body = $('body');

        //change header to mobile when screen smaller that 800
        if (windowWidth <= VOLO.configuration.smallScreenMaxSize) {
            bodyHasClassHome = $body.hasClass('home');
            $header.addClass('header--white header--mobile header-small');
            //Don't show the white header on home page
            if (bodyHasClassHome && !this.pageScrolledDownForHeaderChange()) {
                $header.removeClass('header--white');
            }
            //on homepage remove header--mobile
            if (bodyHasClassHome) {
                $header.removeClass('header--mobile');
            }
            //change header to Desktop when screen bigger that 800
        } else if (windowWidth >= VOLO.configuration.smallScreenMaxSize && $header.hasClass('header--mobile')) {
            //on checkout remove only header mobile
            if ($body.hasClass('checkout') || $body.hasClass('error-page') || $body.hasClass('profile')) {
                $header.removeClass('header--mobile');
            } else {
                //if scrolled down don't remove the white header
                if (this.pageScrolledDownForHeaderChange()) {
                    $header.removeClass('header--mobile header-small');
                } else {
                    $header.removeClass('header--white header--mobile header-small');
                }
            }
        } else {
            //if screen is bigger than 800 remove header logo change
            $header.removeClass('header--logo-change');
        }
    };

    HeaderAnimations.prototype.pageScrolledDownForHeaderChange = function () {
        var distanceY = this.$window.get(0).pageYOffset || this.$document.get(0).documentElement.scrollTop,
            shrinkOn = 1;

        return distanceY > shrinkOn;
    };

    HeaderAnimations.prototype._changeLogoToRestaurantName = function() {
        var $heroMenuInfoheadline = $('.hero-menu__info__headline'),
            $header = $('.header'),
            positionOfHeadline = $heroMenuInfoheadline.offset().top + $heroMenuInfoheadline.height() - $header.height();

        //if the Restaurant name is under the header switch the logo to restaurant name
        if ((positionOfHeadline <= this.$document.scrollTop()) && (this.$window.width() <= VOLO.configuration.smallScreenMaxSize && $('.menu').length)) {
            $header.addClass('header--logo-change');
        } else {
            $header.removeClass('header--logo-change');
        }
    };

    return HeaderAnimations;
}());

