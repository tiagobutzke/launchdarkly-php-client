$(document).on('ready page:load', function () {
    setTimeout(function() {
        new Blazy();
    }, 100);

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

window.addEventListener('scroll', function(){
    var distanceY = window.pageYOffset || document.documentElement.scrollTop,
        shrinkOn = 1,
        header = $(".header");
    if (distanceY > shrinkOn) {
        header.addClass("header--white");
    } else {
        if (header.hasClass("header--white") && !header.hasClass("header-small")) {
            header.removeClass("header--white");
        }
    }
});
