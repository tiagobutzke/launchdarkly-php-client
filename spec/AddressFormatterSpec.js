describe('Address formatter', function() {
    var addressFormatter, testAddress;
    beforeEach(function() {
        addressFormatter = new VOLO.Service.AddressFormater();
        testAddress = {
            street: 'Johannisstrasse',
            building: '20',
            plz: '10117',
            city: 'Berlin'
        };
    });

    describe('constructor', function() {
        it('should set appConfig from options', function() {
            addressFormatter = new VOLO.Service.AddressFormater({lol: true});

            expect(addressFormatter.appConfig).toEqual({lol: true});
        });
    });

    describe('street format function', function() {
        it ('should return correctly formatted street if building number is before street name', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':building :street'
                }
            };

            var street = addressFormatter.getFormattedStreet('johannisstrasse, 10117');
            expect(street).toBe(' johannisstrasse');
        });

        it('should return correctly formatted street if building number is after street name', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':street :building'
                }
            };

            var street = addressFormatter.getFormattedStreet('johannisstrasse, 10117');
            expect(street).toBe('johannisstrasse ');
        });

        it('should return correctly formatted street if street format is with comma', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':street, :building'
                }
            };

            var street = addressFormatter.getFormattedStreet('johannisstrasse, 10117');
            expect(street).toBe('johannisstrasse, ');
        });
    });

    describe('street and building format function', function() {
        it ('should return correct format for :street :building :plz :city', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':street :building :plz :city'
                }
            };

            var street = addressFormatter.getFormattedStreetAndBuilding(testAddress);
            expect(street).toBe('Johannisstrasse 20 ');
        });

        it('should return correct format for :building :street :plz :city', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':building :street :plz :city'
                }
            };


            var street = addressFormatter.getFormattedStreetAndBuilding(testAddress);
            expect(street).toBe('20 Johannisstrasse ');
        });

        it('should return correct format for :street :building :city :plz', function() {
            addressFormatter.appConfig = {
                address_config: {
                    format: ':street :building :city :plz'
                }
            };

            var street = addressFormatter.getFormattedStreetAndBuilding(testAddress);
            expect(street).toBe('Johannisstrasse 20 Berlin ');
        });
    });
});
