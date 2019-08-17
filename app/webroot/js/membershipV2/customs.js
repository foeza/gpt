(function ( $ ) {

	var trigger_display = $('.trigger-display');

	if(trigger_display.length > 0){
		trigger_display.click(function(){
			var self = $(this);
			var data_target = self.data('target');
			var data_text = self.data('text');
			var data_close = self.attr('data-close');

			if(data_close > 0){
				// $(data_target).fadeOut();
				self.parent('.featureList').find(data_target).fadeOut();
				self.removeAttr('data-close');

				self.parent('.featureList').find(data_text).html('Lihat');
				// $(data_text).html('Lihat');
			} else {
				// $(data_target).fadeIn();
				self.parent('.featureList').find(data_target).fadeIn();
				self.attr('data-close', 1);
				self.parent('.featureList').find(data_text).html('Tutup');
				// $(data_text).html('Tutup');
			}
		});
	}

}( jQuery ));