var CartDataHandler = function() {
    var getCartData = function(cart) {
        var deferred = $.Deferred();

        cart.expedition_type = 'delivery';
        cart.location = {
            "location_type": "polygon",
            "latitude": 52.5237282,
            "longitude": 13.3908286
        };
        // For Dev
        //cart.location = {
        //    "location_type": "polygon",
        //    "latitude": 43.2591274,
        //    "longitude": 76.9339438
        //};

        var requestSettings = {
            url: Routing.generate('cart_calculate'),
            method: 'POST',
            data: JSON.stringify(cart)
        };
        $.ajax(requestSettings).done(function(res) {
            deferred.resolve(res);
        });

        return deferred;
    };

    return {
        getCartData: getCartData
    };
};
