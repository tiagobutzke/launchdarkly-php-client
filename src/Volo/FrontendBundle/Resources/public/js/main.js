var VOLO = VOLO || {};
$(document).on('ready page:load', function () {
    setTimeout(function() {
            new Blazy({
                breakpoints: [{
                    width: 300, // max-width
                    src: 'data-src-300',
                    mode: 'viewport'
                },{
                    width: 400, // max-width
                    src: 'data-src-400',
                    mode: 'viewport'
                }, {
                    width: 600, // max-width
                    src: 'data-src-600',
                    mode: 'viewport'
                }, {
                    width: 800, // max-width
                    src: 'data-src-800',
                    mode: 'viewport'
                }, {
                    width: 1000, // max-width
                    src: 'data-src-1000',
                    mode: 'viewport'
                }, {
                    width: 1200, // max-width
                    src: 'data-src-1200',
                    mode: 'viewport'
                },{
                    width: 1400, // max-width
                    src: 'data-src-1400',
                    mode: 'viewport'
                }, {
                    width: 99999999, // max-width
                    src: 'data-src-biggest',
                    mode: 'viewport'
                }]
            });
    }, 100);

    VOLO.cartModel = new CartModel({}, {
        dataProvider: new CartDataProvider()
    });

    VOLO.baseView = new BaseView(); //all your window events/other magic on DOM, belongs to here

    VOLO.menu = new MenuView({
        el: '.menu__main',
        cartModel: VOLO.cartModel
    });

    GeocodingService.attach($('#postal_index_form'));
    GeocodingHandlersHome.handle($('#postal_index_form'));

    VOLO.cartView = new CartView({
        el: '.desktop-cart',
        model: VOLO.cartModel,
        vendor_id: $('.menu__main').data().vendor_id
    });

    VOLO.cartView.render();
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
