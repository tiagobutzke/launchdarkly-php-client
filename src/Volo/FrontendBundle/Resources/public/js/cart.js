var Cart = function() {
    return {
        expeditionType: '', // "delivery", "pickup"
        vouchers: [],
        products: [],
        location: undefined,
        orderTime: new Date(),
        paymentTypeId: 0,
        activeLanguage: 1,
        groupCode: '',
        groupOrderVersion: 0,
        orderComment: '',
        vendorPickupLocationId: 0,
        deliveryTimeMode: ''
    };
};

var CartManager = function() {
    var cartManager = {};

    /**
     * @param {Object} cart
     * @param {Object} selectedProduct
     *
     * @return {Object} The added product
     */
    cartManager.addProduct = function(cart, selectedProduct) {
        var quantity = selectedProduct.quantity;
        var productToSearch = _.cloneDeep(selectedProduct);
        delete productToSearch.quantity;

        var foundProduct = _.chain(cart.products)
            .remove(_.matches(productToSearch))
            .first()
            .thru(function (currentProduct) {
                if (_.isUndefined(currentProduct)) {
                    currentProduct = {
                        variation_id: null,
                        vendor_id: null,
                        quantity: 0,
                        groupOrderUserName: '',
                        toppings: [],
                        choices: []
                    };
                }

                currentProduct = _.assign(currentProduct, productToSearch);
                currentProduct.quantity = quantity;

                return currentProduct;
            })
            .value();

        if (quantity > 0) {
            cart.products.push(foundProduct);
        }
    };

    /**
     * @param {Object} cart
     * @param {Object} selectedProduct
     *
     * @return {Object} The added product
     */
    cartManager.getProduct = function(cart, selectedProduct) {
        return _.find(cart.products, selectedProduct);
    };

    return cartManager;
};
