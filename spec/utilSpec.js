describe('A Util element', function () {
    'use strict';

    var isVisible;

    beforeEach(function () {
        isVisible = VOLO.util.isElementVisible.bind(VOLO.util);
    });

    it('should detect invisible element', function() {
        var target = document.createElement('div');
        document.body.appendChild(target);

        target.setAttribute('style', 'display: none;');
        expect(isVisible(target)).toBeFalsy();

        target.setAttribute('style', 'visibility: hidden;');
        expect(isVisible(target)).toBeFalsy();

        target.setAttribute('style', 'opacity: 0;');
        expect(isVisible(target)).toBeFalsy();

        target.setAttribute('style', '');
        expect(isVisible(target)).toBeTruthy();
    });

    it('should detect invisible element', function() {
        var target = document.createElement('div');
        var parent = document.createElement('section');

        parent.appendChild(target);
        document.body.appendChild(parent);

        expect(isVisible(target)).toBeTruthy();

        parent.setAttribute('style', 'display: none');

        expect(isVisible(target)).toBeFalsy();
    })
});
