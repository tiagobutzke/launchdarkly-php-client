var VOLO = VOLO || {};
VOLO.ToggleClassOnHover = (function() {
    'use strict';

    function ToggleClassOnHover(options) {
        this.containerSelector = options.containerSelector;
        this.targetSelector = options.targetSelector;
        this.$document = options.$document;
        this.hoverClassName = options.hoverClassName;
        this.boundAddHoverClass = this._addHoverClass.bind(this);
        this.boundRemoveHoverClass = this._removeHoverClass.bind(this);
        this.areEventsRegistered = false;
        this.$container = null;
        this.$target = null;
    }


    ToggleClassOnHover.prototype.init = function() {
        this.getDomElements();
        this.$document.on('page:load page:restore', this.getDomElements.bind(this));
        this.$document.on('page:before-unload', this.removeEvents.bind(this));
    };

    ToggleClassOnHover.prototype.getDomElements = function() {
        this.$container = $(this.containerSelector);
        if (this.$container.length) {
            this.$target = this.$container.find(this.targetSelector);
            if (!this.areEventsRegistered) {
                this.areEventsRegistered = true;
                this.$container.on('mouseenter', this.boundAddHoverClass).on('mouseleave', this.boundRemoveHoverClass);
            }
        }
    };

    ToggleClassOnHover.prototype.removeEvents = function() {
        if (this.areEventsRegistered) {
            this.areEventsRegistered = false;
            this.$container.off('mouseenter', this.boundAddHoverClass).off('mouseleave', this.boundRemoveHoverClass);
        }
    };

    ToggleClassOnHover.prototype._addHoverClass = function() {
        this.$target.addClass(this.hoverClassName);
    };

    ToggleClassOnHover.prototype._removeHoverClass = function() {
        this.$target.removeClass(this.hoverClassName);
    };

    return ToggleClassOnHover;
}());
