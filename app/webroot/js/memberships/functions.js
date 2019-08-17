$( document ).ready(function() {
	$('a[href^="#"]').on('click',function (e) {
		e.preventDefault();

		var target		= this.hash;
		var toggle		= $(this).attr('data-toggle');
		var data_target	= $(this).attr('data-target');
		var form_url	= $.checkUndefined($(this).attr('data-form-url'), false);

		if(toggle == 'modal'){
			if( form_url != false ) {
				$(data_target+' form').attr('action', form_url);
			}
		}
		else if($(target).length){
			$('html, body').stop().animate({
				'scrollTop' : $(target).offset().top, 
			}, 900, 'swing', function () {
				window.location.hash = target;
			});
		}
	});
	
	$('body').delegate('#btn-submit-form', 'click', function(event){
		$(this).closest('form').addClass('hide');
		$('#membership-wrapper').append('<div class="spinner"><img src="/img/loading.gif" height="36"></div>');
	});
	
	var selMenu = $('select#menu');
	$('select#menu').on('change', function(){
		var self = $(this);
		if(self.val() == 'register'){
			$('#btnRegister').click();
		}
		else{
			window.location = self.val();
		}
	});

	$('a[href^="#"]').on('click',function (e) {
	    e.preventDefault();

		var self	= $(this);
	    var target	= this.hash;
	    var $target	= $(target);
		var topPos	= 0;
		var href	= self.attr('href');

		console.log(href != '#');
		
		if($target.length){
			topPos = $target.offset().top;
		}

		if(href != '#'){
			$('html, body').stop().animate({
				'scrollTop': topPos
			}, 900, 'swing', function () {
				window.location.hash = target;
			});
		}
	});

	$('.top-affix').affix({
		offset: 15,
	});

	var packagePlaceholders = $('div.f_table');
	if(packagePlaceholders.length){
		packagePlaceholders.each(function(){
			var parent = $(this);
			var childs = parent.find('div.system');
			var height = {};

			if(childs.length){
				childs.each(function(){
					var target	= $(this).find('.thead_system h4');
					var key		= $(this).hasClass('prime') ? 'prime' : 'std';

					var lastHeight = typeof height[key] != 'undefined' ? height[key] : 0;
					if(target.height() > lastHeight){
						height[key] = target.height();
					}
				});

				$.each(height, function(key, value){
					childs.filter('.' + key).find('.thead_system h4').css({
						'height' : value + 'px', 
					});
				});
			}
		});
	}

	function formatMoney(number, places, symbol, thousand, decimal) {
		number = number || 0;
		places = !isNaN(places = Math.abs(places)) ? places : 2;
		symbol = symbol !== undefined ? symbol : "";
		thousand = thousand || ",";
		decimal = decimal || ".";
		var negative = number < 0 ? "-" : "",
			i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
			j = (j = i.length) > 3 ? j % 3 : 0;
		return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
	}

	var checkVoucherJXHR;
	var txtVoucherCode		= $('#PaymentVoucherCode');
	var txtInvoiceNumber	= $('#PaymentInvoiceNumber');
	var baseAmount			= $('#PaymentBaseAmount');
	var discountAmount		= $('#PaymentDiscountAmount');
	var totalAmount			= $('#PaymentTotalAmount');
	var btnCheckVoucher		= $('#PaymentCheckVoucher');
	var selPaymentChannel	= $('#PaymentPaymentChannel');
	var selTenor			= $('#PaymentTenor');
	var chkUserAgreement	= $('#PaymentAgreement');
	var btnContinuePayment	= $('#btnContinuePayment');

//	submit doku handler
	var dokuForm = $('#doku-payment-form');
	if(dokuForm.length > 0){
		dokuForm.submit();
	}

	selPaymentChannel.change(function(){
		var self = $(this);

		if(self.val() == '15'){
			selTenor.closest('div.cartBlockGroup').removeAttr('style');
		}
		else{
			selTenor.closest('div.cartBlockGroup').attr('style', 'display:none;');
		}
	}).trigger('change');

	var frmMembershipCheckout = $('form#MembershipOrderCheckoutForm');
	frmMembershipCheckout.submit(function(e){
		$(this).find('button').prop('disabled', false);

		if(chkUserAgreement.prop('checked') == false){
			alert('Mohon centang pernyataan persetujuan jika Anda telah membaca dan setuju dengan Syarat dan Ketentuan yang berlaku di Prime System dan ingin melanjutkan pembayaran.');
			return false;
		}
		else{
			return true;
		}
	});

	btnCheckVoucher.click(function(){
		var self		= $(this);
		var inputParent	= txtVoucherCode.closest('div');

		txtVoucherCode.removeClass('form-error form-success');
		inputParent.find('div.error-message, div.success-message').remove();

		if(txtVoucherCode.val() != ''){
			if(checkVoucherJXHR){
				checkVoucherJXHR.abort();
			}

			checkVoucherJXHR = $.ajax({
				url		: '/vouchers/validateVoucher', 
				type	: 'post', 
				data	: 'code='+txtVoucherCode.val()+'&invoice='+txtInvoiceNumber.val(), 
				success	: function(data){
					var objResult = $.parseJSON(data);

					if(typeof(objResult) == 'object'){
						var status	= typeof(objResult.status) != 'undefined' ? objResult.status : 'error';
						var message	= typeof(objResult.msg) != 'undefined' ? objResult.msg : 'Error';

						if(status == 'success'){
							var voucher			= typeof(objResult.data) != 'undefined' ? objResult.data : false;
							var discountType	= typeof(voucher.discount_type) != 'undefined' ? voucher.discount_type : 'nominal';
							var discountValue	= typeof(voucher.discount_value) != 'undefined' ? voucher.discount_value : 0;

							var subTotal		= baseAmount.html().split(',').join('');

							if(discountType == 'percentage'){
                                discountValue = $.formatNumber(discountValue, 2);
								discountValue = (parseFloat(subTotal) / 100) * parseFloat(discountValue);
                                discountValue = Math.floor(discountValue);
                            //  alert(discountValue);
							}

							var grandTotal = parseFloat(subTotal) - parseFloat(discountValue);
								grandTotal = grandTotal > 0 ? grandTotal : 0;

							txtVoucherCode.addClass('form-success');
							discountAmount.html(formatMoney(discountValue, 0));
							totalAmount.html(formatMoney(grandTotal, 0));
						}
						else{
							txtVoucherCode.addClass('form-error');
							discountAmount.html(formatMoney(0, 0));
							totalAmount.html(baseAmount.html());
						}

						self.before('<div class="'+status+'-message">'+message+'</div>');
					}
					else{
						return false;
					}
				}, 
				error	: function(){
					console.log('an error occured');
				}
			});
		}

		return false;
	});

	if($('#MembershipOrderIsPrinciple').length){
		$('#MembershipOrderIsPrinciple').bind('init click', function(){
			var principleInputs = $('div#name-placeholder, div#email-placeholder, div#phone-placeholder');

			if(principleInputs.length){
				if($(this).prop('checked')){
					principleInputs.fadeOut(200).removeClass('error').find('input').val('').next('div.error-message').remove();
				}
				else{
					principleInputs.fadeIn(200);
				}
			}
		}).trigger('init');
	}

	if($('.pick-package').length > 0){
		$('.pick-package').change(function(){
			var self = $(this);
			var val = self.val();

			$.ajax({
				url		: '/memberships/get_package', 
				type	: 'post', 
				data	: 'id='+val, 
				success	: function(data){
					
				}
			});
		});
	}

	// if($('.checkboxBorder').length > 0){
	// 	$('.checkboxBorder').click(function(){
	// 		var self = $(this);
	// 		var val = self.val();

	// 		$('.checkboxBorder').removeAttr('checked');
	// 		self.attr('checked', true);

	// 	});
	// }
});