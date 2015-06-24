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
        //-16 is for the scrollbar width, this magic number can be solved with modernizr.mq
        this.noStickyBreakPoint = options.noStickyBreakPoint || 0;
        this.isActiveGetter = options.isActiveGetter || function() { return true; };
        this.stickingOnTopClass = 'stickingOnTop';

        this.targetHeight = null;
        this.windowScrollTop = null;

        // creating bound versions of this methods so they can be used as event handlers
        this.boundUpdateCoordinates = this.updateCoordinates.bind(this);
        this.boundRedraw = this.redraw.bind(this);
    }

    // reset state, recalculate the starting values and save a new target
    StickOnTop.prototype.init = function(newTarget) {
        if (!this.isActiveGetter()) {
            return;
        }
        this.domObjects.$target = newTarget;
        this.updateCoordinates();

        // rendering new position of sticking element on scroll, redoing the init on resize
        this.$window.off('resize', this.boundUpdateCoordinates).on('resize', this.boundUpdateCoordinates);
        this.$window.off('scroll', this.boundRedraw).on('scroll', this.boundRedraw);

        this.redraw();
    };

    // reset state, recalculate the starting values and save a new target
    StickOnTop.prototype.updateCoordinates = function() {
        if (!this.isActiveGetter()) {
            return;
        }
        this._removeSticking();
        this.stickOnTopValue = this.stickOnTopValueGetter();
        this.startingPoint = this.startingPointGetter();
        this.endpoint = this.endPointGetter();
        this.maxHeight = this.$window.outerHeight() - this.stickOnTopValue;
        this.redraw();
    };

    // rendering the position of the sticking element
    StickOnTop.prototype.redraw = function() {
        this.windowScrollTop = this.$window.scrollTop();

        // target after starting point
        if ((this.windowScrollTop + this.stickOnTopValue) > this.startingPoint) {
            // if not below not-sticking breakpoint start sticking
            if (this.$window.outerWidth() >= this.noStickyBreakPoint) {
                this._addSticking();

                if (this.domObjects.$target && this.domObjects.$target.length) {
                    this.targetHeight = this.domObjects.$target.outerHeight();
                    // target is before end point
                    if (this.endpoint > this.windowScrollTop + this.stickOnTopValue + this.targetHeight) {
                        // reset this.target position to normal if just exited the 'under the endpoint area'
                        if (this.domObjects.$target && this.domObjects.$target.length) {
                            this.domObjects.$target.css({
                                top: this.stickOnTopValue
                            });
                        }
                    // target is after end point, stopping scroll by scrolling in opposite direction
                    } else {
                        if (this.domObjects.$target && this.domObjects.$target.length) {
                            this.domObjects.$target.css({
                                top: -(this.targetHeight - (this.endpoint - this.windowScrollTop))
                            });
                        }
                    }
                }
            }

        // target is before starting point
        } else {
            this._removeSticking();
        }
    };

    // adding sticking behaviour
    StickOnTop.prototype._addSticking = function() {
        if (!this.isActiveGetter()) {
            return;
        }
        if (this.domObjects.$target && this.domObjects.$target.length &&
            !this.domObjects.$container.hasClass(this.stickingOnTopClass)) {
            this.domObjects.$container.addClass(this.stickingOnTopClass);
            this.domObjects.$target.css({
                position: 'fixed',
                top: this.stickOnTopValue,
                'max-height': this.maxHeight,
                overflow: 'auto',
                width: this.domObjects.$target.innerWidth()
            });
        this.domObjects.$target.scrollTop(0); // resetting the inner scrolling of the target
        }
    };

    // removing sticking behaviour
    StickOnTop.prototype._removeSticking = function() {
        if (this.domObjects.$target && this.domObjects.$target.length && this.domObjects.$container.hasClass(this.stickingOnTopClass)) {
            this.domObjects.$container.removeClass(this.stickingOnTopClass);
            this.domObjects.$target.css({
                position: '',
                top: '',
                'max-height': '',
                width: '',
                overflow: ''
            });
        }
    };

    // deleting everything containing a dom object and unbinding events to avoid memory leaks
    StickOnTop.prototype.remove = function() {
        this._removeSticking();
        this.$window.off('resize', this.boundUpdateCoordinates);
        this.$window.off('scroll', this.boundRedraw);
        this.domObjects = {};
        this.stickOnTopValueGetter = null;
        this.startingPointGetter = null;
        this.endPointGetter = null;
    };

    return StickOnTop;
}());
