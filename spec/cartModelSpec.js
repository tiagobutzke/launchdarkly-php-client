'use strict';

describe("A cart", function() {
    var cart;
    var vendor_id;
    var dataProvider;
    var cartResponse;
    var response;

    beforeEach(function() {
        vendor_id = 4;
        cartResponse = {
            "subtotal": 7.9,
            "subtotal_before_discount": 7.9,
            "subtotal_after_product_discount": 7.9,
            "subtotal_after_discount": 7.9,
            "subtotal_after_discount_and_delivery_fee": 9.4,
            "subtotal_after_discount_and_service_fee": 7.9,
            "subtotal_after_discount_and_delivery_fee_and_service_fee": 9.4,
            "total_value": 9.4,
            "group_joiner_total": 7.9,
            "container_charge": 0,
            "delivery_fee": 1.5,
            "vat_total": 0,
            "voucher_total": 0,
            "discount_total": 0,
            "delivery_fee_discount": 0,
            "service_tax_total": 0,
            "service_fee_total": 0,
            "vendorCart": [
                {
                    "subtotal": 7.9,
                    "subtotal_before_discount": 7.9,
                    "subtotal_after_product_discount": 7.9,
                    "subtotal_after_discount": 7.9,
                    "subtotal_after_discount_and_delivery_fee": 9.4,
                    "subtotal_after_discount_and_service_fee": 7.9,
                    "subtotal_after_discount_and_delivery_fee_and_service_fee": 9.4,
                    "total_value": 9.4,
                    "group_joiner_total": 7.9,
                    "container_charge": 0,
                    "delivery_fee": 1.5,
                    "vat_total": 0,
                    "voucher_total": 0,
                    "discount_total": 0,
                    "delivery_fee_discount": 0,
                    "service_tax_total": 0,
                    "service_fee_total": 0,
                    "vendor_id": 4,
                    "products": [
                    ],
                    "minimum_order_amount": 10,
                    "minimum_order_amount_difference": 2.1,
                    "discount_text": null
                }
            ],
            "voucher": []
        };

        dataProvider = new CartDataProvider();
        cart = new CartModel({}, {
            dataProvider: dataProvider
        });

        spyOn(dataProvider, "calculateCart").and.callFake(function (data) {
            response = _.cloneDeep(cartResponse);
            response.vendorCart[0].products = _.map(data.products, function(product) {
                return {
                    "product_variation_id": product.product_variation_id,
                    "name": "Quick Chicken",
                    "variation_name": "",
                    "total_price_before_discount": 7.9,
                    "total_price": 7.9,
                    "quantity": product.quantity,
                    "toppings": _.cloneDeep(product.toppings),
                    "choices": _.cloneDeep(product.choices),
                    "group_order_user_name": null,
                    "group_order_user_code": null
                };
            });

            return {
                done: function (callback) {
                    return callback(_.cloneDeep(response));
                }
            }
        });
    });

    it("adds a new product to the cart", function(done) {
        var object = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [],
                "toppings": []
            }]
        };
        var selectedProduct = new Backbone.Model(object);

        cart.getCart(vendor_id).addItem(selectedProduct.toJSON(), 3);

        setTimeout(function() {
            expect(cart.getCart(vendor_id).products.length).toBe(1);

            var expected = _.cloneDeep(response.vendorCart[0].products[0]);
            expect(cart.vendorCart.get(vendor_id).products.toJSON()).toContain(expected);
            done();
        }.bind(this), 800);
    });

    it("adds the same product with different quantity", function(done) {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: []
                    }
                ]
            }]
        };
        var expectedFirst;
        var firstSelectedProduct = new Backbone.Model(product);
        var secondProductToAdd = new Backbone.Model(product);
        secondProductToAdd.set('quantity', 5);

        cart.getCart(vendor_id).addItem(firstSelectedProduct.toJSON(), 3);


        setTimeout(function() {
            expect(cart.vendorCart.get(vendor_id).products.length).toBe(1);
            expectedFirst = _.cloneDeep(response.vendorCart[0].products[0]);
            expect(cart.vendorCart.get(vendor_id).products.toJSON()).toContain(expectedFirst);

            cart.getCart(vendor_id).addItem(secondProductToAdd.toJSON(), 5);

            setTimeout(function() {
                expect(cart.vendorCart.get(vendor_id).products.length).toBe(1);
                expect(cart.vendorCart.get(vendor_id).products.first().get('quantity')).toEqual(8);
                var expectedSecond = _.cloneDeep(response.vendorCart[0].products[0]);
                expect(cart.vendorCart.get(vendor_id).products.first().toJSON()).toEqual(expectedSecond);
                done();
            }.bind(this), 500)

        }.bind(this), 500);
    });

    it('finds similar product in cart', function() {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        cart.getCart(vendor_id).addItem(product, 3);
        expect(cart.vendorCart.get(vendor_id).findSimilarProduct(product).toJSON()).toEqual({
            product_variation_id: 859,
            quantity: 3,
            toppings: [{id: 1, type: 'full', name: 'option 1'}],
            choices: [{id: 1, name: 'choice 1'}],
            name: 'Quick Chicken',
            variation_name: '',
            total_price_before_discount: 0,
            total_price: 0,
            group_order_user_name: null,
            group_order_user_code: null
        });
    });

    it('does not find same product with different topping', function() {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        var differentProduct = _.cloneDeep(product);
        differentProduct.product_variations[0].toppings = [{
            "id": 12,
            "name": "topping 2",
            options: [{id: 1, selected: true, name: 'option 1'}]
        }];

        cart.getCart(vendor_id).addItem(product, 3);

        expect(cart.vendorCart.get(vendor_id).findSimilarProduct(differentProduct)).toEqual(undefined);
    });

    it('does not find same product without topping', function() {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        var differentProduct = _.cloneDeep(product);
        differentProduct.product_variations[0].toppings = [];

        cart.getCart(vendor_id).addItem(product, 3);

        expect(cart.vendorCart.get(vendor_id).findSimilarProduct(differentProduct)).toEqual(undefined);
    });

    it('does not find same product with some additionaltopping', function() {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    },
                    {
                        "id": 11,
                        "name": "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        var differentProduct = _.cloneDeep(product);
        differentProduct.product_variations[0].toppings = [
            {
                "id": 10,
                "name": "topping 1",
                options: [{id: 1, selected: true, name: 'option 1'}]
            }
        ];

        cart.getCart(vendor_id).addItem(product, 3);

        expect(cart.vendorCart.get(vendor_id).findSimilarProduct(differentProduct)).toEqual(undefined);
    });



    it('does not find different product', function() {
        var product = {
            "is_half_type_available": false,
            "id": 854,
            "name": "Quick Chicken",
            "code": null,
            "description": "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 859,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [
                    {
                        "id": 1,
                        "name": "choice 1"
                    }
                ],
                "toppings": [
                    {
                        "id": 10,
                        "name": "topping 1",
                        options: []
                    }
                ]
            }]
        };
        var productInCart = new Backbone.Model(product);
        cart.getCart(vendor_id).addItem(productInCart.toJSON(), 3);

        var productForFind = new Backbone.Model({
            "is_half_type_available": false,
            "id": 853,
            "name": "Quick Chicken",
            "code": null,
            "description": "",
            "file_path": null,
            "half_type": null,
            "product_variations": [{
                "id": 8545,
                "code": null,
                "name": null,
                "price": 7.9,
                "price_before_discount": null,
                "container_price": 0,
                "choices": [],
                "toppings": []
            }]
        });
        expect(cart.vendorCart.get(vendor_id).findSimilarProduct(productForFind.toJSON())).toEqual(undefined);
    });
});
