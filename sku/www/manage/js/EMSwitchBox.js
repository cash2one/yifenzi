/*EMSwitchBox.js
Description: An extremely simple but attractive toggle switch you can use in place of a standard input checkbox.
Author: Collin Henderson
Website: http://syropia.net
Contact: collin@syropia.net
Version: 1.3
*/
 (function($) {

    //Attach this new method to jQuery
    $.fn.extend({


        EMSwitchBox: function(options) {
	
			//Set up defaults
			var defaults = { 
				onLabel:      'On', 
				offLabel:     'Off' 
			};
			var options = $.extend({}, defaults, options)
            //Iterate over the current set of matched elements
            return this.each(function() {
				if (($(this).is(':checked'))==true) {
                var $markup = $('<div class="switch ad_on"><span class="green">'+options.onLabel+'</span><span class="red">'+options.offLabel+'</span><div class="thumb" style="left:55px"></div></div>');

				}
	           if (($(this).is(':checked'))==false) {
                var $markup = $('<div class="switch ad_off"><span class="green">'+options.onLabel+'</span><span class="red">'+options.offLabel+'</span><div class="thumb" style="left:3px"></div></div>');
				}
                $markup.insertAfter($(this));
                $(this).hide();	
			  //关闭 			
			  $('.ad_on').live("click",function(){ 
						 var add_on = $(this); 
						 var ss = $(this).attr("rel");
						 add_on.removeClass("ad_on").addClass("ad_off").children('div.thumb').stop().animate({left:3}, 300);
						 $(this).prev('input').attr('checked', false);  
			  }); 
		    
			  //开启 
			  $('.ad_off').live("click",function(){ 
				   var add_off = $(this); 
				   var ss = $(this).attr("rel");
				   add_off.removeClass("ad_off").addClass("ad_on").children('div.thumb').stop().animate({left:55}, 300);
				   $(this).prev('input').attr('checked', true);
				  }); 
						  
					  });

        }
    });
})(jQuery);