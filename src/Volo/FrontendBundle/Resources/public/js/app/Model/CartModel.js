var CartItemModel = Backbone.Model.extend({
    defaults: {
        product_variation_id: null,
        name: "",
        variation_name: "",
        total_price_before_discount: 0,
        total_price: 0,
        quantity: 0,
        toppings: [],
        choices: [],
        group_order_user_name: null,
        group_order_user_code: null,
        description: ""
    },

    initialize: function() {
        this.toppings = new ToppingCollection(_.cloneDeep(this.get('toppings')));
    },

    isValid: function() {
        var notValidTopping = this.toppings.find(function(topping) {
            return !topping.isValid();
        });

        return !notValidTopping;
    },

    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.call(this);

        json.toppings = this.toppings.toJSON();

        return json;
    }
});

CartItemModel.createFromMenuItem = function(cartItemJSON) {
    var productVariation = cartItemJSON.product_variations[0];

    return new CartItemModel({
        product_variation_id: productVariation.id,
        name: cartItemJSON.name,
        variation_name: productVariation.name,
        total_price_before_discount: productVariation.price_before_discount,
        total_price: productVariation.price,
        quantity: 1,
        toppings: productVariation.toppings,
        choices: productVariation.choices,
        group_order_user_name: null,
        group_order_user_code: null,
        description: cartItemJSON.description
    });
};

var CartProductCollection = Backbone.Collection.extend({
    model: CartItemModel
});

var VendorCartModel = Backbone.Model.extend({
    defaults: {
        "subtotal": 0,
        "subtotal_before_discount": 0,
        "subtotal_after_product_discount": 0,
        "subtotal_after_discount": 0,
        "subtotal_after_discount_and_delivery_fee": 0,
        "subtotal_after_discount_and_service_fee": 0,
        "subtotal_after_discount_and_delivery_fee_and_service_fee": 0,
        "total_value": 0,
        "group_joiner_total": 0,
        "container_charge": 0,
        "delivery_fee": 0,
        "vat_total": 0,
        "voucher_total": 0,
        "discount_total": 0,
        "delivery_fee_discount": 0,
        "service_tax_total": 0,
        "service_fee_total": 0,
        "vendor_id": null,
        "minimum_order_amount": 0,
        "minimum_order_amount_difference": 0,
        "discount_text": null,
        "orderTime": null,
        "voucher": null,
        location: {
            "location_type": "polygon",
            "latitude": null,
            "longitude": null
        }
    },
    idAttribute: 'vendor_id',

    initialize: function() {
        _.bindAll(this);
        this.products = new CartProductCollection();
        this._xhr = null;
        this.timeoutReference = null;
    },

    getProductsCount: function() {
        var count = 0;
        this.products.each(function(product) {
            count += product.get('quantity');
        });

        return parseInt(count, 10);
    },

    validate: function(attrs) {
        if (attrs.location.latitude === null || attrs.location.longitude === null) {
            return 'location_not_set';
        }
    },

    updateLocationIfDeliverable: function (data) {
        var xhr = $.get(
            Routing.generate('vendor_delivery_validation_by_gps', {
                vendorId: this.get('vendor_id'),
                latitude: data.lat,
                longitude: data.lng
            })
        );

        xhr.done(function (response) {
            if (response.result) {
                this.set('location', {
                    location_type: "polygon",
                    latitude: data.lat,
                    longitude: data.lng
                });
            }
        }.bind(this));

        return xhr;
    },

    updateCart: function() {
        console.log('CartModel.updateCart ', this.cid);
        this.trigger('cart:dirty');

        if (this._xhr) {
            this._xhr.abort();
        }

        if (this.timeoutReference) {
            clearTimeout(this.timeoutReference);
        }
        this.timeoutReference = setTimeout(this._sendRequest, 500);
    },

    _sendRequest: function() {
        var data = {
            products: this.products.toJSON(),
            vendor_id: this.id,
            location: this.get('location')
        };

        data.orderTime = _.isDate(this.get('orderTime')) ? this.get('orderTime').toISOString() : new Date().toISOString();
        data.voucher = _.isString(this.get('voucher')) ? [this.get('voucher')] : [];

        this._xhr = this.collection.cart.dataProvider.calculateCart(data).done(function(calculatedData) {
            this.collection.cart.parse(calculatedData);
            this.trigger('cart:ready');
            console.log('cart:ready fired');
            this._xhr = null;
            this.timeoutReference = null;
        }.bind(this)).error(function(jqXHR) {
            if (jqXHR.statusText !== 'abort') {
                this.trigger('cart:error', jqXHR.responseJSON);
                console.log('cart:error fired');
                this.trigger('cart:ready');
            }
        }.bind(this));
    },

    addItem: function(newProduct, quantity) {
        var clone = _.cloneDeep(newProduct);

        clone.toppings = new ToppingCollection(this.getSelectedToppingsFromProduct(clone)).toJSON();

        var foundProduct = this.findSimilarProduct(clone);
        if (_.isObject(foundProduct)) {
            foundProduct.set('quantity', parseInt(foundProduct.get('quantity') + quantity), 10);
        }

        if (_.isUndefined(foundProduct)) {
            clone.quantity = quantity;

            this.products.unshift(clone);
        }

        this.updateCart();
    },

    updateItem: function(cartItemToUpdate, newSelection) {
        if (newSelection.quantity === 0) {
            this.removeItem(cartItemToUpdate);
        } else {
            var newToppings = this.getSelectedToppingsFromProduct(newSelection),
                clone = cartItemToUpdate.toJSON(),
                productFromCart;

            clone.toppings = new ToppingCollection(newToppings).toJSON();
            productFromCart = this.findSimilarProduct(clone);

            if (productFromCart && productFromCart.cid !== cartItemToUpdate.cid) {
                this.removeItem(cartItemToUpdate);
                productFromCart.set('quantity', productFromCart.get('quantity') + newSelection.quantity);
            } else {
                cartItemToUpdate.toppings.set(newToppings);
                cartItemToUpdate.set('quantity', newSelection.quantity);
            }
        }

        this.updateCart();
    },

    removeItem: function(productToRemove) {
        this.products.remove(productToRemove);
        this.updateCart();
    },

    increaseQuantity: function(product, quantityDifference) {
        var oldQuantity = product.get('quantity'),
            newQuantity = parseInt(oldQuantity + quantityDifference, 10);

        if (newQuantity <= 0) {
            this.products.remove(product);
        } else {
            product.set('quantity', newQuantity);
        }

        this.updateCart();
    },

    getSelectedToppingsFromProduct: function(product) {
        return _.chain(product.toppings)
            .map(function (item) {
                return item.options || [];
            })
            .reduce(function (memo, item) {
                return memo.concat(item);
            }, [])
            .where({selected: true})
            .map(function(option) {
                return {
                    id: option.id,
                    type: 'full',
                    name: option.name
                };
            })
            .value();
    },

    findSimilarProduct: function(productToSearch) {
        var compareArrays = function(arr1, arr2) {
            return _.isMatch(arr1, arr2) && _.isMatch(arr2, arr1);
        };

        return this.products.find(function(product) {
            product = product.toJSON();

            var sameVariation = product.product_variation_id === productToSearch.product_variation_id,
                sameToppings = compareArrays(product.toppings, productToSearch.toppings),
                sameChoices = compareArrays(product.choices, productToSearch.choices);

            return sameVariation && sameChoices && sameToppings;
        });
    }
});

var VendorCartCollection = Backbone.Collection.extend({
    model: VendorCartModel,
    initialize: function(models, options) {
        this.cart = options.cart;
    }
});

var CartModel = Backbone.Model.extend({
    defaults: {
        "subtotal": 0,
        "subtotal_before_discount": 0,
        "subtotal_after_product_discount": 0,
        "subtotal_after_discount": 0,
        "subtotal_after_discount_and_delivery_fee": 0,
        "subtotal_after_discount_and_service_fee": 0,
        "subtotal_after_discount_and_delivery_fee_and_service_fee": 0,
        "total_value": 0,
        "group_joiner_total": 0,
        "container_charge": 0,
        "delivery_fee": 0,
        "vat_total": 0,
        "voucher_total": 0,
        "discount_total": 0,
        "delivery_fee_discount": 0,
        "service_tax_total": 0,
        "service_fee_total": 0,
        "voucher": []
    },

    initialize: function(data, options) {
        this.dataProvider = options.dataProvider;
        this.vendorCarts = this.vendorCarts || new VendorCartCollection([], {
            cart: this
        });
    },

    parse: function (cart) {
        if (_.isUndefined(this.vendorCarts)) {
            this.vendorCarts = new VendorCartCollection([], {
                cart: this
            });
        }
        if (_.isObject(cart) && _.isArray(cart.vendorCart)) {
            cart = _.cloneDeep(cart);
            var vendorCarts = cart.vendorCart;
            delete cart.vendorCart;
            this.set(cart);

            _.each(vendorCarts, function (vendorCart) {
                vendorCart.products = vendorCart.products.map(function(product) {
                    product.toppings = product.toppings.map(function(topping) {
                        return {
                            id: topping.id,
                            type: 'full',
                            name: topping.name
                        };
                    });

                    return product;
                });

                this.getCart(vendorCart.vendor_id).products.set(vendorCart.products);
                delete vendorCart.products;
                this.getCart(vendorCart.vendor_id).set(vendorCart);
                this.getCart(vendorCart.vendor_id).set(
                    'voucher',
                    cart.voucher.length > 0 ? cart.voucher[0].code : null
                );
                this.getCart(vendorCart.vendor_id).set('orderTime', new Date(cart.orderTime));
            }, this);

            //clear all carts
            if (vendorCarts.length === 0) {
                this.vendorCarts.each(function(vendorCart) {
                    vendorCart.set(cart);
                    vendorCart.products.reset();
                });
            }
        }
    },

    getCart: function(vendorId) {
        if (_.isUndefined(this.vendorCarts.get(vendorId))) {
            this.vendorCarts.add({vendor_id: vendorId}, {cart: this});
        }

        return this.vendorCarts.get(vendorId);
    },

    emptyCart: function(vendorId) {
        this.vendorCarts.remove(vendorId);
    }
});
