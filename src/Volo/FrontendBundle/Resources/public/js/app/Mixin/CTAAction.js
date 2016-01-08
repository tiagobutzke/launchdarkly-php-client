var VOLO = VOLO || {};
VOLO.CTAActionMixin = {
    _ctaClicked: function(event) {
        var $target = $(event.currentTarget),
            gtmEvent = $target.data('gtm-cta');

        if (gtmEvent) {
            this.trigger('ctaTrackable:ctaClicked', {
                'event': 'homeCTAclick',
                'ctaName': gtmEvent
            });
        }
    }
};
