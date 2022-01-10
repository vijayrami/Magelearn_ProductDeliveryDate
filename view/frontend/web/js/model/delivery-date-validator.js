define(
    [    
        'mage/translate', 
        'Magento_Ui/js/model/messageList',
        'Magelearn_ProductDeliveryDate/js/model/config',
        'jquery',
        'mage/url'
    ],function ($t, messageList,config,$,urlBuilder) {
        'use strict';
        return {
            validate: function () {
				if (config().order.enabled == '0') {
					return true;
				}
				let product_delivery_date = document.getElementById('order-delivery-date').value;
				if (config().order.required == '0'
					&& product_delivery_date == null
					&& product_delivery_date.length === 0 ) {
					return true;
				}
                let isValid = false;
                var message;
                //console.log(config().order);
				//console.log('test');
                let url = urlBuilder.build('deliverydate/order/set');
                $.ajax(
                    {
                        url:url, 
                        data:{
                        	'delivery_date' : product_delivery_date,
                        	'format': config().order.dateformat
                        },
                        async:false,
                        complete: function ( jqXHR ) {
                        
                            let data = $.parseJSON(jqXHR.responseText);
                        
                            if(data.success) {
                            	isValid = true;
                            }
                            else{
                                isValid = false;
                                message = data.message;
                            }
                        }
                    }
                );
            
                
                if (!isValid) {
                    messageList.addErrorMessage({ message: message });
                }
                
                
                return isValid;
            }
        }
    }
);