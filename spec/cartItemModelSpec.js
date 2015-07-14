describe("A cart item", function () {
    'use strict';

    var cartItem, cartItemData, topping, cartItemCopy;

    beforeEach(function () {
        cartItemData = {
            is_half_type_available: false,
            id: 854,
            name: "Quick Chicken",
            code: null,
            description: "Saftig-zartes Hühnerbrustfilet in unserer würzigen Kräuter-Marinade mit mediterranem Nudelsalat und Salsa Rossa Piccante Dip oder Kräuterbutter",
            file_path: null,
            half_type: null,
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
                        name: "choice 1"
                    }
                ],
                toppings: [
                    {
                        id: 1,
                        name: "topping 1",
                        options: [{id: 1, selected: true, name: 'option 1'}]
                    }
                ]
            }]
        };

        cartItem = CartItemModel.createFromMenuItem(cartItemData);
        cartItemCopy = CartItemModel.createFromMenuItem(_.cloneDeep(cartItemData));
        topping = cartItem.toppings.at(0);
    });

    it('should set toppings on object during initialize', function() {
        expect(cartItem.toppings.length).toEqual(1);
        expect(cartItem.toppings.at(0).get('name')).toEqual('topping 1');
    });

    it('should not be valid, if toppings are invalid', function() {
        spyOn(topping, 'isValid').and.callFake(function() {
            return false;
        });

        expect(cartItem.isValid()).toEqual(false);
    });

    it('should be valid, if toppings are valid', function() {
        spyOn(topping, 'isValid').and.callFake(function() {
            return true;
        });

        expect(cartItem.isValid()).toEqual(true);
    });

    it('should serialize toppings', function() {
        expect(cartItem.toJSON().toppings).toEqual(
            [{
                id: 1,
                name: 'topping 1',
                optionsVisible: false,
                quantity_minimum: null,
                quantity_maximum: null,
                options: [{id: 1, selected: true, name: 'option 1'}],
                selectedOptions: [{id: 1, selected: true, name: 'option 1'}]
            }]
        );
    });

    it('should return selected toppings in special format', function() {
        expect(cartItem.getSelectedToppings()).toEqual([
            {id: 1, type: 'full', name: 'option 1'}
        ]);
    });

    it('should return empty array if topping is not selected', function() {
        topping.options.at(0).setSelection(false);
        expect(cartItem.getSelectedToppings()).toEqual([]);
    });

    it('should be similar, if cart item has same toppings and variation', function() {
        expect(cartItem.isSimilar(cartItemCopy)).toEqual(true);
    });

    it('should not be similar, if toppings are different', function() {
        cartItemCopy.toppings = new ToppingCollection();
        expect(cartItem.isSimilar(cartItemCopy)).toEqual(false);
    });

    it('should not be similar, if product variation is different', function() {
        cartItemCopy.set('product_variation_id', 1);
        expect(cartItem.isSimilar(cartItemCopy)).toEqual(false);
    });

    it('should transform toppings to server correctly', function() {
        cartItem.transformToppingsToServerFormat();

        expect(cartItem.toppings.toJSON()).toEqual([
            {
                "id": 1,
                "type": "full",
                "name": "option 1",
                "optionsVisible": false,
                "quantity_minimum": null,
                "quantity_maximum": null,
                "options": [],
                "selectedOptions": []
            }
        ]);
    });

    it('should transform toppings to menu format correctly', function() {
        cartItem.transformToppingsToServerFormat();

        cartItem.transformToppingsToMenuFormat([
            {
                id: 1,
                name: "topping 1",
                options: [{id: 1, name: 'option 1'}]
            }
        ]);

        expect(cartItem.toppings.toJSON()).toEqual([{
            id: 1,
            name: 'topping 1',
            optionsVisible: false,
            quantity_minimum: null,
            quantity_maximum: null,
            options: [{id: 1, name: 'option 1', selected: true}],
            selectedOptions: [{id: 1, name: 'option 1', selected: true}]
        }]);
    });
});
