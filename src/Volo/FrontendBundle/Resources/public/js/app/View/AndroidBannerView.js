VOLO.AndroidBannerView = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this);
        this.$body = options.$body;
    },

    events: {
        'click .android-smart-banner__close-button': 'hide'
    },

    hide: function() {
        Cookies.set(VOLO.AndroidBannerView.HIDE_ANDROID_BANNER_COOKIE_NAME, '1', { expires: 1 });
        this.$body.removeClass('show-android-banner');

        this.trigger('android-banner:hide');
        this.unbind();
    },

    show: function() {
        this.$body.addClass('show-android-banner');
        this.$body.addClass('show-banner');

        //resize called because sticky parts of site needs to be updated
        this.$body.resize();
    },

    shouldBeDisplayed: function() {
        return device.android() && !Cookies.get(VOLO.AndroidBannerView.HIDE_ANDROID_BANNER_COOKIE_NAME) && this.$body.find('.android-smart-banner').length;
    },

    unbind: function() {
        this.stopListening();
        this.undelegateEvents();
    }
});

VOLO.AndroidBannerView.HIDE_ANDROID_BANNER_COOKIE_NAME = 'hideIOSBanner';
