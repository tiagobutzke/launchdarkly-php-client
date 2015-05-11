var EventHandler = function(cartManager, cartDataHandler) {
    cartDataHandler = cartDataHandler || new CartDataHandler();
    cartManager = cartManager || new CartManager();

    var addProduct = function(cart, domNode) {
        var product = $(domNode).data().object;

        product.quantity = 1;
        product.vendor_id = $(domNode).data().vendor_id;

        cartManager.addProduct(cart, product);

        _toggleContainerVisibility(cart);
        _setCartData(cart);
    };

    var _toggleContainerVisibility = function(cart) {
        var $productsContainer = $('.desktop-cart__products'),
            $cartMsg = $('.desktop-cart_order__message'),
            cartEmpty = cart.products.length === 0;

        $cartMsg.toggle(cartEmpty);
        $productsContainer.toggle(!cartEmpty);
    };

    var _setProductToContainer = function(product) {
        var $container = $('.desktop-cart__products'),
            $product = _getCartProductElement(product);

        $container.append($product);
    };

    var _getCartProductElement = function(product) {
        var $product = $('<div class="desktop-cart__product"></div>'),
            $quantity = $('<span class="desktop-cart__product__quantity"></span>').text(product.quantity + 'x'),
            $name = $('<span class="desktop-cart__product__name"></span>').text(product.name),
            $price = $('<span class="desktop-cart__product__price"></span>').html(product.total_price + '&euro;');

        $product.append($quantity);
        $product.append($name);
        $product.append($price);

        return $product;
    };

    var _setCartData = function(cart) {
        cartDataHandler.getCartData(cart).done(function(data) {
            $('.desktop-cart__order__subtotal__price').html(data.subtotal_after_discount + '&euro;');
            $('.desktop-cart__order__delivery span').html(data.delivery_fee + '&euro;');
            $('.desktop-cart__order__total__price').html(data.total_value + '&euro;');
            $('.desktop-cart__products').empty();

            data.vendorCart[0].products.map(function(product) {
                _setProductToContainer(product);
            });
        });
    };

    return {
        addProduct: addProduct
    };
};
