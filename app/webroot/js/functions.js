$( document ).ready(function() {
    if( typeof intervalSlide == 'undefined' ) {
        intervalSlide = 3;
    }

    if( $('.carousel').length > 0 ) {
         $('.carousel').each(function() {
            var data_interval = $(this).attr('data-interval');

            if(typeof data_interval == 'undefined' ) {
                data_interval = intervalSlide;
            }

            $(this).carousel({
                interval: data_interval,
            });
        });

        $('.carousel-inner .item').hover(function() {
            $(this).find('.icon-video-play').fadeIn();
        }, function() {
            $(this).find('.icon-video-play').hide();
        });
    }

    if( $('#all-regionId').length > 0 ) {
        $.generateLocation({
        	regionSelector: $('#all-regionId'),
        	citySelector: $('#all-cityId'),
        	subareaSelector: $('#all-subareaId'),
        });
    }
    if( $('#rent-regionId').length > 0 ) {
        $.generateLocation({
        	regionSelector: $('#rent-regionId'),
        	citySelector: $('#rent-cityId'),
        	subareaSelector: $('#rent-subareaId'),
        });
    }
    if( $('#sell-regionId').length > 0 ) {
        $.generateLocation({
        	regionSelector: $('#sell-regionId'),
        	citySelector: $('#sell-cityId'),
        	subareaSelector: $('#sell-subareaId'),
        });
    }
    if( $('#regionId').length > 0 ) {
        $.generateLocation();
    }

    if( $("a[rel^='prettyPhoto']").length > 0 ) {
		$("a[rel^='prettyPhoto']").prettyPhoto({
			allow_resize: true,
	        animation_speed:'fast',
	        slideshow:10000, 
	        hideflash: true,
	        social_tools:false,
	        deeplinking: false,
	        default_width: 1024,
            default_height: 724,
            autoplay: false,
	    });
	}
	if( $("#tabs-panel").length > 0 ) {
		$('#tabs-panel a').click(function (e) {
			e.preventDefault()
			$(this).tab('show')
		});
	}

	$('.reset-form').click(function(){
        var self = $(this);        
        self.parents('form').find('input, textarea, checkbox, select').val('');
    });

    // reset with class clearit
    $('#resetfilter').on('click', function(e) {
        e.preventDefault();
        $('.clearit').val("");
        
        // defaultnya dijual
        $('.sold').val(1);
    });

    var getInputPrice = function( obj ) {
        $(obj).priceFormat({
            doneFunc: function(obj, val) {
                currencyVal = val;
                currencyVal = currencyVal.replace(/,/gi, "")
                obj.next(".input_hidden").val(currencyVal);
            }
        });
    }

    var getRadio = function( obj ) {
        if( typeof obj == 'undefined' ) {
            obj = $('.rdo input[type="radio"], .radio input[type="radio"]');
        }

        obj.click(function(){
            $('label', $(this).parents('.form-group')).removeClass('checked');
            if( $(this).prop('checked') ) {
                $(this).parents('label').addClass('checked');
            }
        });
    }

    $('.btn-print').click(function(){
        window.print();
    });

    if(typeof $.daterangepicker == 'function'){
        var range_options = {
            locale: {
                format: 'DD/MM/YYYY',
            },
            autoUpdateInput: false,
        };

        var objDateRangeInputs = $('.date-range');

		if(objDateRangeInputs.length){
			objDateRangeInputs.daterangepicker(range_options).on({
				'apply.daterangepicker' : function(ev, picker){
					$(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
				}, 
				'cancel.daterangepicker' : function(ev, picker){
					$(this).val('');
				}
			});
		}
    }

	if(typeof $.inputPrice == 'function'){
    	$.inputPrice();
	}

	if(typeof $.inputNumber == 'function'){
		$.inputNumber();
	}

	if(typeof $.datePicker == 'function'){
		$.datePicker({
			up			: 'fa fa-angle-up',
			down		: 'fa fa-angle-down',
			next		: 'fa fa-angle-right',
			previous	: 'fa fa-angle-left',
			clear		: 'fa fa-trash-o',
			close		: 'fa fa-times',
		});
	}

	if(typeof $.tooltip == 'function'){
		var objTooltips = $('.tooltip-note');

		if(objTooltips.length){
			objTooltips.tooltip({
				'selector'	: '',
				'placement'	: 'top',
				'container'	:'body', 
			});
		}
	}

    $.generateLocation();
    $.rebuildFunction();
    $.initFancybox();
    $.dropDownMenu();
    $.sameHeight();
    $.toggle_display();
    $.checkAll();

//  dropdown search input init
    if($('.dropdown-text-input input:text').length){
        var objDropdown = $('.dropdown-text-input input:text').parents('.dropdown-group');

        if(objDropdown.length && typeof $.setDropdownValue == 'function'){
            $.setDropdownValue(objDropdown, 'change');
        }
    }

    $('.alert.close').click(function(){
        var self = $(this);
        var parent = self.parent('.alert');

        parent.remove();

        return false;
    });

//	membership (line 125 : 164)
	$('body').on('click', 'a.alert.close', function(){
		var self	= $(this);
        var parent	= self.parent('.alert');

        parent.remove();
        return false;
	});

	var membershipFormWrapper	= $('#membership-form-wrapper');
	var frontendModal			= $('#myModal');
	
	frontendModal.on('shown.bs.modal', function(){
		var self	= $(this);
		var closer	= self.find('div.modal-header a.close');

		closer.addClass('pull-right').find('i').removeClass().addClass('fa fa-times fa-2x');
	});

	if(membershipFormWrapper.length > 0){
		var baseUrl = $('#base_url').length > 0 ? $('#base_url').text() : '';
		var requestFormJXHR;

		if(requestFormJXHR){
			requestFormJXHR.abort();
		}

		requestFormJXHR = $.ajax({
			type	: 'post', 
			url		: baseUrl+'/memberships/order', 
			success	: function(data){
				membershipFormWrapper.html(data);
				$.rebuildFunction();
			}, 
			error	: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
				return false;
			}
		});
	}

	function hideAlert(){
		var alertBox	= $('div.alert');
		var parentModal	= alertBox.closest('div#myModal');

		setTimeout(function(){
			if(alertBox.length){
				alertBox.fadeOut().remove();
				if(parentModal.length && alertBox.hasClass('alert-success')){
					parentModal.modal('hide');
				}
			}
		}, 3000);
	}

	function Start(){
		var OldHtml = window.jQuery.fn.html;

		window.jQuery.fn.html = function () {
			var EnhancedHtml = OldHtml.apply(this, arguments);

			if(arguments.length && EnhancedHtml.find('div.alert').length){
				var TheElementAdded = EnhancedHtml.find('div.alert');
				if(TheElementAdded){
				//	hideAlert();
				}
			}

			return EnhancedHtml;
	   }
	}

    // countdown
    // if($('.count-down').length){
    //     var self = $('.count-down');
    //     var url = self.data('url');
    //     var interval = self.data('interval');

    //     var timeOut = setInterval(function(){
    //         $('#view-time').text(interval);
    //         interval = interval - 1;

    //         if(interval == 0){
    //             $("#view-time").remove();
    //             clearInterval(interval);
    //             window.location = url;
    //         }
    //     }, 1000);
    // }

    //  DEFINE GLOBAL MAIN OBJECTS
    var $_OBJcompareCheck   = $('input:checkbox[data-role="kpr-compare-checkbox"]');
    var $_OBJcompareBtn     = $('#btn-submit-comparison');


    if($_OBJcompareBtn.length){
        $_OBJcompareBtn.click(function(e){
            e.preventDefault();

            var self = $(this);
            
            var data_role = self.data('role');
            var formData = $(data_role).serialize();
            var url     = self.attr('href');
            var i = 0;

            var bank_items = $("#mortgage-bank-list input[type=checkbox]:checked").filter(function(key) {
                return $(this).val();
            });

            // $('.modal-header').remove();
            $('.modal-header h4.modal-title').html('');
            if(bank_items.length < 2){
                $.flashNotice('info', 'Anda membutuhkan minimum 2 Bank untuk membandingkan promo KPR'); 
            }else if(bank_items.length > 3){
                $.flashNotice('info', 'Anda hanya bisa memilih maksimal 3 Bank untuk membandingkan promo KPR');
            }else{

                var url_target_detail = self.attr('url-target-detail');

                $.ajax({
                    url: url_target_detail,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(result) {
                        var id = result.id;
                        var uuid = result.uuid;

                        url = url + '/' + id + '/' + uuid;
                        window.location.href = url;
                    }
                });

            }
        });
	}
	$(Start);

    if($('.scrolling').length > 0){
        var hash = window.location.hash;

        if(hash != '') {
            var arr = hash.split('|');
            var target = $.checkUndefined(arr[0], null);
            var top = $.checkUndefined(arr[1], null);

            if( target != null ) {
                var theOffset = $(target).offset();

                $('html, body').scrollTop( theOffset.top - top );
            }
        }
    }

    if($('.change-label').length > 0){
        $('.change-label').change(function(){
            var self = $(this);
            $.changeLabel(self);
        });
    }

    var calculatorInputs = $('.keyup-down-payment, .keyup-percent, .set-property-price');

	if(calculatorInputs.length){
		var propertyPriceInput	= $('.set-property-price');
		var dpPercentageInput	= $('.keyup-percent');
		var dpAmountInput		= $('.keyup-down-payment');

		calculatorInputs.blur(function(event){
			var self			= $(this);
			var propertyPrice	= $.convertNumeric(propertyPriceInput.val(), 'float');
			var dpPercentage	= $.convertNumeric(dpPercentageInput.val(), 'float');
			var dpAmount		= $.convertNumeric(dpAmountInput.val(), 'float');

			if(self.hasClass('keyup-down-payment')){
			//	price change (cannot exceed property value)
				dpAmount = dpAmount > propertyPrice ? propertyPrice : dpAmount; 

				if(propertyPrice > 0){
					dpPercentage = dpAmount / (propertyPrice / 100);
				}
			}
			else{
			//	dp percentage / property price change
				dpPercentage = dpPercentage > 100 ? 100 : dpPercentage;
			}

			dpPercentage	= dpPercentage.toFixed(2);
			dpAmount		= (propertyPrice / 100) * dpPercentage;

			propertyPriceInput.val($.formatNumber(propertyPrice));
			dpPercentageInput.val($.formatNumber(dpPercentage));
			dpAmountInput.val($.formatNumber(dpAmount));

		//	which object element passed doesn't matter
			setTimeout( function(){
				$.directAjaxLink({ obj : dpPercentageInput });
			}, 1000);
		});

		if(propertyPriceInput.length || dpAmountInput.length){
			var propertyPrice	= propertyPriceInput.val();
			var dpAmount		= dpAmountInput.val();

			propertyPriceInput.val($.formatNumber(propertyPrice));
			dpAmountInput.val($.formatNumber(dpAmount));
		}
	}

/*
    if($('.keyup-down-payment').length > 0 || $('.keyup-percent').length > 0){
        var property_price = $('.set-property-price').val();

        $('.keyup-down-payment').blur(function(){
            var self = $(this);
            var down_payment = self.val();
            var down_payment = $.numberToString(down_payment, 0);
            var percent = ( down_payment / property_price )*100;
            percent = percent.toFixed(2);

            var data_target = self.data('target');
            data_target = $.checkUndefined(data_target, false);

            $(data_target).val(percent);

            down_payment = $.formatNumber(down_payment);
            self.val(down_payment);

            setTimeout( function(){
                $.directAjaxLink({
                    obj: self,
                });
            }, 1000 );

        });

        $('.keyup-percent').blur(function(){
            var self = $(this);
            var percent = self.val();
            var downPayment = ( percent / 100 )*property_price;

            var data_target = self.data('target');
            data_target = $.checkUndefined(data_target, false);
            
            downPayment = $.formatNumber(downPayment);
            $(data_target).val(downPayment);

            setTimeout( function(){
                $.directAjaxLink({
                    obj: self,
                });
            }, 1000 );
        });

        if( $('.keyup-down-payment').length > 0 ) {
            var self = $('.keyup-down-payment');
            var down_payment = self.val();
            var down_payment = $.numberToString(down_payment, 0);

            down_payment = $.formatNumber(down_payment);
            self.val(down_payment);
        }
    }
*/

    // KPR BANK LIST
    var Link = typeof $.noUiSlider != 'undefined' ? $.noUiSlider.Link : null;
    if($('.slider-downpayment-kpr').length){
        var property_price = $('.property_price').data('rel');
        var down_payment = $('.down-payment').data('rel');
        var step = property_price/14;
        var data_min = getDpPrice(property_price, 20);
        var data_max = getDpPrice(property_price, 90);
        $(".slider-downpayment-kpr").noUiSlider({
            range: {
              'min': data_min,
              'max': data_max
            }
            ,animate: false
            ,start: down_payment
            ,margin: 100000000
            // ,connect: true
            ,direction: 'ltr'
            ,orientation: 'horizontal'
            ,behaviour: 'tap-drag'
            ,serialization: {

                lower: [
                    new Link({
                        target: $(".kpr_down_payment")
                    })
                ],

                format: {
                // Set formatting
                    decimals: 0,
                    thousand: ',',
                    prefix: '',
                    // prefix: 'IDR ',
                    encoder: function( value ){
                        var val = $('.slider-downpayment-kpr').val();
                        var target_label = $('.slider-downpayment-kpr').attr('target-label');
                        var target_percent = $('.slider-downpayment-kpr').attr('target-percent');
                        target_percent = $.checkUndefined(target_percent, false);

                        val = $.numberToString(val, 0);

                        var percent = ( val / property_price )*100;
                        percent = percent.toFixed(2);

                        $(target_label).html(percent);

                        if(target_percent){
                            $(target_percent).val(percent);
                        }

                        setTimeout( function(){
                            $.ajaxLink({
                                objChange: $('.slider-downpayment-kpr'),
                            });
                        }, 1000 );
                        return value;
                    }
                }

            }
        });

        function getDpPrice(property_price, dp){
            return property_price*(dp/100);
        }
    }

    if($('.slider-installment-kpr').length){
        var property_price = $('.property_price').data('rel');
        var credit_total = $('.credit-total').data('rel');
        $(".slider-installment-kpr").noUiSlider({
            range: {
              'min': 5,
              'max': 30
            }
            ,animate: false
            ,start: credit_total
            ,margin: 1
            ,direction: 'ltr'
            ,orientation: 'horizontal'
            ,behaviour: 'tap-drag'
            ,serialization: {

                lower: [
                    new Link({
                        target: $(".kpr_periode_installment")
                    })
                ],

                format: {
                // Set formatting
                    decimals: 0,
                    thousand: ',',
                    prefix: '',
                    // prefix: 'IDR ',
                    encoder: function( value ){
                        _callPeriodeInstallment ();
            
                        setTimeout( function(){
                            $.ajaxLink({
                                objChange: $('.slider-installment-kpr'),
                            });
                        }, 1000 );

                        return value;
                    }
                }

            }
        });

        function _callPeriodeInstallment () {
            var val = $('.slider-installment-kpr').val();
            var target_label = $('.slider-installment-kpr').attr('target-label');
            $(target_label).html(val);
        }
        
        $(".kpr_periode_installment").on('change', function(){
            $(".slider-installment-kpr").val($(this).val());
            
            _callPeriodeInstallment ();
            
            setTimeout( function(){
                $.directAjaxLink({
                    obj: $('.slider-installment-kpr'),
                });
            }, 1000 );
        });
    }

    if($('.generate-view').length > 0){
        $.directAjaxLink({
            obj: $('.generate-view'),
        });
    }

    if($('.new-window').length > 0){
        $('.new-window').click(function(){
            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var self = $(this);
            var h = $.checkUndefined(self.data('height'), 500);
            var w = $.checkUndefined(self.data('width'), 500);
            var href = self.attr('href');
            var title = self.attr('title');

            var left = ((width / 2) - (w / 2)) + dualScreenLeft;
            var top = ((height / 2) - (h / 2)) + dualScreenTop;

            window.open(href, title, 'width='+w+',height='+h+',top='+top+',left='+left);

            return false;
        });
    }

    $('body').delegate( '.payment-channel', 'change', function(e) {
        var self = $(this);
        var value = self.val();

        var target = $('#tenor-box');

        if(value == 15 || value == '15'){
            target.removeClass('hide');
        }else{
            target.addClass('hide');
        }
    });

    $( "body" ).delegate( '.payment-method-trigger', "click", function() {
        var self = $(this).find('a');
        var target = self.attr('href');

        $(target).find('li').removeClass('active');

        var real_target = $(target+' li:first-child');

        real_target.addClass('active');

        var val = real_target.find('a').data('payment-method');

        $('#product-payment-method-id').val(val);
    });

    $( "body" ).delegate( '.target-payment-method', "click", function() {
        var self = $(this);

        self.parent('ul').find('li').removeClass('active');

        var val = self.data('payment-method');

        $('#product-payment-method-id').val(val);
    });

    if($('.countdown-timer').length > 0){
        var times = $('.times-expired').val();
        var periode_max_expired = $('.periode-max-expired').val();
        var cart_id_booking_unit = $('.cart-id-booking-unit').val();

        if(periode_max_expired == null && periode_max_expired == ''){
            periode_max_expired = 5;
        }

        if(times != ''){
            var current_date = new Date();
            var max_expire = current_date.setMinutes(current_date.getMinutes() + parseInt(periode_max_expired));

            var check_cookie = checkCookie('max_expire');
            var cookie_time = getCookie('cookie_time');

            if(check_cookie && cookie_time == times){
                max_expire = getCookie('max_expire');
            }else{
                setCookie('max_expire', max_expire);
                setCookie('cookie_time', times);
            }

            countDownTimer(max_expire, cart_id_booking_unit);
        }
    }

    if($('.delete-cookie').length > 0){
        deleteCookie('max_expire');
    }

    function countDownTimer(max_expired, cart_id){
        expInterval = setInterval(function() {
            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now an the count down date
            var distance = max_expired - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // If the count down is finished, write some text 
            if (distance < 0) {
                deleteCookie('max_expire');

                stopInterval(expInterval);

                window.location.href = '/transactions/uncart/'+cart_id;
            }else{
                var text = '';
                if(days != 0 || days != '0'){
                    text += pad(days, 2) + " hari ";
                }
                if(hours != 0 || hours != '0'){
                    text += pad(hours, 2) + " jam ";
                }
                if(minutes != 0 || minutes != '0'){
                    text += pad(minutes, 2) + " menit ";
                }
                if(seconds != 0 || seconds != '0'){
                    text += pad(seconds, 2) + " detik";
                }

                $('.countdown-timer').html( text );
            }
        }, 1000);
    }

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    function stopInterval(myVar){
        clearInterval(myVar);
    }


    // global bxslider
    if ($('#gallery-thumbs').length > 0) {

        var width = $(window).width();

        // Cache the thumb selector for speed
        var thumb = $('#gallery-thumbs').find('.thumb');

        // How many thumbs do you want to show & scroll by
        if (width < 430) {
            numbThumbScroll = 3;
        } else {
            numbThumbScroll = 5;
        }
        
        var visibleThumbs = numbThumbScroll;

        // Put slider into variable to use public functions
        var gallerySlider = $('#gallery').bxSlider({
            controls: true,
            pager: false,
            easing: 'easeInOutQuint',
            infiniteLoop: true,
            speed: 500,
            auto: ($(".gallery-images .item").length > 1) ? true: false,
            onSlideAfter: function (currentSlideNumber) {
                var currentSlideNumber = gallerySlider.getCurrentSlide();
                thumb.removeClass('pager-active');
                thumb.eq(currentSlideNumber).addClass('pager-active');
            },

            onSlideNext: function () {
                var currentSlideNumber = gallerySlider.getCurrentSlide();
                slideThumbs(currentSlideNumber, visibleThumbs);
            },

            onSlidePrev: function () {
                var currentSlideNumber = gallerySlider.getCurrentSlide();
                slideThumbs(currentSlideNumber, visibleThumbs);
            }
        });

        // When clicking a thumb
        thumb.click(function (e) {

            // -6 as BX slider clones a bunch of elements
            gallerySlider.goToSlide($(this).closest('.thumb-item').index());

            // Prevent default click behaviour
            e.preventDefault();
        });

        // Thumbnail slider
        var thumbsSlider = $('#gallery-thumbs').bxSlider({
            controls: true,
            pager: false,
            easing: 'easeInOutQuint',
            infiniteLoop: false,
            minSlides: (width < 480) ? 3 : 5,
            maxSlides: (width < 480) ? 3 : 5,
            slideWidth: 360,
            slideMargin: 10
        });

        // Function to calculate which slide to move the thumbs to
        function slideThumbs(currentSlideNumber, visibleThumbs) {

            // Calculate the first number and ignore the remainder
            var m = Math.floor(currentSlideNumber / visibleThumbs);

            // Multiply by the number of visible slides to calculate the exact slide we need to move to
            var slideTo = m * visibleThumbs;

            // Tell the slider to move
            thumbsSlider.goToSlide(m);
        }

        // When you click on a thumb
        $('#gallery-thumbs').find('.thumb').click(function () {

            // Remove the active class from all thumbs
            $('#gallery-thumbs').find('.thumb').removeClass('pager-active');

            // Add the active class to the clicked thumb
            $(this).addClass('pager-active');

        });

        // when image or slider only 1
        if ( $(".gallery-thumbs-list li").length == 1 ) {
            $('#gallery-thumbs').find('.thumb-item').addClass('w-auto');
            $('#gallery-thumbs').find('.thumb').addClass('pager-active');
            $('.gallery-thumbs-container').find('.bx-wrapper').addClass('no-border');
        }

    }

//  mobile quick search
    var objTriggerButtons   = $('button[role=search-trigger]');
    var objSearchWrapper    = $('#mobile-search-wrapper');

    if(objTriggerButtons.length && objSearchWrapper.length){
        objTriggerButtons.on('click', function(event){
            var self    = $(this);
            var value   = self.data('value') || null;

            if(value && $.inArray(value, ['show', 'close']) > -1){
                objSearchWrapper.toggleClass('show', value == 'show');
            }
        });
    }
});