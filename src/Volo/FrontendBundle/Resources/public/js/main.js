$(document).on('ready page:load', function () {
    var bLazy = new Blazy();

    $('.menu__item__add').on('click', function(e) {
        eventHandler.addProduct(cart, $(this));
    });

    var cart = new Cart(); //state-full object with info about order
    var cartManager  = new CartManager(); //service for cart action
    var dataHandler  = new CartDataHandler();
    var eventHandler = new EventHandler(cartManager, dataHandler); //dom events handling
});

Turbolinks.pagesCached(0);

Turbolinks.enableProgressBar();
