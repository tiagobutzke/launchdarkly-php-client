VOLO.IOSBannerView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.$body = options.$body;
    },

    events: {
        'click .ios-smart-banner__close-button': '_hideBanner'
    },

    render: function() {
        if (this._shouldRender()) {
            this.$body.addClass('show-ios-smart-banner');
        }

        return this;
    },

    _hideBanner: function() {
        Cookies.set(VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME, '1', { expires: 1 });
        this.$body.removeClass('show-ios-smart-banner');

        this.unbind();
    },

    _shouldRender: function() {
        return device.ios() && !Cookies.get(VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME) && this.$body.find('.ios-smart-banner').length;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});

VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME = 'hideIOSBanner';
