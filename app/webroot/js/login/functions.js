function changeLabelForm ( obj ) {
    var parent = obj.parents('.form-group');
    var value = obj.val();

    if( value != '' ) {
        parent.children('label').addClass('focus');
    } else {
        parent.children('label').removeClass('focus');
    }
}

function getMobileOS() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

      // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "windows-phone";
    }

    if (/android/i.test(userAgent)) {
        return "android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "ios";
    }

    return "unknown";
}

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

$(function(){
	var objInput = $('.login-form .form-group input');

	objInput.each(function( index ) {
		changeLabelForm( $( this ) );
	});

	objInput.blur(function(e) {
		changeLabelForm( $( this ) );
	});

	var loginBody		= $('body.login');
	var loginWrapper	= $('.login-wrapper');

	$(window).on('resize', function(){
		var windowHeight = $(this).innerHeight();

		if(windowHeight > loginWrapper.height()){
			loginBody.css({
				'height': windowHeight, 
			});

			loginWrapper.css({
				'top'			: windowHeight / 2, 
				'margin-top'	: '-' + (loginWrapper.height() / 2) + 'px', 
			});
		}
		else{
			loginBody.css({
				'height': 'initial', 
			});

			loginWrapper.css({
				'top'			: 'initial', 
				'bottom'		: 'initial', 
				'margin-top'	: 30, 
			});
		}
	}).trigger('resize');

	loginBody.fadeIn();
	loginWrapper.fadeIn();

	var mobileOS		= getMobileOS();
	var downloadButton	= $('.download-btn.btn-' + mobileOS);
	var closeButton		= $('.download-wrapper-close');

	if(downloadButton.length){
		downloadButton.toggleClass('hide');
	}

	if(closeButton.length){
		closeButton.on('click', function(event){
			$('.download-wrapper').toggleClass('hide');
		});
	}

    if(isChildWindow()){
    	var redirect	= getQueryParam('redirect');
    	var closePopup	= getQueryParam('close_popup');

    	if(redirect && closePopup){
    		var objParent = window.opener;
			objParent.document.location = redirect;
			window.close();
    	}
    }
});