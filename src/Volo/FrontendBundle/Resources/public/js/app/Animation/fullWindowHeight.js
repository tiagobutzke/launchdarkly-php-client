var VOLO = VOLO || {};
VOLO.FullWindowHeight = (function() {
    'use strict';

    function FullWindowHeight(options) {
        this.$window = options.$window;
        this.$document = options.$document;
        this.targetSelector = options.targetSelector;
        this.$target = null;
        this.boundOnResize = this._onResize.bind(this);
    }

    FullWindowHeight.prototype.init = function () {
        this.$document.on('page:load page:restore', this.getDomElements.bind(this));
        this.$document.on('page:before-unload', this.removeEvents.bind(this));
        this.getDomElements();
    };

    FullWindowHeight.prototype.getDomElements = function() {
        this.$target = $(this.targetSelector);
        if (this.$target.length) {
            this.$window.off('resize', this.boundOnResize).on('resize', this.boundOnResize);
            this._onResize();
        }
    };

    FullWindowHeight.prototype.removeEvents = function() {
        this.$window.off('resize', this.boundOnResize);
        this.$target = null;
    };

    FullWindowHeight.prototype._onResize = function () {
        this.$target.height(this.$window.height());
    };

    return FullWindowHeight;
}());
