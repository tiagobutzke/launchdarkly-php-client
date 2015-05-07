var Cart = function() {
    return {
        expedition_type: '', // "delivery", "pickup"
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
        var quantity = selectedProduct.quantity,
            productToSearch = _.cloneDeep(selectedProduct);
        delete productToSearch.quantity;

        var foundProduct = _.chain(cart.products)
            .remove(_.matches(productToSearch))
            .first()
            .thru(function (currentProduct) {
                if (_.isUndefined(currentProduct)) {
                    currentProduct = {
                        vendor_id: selectedProduct.vendor_id,
                        variation_id: selectedProduct.product_variations[0].id,
                        quantity: 0,
                        groupOrderUserName: '',
                        toppings: [],
                        choices: []
                    };
                }

                currentProduct = _.assign(currentProduct, productToSearch);
                currentProduct.quantity = quantity + currentProduct.quantity;

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
        var clone = _.cloneDeep(selectedProduct);
        delete clone.quantity;

        return _.find(cart.products, clone);
    };

    return cartManager;
};
