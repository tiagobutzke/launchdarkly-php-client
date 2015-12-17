var VOLO = VOLO || {};

VOLO.util = {
    isElementVisible: function isElementVisible(element) {
        function checkElementVisibility(element) {
            var cs = window.getComputedStyle(element);
            return (
                cs.display !== 'none' &&
                cs.visibility !== 'hidden' &&
                parseInt(cs.opacity) !== 0
            );
        }

        if (!element || element == document) {
            return true;
        }
        else if (!checkElementVisibility(element)) {
            return false;
        }

        return isElementVisible.call(this, element.parentNode);
    }
};
