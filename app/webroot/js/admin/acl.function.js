$(document).ready(function(){
	var aclInputs = $('select.acl-input');
	var aclJXHR;

	$('body').on('click', 'a.acl-controller-toggle', function(event){
		event.preventDefault();

		var toggler			= $(this);
		var targetURL		= toggler.attr('href');

		var acoID			= toggler.data('aco-id');
		var parentWrapper	= toggler.closest('td');
		var parentRow		= toggler.closest('tr[data-aco-id="' + acoID + '"]');

		if(parentRow.length && targetURL){
			var childSelector	= 'tr[data-parent-id="' + acoID + '"]';
			var childRows		= parentRow.closest('table').find(childSelector);

			var activeClass		= 'expanded';
			var animationSpeed	= 200;

			if(childRows.length){
				toggler.toggleClass(activeClass);
				childRows.toggleClass('hide', !toggler.hasClass(activeClass));
			}
			else{
			//	add loading class
				parentWrapper.addClass('loading');

				if(aclJXHR){
					aclJXHR.abort();
				}

				aclJXHR = $.ajax({
					method	: 'get', 
					url		: targetURL, 
					success	: function(response){
						var childRows = $(response).find(childSelector);

						if(childRows.length == 0){
							childRows = $(response).filter(childSelector);
						}

						if(childRows.length){
							if(parentRow.data('parent-id')){
							//	parent punya parent, berarti ini controller dalam plugin
								childRows.addClass('double-padding');
							}

							parentRow.after(childRows);

							toggler.addClass(activeClass);

							if(typeof $.ajaxLink == 'function'){
								$.ajaxLink();
							}

							if($('select.acl-input').length){
								$('select.acl-input').trigger('init');
							}

							console.log('wrapper replaced');
						}
						else{
							console.log('replacement not found');
						}

					//	remove loading class
						parentWrapper.removeClass('loading');
					}, 
					error	: function(jqXHR, textStatus, errorThrown){
						console.log('an error occured');
					}, 
				});
			}
		}
	});

	$('body').on('init click', 'select.acl-input', function(event){
		var self	= $(this);
		var value	= self.val();

		self.removeClass('inherit allow deny').addClass(value);
	});

	if($('select.acl-input').length){
		$('select.acl-input').trigger('init');
	}
});