VOLO.IOSBannerView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);

        this.$body = options.$body;
    },

    events: {
        'click .ios-smart-banner__close-button': 'hide'
    },

    hide: function() {
        Cookies.set(VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME, '1', { expires: 1 });
        this.$body.removeClass('show-ios-banner');

        this.trigger('ios-banner:hide');
        this.unbind();
    },

    show: function() {
        this.$body.addClass('show-ios-banner');
        this.$body.addClass('show-banner');

        //resize called because sticky parts of site needs to be updated
        this.$body.resize();
    },

    shouldBeDisplayed: function() {
        return device.ios() && !Cookies.get(VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME) && this.$body.find('.ios-smart-banner').length;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});

VOLO.IOSBannerView.HIDE_IOS_BANNER_COOKIE_NAME = 'hideIOSBanner';
