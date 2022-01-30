define(
    [
    'ko',
    'uiComponent',
    'Magelearn_ProductDeliveryDate/js/model/config',
    'jquery',
    "mage/calendar"

    ], function (ko,Component, config, $) {
        'use strict';
    
        return Component.extend(
            {
                defaults: {
                    template: 'Magelearn_ProductDeliveryDate/checkout/shipping/delivery-date'
                },
                initialize: function () {
                    this.afterRender.bind(this);
                    this._super();
                    //console.log(config());
                    //console.log(window.checkoutConfig.magelearnDeliveryDate.order.datedisable);
                },
                afterRender: function () {
                    $('#order-delivery-date').datepicker(
                        {
                            minDate:this.config.order.mindate,
                            maxDate:this.config.order.maxdate,
                            dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                            dateFormat:this.config.order.dateformat,
                            beforeShowDay: this.nonWorkingDates,
                            onSelect: this.setDeliveryDate
                        }
                    );
            

                },
                setDeliveryDate: function (date) {

                },
                nonWorkingDates: (date) => {
            		
                    let day = date.getDay();
                    let string = $.datepicker.formatDate('yy-mm-dd', date);
                    let date_obj = [];
                    //console.log(day);
                    let days =  {"sunday":0,"monday":1,"tuesday":2,"wednesday":3,"thursday":4,"friday":5,"saturday":6};

	                let blackout = window.checkoutConfig.magelearnDeliveryDate.order.datedisable; 
                    let closedDays = window.checkoutConfig.magelearnDeliveryDate.order.daydisable;
					
					if(closedDays && closedDays != null){
						var disabledDay = closedDays.split(",");
					} else {
						var disabledDay = [];
					}
                    
                    let noday = window.checkoutConfig.magelearnDeliveryDate.order.noday;
                    if(blackout || noday){
	                    function arraySearch(arr,val) {
	                        for (var i=0; i<arr.length; i++)
	                            if (arr[i].date === val)                    
	                                return arr[i].content;
	                        return false;
	                    }
	                    function arrayTooltipClass(arr,val) {
	                        for (var i=0; i<arr.length; i++)
	                            if (arr[i].date === val)                    
	                                return 'redblackday';
	                        return 'redday';
	                    }
	                    
	                    for(var i = 0; i < blackout.length; i++) {
						   var tooltipDate = blackout[i].content;
						   if(blackout[i].date === string) {
	                         date_obj.push(blackout[i].date);
						   }
						}
	                    
	                    for(var i = 0; i < blackout.length; i++) {
						   var tooltipDate = blackout[i].content;
						   if(blackout[i].date === string) {
                             date_obj.push(blackout[i].date);
						   }
						}    
						
						if(date_obj.indexOf(string) != -1 || disabledDay.indexOf(day) > -1) {
                            return [false, arrayTooltipClass(blackout,string), arraySearch(blackout,string)];
                        }
						for (const property in disabledDay) {
							if(day == disabledDay[property]) {
								return [false, arrayTooltipClass(blackout,string), arraySearch(blackout,string)];
							}
						}
	                    
	                    return [true];
                    }
                },
                config: config()
            }
        );
    }
);