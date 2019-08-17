/**
PrimeAddress editable input.
Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}

@class address
@extends abstractinput
@final
@example
<a href="#" id="address" data-type="address" data-pk="1">awesome</a>
<script>
$(function(){
	$('#address').editable({
		url: '/post',
		title: 'Enter city, street and building #',
		value: {
			city: "Moscow", 
			street: "Lenina", 
			building: "15"
		}
	});
});
</script>
**/
(function ($) {
	"use strict";
	
	var PrimeAddress = function (options) {
		this.init('prime_address', options, PrimeAddress.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(PrimeAddress, $.fn.editabletypes.abstractinput);

	$.extend(PrimeAddress.prototype, {
		/**
		Renders input from tpl

		@method render() 
		**/		
		render: function() {
			this.$input = this.$tpl.find('input');
		},
		
		/**
		Default method to show value in element. Can be overwritten by display option.
		
		@method value2html(value, element) 
		**/
		value2html: function(value, element) {
			if(!value) {
				$(element).empty();
				return; 
			}

			var html	= '';
			var prefix	= {'no' : 'No. ', 'rt' : 'RT. ', 'rw' : 'RW. '};

			$.each(value, function(key, val){
				if(typeof value[key] != 'undefined' && value[key].length){
					html+= html.length > 0 ? ' ' : '';

					if(typeof prefix[key] != 'undefined'){
						html+= prefix[key];
					}

					html+= $('<div>').text(value[key]).html();
				}
			});

			$(element).html(html); 
		},
		
		/**
		Gets value from element's html
		
		@method html2value(html) 
		**/		
		html2value: function(html) {		
		  /*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g. 
			editable({
				value: {
					city: "Moscow", 
					street: "Lenina", 
					building: "15"
				}
			});
		  */ 
		  return null;  
		},
	  
	   /**
		Converts value to string. 
		It is used in internal comparing (not for sending to server).
		
		@method value2str(value)  
	   **/
	   value2str: function(value) {
		   var str = '';
		   if(value) {
			   for(var k in value) {
				   str = str + k + ':' + value[k] + ';';  
			   }
		   }
		   return str;
	   }, 
	   
	   /*
		Converts string to value. Used for reading value from 'data-value' attribute.
		
		@method str2value(str)  
	   */
	   str2value: function(str) {
		   /*
		   this is mainly for parsing value defined in data-value attribute. 
		   If you will always set value by javascript, no need to overwrite it
		   */
		   return str;
	   },				
	   
	   /**
		Sets value of input.
		
		@method value2input(value) 
		@param {mixed} value
	   **/		 
		value2input: function(value){
			if(!value){
				return;
			}

			this.$input.filter('[data-role="address"]').val(value.address);
			this.$input.filter('[data-role="no"]').val(value.no);
			this.$input.filter('[data-role="rt"]').val(value.rt);
			this.$input.filter('[data-role="rw"]').val(value.rw);
		}, 
	   
	   /**
		Returns value of input.
		
		@method input2value() 
	   **/		  
		input2value: function() { 
			return {
				address	: this.$input.filter('[data-role="address"]').val(), 
				no		: this.$input.filter('[data-role="no"]').val(), 
				rt		: this.$input.filter('[data-role="rt"]').val(), 
				rw		: this.$input.filter('[data-role="rw"]').val(),
			};
		},

		/**
		Activates input: sets focus on the first field.
		
		@method activate() 
	   **/		
	   activate: function() {
			this.$input.filter('[data-role="address"]').focus();
	   },  
	   
	   /**
		Attaches handler to submit form in case of 'showbuttons=false' mode
		
		@method autosubmit() 
	   **/	   
	   autosubmit: function() {
		   this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
		   });
	   }	   
	});

	var inputTemplate =	'<div class="editable-address">';
		inputTemplate+= 	'<div class="form-control">';
		inputTemplate+= 		'<div class="row">';
		inputTemplate+= 			'<div class="col-xs-12 col-md-6">';
		inputTemplate+= 				'<input type="text" class="form-control input-sm" data-role="address" placeholder="Nama Jalan">';
		inputTemplate+= 			'</div>';
		inputTemplate+= 			'<div class="col-xs-4 col-md-2 no-pleft">';
		inputTemplate+= 				'<input type="text" class="form-control input-sm" data-role="no" placeholder="Nomor">';
		inputTemplate+= 			'</div>';
		inputTemplate+= 			'<div class="col-xs-4 col-md-2 no-pleft">';
		inputTemplate+= 				'<input type="text" class="form-control input-sm" data-role="rt" placeholder="RT">';
		inputTemplate+= 			'</div>';
		inputTemplate+= 			'<div class="col-xs-4 col-md-2 no-pleft">';
		inputTemplate+= 				'<input type="text" class="form-control input-sm" data-role="rw" placeholder="RW">';
		inputTemplate+= 			'</div>';
		inputTemplate+=			'</div>';
		inputTemplate+=		'</div>';
		inputTemplate+=	'</div>';

	PrimeAddress.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl			: inputTemplate,
		inputclass	: '', 
	});

	$.fn.editabletypes.prime_address = PrimeAddress;

}(window.jQuery));

/**
PrimeLocation (dropdown)

@class prime_location
@extends list
@final
@example
<a href="#" id="status" data-type="prime_location" data-pk="1" data-url="/post" data-title="PrimeLocation status"></a>
<script>
$(function(){
	$('#status').editable({
		value: 2,	
		source: [
			  {value: 1, text: 'Active'},
			  {value: 2, text: 'Blocked'},
			  {value: 3, text: 'Deleted'}
		   ]
	});
});
</script>
**/
(function ($) {
	"use strict";
	
	var PrimeLocation = function (options) {
		this.init('prime_location', options, PrimeLocation.defaults);
	};

	$.fn.editableutils.inherit(PrimeLocation, $.fn.editabletypes.abstractinput);

	$.extend(PrimeLocation.prototype, {
		render : function(){
			this.$input = this.$tpl.find(':input');
		},
		value2html : function(value, element){
			var regions	= typeof regions == 'undefined' ? [] : regions;
			var cities	= typeof cities == 'undefined' ? [] : cities;
			var areas	= typeof areas == 'undefined' ? [] : areas;

			if(!value){
				$(element).empty();
				return; 
			}

			var html = [];

			if(value.subarea_name){
				html.push(value.subarea_name);
			}

			if(value.city_name){
				html.push(value.city_name);
			}

			if(value.region_name){
				html.push(value.region_name);
			}

		//	join area
			html = html.join(', ');

			if(value.zip){
				html+= '. ' + value.zip;
			}

			$(element).html(html);
		},
		html2value: function(html){
			return null;  
		},
		value2str: function(value){
			var str = '';

			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}

			return str;
		}, 
		str2value: function(str){
			return str;
		},				
		value2input: function(value){
			if(!value){
				return;
			}

			this.$input.filter('[data-role="region-input"]').val(value.region_id);
			this.$input.filter('[data-role="city-input"]').val(value.city_id);
			this.$input.filter('[data-role="subarea-input"]').val(value.subarea_id);
			this.$input.filter('[data-role="zip-input"]').val(value.zip);
		},
		input2value : function(){
			var inputRegion		= this.$input.filter('[data-role="region-input"]');
			var inputCity		= this.$input.filter('[data-role="city-input"]');
			var inputSubarea	= this.$input.filter('[data-role="subarea-input"]');
			var inputZip		= this.$input.filter('[data-role="zip-input"]');

			var value = {
				region_id		: inputRegion.val(),
				region_name		: inputRegion.find('option:selected').text(),
				city_id			: inputCity.val(),
				city_name		: inputCity.find('option:selected').text(),
				subarea_id		: inputSubarea.val(),
				subarea_name	: inputSubarea.find('option:selected').text(),
				zip				: inputZip.val(),
			};

			return value;
		},
		autosubmit: function() {
			this.$input.off('keydown.editable').on('change.editable', function(){
				$(this).closest('form').submit();
			});
		}
	});

	var inputTemplate =	'<div>';
		inputTemplate+=		'<select class="form-control input-sm" data-role="region-input"></select>';
		inputTemplate+=		'<select class="form-control input-sm" data-role="city-input"></select>';
		inputTemplate+=		'<select class="form-control input-sm" data-role="subarea-input"></select>';
		inputTemplate+=		'<input type="text" class="form-control input-sm" data-role="zip-input" placeholder="Kode Pos"/>';
		inputTemplate+=	'</div>';

	PrimeLocation.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl : inputTemplate,
	});

	$.fn.editabletypes.prime_location = PrimeLocation;	  

}(window.jQuery));