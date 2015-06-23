var VOLO = VOLO || {};
VOLO.RevealOnScroll = (function() {
    'use strict';

    function RevealOnScroll(options) {
        this.$window = options.$window;
        this.$document = options.$document;
        this.containerSelector = options.containerSelector;
        this.targetSelector = options.targetSelector;
        this.smoothness = options.smoothness;
        this.startAtViewPercentage = options.startAtViewPercentage;
        this.endAtViewPercentage = options.endAtViewPercentage;
        this.targetTransitionLength = options.targetTransitionLength;
        this.boundOnResize = this._onResize.bind(this);
        this.boundOnScroll = this._onScroll.bind(this);
        this.areEventsRegistered = false;
        this.lastScrollValue = null;
        this.viewportHeight = null;
        this.$target = null;
        this.$container = null;
        this.min = null;
        this.max = null;
    }

    RevealOnScroll.prototype.init = function () {
        this.$document.on('page:load page:restore', this.getDomElements.bind(this));
        this.$document.on('page:before-unload', this.removeEvents.bind(this));
    };

    RevealOnScroll.prototype.getDomElements = function() {
        this.$target = $(this.targetSelector);
        if (this.$target.length) {
            this.$target.css({ top: this.targetTransitionLength + '%' });
            this.$container = $(this.containerSelector);
            this._onResize();
            this._onScroll();
            if (!this.areEventsRegistered) {
                this.areEventsRegistered = true;
                this.$window.on('resize', this.boundOnResize);
                this.$window.on('scroll', this.boundOnScroll);
            }
        }
    };

    RevealOnScroll.prototype.removeEvents = function() {
        if (this.areEventsRegistered) {
            this.areEventsRegistered = false;
            this.$window.off('resize', this.boundOnResize);
            this.$window.off('scroll', this.boundOnScroll);
        }
    };

    RevealOnScroll.prototype._onResize = function () {
        var containerTop = this.$container.offset().top;

        this.viewportHeight = this.$window.height();
        this.min = containerTop - (this.viewportHeight * this.startAtViewPercentage);
        this.max = containerTop - (this.viewportHeight * this.endAtViewPercentage);
    };

    RevealOnScroll.prototype._onScroll = function () {
        var animationRange = this.max > this.min ? this.max - this.min : 1,
            currentScroll = this.$window.scrollTop(),
            scrollingDown = (this.lastScrollValue - currentScroll) < 0,
            proportion = (currentScroll - this.min) / animationRange,
            newTopPosition;

        if (scrollingDown) {
            if (proportion > 1) {
                this.$target.animate({ top: '0' }, this.smoothness);
            } else if (proportion > 0) {
                newTopPosition = (proportion * this.targetTransitionLength) - this.targetTransitionLength;
                this.$target.animate({ top: -newTopPosition + '%' }, this.smoothness);
            }
        } else {
            if (proportion < 0) {
                this.$target.animate({ top:  this.targetTransitionLength + '%' }, 0);
            } else if (proportion < 1) {
                newTopPosition = (proportion * this.targetTransitionLength) - this.targetTransitionLength;
                this.$target.animate({ top: -newTopPosition + '%' }, this.smoothness);
            }
        }

        this.lastScrollValue = currentScroll;
    };

    return RevealOnScroll;
}());
