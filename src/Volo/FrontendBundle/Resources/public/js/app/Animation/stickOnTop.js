var StickOnTop  = (function() {
    'use strict';

    function StickOnTop(options) {
        // storing dom objects in one object so it's easy to clean after
        this.domObjects = {};
        this.domObjects.$container = options.$container;
        this.$window = $(window);

        this.domObjects.$target = null;
        this.stickOnTopValueGetter = options.stickOnTopValueGetter;
        this.startingPointGetter = options.startingPointGetter;
        this.endPointGetter = options.endPointGetter;
        this.stickingOnTopClass = 'stickingOnTop';

        this.isInitialized = false;
        this.isSticking = null;
        this.isAfterEndPoint = null;
        this.targetHeight = null;
        this.windowScrollTop = null;

        // creating bound versions of this methods so they can be used as event handlers
        this.boundUpdateCoordinates = this.updateCoordinates.bind(this);
        this.boundRedraw = this.redraw.bind(this);
    }

    // reset state, recalculate the starting values and save a new target
    StickOnTop.prototype.init = function(newTarget) {
        this.domObjects.$target = newTarget;
        this.updateCoordinates();

        if(!this.isInitialized) {
            this.isInitialized = true;
            // rendering new position of sticking element on scroll, redoing the init on resize
            this.$window.on('resize', this.boundUpdateCoordinates).on('scroll', this.boundRedraw);
        }

        this.redraw();
    };

    // reset state, recalculate the starting values and save a new target
    StickOnTop.prototype.updateCoordinates = function() {
        this.isSticking = false;
        this.isAfterEndPoint = false;
        this.stickOnTopValue = this.stickOnTopValueGetter();
        this.startingPoint = this.startingPointGetter();
        this.endpoint = this.endPointGetter();
        this.maxHeight = this.$window.outerHeight() - this.stickOnTopValue;
    };

    // rendering the position of the sticking element
    StickOnTop.prototype.redraw = function() {
        this.windowScrollTop = this.$window.scrollTop();

        // target after starting point
        if ((this.windowScrollTop + this.stickOnTopValue) > this.startingPoint) {
            // start sticking if not already
            if (!this.isSticking) {
                this.addSticking();
            }

            this.targetHeight = this.domObjects.$target.outerHeight();
            // target is before end point
            if (this.endpoint > this.windowScrollTop + this.stickOnTopValue + this.targetHeight) {
                // reset this.target position to normal if just exited the 'under the endpoint area'
                if (this.isAfterEndPoint) {
                    this.domObjects.$target.css({
                        top: this.stickOnTopValue
                    });
                    this.isAfterEndPoint = false;
                }

            // target is after end point, stopping scroll by scrolling in opposite direction
            } else {
                this.domObjects.$target.css({
                    top: -(this.targetHeight - (this.endpoint - this.windowScrollTop))
                });
                this.isAfterEndPoint = true;
            }

        // target is before starting point
        } else {
            if (this.isSticking) {
                this.removeSticking();
            }
        }
    };

    // adding sticking behaviour
    StickOnTop.prototype.addSticking = function() {
        this.isSticking = true;
        this.domObjects.$target.css({
            position: 'fixed',
            top: this.stickOnTopValue,
            'max-height': this.maxHeight,
            overflow: 'auto',
            width: this.domObjects.$target.width()
        });
        this.domObjects.$target.scrollTop(0); // resetting the inner scrolling of the target
        this.domObjects.$container.addClass(this.stickingOnTopClass);
    };

    // removing sticking behaviour
    StickOnTop.prototype.removeSticking = function() {
        this.isSticking = false;
        this.domObjects.$target.css({
            position: '',
            top: '',
            'max-height': '',
            width: '',
            overflow: ''
        });
        this.domObjects.$container.removeClass(this.stickingOnTopClass);
    };


    // deleting everything containing a dom object and unbinding events to avoid memory leaks
    StickOnTop.prototype.remove = function() {
        this.isInitialized = false;
        this.$window.off('resize', this.boundInit).off('scroll', this.boundRedraw);
        this.domObjects = {};
        this.stickOnTopValueGetter = null;
        this.startingPointGetter = null;
        this.endPointGetter = null;
    };

    return StickOnTop;
}());
