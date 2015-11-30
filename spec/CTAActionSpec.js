describe('A CTAAction', function () {
    var ctaView,
        spy,
        event;
    beforeEach(function() {
        spy = jasmine.createSpy('spy');
        ctaView = _.extend({
            trigger: spy
        }, VOLO.CTAActionMixin);
    });

    it('should trigger evetn ctaTrackable:ctaClicked', function() {
        var ctaName = 'testing',
            target = $('<button></button>');

        target.attr('data-gtm-cta', ctaName);
        event = {
            currentTarget: target
        };

        ctaView._ctaClicked(event);
        expect(spy.calls.count()).toEqual(1);
    });
});
