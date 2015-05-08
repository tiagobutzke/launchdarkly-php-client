$(document).on('ready page:load', function () {
    setTimeout(function() {
        if ($('body').hasClass('menu-page')) {
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
                    width: 2000, // max-width
                    src: 'data-src-2000',
                    mode: 'viewport'
                }],
                selector: '.b-lazy.hero-menu__img'
            });
        } else {
            new Blazy();
        }
    }, 100);


    $('.menu__item__add').on('click', function(e) {
        eventHandler.addProduct(cart, $(this));
    });

    var cart = VOLO.cart || new Cart(); //state-full object with info about order
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
