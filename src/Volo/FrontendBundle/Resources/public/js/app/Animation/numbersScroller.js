var VOLO = VOLO || {};
VOLO.NumberScroller = (function() {
    'use strict';

    function NumberScroller(options) {
        this.$window = options.$window;
        this.$document = options.$document;
        this.scrollersSelector = options.scrollersSelector;
        this._startingPointGetter = options.startingPointGetter;
        this.toNumber = options.toNumber;
        this.fromNumber = options.fromNumber;
        this.transition = options.transition;
        this.boundOnScroll = this._onScroll.bind(this);
        this.speedFactor = (110 - this.toNumber) / 100;
        this.areEventsRegistered = false;
        this.$scrollers = null;
        this.startingPoint = null;
    }

    NumberScroller.prototype.init = function() {
        this.getDomElements();
        this.$document.on('page:load page:restore', this.getDomElements.bind(this));
        this.$document.on('page:before-unload', this.removeEvents.bind(this));
    };

    NumberScroller.prototype.getDomElements = function() {
        this.$scrollers = $(this.scrollersSelector);
        if (this.$scrollers.length) {
            this.startingPoint = this._startingPointGetter();
            if (!this.areEventsRegistered) {
                this.areEventsRegistered = true;
                this.$window.on('scroll', this.boundOnScroll);
            }
        }
    };

    NumberScroller.prototype.removeEvents = function() {
        if (this.areEventsRegistered) {
            this.areEventsRegistered = false;
            this.$window.off('scroll', this.boundOnScroll);
        }
    };

    NumberScroller.prototype._onScroll = function() {
        var speedValue = 3 * this.speedFactor;

        if ((this.$window.scrollTop() + this.$window.height()) > this.startingPoint) {
            this.$scrollers.css({
                top: '-' + (this.fromNumber - this.toNumber) + '00%',
                '-webkit-transition': 'top ' + speedValue + 's ' + this.transition,
                transition: 'top ' + speedValue + 's ' + this.transition
            });

            this.$window.off('scroll', this.boundOnScroll);
        }
    };

    return NumberScroller;
}());
