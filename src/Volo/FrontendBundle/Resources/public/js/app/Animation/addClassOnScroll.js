var VOLO = VOLO || {};
VOLO.AddClassOnScroll = (function() {
    'use strict';

    function AddClassOnScroll(options) {
        this.$window = options.$window;
        this.$document = options.$document;
        this.targetSelector = options.targetSelector;
        this.startingPointGetter = options.startingPointGetter;
        this.isActiveGetter = options.isActiveGetter;
        this.className = options.className;
        this.$target = null;
        this.startingPoint = null;
        this.boundOnScroll = this._onScroll.bind(this);
        this.boundOnResize = this._onResize.bind(this);
    }

    AddClassOnScroll.prototype.init = function () {
        this.$document.on('page:load page:restore', this.getDomElements.bind(this));
        this.$document.on('page:before-unload', this.removeEvents.bind(this));
    };

    AddClassOnScroll.prototype.getDomElements = function() {
        if (this.isActiveGetter()) {
            this.$target = $(this.targetSelector);
            this.startingPoint = this.startingPointGetter();
            this.$window.off('scroll', this.boundOnScroll).on('scroll', this.boundOnScroll);
            this.$window.off('resize', this.boundOnResize).on('resize', this.boundOnResize);
        }
    };

    AddClassOnScroll.prototype.removeEvents = function() {
        this.$window.off('scroll', this.boundOnScroll);
        this.$window.off('resize', this.boundOnResize);
        this.$target = null;
    };

    AddClassOnScroll.prototype._onScroll = function () {
        this.$target.toggleClass(this.className, this.$window.scrollTop() > this.startingPoint);
    };

    AddClassOnScroll.prototype._onResize = function () {
        this.startingPoint = this.startingPointGetter();
        this._onScroll();
    };

    return AddClassOnScroll;
}());
