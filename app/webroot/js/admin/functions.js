function getQueryParam(name, url){
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function isChildWindow(){
    return window.opener || (window.top !== window.self);
}

function preview(img, selection) {
    var scaleX = 300 / selection.width;
    var scaleY = 300 / selection.height;

    $('#preview_thumbnail').css({
        width: Math.round(scaleX * $('#preview_image').width()) + 'px',
        height: Math.round(scaleY * $('#preview_image').height()) + 'px',
        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    });
    $('#x1').val(selection.x1);
    $('#y1').val(selection.y1);
    $('#x2').val(selection.x2);
    $('#y2').val(selection.y2);
    $('#w').val(selection.width);
    $('#h').val(selection.height);
    $('#w_img').val($('#preview_image').width());
    $('#h_img').val($('#preview_image').height());
}

function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
}

//	B:YOUTUBE RELATED FUNCTIONS ==============================================================================================

//	can be used for all google api serivces
	window.GoogleAuth;
	window.GoogleUser;
	window.youtubeScopes = 'https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.upload';

	var objChannelName		= $('#channel-name');
	var objPreSignInBlock	= $('.pre-sign-in');
	var objPostSigninBlock	= $('.post-sign-in');

	function handleClientLoad(){
		if(typeof gapi == 'undefined'){
			console.log('gapi lib not found');
		}
		else{
			gapi.load('client:auth2', initClient);
		}
	}

	function initClient(){
	//	console.log('gapi client initialized');

	//	initialize the gapi.client object, which app uses to make api requests.
	//	get api key and client id from api console.
	//	'scope' field specifies space-delimited list of access scopes
		var objSignInBtn	= $('#signInButton');
		var objSignOutBtn	= $('#signOutButton');
		var clientId		= objSignInBtn.data('clientid');
		var apiKey			= objSignInBtn.data('key');

		if(clientId && apiKey){
			gapi.client.init({
				'apiKey'		: apiKey, 
				'clientId'		: clientId,
				'scope'			: window.youtubeScopes, 
				'discoveryDocs'	: [
					'https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest', 
				],
			}).then(function(){
			//	google auth obj
				window.GoogleAuth = gapi.auth2.getAuthInstance();

			//	listen for sign-in state changes. (automaticaly calling the updateSigninStatus whenever the state changes)
				window.GoogleAuth.isSignedIn.listen(updateSigninStatus);

			//	listen for changes to current user.
				window.GoogleAuth.currentUser.listen(updateUserChanges);

			//	prepare upload and toggle display (initial)
				setSigninStatus();

				objSignInBtn.click(function(event){
				//	console.log('login');
					handleAuthClick();
				});

				objSignOutBtn.click(function(event){
				//	console.log('logout');
					revokeAccess();
				});
			});
		}
	}

	function handleAuthClick(event){
		window.GoogleAuth.signIn();
	}

	function revokeAccess(){
		window.GoogleAuth.disconnect();
	}

	function setSigninStatus(){
		var user		= window.GoogleAuth.currentUser.get();
		var accessToken	= getAccessToken();

		isAuthorized = user.hasGrantedScopes(window.youtubeScopes);

	//	console.log('is authorized : ' + isAuthorized + ' token : ' + accessToken);

		if(accessToken && typeof UploadVideo == 'function'){
			var uploadVideo = new UploadVideo();

		//	renew attr value
			document.getElementsByTagName('body')[0].setAttribute('data-youtube-token', accessToken);

		//	authenticate on uploadVideo ready
			uploadVideo.ready(accessToken);
		}
		else{
		//	hide user info block and reset user data
			objPostSigninBlock.hide();
			objPostSigninBlock.find('#channel-thumbnail').attr('src', '');
			objPostSigninBlock.find('#channel-name').attr('html', '');

		//	show login block
			objPreSignInBlock.show();
		}
	}

	function getAccessToken(event){
		var user			= window.GoogleAuth.currentUser.get();
		var authResponse	= user.getAuthResponse(true);

		return authResponse ? authResponse.access_token : null;
	}

	function updateSigninStatus(isSignedIn){
	//	console.log('Signin state changed to : ', isSignedIn);

		setSigninStatus();
	}

	function updateUserChanges(currentUser){
	//	console.log('Current user changed to : ', currentUser);

		window.GoogleUser = currentUser;
	}

//  untuk youtube biar bisa di call terus via ajax
	var signinCallback = function(result){
		accessToken = result.access_token ? result.access_token : document.getElementsByTagName('body')[0].getAttribute('data-youtubeToken');

		if(accessToken && typeof UploadVideo == 'function'){
			var uploadVideo = new UploadVideo();

		//  set global access token
		//  accessToken = token;

		//  renew attr value
			document.getElementsByTagName('body')[0].setAttribute('data-youtubeToken', accessToken);

		//  authenticate on uploadVideo ready
			uploadVideo.ready(accessToken);
		}
	};

//	E:YOUTUBE RELATED FUNCTIONS ==============================================================================================

$( document ).ready(function() {
    if(typeof $.fn.editable == 'function'){
        var toggleSpecJXHR;

        $('body').on('save', '.editable[data-role="property-spec-toggle"]', function(event, params){
            var self        = $(this);
            var dataURL     = self.data('url');
            var dataWrapper = self.data('wrapper');

            dataURL = dataURL ? dataURL : self.closest('form').attr('action');

            var objWrapper = $(dataWrapper);

            if(objWrapper.length && dataURL){
                var objEditables    = objWrapper.find('.editable');
                var postData        = {};

                objEditables.each(function(index, objEditable){
                    var objEditable = $(objEditable);
                    var inputName   = objEditable.data('name');
                    var inputValue  = objEditable.editable('getValue', true);

                    postData[ inputName ] = inputValue;
                });

                if(toggleSpecJXHR){
                    toggleSpecJXHR.abort();
                }

                toggleSpecJXHR = $.ajax({
                    method  : 'post', 
                    url     : dataURL, 
                    data    : postData, 
                    success : function(response){
                        var replacementWrapper;

                        if($(response).find(dataWrapper)){
                            replacementWrapper = $(response).find(dataWrapper);
                        }
                        else{
                            replacementWrapper = $(response).filter(dataWrapper);
                        }

                        if(replacementWrapper.length){
                            objWrapper.html(replacementWrapper.html());

                            $.rebuildFunctionAjax(objWrapper);

                            console.log('wrapper replaced');
                        }
                        else{
                            console.log('replacement not found');
                        }
                    }, 
                    error   : function(jqXHR, textStatus, errorThrown){
                        console.log('an error occured');
                    }
                });
            }
        });
    }

    // Non Companies - handling field email
    $('.handler-parent-mail').change(function(){
        var self = $(this);
        var target  = '.handling-group';
        var target2 = '.handling-group2';

        if(self.val() == 2 || self.val() == 5){
            var label_target = target+' label';
            var attr_target = target+' input';
            var text_parent = 'parent';
            
            $(target).show();

            if(self.val() == 3){
                $value_attr = 4;
                text_parent = 'Direktur';
            }else{
                $(target).append();
                $value_attr = 3;
                text_parent = 'Principle';
            }

            var full_text = 'Email '+text_parent+' *';

            $(label_target).text(full_text);
            $(attr_target).attr('data-ajax-url', '/ajax/list_users/'+$value_attr);

            $.Autocomplete();
        }else{
            $(target).hide();
        }

        if (self.val() == 1 || self.val() == 3 || self.val() == 5) {
            $(target2).hide();
        }else{
            $(target2).show();
        }
        
    });

    $("#min-toggle").click(function(e) {
        e.preventDefault();
        $("#big-wrapper").toggleClass("toggled");
    });

    if(typeof WOW == 'function'){
        new WOW().init();
    }

    $('.tracker-radio-id li a.action').click(function(e){
        var self = $(this);

        var parent = self.parents('.tracker-radio-id');
        var dropdown = self.parents('.dropdown-group');
        var parentLink = dropdown.children('a.dropdown-toggle');

        var child = parent.children('.info-radio-id');
        var group_id = self.attr('data-value');

        parent.find('a').removeClass('active');

        child.val(group_id).trigger('change');

        self.addClass('active');
        parentLink.addClass('active');
    });

    if( $('.tracker-radio-id').length > 0 && $('.info-radio-id').length > 0 ) {
        if( $('.info-radio-id').val() != '' ) {
            var value_radio = $('.info-radio-id').val();
            $('.tracker-radio-id .action input[type="radio"][value="'+value_radio+'"]').parents('.action').addClass('active');
        }
    }

    if( $('#regionId').length > 0 ) {
        $.generateLocation();
    }
    if( $('.regionId').length > 0 ) {
        $.generateLocation();
    }

    if( $('#gmap-rku').length > 0 ) {
        $.gmapLocation();
    }

    if($('#wrapper-write-crm .status_marital').length > 0){
        $('#wrapper-write-crm .status_marital').change(function(){
            var self = $(this);
            var value = self.val();

            if(value == 'marital'){
                $('#spouse-particular').show(); 
            }else{
                $('#spouse-particular').hide(); 
            }
        });
    }

    $(document).ready(function(){
    //  custom location selector
        if($('#regionId1').length){
            var additionals = $('#regionId1').attr('aditionals');
            $.generateLocation({
                regionSelector  : $('#regionId'+additionals),
                citySelector    : $('#cityId'+additionals),
                subareaSelector : $('#subareaId'+additionals),
                zipSelector     : $('#rku-zip'+additionals),
                additionals     : additionals,
            });
        }
    });

    $('#removeAgentConfirmationModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var agent_id = button.data('agent-id');
        var modal = $(this);
        modal.find('#hdnAgentId').val(agent_id);
    });

    $.rebuildFunction();
    $.callInterval();
    $.callChoosen();
    $.daterangepicker();
    $.handle_toggle();
    $.toggle_display();
    $.checkAll();

    if(typeof $.editable == 'function'){
        $.editable();
    }

    if( $('.add-custom-field').length > 0 ){
        $.addCustomTextField();
    }

    // if($('.kpr-dp-price').length > 0 || $('.kpr-dp-percent').length > 0){
    //  $.calcDp();
    // }

    $.actionPopover();
    $.inputPrice();
    $.inputNumber();
    $.increment();
    $.datePicker();
    $.submitCustomForm();
    $.Autocomplete();
    $.ajaxMediaTitleChange();
    $.dropDownMenu();
    $.colorPicker();
    $.handle_input_file();
    $.option_image_ebrosur();
    $.carousel();
    $.limit_word_package();
    $.limit_word_package({
        obj: $('.desc-info-cls'),
        objCounter: $('.limit-character2'),
        objBody: false
    });
    $.handle_toggle_content();
    $.load_more_paginate();
    $.sameHeight();
    $.triggerSubmitReport();
    $.triggerComponentPoint();
    $.component_range();

    if(typeof $.triggerComponentPoint == 'function'){
        $.triggerComponentPoint();
    }

    if(typeof $.component_range == 'function'){
        $.component_range();
    }

    if( $('#preview_image').length > 0 ) {
        setTimeout( function(){
            var default_w = $('#default_width').val();
            var default_h = $('#default_height').val();

            var default_preview_w = $('#wrapper-crop-preview img').width();
            var default_preview_h = $('#wrapper-crop-preview img').height();

            $('#x1').val(0);
            $('#y1').val(0);
            $('#x2').val(default_w);
            $('#y2').val(default_h);
            $('#w').val(default_w);
            $('#h').val(default_h);

            $('#crop_thumbnail img').width(default_preview_w);
            $('#crop_thumbnail img').height(default_preview_h);
            $('#w_img').val(default_preview_w);
            $('#h_img').val(default_preview_h);

            if( default_preview_w > 300 && default_preview_h > 300 ) {
                $('#preview_image').imgAreaSelect({ 
                    aspectRatio: '1:1', 
                    x1: 0, y1: 0, x2: default_w, y2: default_h,
                    onSelectChange: preview
                });
            }
        }, 100 );
    }

    $(document).on('change init', '#lot-unit-id', function() {
        var val = $('#lot-unit-id :selected').text().replace(/per /g, '');
        var value = $('#lot-unit-id :selected').val();
        val = val.toLowerCase();

        if( val == 'borongan' || value == '' ) {
            val = 'm2';
        }

        $('.lot-unit').html(val);
    });

    $('#lot-unit-id').trigger('init');

    $('.chk-other-item').click(function() {
        var self = $(this);
        var dataTarget = $.checkUndefined(self.attr('data-target'), '');

        if(dataTarget == ''){
            var parent = self.parents('.cb-checkmark');
            var target = parent.find('.other-checkbox');
        }
        else{
            var target = $(dataTarget);
        }

        if( self.is(':checked') ) {
            target.attr('disabled', false);
        } else {
            target.attr('disabled', true);
            target.val('');
        }
    });

    if( $('.toggle').length ) {
        var processToggleDisplay = function( toggleInput, otherInputSelector, negation, hiddenInput ) {
            var propChecked = toggleInput.prop('checked');
            var negation = ( typeof negation != 'undefined' && negation != false) ? true : false;
            var hiddenInput = ( typeof hiddenInput != 'undefined') ? hiddenInput : false;

            if( negation ) {
                propChecked = !propChecked;
            }

            if( propChecked ) {
                if( otherInputSelector.hasClass('partial-toggle') ) {
                    otherInputSelector.show();
                } else {
                    otherInputSelector.closest('div.form-group').show();
                }

                if( hiddenInput != false ) {
                    hiddenInput.closest('div.form-group').hide();
                }
            } else {
                if( otherInputSelector.hasClass('partial-toggle') ) {
                    otherInputSelector.hide();
                } else {
                    otherInputSelector.closest('div.form-group').hide();
                }

                if( hiddenInput != false ) {
                    hiddenInput.closest('div.form-group').show();
                }
            }
        }

        if( $('.toggle').find('.toggle-input').attr('triggered-selector-class') !== 'undefined' ) {
            $('.toggle').each(function(){
                var self = $(this).find('.toggle-input');
                var selector = $('.'+self.attr('triggered-selector-class'));
                var hidden = $('.'+self.attr('triggered-selector-hide-class'));
                processToggleDisplay( self, selector, false, hidden );
            });

            $('.toggle').click(function() {
                var self = $(this).find('.toggle-input');
                var selector = $('.'+self.attr('triggered-selector-class'));
                var hidden = $('.'+self.attr('triggered-selector-hide-class'));
                processToggleDisplay( self, selector, true, hidden );             
            });
        }
    }

    $('.btn-theme-default').click(function(){
        var self = $(this);
        var data_field = self.attr('data-field');
        
        if(data_field != ''){
            $("#"+data_field).val(self.attr('data-default'));
            $("#"+data_field).trigger('keyup');
        }

        return false;
    });

//  voucher handler
    var voucherForm = $('#VoucherAdminAddForm, #VoucherAdminEditForm');
    if(voucherForm.length > 0){
        var packagePlaceholder  = $('#package-placeholder');
        var btnAddMembership    = $('a[data-role="add-membership"]');
        var btnVoucherSubmit    = $('#voucher-submit-button');

        function toggleInputView(){
            var inputs = $('#VoucherCodeMechanism, #VoucherPeriodType, #VoucherApplyTo');
            inputs.each(function(){
                var self    = $(this);
                var selfID  = self.attr('id');

                switch(selfID){
                    case 'VoucherCodeMechanism' : 
                        if(self.val() == 'manual'){
                            $('#VoucherCodeCode').prop('disabled', false).closest('div#voucher-code-placeholder').removeClass('hide');
                            $('div#voucher-length-placeholder, div#voucher-prefix-placeholder').addClass('hide').find('input:text').val('').prop('disabled', true);
                        }
                        else if(self.val() == 'auto'){
                            $('#VoucherCodeCode').prop('disabled', true).closest('div#voucher-code-placeholder').addClass('hide');
                            $('div#voucher-length-placeholder, div#voucher-prefix-placeholder').removeClass('hide').find('input:text').prop('disabled', false);
                        }
                    break;
                    case 'VoucherPeriodType' : 
                        if(self.val() == 'periodic'){
                            $('#period-date-placeholder').prop('disabled', false).removeClass('hide');
                        }
                        else if(self.val() == 'unlimited'){
                            $('#period-date-placeholder').prop('disabled', true).addClass('hide');
                        }
                    break;
                    case 'VoucherApplyTo' : 
                        if(self.val() == 'all'){
                            $('#discount-placeholder').removeClass('hide').find('select, input').prop('disabled', false);
                            $('#package-placeholder').addClass('hide').find('select, input').prop('disabled', true);
                        }
                        else if(self.val() == 'manual'){
                            $('#discount-placeholder').addClass('hide').find('select, input').prop('disabled', true);
                            $('#package-placeholder').removeClass('hide').find('select, input').prop('disabled', false);
                        }
                    break;
                }
            });
        }

        function toggleDiscountInput(objElement){
            function toggleShow(element){
                var parent          = $(element).closest('div.form-group');
                var percentageAddon = parent.find('div.input-group-addon[data-role="percentage-code"]');
                var currencyAddon   = parent.find('div.input-group-addon[data-role="currency-code"]');
                var targetInput     = parent.find('input:text[data-role="discount-value-input"]');

                if($(element).val() == 'nominal'){
                    percentageAddon.hide();
                    currencyAddon.show();
                    targetInput.removeClass('at-right').addClass('at-left input_price');

                //  trigger price input
                    $.inputPrice({ obj : targetInput });
                }
                else if($(element).val() == 'percentage'){
                    percentageAddon.show();
                    currencyAddon.hide();
                    targetInput.removeClass('at-left input_price').addClass('at-right');

                    targetInput.replaceWith(targetInput.clone());
                    targetInput = parent.find('input:text[data-role="discount-value-input"]');

                //  trigger number input
                    var newValue = targetInput.val().replace(/,/g, '');
                    targetInput.val(newValue);
                }

                $.inputNumber({ obj : targetInput });
            }

            if(typeof objElement == 'object' && objElement.length > 0){
            //  toggle single
                toggleShow(objElement);
            }
            else{
            //  toggle all
                var elements = $('select[data-role="discount-type-selector"]');

                elements.each(function(){
                    toggleShow($(this));
                });
            }
        }

        voucherForm.on('change', 'select[data-role="discount-type-selector"]', function(){
            toggleDiscountInput($(this));
        });

        function checkCodeLength(){
            var txtCodeMechanism    = $('#VoucherCodeMechanism');
            var txtVoucherLength    = $('#VoucherLength');
            var txtVoucherPrefix    = $('#VoucherPrefix');
            var txtVoucherCode      = $('#VoucherCodeCode');

            if(txtCodeMechanism.val() == 'auto'){
                if(txtVoucherLength.length > 0 && txtVoucherPrefix.length > 0 && txtVoucherCode.length > 0){
                    var maxLength   = txtVoucherLength.val();
                        maxLength   = isNaN(maxLength) ? 0 : maxLength;
                    var codeLength  = txtVoucherPrefix.val().length + txtVoucherCode.val().length;

                    if(codeLength > maxLength){
                        var newCode = txtVoucherCode.val().substr(0, maxLength - txtVoucherPrefix.val().length);
                        txtVoucherCode.val(newCode);
                    }

                    txtVoucherCode.attr('maxlength', maxLength - txtVoucherPrefix.val().length);
                }
            }
            else{
                txtVoucherCode.removeAttr('maxLength');
            }
        }

        $('#VoucherCodeMechanism, #VoucherPeriodType, #VoucherApplyTo').change(function(){
            toggleInputView();
        });

        $('#VoucherLength, #VoucherPrefix').change(function(){
            checkCodeLength();
        });

        toggleInputView();
        checkCodeLength();
        btnVoucherSubmit.prop('disabled', false);
        toggleDiscountInput();

        btnAddMembership.click(function(e){
            e.preventDefault();
            var template    = packagePlaceholder.find('div.form-group:eq(1)').clone();
            var inputs      = template.find('select, input');
            var maxAllowed  = template.find('select:eq(0) option').length;
            var counter     = packagePlaceholder.find('div.form-group:gt(0)').length;

            $(template).find('input').val('');

            if(counter < maxAllowed){
                packagePlaceholder.append(template);
            }
            else{
                alert('Tidak bisa menambahkan detail lebih dari jumlah paket Membership.');
            }

            recountPackageForm();
        });

        packagePlaceholder.on('click', 'a[data-role="remove-membership"]', function(e){
            e.preventDefault();

            var self        = $(this);
            var parent      = self.closest('div.form-group');
            var packages    = packagePlaceholder.find('div.form-group:gt(0)');

            if(packages.length > 1){
                parent.remove();
                recountPackageForm();
            }
            else{
                alert('Tidak dapat menghapus daftar paket terakhir.');
            }
        });

        packagePlaceholder.on('change', 'select[data-role="membership-package-selector"]', function(e){
            e.preventDefault();
            var packageSelector = packagePlaceholder.find('select[data-role="membership-package-selector"]');
            var errorCounter    = 0;

            packageSelector.removeClass('form-error').each(function(){
                var selectedValue = this.value;

                packageSelector.not(this).filter(function(){
                    if(this.value == selectedValue){
                        errorCounter++;
                    };

                    return this.value == selectedValue;
                }).addClass('form-error');
            });

            if(errorCounter > 0){
            //  alert('Detail paket tidak boleh sama');
            }

            return !errorCounter;
        });

        function recountPackageForm(){
        //  skip package 1
            var packagePlaceholder  = $('#package-placeholder');
            var packages            = packagePlaceholder.find('div.form-group:gt(0)');

            if(packages.length > 0){
                packages.each(function(){
                    var self    = $(this);
                    var counter = $('#package-placeholder div.form-group').index(this);
                    var label   = self.find('label');
                    var inputs  = self.find('select, input');

                //  set numbering
                    label.html(counter);

                //  change attribute value
                    inputs.each(function(){
                        var input       = $(this);
                        var inputID     = input.attr('id').replace(/\d+/g, (counter - 1));
                        var inputName   = input.attr('name').replace(/\d+/g, (counter - 1));

                        input.attr({'id' : inputID, 'name' : inputName});
                    });
                });

                $('select[data-role="membership-package-selector"], select[data-role="discount-type-selector"]').trigger('change');
            }
        }
    }

//  submit doku handler
    var dokuForm = $('#doku-payment-form');
    if(dokuForm.length > 0){
        dokuForm.submit();
    } 


    var wrapper_add_activity = $('#wrapper-add-activity');
    if(wrapper_add_activity.length > 0){

        wrapper_add_activity.on('focus blur', 'input', function(e){

            var self = $(this);

            if(e.type == 'focusin' && self.prop('readOnly') == false){
                self.data('default_value', self.val());
            }
            else if(e.type == 'focusout' && self.prop('readOnly') == false){
                var defaultValue = self.data('default_value');
                var currentValue = self.val();

                if(defaultValue != currentValue){ 
                    var price_key           = self.attr('add-attribute');
                    var dp_bank_persen      = $('#hidden_dp_bank'+price_key).val();
                    var down_payment_view   = $('#KprApplicationRequest'+price_key+'DownPayment');
                    var price               = $('.KPR-price').val();

                    var down_payment        = currentValue.replace(/,/g,'');
                    down_payment            = parseInt(down_payment);
                    var dp_persen           = (down_payment / price)*100;
                    
                    if(dp_persen < dp_bank_persen){
                        alert('Uang muka harus >= '+dp_bank_persen+' %');
                        down_payment = defaultValue;
                        var down_payment        = defaultValue.replace(/,/g,'');
                        down_payment            = parseInt(down_payment);
                        down_payment_view.val(defaultValue);
                    }

                    var first_credit        = $('#hidden_first_credit'+price_key);
                    var interest_rate_fix   = $('#hidden_interest_rate_fix'+price_key).val();
                    var credit_total        = $('#hidden_credit_total'+price_key).val();
                    var cicilan_perbulan    = $('#value'+price_key);

                    var loan_price          = (price - down_payment);
                    var firstCredit         = creditFix(loan_price,interest_rate_fix,credit_total);
                    var firstCreditRound    = Math.round(firstCredit);
                    var first_credit_view   = tandaPemisahTitik(firstCreditRound);

                    first_credit.val(firstCredit);
                    cicilan_perbulan.html('<span id="value'+price_key+'" class="pay-btn"> Rp. '+first_credit_view + '</span>');

                }
            }
            else{
                return false;
            }
        });

        wrapper_add_activity.on('change', 'input:checkbox.check-option', function(e){
            
            var self = $(this);
            var checkbox_key = self.attr('add-attribute');
            var down_payment_value;
            var price_value;
            var dp_persen;
            var defaultValue;

            var checked = self.prop('checked');
            var parent = self.parents('.calculator-kpr-credit');

            var down_payment        = $('.down-payment-id-'+checkbox_key);
            var persen_loan         = $('.persen-loan-id-'+checkbox_key);
            var bank                = $('#hidden_bank_id'+checkbox_key);
            var price               = $('#KPR-price');
            var dp_bank_persen      = $('#hidden_dp_bank'+checkbox_key).val();
            var first_credit        = $('#hidden_first_credit'+checkbox_key);
            var interest_rate_fix   = $('#hidden_interest_rate_fix'+checkbox_key);
            var credit_total        = $('#hidden_credit_total'+checkbox_key);
            var commission          = $('#hidden_commission_id'+checkbox_key);
            var cicilan_perbulan    = $('#value'+checkbox_key);
            var findInput = 'input[type="text"],input[type="hidden"],select';

            if(checked){
                parent.find(findInput).prop('disabled',false);
                commission.prop('disabled',false);

                down_payment_value  = down_payment.val();

                var interest_rate_fix_value = interest_rate_fix.val();
                var credit_total_value  = credit_total.val();
                var price_value         = price.val();
                down_payment_value  = down_payment_value.replace(/,/g,'');
                dp_persen           = (down_payment_value / price_value)*100;
                
                if(dp_persen < dp_bank_persen){
                    alert('Uang muka harus >= '+dp_bank_persen+' %');
                    defaultValue    = (dp_bank_persen/100)*price_value;
                    
                    var loan_price      = price_value - defaultValue;
                    var firstCredit         = creditFix(loan_price,interest_rate_fix_value,credit_total_value);
                    var firstCreditRound    = Math.round(firstCredit);
                    var first_credit_view   = tandaPemisahTitik(firstCreditRound);
                    defaultValue    = Math.round(defaultValue);
                    defaultValue    = tandaPemisahTitik(defaultValue);
                    down_payment.val(defaultValue);

                    first_credit.val(firstCredit);
                    cicilan_perbulan.html('Rp. '+first_credit_view);
                }
            }else{
                parent.find(findInput).prop('disabled', true).attr('disabled', true);
                commission.prop('disabled',true);
            }
        });

        function creditFix(amount, rate, year){
            if( rate == 'undefined' ){
                return 0;
            } else {

                if( rate != 0 ) {
                    rate = (rate/100)/12;
                }
                var rateYear    = Math.pow((1+rate), (year*12));
                var rateMin     = (Math.pow((1+rate), (year*12))-1);

                if( rateMin != 0 ) {
                    rateYear    = rateYear / rateMin;
                }

                var mortgage    = rateYear * amount * rate; // rumus angsuran fix baru 
                return mortgage;
            }
        }

        function tandaPemisahTitik(b){  
            var _minus = false;
            if (b<0) _minus = true;
            b = b.toString();
            b=b.replace(",","");
            b=b.replace("-","");
            c = "";
            panjang = b.length;
            j = 0;
            for (i = panjang; i > 0; i--){
                 j = j + 1;
                 if (((j % 3) == 1) && (j != 1)){
                   c = b.substr(i-1,1) + "," + c;
                 } else {
                   c = b.substr(i-1,1) + c;
                 }
            }
            if (_minus) c = "-" + c ;
            return c;
        }

        
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

//  checkout payment handler
    var checkoutForm = $('#PaymentAdminCheckoutForm');
    if(checkoutForm.length > 0){
        var txtVoucherCode      = $('#PaymentVoucherCode');
        var txtInvoiceNumber    = $('#PaymentInvoiceNumber');
        var baseAmount          = $('#PaymentBaseAmount');
        var discountAmount      = $('#PaymentDiscountAmount');
        var totalAmount         = $('#PaymentTotalAmount');
        var btnCheckVoucher     = $('#PaymentCheckVoucher');
        var selPaymentChannel   = $('#PaymentPaymentChannel');
        var selTenor            = $('#PaymentTenor');
        var btnContinuePayment  = $('#btnContinuePayment');
        var chkUserAgreement    = $('#PaymentAgreement');

        if(txtVoucherCode.length > 0){
            txtVoucherCode.val('');
        }

        btnContinuePayment.click(function(){
            if(chkUserAgreement.prop('checked') == false){
                alert('Mohon centang pernyataan persetujuan jika Anda telah membaca dan setuju dengan Syarat dan Ketentuan yang berlaku di Prime System dan ingin melanjutkan pembayaran.');
                return false;
            }
            else{
                $(this).closest('form').submit();
            }
        });

        var checkVoucherJXHR;
        btnCheckVoucher.click(function(){
            var self        = $(this);
            var inputParent = txtVoucherCode.closest('div.form-group');

            txtVoucherCode.removeClass('form-error form-success');
            inputParent.find('div.error-message').remove();

            if(txtVoucherCode.val() != ''){
                if(checkVoucherJXHR){
                    checkVoucherJXHR.abort();
                }

                checkVoucherJXHR = $.ajax({
                    url     : '/vouchers/validateVoucher', 
                    type    : 'post', 
                    data    : 'code='+txtVoucherCode.val()+'&invoice='+txtInvoiceNumber.val(), 
                    success : function(data){
                        var objResult = $.parseJSON(data);

                        if(typeof(objResult) == 'object'){
                            var status  = typeof(objResult.status) != 'undefined' ? objResult.status : 'error';
                            var message = typeof(objResult.msg) != 'undefined' ? objResult.msg : 'Error';

                            if(status == 'success'){
                                var voucher         = typeof(objResult.data) != 'undefined' ? objResult.data : false;
                                var discountType    = typeof(voucher.discount_type) != 'undefined' ? voucher.discount_type : 'nominal';
                                var discountValue   = typeof(voucher.discount_value) != 'undefined' ? voucher.discount_value : 0;
                                var subTotal        = baseAmount.html().split(',').join('');

                                if(discountType == 'percentage'){
                                    discountValue = $.formatNumber(discountValue, 2);
                                    discountValue = (parseFloat(subTotal) / 100) * parseFloat(discountValue);
                                    discountValue = Math.floor(discountValue);
                                //  alert(discountValue);
                                }

                                var grandTotal = parseFloat(subTotal) - parseFloat(discountValue);
                                    grandTotal = grandTotal > 0 ? grandTotal : 0;

                                txtVoucherCode.addClass('form-success');
                                discountAmount.html(formatMoney(discountValue, 2));
                                totalAmount.html(formatMoney(grandTotal, 2));
                            }
                            else{
                                txtVoucherCode.addClass('form-error');
                                discountAmount.html(formatMoney(0, 2));
                                totalAmount.html(baseAmount.html());

                                inputParent.append('<div class="error-message">'+message+'</div>');
                            }
                        }
                        else{
                            return false;
                        }
                    }, 
                    error   : function(){
                        console.log('an error occured');
                    }
                });
            }

            return false;
        });

        selPaymentChannel.change(function(){
            var self = $(this);

            if(self.val() == '15'){
                selTenor.closest('div.form-group').removeClass('hide');
            }
            else{
                selTenor.closest('div.form-group').addClass('hide');
            }
        });
    }

//  setting launcher
    var launcherThemeForm = $('#LauncherThemeForm');
    if(launcherThemeForm.length > 0){
        var baseUrl     = $('#base_url').length > 0 ? $('#base_url').text() : '';
        var themeID     = $('#theme_id').length > 0 ? $('#theme_id').text() : '';
        var previewElem = $('#launcher-preview');
        var inputs      = launcherThemeForm.find('.colorPicker, #selButtonTop');
        var fileInputs  = launcherThemeForm.find('input:file');
        var bgType      = launcherThemeForm.find('#selBackgroundType');

    //  preview handler
        fileInputs.change(function(){
            var self        = $(this);
            var name        = self.attr('name');
            var targetUrl   = previewElem.attr('src');
            var targetName  = previewElem.attr('name');

        //  create temporary form
            if($('#tempForm').length == 0){
                $('body').append('<form action="'+targetUrl+'" method="post" target="'+targetName+'" id="tempForm"></form>');
            }

            if(this.files && this.files[0]){
                var reader = new FileReader();

                reader.onloadend = function(e){
                    var imageSource = e.target.result;

                    if($('input:hidden[name="'+name+'"]').length > 0){
                    //  re-upload
                        $('input:hidden[name="'+name+'"]').val(imageSource);
                    }
                    else{
                    //  generate new hidden input
                        $('#tempForm').append('<input type="hidden" name="'+name+'" value="'+imageSource+'"/>');
                    }

                //  create preview on left region
                    var parent              = self.closest('div.form-group');
                    var previewPlaceholder  = parent.prev('div.form-group.preview-img');
                    if(previewPlaceholder.length > 0){
                        var previewImage = previewPlaceholder.find('div.wrapper-img img');
                        previewImage.attr('src', imageSource).addClass('img-responsive');
                    }
                    else{
                        var previewPlaceholder = '<div class="form-group preview-img"><div class="wrapper-img"><img src="'+imageSource+'" class="img-responsive"/></div></div>';
                        parent.before(previewPlaceholder);
                    }

                    $('#tempForm').submit();
                }

                reader.readAsDataURL(this.files[0]);
            }
        });

        bgType.change(function(){
            var self = $(this);
            if(self.val() == 'color'){
                $('#bg-color-placeholder').removeClass('hide');
                $('#bg-img-placeholder').addClass('hide').find('input').val('');
            }
            else if(self.val() == 'image'){
                $('#bg-color-placeholder').addClass('hide').find('input').val('').next('span').find('.minicolors-swatch-color').removeAttr('style');
                $('#bg-img-placeholder').removeClass('hide');
            }

            inputs.trigger('change');
        });

        inputs.change(function(){
            var urlQuery        = '';
            var colorPickers    = launcherThemeForm.find('.colorPicker');
            var buttonPosition  = launcherThemeForm.find('#selButtonTop');

            colorPickers.each(function(){
                var self    = $(this);
                var field   = self.data('field');
                var color   = self.val().replace('#', '');

                if(field && color){
                    urlQuery += urlQuery == '' ? '' : '&';
                    urlQuery += field+'='+color;
                }
            });

            if(buttonPosition.val()){
                urlQuery += urlQuery == '' ? '' : '&';
                urlQuery += 'button_top='+buttonPosition.val(); 
            }

            var oldSource = previewElem.attr('src');
            var newSource = oldSource.split('?')[0]+'?'+urlQuery;

            previewElem.attr('src', newSource);

            if($('#tempForm').length > 0){
                var targetUrl = previewElem.attr('src');
                $('#tempForm').attr('action', targetUrl).submit();
            }
        });
    }

    $('.colorPicker, #changeFontSize, #changeFontType, #slideshowInterval, #limit_top_agent, #limit_property_list, #limit_property_popular, #limit_latest_news').change(function(){
        if( $('#iframe_preview').length > 0 ) {
            var color_target = $('.colorPicker');
            var root_url = $('#base_url').text();
            var text_url = '';
            var theme_id = $('#theme_id').text();
            for (var i = 0; i < color_target.length; i++) {
                if(typeof color_target[i].value != 'undefined' && color_target[i].value != ''){
                    var color_val = color_target[i].value;
                    text_url += color_target[i].getAttribute('data-field')+'='+color_val.replace('#', '')+'&';
                }
            };

            if( $('#changeFontSize').val() != ''){
                text_url += 'font_size='+$('#changeFontSize').val()+'&';
            }

            if( $('#changeFontType').val() != ''){
                text_url += 'font_type='+$('#changeFontType').val()+'&';
            }

            if( $('#slideshowInterval').val() != ''){
                text_url += 'slideshow_interval='+$('#slideshowInterval').val()+'&';
            }

            if( $('#limit_top_agent').val() != '' && $('#limit_top_agent').val() != undefined ){
                text_url += 'limit_top_agent='+$('#limit_top_agent').val()+'&';
            }

            if( $('#limit_property_list').val() != '' && $('#limit_property_list').val() != undefined ){
                text_url += 'limit_property_list='+$('#limit_property_list').val()+'&';
            }

            if( $('#limit_property_popular').val() != '' && $('#limit_property_popular').val() != undefined ){
                text_url += 'limit_property_popular='+$('#limit_property_popular').val()+'&';
            }

            if( $('#limit_latest_news').val() != '' && $('#limit_latest_news').val() != undefined ){
                text_url += 'limit_latest_news='+$('#limit_latest_news').val()+'&';
            }
            $('#iframe_preview').attr('src', root_url+'?flash=false&theme_id='+theme_id+'&'+text_url);
        }
    });

    $('.reset-form').click(function(){
        var self = $(this);
        
        var r = confirm("Apakah Anda yakin ingin mereset perubahan web Anda?");
        if (r == true) {
            self.parents('form').find('input, textarea, checkbox, select').val('');
            var dataForm    = self.parents('form');
            var urlAction   = dataForm.attr('action');

        //  tambahan untuk setting tema launcher
            dataForm.attr('action', urlAction + (urlAction.indexOf('?') > 0 ? '&' : '?') + 'reset=1');

            if($('.minicolors').length > 0){
                $('.minicolors').trigger('change');
            }

            dataForm.submit();
        }else{
            return false;
        }
    });

    $('.btn-save-confirm').click(function(){
        var msg = $(this).attr('data-msg');

        var r = confirm(msg);
        if (r == true) {
            return true;
        }else{
            return false;
        }
    });

    // TABS
//  if( $('.rku-tabs').length ) {
        var self = $('.rku-tabs');
        var action_type = $('#tabs_action_type').val();
        if( action_type != '' && typeof action_type != 'undefined' ) {
            $('.rku-tabs li').removeClass('active');
            $('.rku-tabs li a').removeClass('active');
            $('.rku-tabs').find('a[href="'+action_type+'"]').addClass('active').parent().addClass('active');
            
            if( action_type.indexOf("#") > -1 ) {
                $('.tab-handle').addClass('hide');
                $(action_type).removeClass('hide');
            } else {
                $('a[href="'+action_type+'"]').removeClass('hide');
            }
        }

        $(document).on('click', '.rku-tabs a', function(event){
            var self        = $(this);
            var parent      = self.closest('.rku-tabs');
            var redirect    = parent.attr('redirect');

            if(redirect == 'false' || redirect === false){
                event.preventDefault();

                var self    = $(this);
                var target  = self.attr('href');

                if($(target).length){
                    parent.find('li, li a').removeClass('active');
                    self.addClass('active').closest('li').addClass('active');

                    $(target).removeClass('hide').siblings('').addClass('hide');
                }
            }
        });

       /*
        if( $('.rku-tabs').attr('redirect') == 'false'  ) {
            $('.rku-tabs a').click(function(event){
                event.preventDefault();

                var self = $(this);
                var target = self.attr('href');

                $('.rku-tabs li').removeClass('active');
                $('.rku-tabs li a').removeClass('active');

                self.parents('li').addClass('active');
                self.addClass('active');

            //  $('.tab-handle').addClass('hide'); ????
            //  $(target).removeClass('hide')
            //  return false;

                $(target).removeClass('hide').siblings().addClass('hide');
            });
        }
        */
//  }

    $('.print-page').click(function(e){
        e.preventDefault();
        window.print();
    });

    $('.type-broschure-handle').change(function(){
        var self = $(this);

        var resolution = '1024x724';
        if(self.val() == 'potrait'){
            resolution = '724x1024';
        }

        $('.resolution-broschure').text(resolution);
    });

    if($('#interval-ajax-load').length > 0){
        var self = $('#interval-ajax-load');
        var url = self.attr('data-url');
        var interval = self.attr('data-interval');

        setInterval(function(){redirect_page(url)}, interval);
    }

    function redirect_page(url){
        window.location.href = url;
    }

    $('.change-period-request').change(function(){
    
    // $('body').on('click', function(e){
    //     e.preventDefault();
    // });

    // if ($(window).scrollTop() >= $(document).height() - $(window).height() - 700){
        
    });

    $('body').on('click', '.daterange-dasboard-custom', function(e){
        e.preventDefault();

        var self = $(this);
        var trigger_element = self.attr('trigger-element');
        $('.'+trigger_element).toggle();
    });

    if( $('.mapping-filter').length ) {

        $('.arrow-mapping').click(function(e){
            e.preventDefault();

            var self = $(this);
            var source_table = $('#'+self.attr('data-from')).find('table');
            var target_table = $('#'+self.attr('data-to')).find('table');
            var remove_hid = $.checkUndefined(self.attr('data-remove-hidden'), null);

            source_table.find('tbody tr').each(function(){
                var _self = $(this);
                var chk = _self.find('input[type="checkbox"]');

                if( chk.prop('checked') ) {
                    var chk_id = chk.val();
                    var field_name = $.checkUndefined(chk.attr('data-field-name'), null);

                    chk.prop('checked', false);

                    if( field_name != null && remove_hid == null ) {
                        _self.append('<input type="hidden" name="'+field_name+'['+chk_id+']" value="'+chk_id+'"/>');
                    } else if ( remove_hid == 'true' ) {
                        _self.find('input[type="hidden"]').remove();
                    }

                    target_table.find('tbody').append(_self);
                }
            });
            
            target_table.find('tbody tr').show();
        });

        $('.refine-keyword', $('.mapping-filter')).keyup(function(){
            var self = $(this);
            var wrapper = self.parents('.wrapper-filter-agent');

            var value = self.val().toLowerCase().trim();
            if( value == '' ) {
                wrapper.find('table tbody tr').show();
            } else {
                wrapper.find('table tbody tr').each(function(){

                    var _self = $(this);
                    var found = false;
                    _self.find('td').each(function(){
                        var _value = $(this).text().toLowerCase().trim();
                        if( _value.indexOf(value) > -1 ) {
                            found = true;
                            return;
                        }
                    }); 

                    if( found ) {
                        _self.show();
                    } else {
                        _self.hide();
                    }
                });
            }
        });

        $('.btnSaveMapping').click(function(e){
            e.preventDefault();
            var self = $(this);
            var wrapper = $('#'+self.attr('data-from'));
            var url = self.attr('href');
            var form = wrapper.find('.form-target');

            form.find('tbody tr .cb-checkmark input').prop('checked', true);
            form.attr('action', url);
            form.submit();
            form.find('tbody tr .cb-checkmark input').prop('checked', false);
        });     
    }
    
    $('.btn-multiple-select').click(function(){
        var self = $(this);
        var dataTarget = self.attr('data-target');

        $(dataTarget + ' option').attr('selected', 'selected');

        return true;
    });

    $('.handle-box-time').change(function(){
        var self = $(this);
        
        if(self.val() == 'directly'){
            $('.box-time').addClass('hide');
        }else{
            $('.box-time').removeClass('hide');
        }
    });

    $('.handle-type-template').change(function(){
        var self = $(this);
        
        if(self.val() == 'normal'){
            $('.box-template').addClass('hide');
        }else{
            $('.box-template').removeClass('hide');
        }
    });

    $('#other-field').change(function() {
        var self = $(this);
        var show = self.attr('data-show');

        if( self.val() == -1 ) {
            $(show).show();
        } else {
            $(show).hide();
            $(show).val('');
            $(show).removeClass('show');
        }
    });

    if($('.change-label').length > 0){
        $('.change-label').change(function(){
            var self = $(this);
            $.changeLabel(self);
        });
    }

    $('.trigger-toggle').click(function() {
        var self = $(this);
        var show = self.attr('data-show');
        var hide = self.attr('data-hide');

        if( self.is(':checked') ) {
            $(show).show();
            $(hide).hide();
        } else {
            $(show).hide();
            $(show).val('');
            $(show).removeClass('show');
            $(hide).show();
            $(hide).val('');
            $(hide).removeClass('hide');
        }
    });

    $('body').on('click', '#help #message .head a.min, #help #open-message', function(e){
        e.preventDefault();
        var opacity = parseInt($('#help #message').css('opacity'));
        opacity = ( opacity == 1 ) ? 0 : 1;
        if( opacity == 0 ) {
            $('#help #message').removeClass('open');
        } else {
            $('#help #message').addClass('open');
        }
    });

    $(document).ajaxStart(function(){
        $('button[type="submit"],button[type="button"]').attr('disabled', true);
        // $('a').attr('onClick', 'return false;').attr('disabled', true);
    });

    $(document).ajaxStop(function(){
        $('button[type="submit"],button[type="button"]').attr('disabled', false);
        // $('a').removeAttr('onClick').attr('disabled', false);
    });

    $('body').on('click', '.btnDeleteCurrentRow', function(e){
        e.preventDefault();

        var self = $(this);
        var count = $('.btnDeleteCurrentRow:visible').length;

        if( count == 1 ) {
            var selector_empty = self.attr('hide-on-empty');
            if( selector_empty != 'undefined' ) {
                $(selector_empty).hide();
            }
        } else {
            var target_parent_selector = self.attr('target-parent-selector');
            self.parents(target_parent_selector).remove();
        }
    });

    $('body').on('click', '.submit-visible', function(){
        var self = $(this);
        var target_selector = self.attr('target-selector');
        
        $(target_selector).not(':visible').remove();
    });

    var setFilterReportAttributes = function( self ) {

        var parent = self.parents('.form-group');
        var field = parent.find('.valueField');

        var filter_type = parent.find('.ddlFilterType').val();
        var filter_param = parent.find('.ddlFilterParamReport option:selected').val();
        var filter_condition = parent.find('.ddlFilterCondition:visible option:selected').val();
        var prefix_filter_type = filter_type.substring(0, 3).toLowerCase();

        if( filter_condition === undefined ) {
            filter_condition = 'match';
        }

        field.attr('name', 'data[FilterParam]['+prefix_filter_type+'|'+filter_param+'|'+filter_condition+'][]');
    }

    $('body').on('change', '.ddlFilterType, .ddlFilterCondition', function(){
        setFilterReportAttributes($(this));
    });

    $('body').on('change', '.ddlFilterParamReport', function(){
        var self = $(this);
        var parent = self.parents('.form-group');
        var value = self.find('option:selected').attr('optvalue');

        parent.find('.customFilter').hide();

        if( value < 2 ) {
            parent.find('.customFilter.directFilterText').show();
        } else if( value >= 2 && value < 6 ) {
            parent.find('.customFilter.directFilterNumeric').show();
        } else {
            parent.find('.customFilter.directFilter'+value).show();
        }

        setFilterReportAttributes(self);
    });

    $('body').on('click', '.btnAddReportFilter', function(e){
        e.preventDefault();

        var tmp = $('.adv-report').find('.template-filter-item').clone().removeClass('template-filter-item hide');
        $('.adv-report .basic').append(tmp);
    });

    $('.print-window').click(function(){
        var self = $(this); 
        var url = self.attr('href');
        var title = self.attr('title');
        var w = self.attr('data-width');
        var h = self.attr('data-height');
        
         // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) {
            newWindow.focus();
        }

        return false;
    });

    var slide_tour = $('[data-role="modal-slide-tour"]');
    var sign_integrated = $('[data-role="modal-sign-integrated"]');
    var modal_tour = $('#myModal');

    if( slide_tour.length > 0 ) {
        modal_tour.addClass('modal-tour');
        modal_tour.find('.modal-body').html(slide_tour.html());
        modal_tour.find('.modal-dialog').addClass('modal-md');
        modal_tour.find('.close').addClass('close-modal');
        slide_tour.remove();

        modal_tour.modal({
            show: true,
            backdrop: 'static',
        });
        modal_tour.find('.carousel-tour').carousel({
            interval: false,
            wrap: false,
        });
        $.carouselSwipe({
            objTarget: modal_tour,
        });

        modal_tour.find('.close-modal').click(function(){
            modal_tour.modal('hide');
            window.location.reload();
            return false;
        });

        $.ajax({
            url: '/ajax/slide_tour/',
            type: 'POST',
            success: function(response, status) {
                console.log('Berhasil membuka tour');

                return true;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    if( sign_integrated.length > 0 ) {
        modal_tour.addClass('modal-tour');
        modal_tour.find('.modal-body').html(sign_integrated.html());
        modal_tour.find('.modal-dialog').addClass('modal-md');
        sign_integrated.remove();

        modal_tour.modal({
            show: true,
            backdrop: 'static',
        });


        modal_tour.find('.close-modal').click(function(){
            modal_tour.modal('hide');
            return false;
        });

        $.ajax({
            url: '/ajax/sign_integrated/',
            type: 'POST',
            success: function(response, status) {
                console.log('Berhasil membuka form integrasi');

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    $('.trigger-change-currency').off('change');
    $('.trigger-change-currency').change(function(){
        var self = $(this);
        var target_label = self.data('target-label');
        var value = self.find('option:selected').text();
        var target = $(target_label);

        target.text(value);
    });

    if($('.trigger-change-currency').length > 0){
        $('.trigger-change-currency').trigger('change');
    }

    /*autorun dengan trigger event*/
    if($('.autorun').length > 0){
        var trigger_auto = $.checkUndefined($('.autorun').data('trigger-auto'), ''); 

        if(trigger_auto != ''){
            $('.autorun').trigger(trigger_auto);
        }
    }

    if($('.false-alert').length > 0){
        $('.false-alert').change(function(){
            var self        = $(this);
            var val         = self.val();
            var data_alert  = self.attr('data-alert');

            if(!self.is(':checked') && data_alert != ''){
                alert(data_alert);
            }
        });
    }

    // ===== Scroll to Top ==== 
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 500) {        // If page is scrolled more than 50px
            $('.scrollToTop').fadeIn(200);    // Fade in the arrow
        } else {
            $('.scrollToTop').fadeOut(200);   // Else fade out the arrow
        }
    });
    $('.scrollToTop').click(function() {      // When arrow is clicked
        $('body,html').animate({
            scrollTop : 0                       // Scroll to top of body
        }, 500);
    });

    if(isChildWindow()){
        var redirect    = getQueryParam('redirect');
        var closePopup  = getQueryParam('close_popup');

        if(redirect && closePopup){
            var objParent = window.opener;
            objParent.document.location = redirect;
            window.close();
        }
    }
});