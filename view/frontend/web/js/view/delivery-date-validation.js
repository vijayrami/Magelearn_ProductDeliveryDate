define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magelearn_ProductDeliveryDate/js/model/delivery-date-validator'
    ],
    function (Component, additionalValidators, deliveryDateValidator) {
        'use strict';
        additionalValidators.registerValidator(deliveryDateValidator);
        return Component.extend({});
    }
);
