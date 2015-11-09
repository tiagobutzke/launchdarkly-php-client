describe('A cart', function () {
    'use strict';

    var cart, vendorId, dataProvider, cartResponse, response, product, vendorCart;

    beforeEach(function () {
        VOLO = {
            configuration: {
                timeZone: 'Europe/Berlin'
            }
        };

        vendorId = 4;
        cartResponse = {
            subtotal: 7.9,
            subtotal_before_discount: 7.9,
            subtotal_after_product_discount: 7.9,
            subtotal_after_discount: 7.9,
            subtotal_after_discount_and_delivery_fee: 9.4,
            subtotal_after_discount_and_service_fee: 7.9,
            subtotal_after_discount_and_delivery_fee_and_service_fee: 9.4,
            total_value: 9.4,
            group_joiner_total: 7.9,
            container_charge: 0,
            delivery_fee: 1.5,
            vat_total: 0,
            voucher_total: 0,
            discount_total: 0,
            delivery_fee_discount: 0,
            service_tax_total: 0,
            service_fee_total: 0,
            voucher: [],
            order_time: null,
            vendorCart: [
                {
                    subtotal: 7.9,
                    subtotal_before_discount: 7.9,
                    subtotal_after_product_discount: 7.9,
                    subtotal_after_discount: 7.9,
                    subtotal_after_discount_and_delivery_fee: 9.4,
                    subtotal_after_discount_and_service_fee: 7.9,
                    subtotal_after_discount_and_delivery_fee_and_service_fee: 9.4,
                    total_value: 9.4,
                    group_joiner_total: 7.9,
                    container_charge: 0,
                    delivery_fee: 1.5,
                    vat_total: 0,
                    voucher_total: 0,
                    discount_total: 0,
                    delivery_fee_discount: 0,
                    service_tax_total: 0,
                    service_fee_total: 0,
                    vendor_id: 4,
                    products: [],
                    minimum_order_amount: 10,
                    minimum_order_amount_difference: 2.1,
                    discount_text: null
                }
            ]
        };
        jasmine.clock().install();

        dataProvider = new CartDataProvider();
        cart = new CartModel({}, {
            dataProvider: dataProvider,
            defaultCartValues: {
                vendor_id: vendorId,
                products_count: 1
            }
        });

        spyOn(dataProvider, 'calculateCart').and.callFake(function (data) {
            response = _.cloneDeep(cartResponse);
            response.vendorCart[0].products = _.map(data.products, function (product) {
                return {
                    product_variation_id: product.product_variation_id,
                    name: 'Quick Chicken',
                    variation_name: '',
                    total_price_before_discount: 7.9,
                    total_price: 7.9,
                    quantity: product.quantity,
                    toppings: _.cloneDeep(product.toppings),
                    choices: _.cloneDeep(product.choices),
                    group_order_user_name: null,
                    group_order_user_code: null,
                    description: '',
                    special_instructions: ''
                };
            });

            return {
                done: function (callback) {
                    callback(_.cloneDeep(response));

                    return {
                        error: $.noop
                    };
                }.bind(this)
            }
        });

        product = {
            is_half_type_available: false,
            id: 854,
            name: 'Quick Chicken',
            code: null,
            description: 'Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter',
            file_path: null,
            half_type: null,
            special_instructions: '',
            product_variations: [{
                id: 859,
                code: null,
                name: null,
                price: 7.9,
                price_before_discount: null,
                container_price: 0,
                choices: [
                    {
                        id: 1,
                        name: 'choice 1'
                    }
                ],
                toppings: [
                    {
                        id: 1,
                        name: 'topping 1',
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        vendorCart = cart.getCart(vendorId);
    });

    afterEach(function () {
        jasmine.clock().uninstall();
    });

    it('adds a new product to the cart', function () {
        var selectedProduct = CartItemModel.createFromMenuItem(product);

        vendorCart.addItem(selectedProduct);

        jasmine.clock().tick(800);
        expect(vendorCart.products.length).toBe(1);

        var expected = _.cloneDeep(response.vendorCart[0].products[0]);
        expect(vendorCart.products.toJSON()).toContain(expected);
    });

    it('adds the same product with different quantity', function () {
        var firstSelectedProduct = CartItemModel.createFromMenuItem(product),
            secondProductToAdd = CartItemModel.createFromMenuItem(product),
            expectedFirst;

        secondProductToAdd.set('quantity', 5);
        vendorCart.addItem(firstSelectedProduct);

        jasmine.clock().tick(800);
        expect(vendorCart.products.length).toBe(1);
        expectedFirst = _.cloneDeep(response.vendorCart[0].products[0]);
        expect(vendorCart.products.toJSON()).toContain(expectedFirst);

        vendorCart.addItem(secondProductToAdd);

        jasmine.clock().tick(800);
        expect(vendorCart.products.length).toBe(1);
        expect(vendorCart.products.first().get('quantity')).toEqual(6);
        var expectedSecond = _.cloneDeep(response.vendorCart[0].products[0]);
        expect(vendorCart.products.first().toJSON()).toEqual(expectedSecond);
    });
    it('finds similar product in cart', function () {
        var addedProduct = CartItemModel.createFromMenuItem(product),
            clone = addedProduct.clone();

        vendorCart.addItem(addedProduct);
        clone.transformToppingsToServerFormat();

        expect(vendorCart.findSimilarProduct(clone).toJSON()).toEqual({
            product_variation_id: 859,
            name: 'Quick Chicken',
            variation_name: null,
            total_price_before_discount: null,
            total_price: 7.9,
            special_instructions: '',
            quantity: 1,
            toppings: [{
                id: 1,
                type: 'full',
                name: 'option 1',
                optionsVisible: false,
                quantity_minimum: null,
                quantity_maximum: null,
                selectedOptions: [],
                options: []
            }],
            choices: [{id: 1, name: 'choice 1'}],
            group_order_user_name: null,
            group_order_user_code: null,
            description: 'Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter'
        });
    });

    it('does not find same product with different topping', function () {
        var differentData = _.cloneDeep(product),
            differentProduct;

        differentData.product_variations[0].toppings = [{
            id: 12,
            name: 'topping 2',
            options: [{id: 2, selected: true, name: 'option 1'}]
        }];
        differentProduct = CartItemModel.createFromMenuItem(differentData);
        differentProduct.transformToppingsToServerFormat();

        vendorCart.addItem(CartItemModel.createFromMenuItem(product));
        expect(vendorCart.findSimilarProduct(differentProduct)).toEqual(undefined);
    });

    it('does not find same product without topping', function () {
        var differentData = _.cloneDeep(product),
            differentProduct;

        differentData.product_variations[0].toppings = [];
        differentProduct = CartItemModel.createFromMenuItem(differentData);

        vendorCart.addItem(CartItemModel.createFromMenuItem(product));
        expect(vendorCart.findSimilarProduct(differentProduct)).toEqual(undefined);
    });

    it('does not find same product with some additional topping', function () {
        var differentData = _.cloneDeep(product),
            differentProduct;

        differentData.product_variations[0].toppings.push(
            {
                id: 2,
                name: 'topping 2',
                options: [{id: 2, selected: true, name: 'option 2'}]
            }
        );
        differentProduct = CartItemModel.createFromMenuItem(differentData);
        differentProduct.transformToppingsToServerFormat();

        vendorCart.addItem(CartItemModel.createFromMenuItem(product));
        expect(vendorCart.findSimilarProduct(differentProduct)).toEqual(undefined);
    });

    it('does not find different product', function () {
        var productInCart = CartItemModel.createFromMenuItem(product);
        vendorCart.addItem(productInCart);

        var productForFind = CartItemModel.createFromMenuItem({
            is_half_type_available: false,
            id: 853,
            name: 'Quick Chicken',
            code: null,
            description: '',
            file_path: null,
            half_type: null,
            special_instructions: '',
            product_variations: [{
                id: 8545,
                code: null,
                name: null,
                price: 7.9,
                price_before_discount: null,
                container_price: 0,
                choices: [],
                toppings: []
            }]
        });
        expect(vendorCart.findSimilarProduct(productForFind)).toEqual(undefined);
    });

    it('should remove product from cart', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.length).toBe(1);

        vendorCart.removeItem(vendorCart.products.at(0));
        expect(vendorCart.products.length).toBe(0);
    });

    it('should increase quantity of product', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.at(0).get('quantity')).toBe(1);


        vendorCart.increaseQuantity(vendorCart.products.at(0), 2);
        expect(vendorCart.products.at(0).get('quantity')).toBe(3);

        vendorCart.increaseQuantity(vendorCart.products.at(0), -2);
        expect(vendorCart.products.at(0).get('quantity')).toBe(1);
    });

    it('should remove object with 0 or less quantity', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.length).toBe(1);

        vendorCart.increaseQuantity(vendorCart.products.at(0), -1);
        expect(vendorCart.products.length).toBe(0);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.length).toBe(1);

        vendorCart.increaseQuantity(vendorCart.products.at(0), -2);
        expect(vendorCart.products.length).toBe(0);
    });

    it('should update item toppings', function () {
        var updatedProduct = _.cloneDeep(product);
        updatedProduct.product_variations[0].toppings = [
            {
                id: 2,
                name: 'topping 2',
                options: [{id: 2, selected: true, name: 'option 2'}]
            }
        ];

        var cartItem = CartItemModel.createFromMenuItem(product);
        var updatedItem = CartItemModel.createFromMenuItem(updatedProduct);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.at(0).toppings.at(0).get('name')).toEqual('option 1');
        vendorCart.updateItem(vendorCart.products.at(0), updatedItem);
        expect(vendorCart.products.at(0).toppings.at(0).get('name')).toEqual('option 2');
    });

    it('should update item quantity', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);
        var updatedItem = CartItemModel.createFromMenuItem(product);
        updatedItem.set('quantity', 5);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.at(0).get('quantity')).toEqual(1);
        vendorCart.updateItem(vendorCart.products.at(0), updatedItem);
        expect(vendorCart.products.at(0).get('quantity')).toEqual(5);
    });

    it('should remove item, if there is another similar product in cart on update', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);

        var differentProduct = _.cloneDeep(product);
        differentProduct.product_variations[0].toppings = [
            {
                id: 10,
                name: 'topping 1',
                options: [{id: 2, selected: true, name: 'option 2'}]
            }
        ];
        var differentCartItem = CartItemModel.createFromMenuItem(differentProduct);

        vendorCart.addItem(cartItem);
        vendorCart.addItem(differentCartItem);

        vendorCart.updateItem(vendorCart.products.at(0), cartItem);
        expect(vendorCart.products.at(0).get('quantity')).toEqual(2);
        expect(vendorCart.products.length).toEqual(1);
    });

    it('should remove item, if it is updated with 0 quantity', function () {
        var cartItem = CartItemModel.createFromMenuItem(product);
        var updatedItem = CartItemModel.createFromMenuItem(product);
        updatedItem.set('quantity', 0);

        vendorCart.addItem(cartItem);
        expect(vendorCart.products.at(0).get('quantity')).toEqual(1);
        vendorCart.updateItem(vendorCart.products.at(0), updatedItem);
        expect(vendorCart.products.length).toBe(0);
    });

    it('should return correct quantity of products', function () {
        var differentProduct = _.cloneDeep(product);
        differentProduct.product_variations[0].id = 860;

        var cartItem = CartItemModel.createFromMenuItem(product);
        vendorCart.addItem(cartItem);

        expect(vendorCart.getProductsCount()).toBe(1);
        cartItem.set('quantity', 5);
        vendorCart.addItem(cartItem);
        expect(vendorCart.getProductsCount()).toBe(6);

        var differentCartItem = CartItemModel.createFromMenuItem(differentProduct);
        differentCartItem.set('quantity', 3);
        vendorCart.addItem(differentCartItem);
        expect(vendorCart.getProductsCount()).toBe(9);
    });
});
