describe('A restaurants view', function () {
    var restaurantsView;
    beforeEach(function() {
        restaurantsView = new VOLO.RestaurantsView({});
    });

    it('should trigger restaurants loaded event in proper format', function(done) {
        var vendors = [new Backbone.Model({code: 'aaa'}), new Backbone.Model({code: 'bbb'})],
            vendorsCount = {
                allVendors: 2,
                openVendors: 1
            };

        restaurantsView.on('restaurants-view:gtm-restaurants-loaded', function(event) {
            expect(event.totalRestaurants).toBe(2);
            expect(event.openRestaurants).toBe(1);
            expect(event.allRestaurants).toEqual(['aaa', 'bbb']);
            done();
        });
        restaurantsView._triggerRestaurantsLoaded(vendors, vendorsCount);
    });

    it('should trigger restaurants-view:gtm-restaurants-loaded after GTM is created', function(done) {
        restaurantsView.on('restaurants-view:gtm-restaurants-loaded', function(event) {
            expect(true).toBe(true); //just fake assert, calling done is important in this test
            done();
        });
        restaurantsView.onGtmServiceCreated();
    });
});
