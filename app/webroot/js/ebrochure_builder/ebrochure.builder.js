if(typeof fabric == 'object'){
//	fabricjs extensions
	fabric.Canvas.prototype.getItemByAttr = function(attr, value){
		var object	= null;
		var objects	= this.getObjects();

		for(var i = 0, len = this.size(); i < len; i++){
		//	if(objects[i][attr] && objects[i][attr] === value){
			if(objects[i][attr] && objects[i][attr] == value){
				object = objects[i];
				break;
			}
		}

		return object;
	};

	function notify(message, options){
		if(message && typeof $.fn.notify == 'function'){
			var config = {
				'className'			: 'info', 
				'globalPosition'	: 'top center', 
			};

			if(typeof options == 'string'){
				config = $.extend(config, {
					'className' : options, 
				});
			}
			else if($.inArray(typeof options, ['object', 'array'])){
				config = $.extend(config, options);
			}

			$.notify(message, config);
		}
	}

	function renderError(errors){
		if(typeof $.fn.notify == 'function'){
			if(typeof errors == 'object' && Object.keys(errors).length){
				$.each(errors, function(field, errorCollections){
					$.each(errorCollections, function(index, error){
						notify(error, 'error');
					});
				});
			}
		}
	}

//	disable object blurring (because of cache), but can be overkill when many object
//	fabric.Object.prototype.objectCaching = false;

//	keep stroke width on object scaling
	fabric.Object.prototype._renderStroke = function(ctx){
		if(!this.stroke || this.strokeWidth === 0){
			return;
		}

		if(this.shadow && !this.shadow.affectStroke){
			this._removeShadow(ctx);
		}

		ctx.save();
		ctx.scale(1 / this.scaleX, 1 / this.scaleY);

		this._setLineDash(ctx, this.strokeDashArray, this._renderDashedStroke);
		this._applyPatternGradientTransform(ctx, this.stroke);

		ctx.stroke();
		ctx.restore();
	};

	var canvas			= new fabric.Canvas('canvas', { backgroundColor : '#FFFFFF' });
	var canvasScale		= 1;
	var scaleFactor		= 1 + (2 / 10);
	var controlOptions	= {
		primaryColor	: '#069D54', 
		cornerStyle		: 'circle', 
		cornerPadding	: 5, 
		cornerSize		: 10, 
	};

	var defaultOrientation	= 'landscape';
	var defaultWidth		= 1024;
	var defaultHeight		= 768;

	window.canvasOptions = {
		defaultText			:'Lorem Ipsum Dolor Sit Amet', 
		fontFamily			: 'Times New Roman', 
		fontColor			: '#333333', 
		fontSize			: 24, 
		step				: 5, 
		scale				: 1, 
		currentStep			: 5, 
		currentScale		: 1, 
		currentOrientation	: defaultOrientation, 
		fill				: '#FFFFFF', 
		stroke				: '#666666', 
		strokeWidth			: 1, 
		predefinedID		: [
			'prime-property-photo', 
			'prime-property-type', 
			'prime-property-action', 
			'prime-property-id', 
			'prime-property-title', 
			'prime-property-keyword', 
			'prime-property-description', 
			'prime-property-price', 
			'prime-property-specification', 
			'prime-property-location', 
			'prime-agent-photo', 
			'prime-agent-name', 
			'prime-agent-phone', 
			'prime-agent-email', 
			'prime-company-logo', 
			'prime-company-name', 
			'prime-company-phone', 
			'prime-company-email', 
			'prime-company-address', 
			'prime-company-domain', 
		], 
	};

	window.canvasHistory = {
		canvasState			: [],
		currentStateIndex	: -1,
		undoStatus			: false,
		redoStatus			: false,
		undoFinishedStatus	: 1,
		redoFinishedStatus	: 1,
		renderStatus		: 1, 
		undoButton			: document.getElementById('undo'),
		redoButton			: document.getElementById('redo'),
	};

//	helper
	if(typeof initCenteringGuidelines == 'function'){
		initCenteringGuidelines(canvas);
	}

	if(typeof initAligningGuidelines == 'function'){
		initAligningGuidelines(canvas);
	}

//	global canvas object options
	fabric.Object.prototype.set({
	//	preserveObjectStacking	: true, 
		transparentCorners		: false,
		centeredRotation		: true, 
		centerTransform			: true, 
		snapAngle				: 10, 
		snapThreshold			: 10, 
		borderColor				: controlOptions.primaryColor,
		cornerColor				: controlOptions.primaryColor, 
		cornerStrokeColor		: controlOptions.primaryColor, 
		cornerStyle				: controlOptions.cornerStyle, 
		cornerSize				: controlOptions.cornerSize, 
		padding					: controlOptions.cornerPadding, 
	});

	var contextMenu	= $('#context-menu');
	var appObject	= function(){
		return {
			__canvas			: canvas,
			__contextMenu		: contextMenu, 
			__contextOffset		: { top : 0, left : 0 }, 
			__tmpgroup			: {},
			__options			: {}, 
			__isIdevice			: /(iPad|iPhone|iPod)/g.test(navigator.userAgent),
			__customAttributes	: ['prime_id', 'prime_name', 'original_width', 'original_height', 'original_scaleX', 'original_scaleY'], 
			__clipboard			: null, 

		//	functionality here
			generateID : function(){
				var date = new Date();

				return date.getTime().toString().substr(5);
			}, 
			setOptions : function(){
				
			}, 
			getSelection : function(){
				return this.__canvas.getActiveObject() || this.__canvas.getActiveGroup();
			}, 
			getActiveProp : function(name){
				var object = this.__canvas.getActiveObject();

				return object ? object[name] || '' : '';
			}, 
			setActiveProp(name, value){
				var object = this.__canvas.getActiveObject();

				if(!object) return;

				object.set(name, value).setCoords();
				this.__canvas.renderAll();
			}, 
			getActiveStyle : function(styleName){
				var object		= this.__canvas.getActiveObject();
				var activeStyle	= '';

				if(object){
					if(object.getSelectionStyles && object.isEditing){
						activeStyle = object.getSelectionStyles()[styleName] || '';
					}
					else{
						activeStyle = object[styleName] || '';
					}
				}

				return activeStyle;
			}, 
			setActiveStyle : function(styleName, value, object){
				object = object || this.__canvas.getActiveObject();

				if(object){
					if(object.setSelectionStyles && object.isEditing){
						var style = {};

						style[styleName] = value;
						object.setSelectionStyles(style).setCoords();
					}
					else{
						object.set(styleName, value);
					}

					this.__canvas.renderAll();
					this.toggleToolbox(object);
				}
			}, 
			addRect : function(){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth		= canvas.width / 2;
				var halfHeight		= canvas.height / 2;
				var quarterHeight	= canvas.height / 4;

				var object = new fabric.Rect({
					prime_id	: objectID, 
					prime_name	: 'rectangle-layer-' + objectID, 
					width		: quarterHeight, 
					height		: quarterHeight, 
					fill		: window.canvasOptions.fill, 
					stroke		: window.canvasOptions.stroke,
					strokeWidth	: window.canvasOptions.strokeWidth, 
					top			: halfHeight, 
					left		: halfWidth, 
				}).setCoords();

				canvas.add(object);
			//	canvas.centerObject(object);
				canvas.setActiveObject(object);
			}, 
			addCircle : function(){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth		= canvas.width / 2;
				var halfHeight		= canvas.height / 2;
				var quarterHeight	= canvas.height / 4;

				var object = new fabric.Circle({
					prime_id	: objectID, 
					prime_name	: 'circle-layer-' + objectID, 
					radius		: quarterHeight / 2, 
					fill		: window.canvasOptions.fill, 
					stroke		: window.canvasOptions.stroke,
					strokeWidth	: window.canvasOptions.strokeWidth, 
					top			: halfHeight, 
					left		: halfWidth, 
				}).setCoords();

				canvas.add(object);
			//	canvas.centerObject(object);
				canvas.setActiveObject(object);
			}, 
			addTriange : function(){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth		= canvas.width / 2;
				var halfHeight		= canvas.height / 2;
				var quarterHeight	= canvas.height / 4;

				var object = new fabric.Triangle({
					prime_id	: objectID, 
					prime_name	: 'triangle-layer-' + objectID, 
					width		: quarterHeight,
					height		: quarterHeight,
					fill		: window.canvasOptions.fill, 
					stroke		: window.canvasOptions.stroke,
					strokeWidth	: window.canvasOptions.strokeWidth, 
					top			: halfHeight, 
					left		: halfWidth, 
				}).setCoords();

				canvas.add(object);
			//	canvas.centerObject(object);
				canvas.setActiveObject(object);
			}, 
			addLine : function(){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth	= canvas.width / 2;
				var halfHeight	= canvas.height / 2;

				var object = new fabric.Line([halfWidth, 100, 150, 100], {
					prime_id	: objectID, 
					prime_name	: 'line-layer-' + objectID, 
					stroke		: window.canvasOptions.stroke,
					strokeWidth	: window.canvasOptions.strokeWidth, 
					top			: halfHeight, 
					left		: halfWidth, 
				}).setCoords();

				canvas.add(object);
			//	canvas.centerObject(object);
				canvas.setActiveObject(object);
			}, 
			addText : function(){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth	= canvas.width / 2;
				var halfHeight	= canvas.height / 2;

			//	var object = new fabric.IText(window.canvasOptions.defaultText, {
				var object = new fabric.Textbox(window.canvasOptions.defaultText, {
					prime_id		: objectID, 
					prime_name		: 'text-layer-' + objectID, 
					fontFamily		: window.canvasOptions.fontFamily,
					fill			: window.canvasOptions.fontColor, 
					fontSize		: window.canvasOptions.fontSize, 
					strokeWidth		: 0, 
					objectCaching	: false, 
					top				: halfHeight, 
					left			: halfWidth, 
				}).setCoords();

				canvas.add(object);
			//	canvas.centerObject(object);
				canvas.setActiveObject(object);

			//	force default font here
			//	this.toggleFontStyle('fontFamily', window.canvasOptions.fontFamily);
			},
			addImage : function(source, options){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth	= canvas.width / 2;
				var halfHeight	= canvas.height / 2;

			//	fabric.util.loadImage(source, function(img){
			//		var image = new fabric.Image(img);
			//		var scale = 300 / image.width;

			//		options = $.extend({
			//			prime_id	: objectID, 
			//			prime_name	: 'image-layer-' + objectID, 
			//			scaleX		: scale * canvasScale, 
			//			scaleY		: scale * canvasScale, 
			//			top			: halfHeight, 
			//			left		: halfWidth, 
			//		}, options);

			//		image.set(options).setCoords();

			//		canvas.add(image);
			//	//	canvas.centerObject(image);
			//		canvas.setActiveObject(image);
			//	}, null);

				fabric.Image.fromURL(source, function(image){
					var element	= image.getElement();

					if($(element).is('img')){
						var scale = 300 / image.width;

						options = $.extend({
							prime_id	: objectID, 
							prime_name	: 'image-layer-' + objectID, 
							scaleX		: scale * canvasScale, 
							scaleY		: scale * canvasScale, 
						}, options);

						image.set(options).setCoords();

						canvas.add(image);
					//	canvas.centerObject(image);
						canvas.setActiveObject(image);
					}
					else{
						alert('failed to load image');
					}
				}.bind(this), {
					crossOrigin : 'anonymous', 
				});
			}, 
			addShape : function(source){
				var canvas		= this.__canvas;
				var objectID	= this.generateID();

				var halfWidth	= canvas.width / 2;
				var halfHeight	= canvas.height / 2;

				fabric.loadSVGFromURL(source, function(objects, options){
					var shape = fabric.util.groupSVGElements(objects, options);
					var scale = 300 / shape.width;

					shape.set({
						prime_id	: objectID, 
						prime_name	: 'shape-layer-' + objectID, 
						scaleX		: scale * canvasScale, 
						scaleY		: scale * canvasScale, 
						top			: halfHeight, 
						left		: halfWidth, 
					}).setCoords();

					canvas.add(shape);
				//	canvas.centerObject(shape);
					canvas.setActiveObject(shape);
				});
			}, 
			setTextValue : function(value){
				var object = this.__canvas.getActiveObject();

				if(object){
					object.setText(value);
					this.__canvas.renderAll();
				}
			},
			resetRotation : function(object){
				var object = this.__canvas.getActiveObject();

				if(object){
					var objTop	= object.top;
					var objLeft	= object.left;

					object.set({
						'angle'	: 0, 
						'top'	: objTop, 
						'left'	: objLeft, 
					}).setCoords();

					this.__canvas.renderAll();
				}
			}, 
			removeSelected : function(){
				var activeObjects = this.__canvas.getActiveObjects();

				if(activeObjects.length){
					var selectedID = [];

					$.each(activeObjects, function(index, object){
						var primeID = object.prime_id || null;

						if(primeID){
							selectedID.push(primeID);
						}
					});

					// if($.inArray('prime-company-logo', selectedID) > -1){
					// 	notify('Logo Perusahaan tidak dapat dihapus', 'warn');
					// }
					// else if($.inArray('prime-copyright', selectedID) > -1){
					// 	notify('Logo Prime System tidak dapat dihapus', 'warn');
					// }
					// else{
						if(confirm('Apa Anda yakin?')){
							this.__canvas.discardActiveObject();
							this.__canvas.remove.apply(this.__canvas, activeObjects);
						}
					// }
				}
			}, 
			removeAll : function (){
				if(confirm('Apa Anda yakin?')){
					this.__canvas.clear();
				}
			}, 
			deselectAll : function(){
				var activeObjects = this.__canvas.getActiveObjects();

				if(activeObjects.length){
					this.__canvas.discardActiveObject().renderAll();
				}
			}, 
			setCanvasDimension : function(value){
				var isValid	= value == 'landscape' || value == 'potrait';
					value	= isValid ? value : defaultOrientation;

			//	var wrapperScale	= $('#canvas-wrapper').data('scale') || 1;
				var canvasWidth		= defaultWidth * window.canvasOptions.currentScale;
				var canvasHeight	= defaultHeight * window.canvasOptions.currentScale;
				var newWidth		= canvasWidth;
				var newHeight		= canvasHeight;

			//	if((value == 'landscape' && this.__canvas.getWidth() < this.__canvas.getHeight()) || (value == 'potrait' && this.__canvas.getWidth() > this.__canvas.getHeight())){
			//	//	switch value
			//		canvasWidth		= defaultHeight * wrapperScale;
			//		canvasHeight	= defaultWidth * wrapperScale;
			//	}

				var isWidthGreater = canvasWidth > canvasHeight;

				if(value == 'landscape'){
					newWidth	= isWidthGreater ? canvasWidth : canvasHeight;
					newHeight	= isWidthGreater ? canvasHeight : canvasWidth;
				}
				else{
					newWidth	= isWidthGreater ? canvasHeight : canvasWidth;
					newHeight	= isWidthGreater ? canvasWidth : canvasHeight;
				}

				if($('#canvas-wrapper').length){
					$('#canvas-wrapper').removeClass('landscape potrait').addClass(value).css({
						'width'		: newWidth, 
						'height'	: newHeight, 
					});

					if($('#CanvasOrientation').length){
						$('#CanvasOrientation').val(value);
					}
				}

				this.__canvas.setDimensions({
					width	: newWidth, 
					height	: newHeight, 
				});

				this.__canvas.renderAll().trigger('canvas:modified');
				this.updateCopyrightPosition();

			//	console.log(value);
			//	console.log({
			//		width	: newWidth, 
			//		height	: newHeight, 
			//	});

				window.canvasOptions.currentOrientation = value;
			}, 
			updateCopyrightPosition : function(){
			//	special case for copyright (always at bottom)
				var copyright = this.__canvas.getItemByAttr('prime_id', 'prime-copyright');

				if(copyright){
					copyright.set({
						top : this.__canvas.height - (copyright.height + 10) * window.canvasOptions.currentScale, 
					});

					copyright.setCoords();
				}

				this.__canvas.renderAll();
			}, 
			rasterize : function(multiplier){
				if(!fabric.Canvas.supports('toDataURL')){
				//	alert('This browser doesn\'t provide means to serialize canvas to an image');
					notify('Browser Anda tidak mendukung operasi generate gambar dari kanvas', 'warn');
				}
				else{
					var options = {
						'format' : 'png', 
					};

					if(typeof multiplier != 'undefined'){
						options = $.extend(options, { multiplier : multiplier });
					}
					else{
						var orientation, width, height;

						if(this.__canvas.width == this.__canvas.height){
							orientation = 'box';
							width		= 1024;
							height		= 1024;
						}
						else{
							orientation = this.__canvas.width > this.__canvas.height ? 'landscape' : 'potrait';
							width		= orientation == 'landscape' ? 1024 : 768;
							height		= orientation == 'landscape' ? 768 : 1024;
						}

						options = $.extend(options, {
							width	: width, 
							height	: height, 
						});
					}

					return this.__canvas.toDataURL(options);
				}
			}, 
			rasterizeJSON : function(){
				var json = this.__canvas.toJSON(this.__customAttributes);

				return JSON.stringify(json);
			}, 
			loadJSON : function(jsonString, callback){
				var app			= this;
				var canvas		= app.__canvas;
				var forceRender	= true;
				var jsonObjects = {};

			//	before render
			//	console.log('canvas rendering');
				canvas.trigger('canvas:rendering');
				window.canvasHistory.renderStatus = 0;

				if(typeof WebFont == 'object'){
					try{
						jsonObjects = typeof jsonString == 'object' ? jsonString : $.parseJSON(jsonString);
					}
					catch(e){
						console.log('error parsing json : ' + e);
					}

					if(typeof jsonObjects == 'object' && Object.keys(jsonObjects).length){
						var objects = jsonObjects.objects || null;

						if(objects){
							var fonts = [];

							$.each(objects, function(index, object){
							//	console.log(index + ' ---- ' + object.type + ' ---- ' + object.fontFamily);

								if($.inArray(object.type, ['text', 'i-text', 'textbox']) > -1){
									var fontFamily = object.fontFamily || null;

									if(fontFamily && $.inArray(fontFamily, fonts) == -1 && fontFamily != window.canvasOptions.fontFamily){
										fonts.push(fontFamily)
									};
								}
							});

						//	console.log(fonts);

							if(fonts.length){
								WebFont.load({
									google		: { families : fonts }, 
									active		: function(){
									//	font loaded
									//	console.log(fonts.join(', ') + ' loaded');

										loadFromJson(jsonString, callback);
									}, 
									inactive	: function(){
									//	load anyway if load font failed
									//	console.log(fonts.join(', ') + ' failed to load');

										loadFromJson(jsonString, callback);
									}
								});

								forceRender = false;
							}

						//	console.log('fonts to be loaded : ' + fonts.join(', '));
						}
					}
				}

				if(jsonObjects && typeof jsonObjects == 'object'){
					var orientation = jsonObjects.orientation || 'landscape';

					app.setCanvasDimension(orientation);
				}

				if(forceRender){
				//	fallback if load font above failed
				//	console.log('forced render');

					loadFromJson(jsonString, callback);
				}
			}, 
			toggleToolbox : function(object){
				object = object || this.__canvas.getActiveObject();

				var objToolbox			= $('.toolbox-wrapper');
				var objToolboxInputs	= objToolbox.find(':input[data-role="text-command"]');

				if(objToolbox.length){
					$('body').toggleClass('toolbox-show', object ? true : false);

					objToolbox.toggleClass('toolbox-image', object && $.inArray(object.type, ['text', 'i-text', 'textbox']) < 0);

					if(object && objToolboxInputs.length){
					//	set toolbox value (based on selected object)
						var validStyles = ['textAlign', 'textDecoration', 'fontStyle', 'fontWeight', 'fontFamily', 'strokeWidth', 'fontSize', 'opacity', 'fill'];

						for(i = 0; i < validStyles.length; i++){
							var styleName	= validStyles[i];
							var styleValue	= object[styleName];

							var targetInputs = objToolboxInputs.filter(function(){
								return $(this).data('type') == styleName;
							});

							if(targetInputs.length){
								targetInputs.each(function(){
									var targetInput = $(this);

									if(targetInput.is('button')){
										targetInput.toggleClass('active', styleValue == targetInput.val());
									}
									else{
										targetInputs.val(styleValue);		
									}
								});
							}

						//	console.log(styleName + ' : ' + styleValue);
						}
					}
				}

			//	set config on left panel
				this.setObjectConfig(object);
			}, 
			setObjectConfig : function(object){
				object = object || this.__canvas.getActiveObject();

				var objConfigPanel	= $('#config-panel');
				var objConfigInputs	= objConfigPanel.find(':input.layer-config-input');
				var generalType		= null;

				if(object){
					if($.inArray(object.type, ['text', 'i-text', 'textbox']) > -1){
						generalType = 'text';
					}
					else{
					//	shape, rectangle, circle, triangle, image goes here
						generalType = 'other';
					}
				}

			//	var isValidObject	= object && $.inArray(object.type, ['text', 'i-text', 'textbox']) > -1;

			//	if(isValidObject && objConfigPanel.length && objConfigPanel.find('input[data-role="layer-config-input"]')){
				if(generalType && objConfigPanel.length && objConfigInputs.length){
				//	object id that cannot be renamed
					var predefinedID = window.canvasOptions.predefinedID;

					objConfigInputs.each(function(index, element){
						var configField	= $(element).data('field');

						if(configField){
							var objColorPicker	= $(element).closest('.colorpicker-component');
							var configValue		= object[configField] || '';

							if(configField == 'prime_id'){
								if($.inArray(configValue, predefinedID) < 0){
									configValue = 'freetext';
								}
							}
							else if(configField == 'strokeWidth'){
								configValue = configValue == '' ? 0 : configValue;
							}

							$(element).val(configValue);

							if(objColorPicker.length && typeof $.fn.colorpicker == 'function'){
							//	user update and not setValue, because we doesn't need to trigger the change event
								objColorPicker.colorpicker('setValue', configValue.length ? configValue : 'transparent');

							//	color of group of path cannot be modified
								var isDisable = false;

								if(object.type == 'image'){
									isDisable = configField == 'fill';
								}
								else{
									isDisable = object.type == 'group';
								}

								objColorPicker.toggleClass('disabled', isDisable);
							}
						}
					});

				//	bind change event
					var canvas	= this.__canvas;
					var app		= this;

					objConfigInputs.off('change colorpicker:change').on('change colorpicker:change', function(event){
						var objColorPicker		= $(this).closest('.colorpicker-component');
						var isValidColorPicker	= objColorPicker.length && event.type == 'colorpicker:change';
						var isValidInput		= objColorPicker.length == 0 && event.type == 'change';

						if(isValidColorPicker || isValidInput){
							var configValue	= $(this).val();
							var configField	= $(this).data('field');

							if(configField == 'strokeWidth'){
								configValue = parseInt(configValue, 10);
							}
							else if(configField == 'prime_id' && configValue == 'freetext'){
								configValue = app.generateID();
							}

						//	console.log(configField + ' --- ' + configValue);

							object.set(configField, configValue);
							canvas.renderAll();
							canvas.trigger('object:modified');

							if(configField == 'prime_id' && $.inArray(configValue, predefinedID) > -1){
								var companyData	= $('#company-data-input').val();
									companyData	= companyData ? $.parseJSON(companyData) : null;

								if(configValue.indexOf('prime-company') > -1 && companyData){
								//	using cached data
									var value = getResponseValue(companyData, configValue);

									object.set('text', value);
									canvas.renderAll();
								}
								else{
								//	using realtime data
									var selector = '.searchbox input[data-role="catcomplete"]';

									if(configValue.indexOf('prime-agent') > -1){
										selector+= '#SearchAgentKeyword';
									}
									else{
										selector+= '#SearchPropertyKeyword';
									}

								//	set "input from config" flag, so ajax will only updating currently active object
									$(selector).attr('data-config-trigger', true).trigger('catcompleteselect:after');
								}
							}
						}
					});

				//	close design panel
					$('body').removeClass('design-show');
				}

			//	$('body').toggleClass('config-show', isValidObject ? true : false);

			//	versi baru, config muncul untuk semua jenis object
				var isAddClass = generalType ? true : false;

				$('body').toggleClass('config-show', isAddClass);
				$('body').removeClass('config-other config-text').toggleClass('config-' + generalType, isAddClass);

				recalculateSimpleBar(objConfigPanel);
			},
		//	showContextMenu : function(object){
		//		object = object || this.__canvas.getActiveObject();

		//		var objContextMenu = $('#context-menu');

		//		if(object && objContextMenu.length){
		//			var objectType	= object.type;
		//			var content		= [
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="remove">remove</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="bring-forward">send forward</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="bring-backward">send backward</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="bring-front">bring to front</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="bring-back">send to back</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="flipX">flip horizontal</button>', 
		//				'<button type="button" data-role="img-cmd-tool" data-toggle="flipY">flip vertical</button>', 
		//			];

		//			this.__contextMenu.html(content.join('')).show();
		//		}

		//		this.repositionContextMenu(object);
		//	}, 
		//	repositionContextMenu: function(object){
		//		object = object || this.__canvas.getActiveObject();

		//		var objectScaleX = (object.width * object.scaleX);
		//		var objectScaleY = (object.height * object.scaleY);

		//		this.__contextOffset = {
		//			top		: object.top + objectScaleY + controlOptions.cornerPadding + controlOptions.cornerSize, 
		//			left	: object.left - controlOptions.cornerPadding - (this.__contextMenu.width() / 4),	
		//		};

		//		this.__contextMenu.css(this.__contextOffset);
		//	}, 
			moveObject : function(toggle, object){
				object = object || this.__canvas.getActiveObject();

				if(object){
					switch(toggle){
						case 'bringForward' :
							this.__canvas.bringForward(object);
						break;
						case 'bringToFront':
							this.__canvas.bringToFront(object);
						break;
						case 'sendBackward':
							this.__canvas.sendBackwards(object);
						break;
						case 'sendToBack':
							this.__canvas.sendToBack(object);
						break;
					}

					this.__canvas.renderAll();
					this.__canvas.trigger('object:modified');
				}
			},
			flipObject : function(toggle, object){
				object = object || this.__canvas.getActiveObject();

				if(object){
					object.toggle(toggle);
					object.set('angle', 360 - object.angle);

					this.__canvas.renderAll();
				}
			},

		//	font styling ============================================================================================

			isFontStyleActive : function(styleName, value){
				var isFontStyleActive;

				if(styleName == 'textDecoration'){
					var tempValue = value == 'linethrough' ? 'line-through' : value;

					isFontStyleActive = this.getActiveStyle(styleName).indexOf(tempValue) > -1 || this.getActiveStyle(value);
				}
				else{
					isFontStyleActive = this.getActiveStyle(styleName) === value;
				}

				return isFontStyleActive;
			}, 
			toggleFontStyle : function(styleName, value, object){
				object = object || this.__canvas.getActiveObject();

				if(styleName == 'textDecoration'){
					var isFontStyleActive = this.isFontStyleActive(styleName, value);

					var tempValue	= value == 'linethrough' ? 'line-through' : value;
					var newValue	= isFontStyleActive ? this.getActiveStyle(styleName).replace(tempValue, '') : (this.getActiveStyle(styleName) + ' ' + tempValue);

					this.setActiveStyle(styleName, newValue);
					this.setActiveStyle(value, !this.getActiveStyle(value));
				}
				else if($.inArray(styleName, ['textAlign', 'fontStyle', 'fontWeight']) > -1){
					var isFontStyleActive = this.isFontStyleActive(styleName, value);

					this.setActiveStyle(styleName, isFontStyleActive ? '' : value);
				}
				else if(styleName == 'fontFamily'){
					this.loadFont(value);
				}
				else if($.inArray(styleName, ['strokeWidth', 'fontSize', 'opacity']) > -1){
					value = parseInt(value, 10);

					if(styleName == 'opacity'){
						value = value / 100;
					}

					this.setActiveStyle(styleName, value);
				}
			}, 
			loadFont : function(fontName, object){
				var app		= this;
				var canvas	= app.__canvas;

				object = object || canvas.getActiveObject();

				if(object){
					fontName = fontName || window.canvasOptions.fontFamily;

				//	console.log('initiating font load : ' + fontName);

					if(fontName && fontName.length){
						if(typeof WebFont == 'object'){
							WebFont.load({
								google : { families : [fontName] }, 
								active : function(){
									object.set('fontFamily', fontName);
									canvas.renderAll().trigger('object:modified');

									app.toggleToolbox(object);

								//	console.log(fontName + ' loaded');

								/*	
									var fontObserver = new FontFaceObserver(fontName);

									fontObserver.load().then(function(){
									//	when font is loaded, use it.
										canvas.getActiveObject().set('fontFamily', fontName);
										canvas.renderAll();
									}).catch(function(event){
										console.log(event);
										console.log('failed to load font, invalid font "' + fontName + '"');
									});
								*/
								}
							});
						}
					}
					else{
					//	console.log('failed to load font, invalid font "' + fontName + '"');
						object.set('fontFamily', window.canvasOptions.fontFamily);
						canvas.renderAll().trigger('object:modified');

						app.toggleToolbox(object);
					}
				}
			}, 
		};
	}

	function loadFromJson(jsonString, callback){
		var app		= window.app;
		var canvas	= app.__canvas;

		canvas.loadFromJSON(jsonString, function(){
			canvas.renderAll();
			canvas.trigger('canvas:rendered');

			window.canvasHistory.renderStatus = 1;

			if(typeof callback == 'function'){
				callback(app);
			}
		});
	}

//	set global
	window.app = appObject();

//	cropper
	var cropperOverlay, cropperObject, lastActive;

	window.app.startCrop = function(object){
	//	remove cropper if exist
		if(cropperOverlay){
			this.__canvas.remove(cropperOverlay);
		}

		cropperObject = object || this.__canvas.getActiveObject();

		if(cropperObject && cropperObject.type == 'image'){
			if(lastActive && lastActive !== cropperObject){
				lastActive.clipTo = null;
			}

		//	create cropper rectangle
			cropperOverlay = new fabric.Rect({
				fill				: 'rgba(0,0,0,0.2)',
				originX				: 'left',
				originY				: 'top',
				stroke				: '#ddd',
				opacity				: 1,
				width				: 1,
				height				: 1,
				borderColor			: 'blue',
				cornerColor			: 'blue',
				hasRotatingPoint	: false,
				name				: 'crop-overlay', 
				padding				: 0, 
				cornerColor			: 'blue', 
				cornerStrokeColor	: 'blue', 
				cornerStyle			: 'rectangle', 
				cornerSize			: 5, 
			});

			cropperOverlay.left		= cropperObject.left;
			cropperOverlay.top		= cropperObject.top;
			cropperOverlay.width	= cropperObject.width * cropperObject.scaleX;
			cropperOverlay.height	= cropperObject.height * cropperObject.scaleY;

		//	disable all layer selection
			for(var index = 0; index < this.__canvas.size(); index++){
				this.__canvas.item(index).selectable = false;
			}

			this.__canvas.add(cropperOverlay);
			this.__canvas.setActiveObject(cropperOverlay);
		}
	}

	window.app.cancelCrop = function(){
	//	$('.tools-imgEditor .fa[onclick="startCrop()"]').show();
	//	$('.tools-imgEditor .fa[onclick="applyCrop()"]').hide();
	//	$('.tools-imgEditor .fa[onclick="cancelCrop()"]').hide();
	//	$('.tools-imgEditor .fa[onclick="addRect()"],.tools-imgEditor .fa[onclick="addArrow()"]').addClass('addElement');

		cropperOverlay = this.__canvas.getItemByAttr('name', 'crop-overlay');

		if(cropperOverlay){
			this.__canvas.remove(cropperOverlay);

		//	reset target object
			cropperObject = null;
		}

	//	enable all layer selection
		for(var index = 0; index < this.__canvas.size(); index++){
			this.__canvas.item(index).selectable = true;
		}
	}

	window.app.applyCrop = function(){
	//	$('.tools-imgEditor .fa[onclick="startCrop()"]').show();
	//	$('.tools-imgEditor .fa[onclick="applyCrop()"]').hide();
	//	$('.tools-imgEditor .fa[onclick="cancelCrop()"]').hide();
	//	$('.tools-imgEditor .fa[onclick="addRect()"],.tools-imgEditor .fa[onclick="addArrow()"]').addClass('addElement');

		if(cropperOverlay && cropperObject){
			var left	= cropperOverlay.left - cropperObject.left;
			var top		= cropperOverlay.top - cropperObject.top;

			left*= 1;
			top*= 1;

			var width	= cropperOverlay.width * 1;
			var height	= cropperOverlay.height * 1;

			cropperObject.clipTo = function(ctx){
				ctx.rect(-(cropperOverlay.width / 2) + left, -(cropperOverlay.height / 2) + top, parseInt(width * cropperOverlay.scaleX), parseInt(cropperOverlay.scaleY * height));
			}

		//	enable all layer selection
			for(var index = 0; index < this.__canvas.size(); index++){
				this.__canvas.item(index).selectable = true;
			}

		//	remove overlay
			this.__canvas.remove(cropperOverlay);
	
			cropperObject.setLeft(-cropperOverlay.left);
			cropperObject.setTop(-cropperOverlay.top);
			cropperObject.setCoords();

		//	this.__canvas.readjustObjectsPosition(cropperOverlay.top, cropperOverlay.left);
			this.__canvas.setWidth(parseInt(width * cropperOverlay.scaleX));
			this.__canvas.setHeight(parseInt(cropperOverlay.scaleY * height));
			this.__canvas.renderAll();
			this.__canvas.trigger('object:modified');

		//	reset state
			lastActive		= cropperObject;
			cropperObject	= null;
			cropperOverlay	= null;
		}
	}

	function getScale(width, height, maxWidth, maxHeight){
		width		= parseInt(width);
		height		= parseInt(height);
		maxWidth	= maxWidth || width;
		maxHeight	= maxHeight || height;

		var scale = 1;

		if((width > maxWidth) && (height > maxHeight)){
			var widthScale	= maxWidth / width;
			var heightScale	= maxHeight / height;

			scale = widthScale <= heightScale ? widthScale : heightScale;
		}
		else if(width > maxWidth){
			scale = maxWidth / width;
		}
		else if(height > maxHeight){
			scale = maxHeight / height;
		}
		else{
			scale = 1;
		}

		return scale;
    }

	window.app.setBackgroundImage = function(type, value){
		var canvas = this.__canvas;

		if(type == 'reset'){
		//	canvas.setBackgroundColor('', canvas.renderAll.bind(canvas));
		//	canvas.setBackgroundImage('', canvas.renderAll.bind(canvas));
			canvas.backgroundImage = 0;
			canvas.renderAll();
		}
		else{
			var source	= canvas.backgroundImage;
			var options	= { crossOrigin : 'anonymous' };

			if(type == 'switch'){
			//	add new background
				source = value;

				fabric.Image.fromURL(source, function(bgImage){
					var canvasAspect	= canvas.width / canvas.height;
					var imageAspect		= bgImage.width / bgImage.height;

					var left, top, scaleFactor;

					if(canvasAspect >= imageAspect){
						scaleFactor	= canvas.width / bgImage.width;
						left		= 0;
						top			= -((bgImage.height * scaleFactor) - canvas.height) / 2;
					}
					else{
						scaleFactor	= canvas.height / bgImage.height;
						top			= 0;
						left		= -((bgImage.width * scaleFactor) - canvas.width) / 2;
					}

					var objSelOpacity	= $('select[data-role="canvas-command"][data-type="opacity"]');
					var opacity			= 1;

					if(objSelOpacity.length){
						opacity = objSelOpacity.val();
					}

					options	= $.extend({
						originX	: 'left',
						originY	: 'top',
						top		: top,
						left	: left,
						scaleX	: scaleFactor,
						scaleY	: scaleFactor, 
						opacity	: opacity, 
					}, options);

					canvas.setBackgroundImage(bgImage, canvas.renderAll.bind(canvas), options);
				//	canvas.renderAll();
				/*
					var scale = getScale(bgImage.width, bgImage.height, canvas.width, canvas.height);

					options	= $.extend({
						originX	: 'left',
						originY	: 'top', 
						scaleX	: scale,
						scaleY	: scale, 
					}, options);

					canvas.setBackgroundImage(bgImage, canvas.renderAll.bind(canvas), options);
				//	console.log(options);
				*/

				/*
					var scale	= getScale(bgImage.width, bgImage.height, canvas.width, canvas.height);
					var	options	= {
						originX	: 'left',
						originY	: 'top', 
						width	: bgImage.width * scale, 
						height	: bgImage.height * scale, 
					//	scaleX	: canvas.width / bgImage.width,
					//	scaleY	: canvas.width / (bgImage.height * aspect), 
					};

					console.log(options);

					bgImage.set(options);
					canvas.setBackgroundImage(bgImage, canvas.renderAll.bind(canvas));
				*/
				}.bind(this), {
					crossOrigin : 'anonymous', 
				});
			}
			else if(type){
			//	modify existing background
				if(!source) return;

				options[type] = value;
				canvas.setBackgroundImage(source, canvas.renderAll.bind(canvas), options);
			}
		}
	}

	window.app.setBackgroundColor = function(type, value){
		var canvas	= this.__canvas;
		var source	= canvas.backgroundColor;

		if(type == 'opacity'){
		//	modify existing background
			if(!source) return;

			source = new fabric.Color(source);
			source = source.setAlpha(value).toRgba();
		}
		else{
			source = value;
		}

		canvas.setBackgroundColor(source, canvas.renderAll.bind(canvas));
	}

	window.app.renderLayer = function(){
		var objLayerPlaceholder	= $('#layer-placeholder');

		if(objLayerPlaceholder.length){
			var canvas	= this.__canvas;
			var objects	= canvas.getObjects();

			var objLayerList	= $('#layer-list');
			var layers			= [];

			if(objects){
				for(var index = objects.length - 1; index >= 0; index--){
					var primeID		= objects[index].prime_id;
					var primeName	= objects[index].prime_name;
					var type		= objects[index].type;

					if(primeID != 'prime-copyright'){
						var objItem = $('<li></li>').attr({
							'class'			: 'ui-sortable-handle', 
							'data-guid'		: primeID, 
							'data-order'	: index, 
							'data-role'		: 'section', 
							'data-revert'	: 'invalid', 
						});

						var objRemove = $('<a></a>').html('<i class="fas fa-fw fa-trash"></i>').attr({
							'data-guid'	: primeID, 
							'data-role'	: 'remove-section', 
							'href'		: 'javascript:void(0);', 
							'class'		: 'delete', 
							'title'		: 'Hapus', 
						});

						var objMove = $('<a></a>').html('<i class="fas fa-fw fa-ellipsis-v"></i>').attr({
							'data-guid'	: primeID, 
							'href'		: 'javascript:void(0);', 
							'class'		: 'move', 
							'title'		: 'Geser', 
						});

						var icon = '';

						if($.inArray(type, ['text', 'i-text', 'textbox']) > -1){
							icon = 'fas fa-font';
						}
						else if($.inArray(type, ['triangle', 'circle', 'rect']) > -1){
							icon = 'fas fa-shapes';
						}
						else if($.inArray(type, ['group', 'path']) > -1){
							icon = 'fas fa-draw-polygon';
						}
						else if(type == 'line'){
							icon = 'fas fa-pencil-alt';
						}
						else if(type == 'image'){
							icon = 'far fa-image';
						}
						else{
							icon = 'fas fa-fw fa-layer-group';
						}

						var objLabel	= '<i class="fa-fw ' + icon + '"></i> ' + primeName;
						var objEdit		= $('<a></a>').html(objLabel).attr({
							'data-guid'	: primeID, 
							'data-role'	: 'edit-section', 
							'href'		: 'javascript:void(0);', 
							'title'		: 'Edit', 
						});

					//	append layer
					//	layers.push(objItem.append(objRemove, objMove, objEdit));
						layers.push(objItem.append(objEdit));
					}
				}
			}

			var objEmptyLayer = objLayerList.find('li.empty-layer');

		//	remove old layer
			objEmptyLayer.siblings('li').remove();

			if(layers.length){
				objEmptyLayer.hide();

			//	render layer list
				objLayerList.prepend(layers);
			}
			else{
				objEmptyLayer.show();
			}
		}
	}

//	copy paste functionability
	window.app.copyObject = function(){
	//	clone what are you copying since you may want copy and paste on different moment.
	//	and you do not want the changes happened later to reflect on the copy.
		var app		= this;
		var object	= app.getSelection();

		if(object){
			var isText = $.inArray(object.type, ['text', 'i-text', 'textbox']) > -1;

			if((isText && object.isEditing === false) || isText === false){
				object.clone(function(cloned){
					app.__clipboard = cloned;
				}, app.__customAttributes);
			}
		}
	}

	window.app.pasteObject = function(){
	//	clone again, so you can do multiple copies.
		var app = this;

		app.__clipboard.clone(function(clonedObj){
		//	deselect active object
			app.__canvas.discardActiveObject();

			var objectID	= app.generateID();
			var objectName	= 'clone-layer-' + objectID;

			if(clonedObj.prime_name && clonedObj.prime_name.indexOf('-clone') == -1){
				objectName = clonedObj.prime_name + '-clone';
			}

			clonedObj.set({
				prime_id		: objectID, 
				prime_name		: objectName, 
				left			: clonedObj.left + 10,
				top				: clonedObj.top + 10,
				evented			: true,
				objectCaching	: false, 
			});

			if(clonedObj.type === 'activeSelection'){
			//	active selection needs a reference to the canvas.
				clonedObj.canvas = app.__canvas;

				clonedObj.forEachObject(function(obj){
					app.__canvas.add(obj);
				});

			//	this should solve the unselectability
				clonedObj.setCoords();
			}
			else{
				app.__canvas.add(clonedObj);
			}

		//	console.log('cloned object :');
		//	console.log(clonedObj);

			app.__clipboard.top+= 10;
			app.__clipboard.left+= 10;

			app.__canvas.setActiveObject(clonedObj);
			app.__canvas.requestRenderAll();
		}, app.__customAttributes);
	}

	window.app.undo = function(){
		var app		= this;
		var canvas	= app.__canvas;
		var history = window.canvasHistory;

	//	console.log('history.undoFinishedStatus : ' + history.undoFinishedStatus);

		if(history.undoFinishedStatus){
			if(history.currentStateIndex == -1){
				history.undoStatus = false;
			}
			else{
				if(history.canvasState.length >= 1){
					history.undoFinishedStatus = 0;

					if(history.currentStateIndex > 0){
					//	current undo index
						var undoIndex = history.currentStateIndex - 1;

						history.undoStatus = true;

						app.loadJSON(history.canvasState[undoIndex], function(){
							history.undoStatus			= false;
							history.currentStateIndex	= undoIndex;
							history.undoFinishedStatus	= 1;

						//	console.log('undo : ' + history.currentStateIndex);
						});

					/*
						canvas.loadFromJSON(history.canvasState[undoIndex], function(){
						//	var jsonData = JSON.parse(history.canvasState[undoIndex]);

							canvas.renderAll();

							history.undoStatus			= false;
							history.currentStateIndex	= undoIndex;
							history.undoFinishedStatus	= 1;

							console.log('undo : ' + history.currentStateIndex);

						//	set button state
						//	history.undoButton.removeAttribute("disabled");

						//	if(history.currentStateIndex !== history.canvasState.length - 1){
						//		history.redoButton.removeAttribute('disabled');
						//	}
						});
					*/
					}
					else{
						history.currentStateIndex	= 0;
						history.undoFinishedStatus	= 1;
					}
				}
			}
		}
	}

	window.app.redo = function(){
		var app		= this;
		var canvas	= app.__canvas;
		var history = window.canvasHistory;

		if(history.redoFinishedStatus){
			if((history.currentStateIndex == history.canvasState.length - 1) && history.currentStateIndex != -1){
			//	history.redoButton.disabled= "disabled";
			}
			else{
				if(history.canvasState.length > history.currentStateIndex && history.canvasState.length != 0){
				//	current undo index
					var redoIndex = history.currentStateIndex + 1;

					history.redoFinishedStatus	= 0;
					history.redoStatus			= true;

					app.loadJSON(history.canvasState[redoIndex], function(){
						history.redoStatus			= false;
						history.currentStateIndex	= redoIndex;
						history.redoFinishedStatus	= 1;

					//	console.log('redo : ' + history.currentStateIndex);
					});

				/*
					canvas.loadFromJSON(history.canvasState[redoIndex],function(){
						var jsonData = JSON.parse(history.canvasState[redoIndex]);

						canvas.renderAll();

						history.redoStatus			= false;
						history.currentStateIndex	= redoIndex;
						history.redoFinishedStatus	= 1;

						console.log('redo : ' + history.currentStateIndex);
						console.log(jsonData);

					//	if(history.currentStateIndex != -1){
					//		history.undoButton.removeAttribute('disabled');
					//	}

					//	if((history.currentStateIndex == history.canvasState.length - 1) && history.currentStateIndex != -1){
					//		history.redoButton.disabled= "disabled";
					//	}
					});
				*/
				}
			}
		}
	}

	function updateCanvasState(options, eventName){
	//	set option
		console.log(options);
		window.app.__canvas.renderAll();
	}

	$.each(['object:selected', 'path:created', 'selection:cleared'], function(index, eventName){
		window.app.__canvas.on(eventName, function(options){
		//	console.log(eventName);
		//	updateCanvasState(options, eventName);

			if($.inArray(eventName, ['object:selected', 'selection:cleared']) > -1){
				window.app.toggleToolbox();
			}

		//	if(eventName == 'object:selected'){
		//		window.app.showContextMenu();
		//	}
		//	else if(eventName == 'selection:cleared'){
		//		window.app.__contextMenu.hide();
		//	}
		});
	});

//	window.app.__canvas.on('mouse:down', function(options){
//		if(options.target){
//			console.log('an object was clicked! ', options.target.type);
//		}
//		else{
//			console.log('click');
//		}
//	});

	function updateCanvas(eventName){
		var app			= window.app;
		var history		= window.canvasHistory;
		var canvas		= app.__canvas;
		var copyright 	= canvas.getItemByAttr('prime_id', 'prime-copyright');

		if(copyright){
			// copyright.set('selectable', false);

			canvas.bringToFront(copyright);
			canvas.renderAll();
		}

		var saveHistory = true;

		if(eventName == 'object:added'){
			var objects	= canvas.getObjects();
			var object	= objects[0] || null;

		//	disable history recording when adding copyright and company logo
			var ignoreID = ['prime-copyright', 'prime-company-logo'];

			if(objects.length < ignoreID.length && object && $.inArray(object.prime_id, ignoreID) > -1){
				saveHistory = false;
			}
		}

		saveHistory	= saveHistory && $.inArray(eventName, ['object:added', 'object:modified']) > -1;
		saveHistory	= saveHistory && history.undoStatus == false && history.redoStatus == false;
		saveHistory	= saveHistory && history.renderStatus;

		if(saveHistory){
		//	update history (for undo & redo)
			var jsonString = app.rasterizeJSON();
			var jsonObject = $.parseJSON(jsonString);

		//	console.log('saved canvas :');
		//	console.log(jsonObject);

			if(history.currentStateIndex < history.canvasState.length - 1){
				var indexToBeInserted					= history.currentStateIndex + 1;
				history.canvasState[indexToBeInserted]	= jsonString;

				var numberOfElementsToRetain			= indexToBeInserted + 1;
				history.canvasState						= history.canvasState.splice(0, numberOfElementsToRetain);
			}
			else{
				history.canvasState.push(jsonString);
			}

			history.currentStateIndex = history.canvasState.length - 1;
		}

	//	render layer list
		app.renderLayer();

	//	var logs = [
	//		'event : ' + eventName, 
	//		'history index  : ' + history.currentStateIndex, 
	//		'saved to history : ' + saveHistory, 
	//		'history length : ' + history.canvasState.length, 
	//	];

	//	console.log(logs.join(', '));
	}

//	window.app.__canvas.on('after:render', function(options){
//		console.log('after:render');
//	});

	window.app.__canvas.on({
		'object:added' : function(options){
			updateCanvas('object:added');
		}, 
		'object:modified' : function(options){
			updateCanvas('object:modified');
		}, 
		'object:removed' : function(options){
			updateCanvas('object:removed');
		}, 
		'path:created' : function(options){
			updateCanvas('path:created');
		}, 
		'selection:updated' : function(options){
		//	window.app.repositionContextMenu();
			window.app.toggleToolbox();
		}, 
	//	'object:moving' : function(options){
	//		window.app.repositionContextMenu();
	//	}, 
	//	'object:scaling' : function(options){
	//		window.app.repositionContextMenu();
	//	}, 
	//	'object:rotating' : function(options){
	//		window.app.repositionContextMenu();
	//	}, 
	});

	function __keyboardListener(){
		document.onkeydown = function(event){
		//	event.preventDefault();
			var key = window.event ? window.event.keyCode : event.keyCode;

		//	shortcuts
			switch(key){
			//	zoom in (ctrl+"+")
				case 187:
					if(ableToShortcut()){
						if(event.ctrlKey){
							event.preventDefault();
							calculateZoom('zoomIn');
						}
					}
				break;
			//	zoom out (ctrl+"-")
				case 189:
					if(ableToShortcut()){
						if(event.ctrlKey){
							event.preventDefault();
							calculateZoom('zoomOut');
						}
					}
				break;
			//	reset zoom (ctrl+"0")
				case 48:
					if(ableToShortcut()){
						if(event.ctrlKey){
							event.preventDefault();
							calculateZoom('zoomReset');
						}
					}
				break;
			//	delete
				case 46:
					if(ableToShortcut()){
						var objects		= window.app.__canvas.getActiveObjects();
						var isEditing	= objects.isEditing || false;

						if(isEditing === false){
							event.preventDefault();
							window.app.removeSelected();
						}
					}
				break;
			//	copy (ctrl + c)
				case 67:
					if(ableToShortcut()){
						if(event.ctrlKey){
							var object		= window.app.getSelection();
							var isEditing	= object.isEditing || false;

							if(isEditing === false){
								event.preventDefault();
								window.app.copyObject();
							}
						}
					}
				break;
			//	paste (ctrl + v)
				case 86:
					if(ableToShortcut()){
						if(event.ctrlKey){
							var object		= window.app.__clipboard || window.app.getSelection();
							var isEditing	= object.isEditing || false;

							if(isEditing === false){
								event.preventDefault();
								window.app.pasteObject();
							}
						}
					}
				break;
			//	redo (ctrl + y)
				case 89:
					if(ableToShortcut()){
						if(event.ctrlKey){
							event.preventDefault();
							window.app.redo();
						}
					}
				break;
			//	undo (ctrl + z)
				case 90:
					if(ableToShortcut()){
						if(event.ctrlKey){
							event.preventDefault();
							window.app.undo();
						}
					}
				break;
				default:
				//	TODO
				break;
			}
		};

	//	document.onkeyup = onKeyUpHandler;
	}

	__keyboardListener();

	function ableToShortcut(){
		var result = $(':input:focus').length ? false : true;
		return result;
	}

	function recalculateSimpleBar(objTarget){
		var objTarget = $(objTarget);

		if(objTarget.length){
			var objSimplebar	= objTarget.find('[data-simplebar]');
			var isSimplebar		= false;

			if(typeof objTarget.attr('data-simplebar') !== typeof undefined && objTarget.attr('data-simplebar') !== false) {
			    objSimplebar	= objTarget;
				isSimplebar		= true;
			}

		//	if(typeof $.fn.simplebar == 'function' && objSimplebar.length){
			if(objSimplebar.length){
			//	objSimplebar.simplebar('recalculate');
				objSimplebar.find('.simplebar-scroll-content').scrollTop(0);
			}
		}
	}

	function calculateZoom(zoomType){
		if($.inArray(zoomType, ['zoomIn', 'zoomOut', 'zoomReset']) > -1){
			var isValidZoomIn	= zoomType == 'zoomIn' && window.canvasOptions.currentStep < window.canvasOptions.step;
			var isValidZoomOut	= zoomType == 'zoomOut' && window.canvasOptions.currentStep > 1;

			if(zoomType == 'zoomReset' || isValidZoomIn || isValidZoomOut){
				if(zoomType == 'zoomReset'){
					window.canvasOptions.currentStep	= window.canvasOptions.step;
					window.canvasOptions.currentScale	= window.canvasOptions.scale;
				}
				else{
					window.canvasOptions.currentStep	= zoomType == 'zoomIn' ? window.canvasOptions.currentStep + 1 : window.canvasOptions.currentStep - 1;
					window.canvasOptions.currentScale	= (1 / window.canvasOptions.step) * window.canvasOptions.currentStep;
				}

				var objects			= canvas.getObjects();
				var canvasWidth		= window.canvasOptions.currentOrientation == 'landscape' ? defaultWidth : defaultHeight;
				var canvasHeight	= window.canvasOptions.currentOrientation == 'landscape' ? defaultHeight : defaultWidth;

				canvasWidth		= canvasWidth * window.canvasOptions.currentScale;
				canvasHeight	= canvasHeight * window.canvasOptions.currentScale;

				canvas.setWidth(canvasWidth);
				canvas.setHeight(canvasHeight);

			//	set html wrapper css ==============================================================

				if($('#canvas-wrapper').length){
					$('#canvas-wrapper').css({
						'width'		: canvasWidth, 
						'height'	: canvasHeight, 
					});

					if($('#canvas-scale-placeholder').length){
						$('#canvas-scale-placeholder').text(parseInt(window.canvasOptions.currentScale * 100) + '%');
					}
				}

			//	===================================================================================

			//	console.log('current scale : ' + window.canvasOptions.currentScale);

				for(var i in objects){
					if(typeof objects[i].originTop == 'undefined'){
					//	set original value of top - left for first time modification only
						objects[i].originTop	= objects[i].top;
						objects[i].originLeft	= objects[i].left;
					}

					objects[i].scaleX	= window.canvasOptions.currentScale;
					objects[i].scaleY	= window.canvasOptions.currentScale;

					objects[i].top		= objects[i].originTop * window.canvasOptions.currentScale;
					objects[i].left		= objects[i].originLeft * window.canvasOptions.currentScale;

					objects[i].setCoords();
				//	console.log('type : ' + objects[i].type + ' > ' + objects[i].top + ' - ' + objects[i].left);
				}

			//	console.log('---------------------------------------------------------------------------------------');

				canvas.renderAll();

				if(canvas.backgroundImage){
					var img		= canvas.backgroundImage;
					var aspect	= img.width / img.height;

					canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
						scaleX	: canvas.width / img.width,
						scaleY	: canvas.width / (img.height * aspect), 
					});
				}

				window.app.updateCopyrightPosition();

			//	console.log(window.canvasOptions);
			//	console.log(defaultWidth);
			}
			else{
				console.log('scale exeeded [current step : ' + window.canvasOptions.currentStep + ']');
			}
		}
		else{
			console.log('invalid zoom type' + zoomType + ']');
		}

	/*
		if(zoomType == 'reset' || (zoomType == 'out' && canvasScale >= 0.5) || (zoomType == 'in' && canvasScale <= 2)){
			var isValid = true;
			var rescaleValue;

			if(zoomType == 'in'){
				rescaleValue	= scaleFactor;
				canvasScale		= canvasScale * scaleFactor;
			}
			else if(zoomType == 'out'){
				rescaleValue	= 1 / scaleFactor;
				canvasScale		= canvasScale / scaleFactor;
			}
			else if(zoomType == 'reset'){
				rescaleValue	= 1 / canvasScale;
				canvasScale		= 1;
			}
			else{
				isValid = false;
			}

			if(isValid){
				var objects			= canvas.getObjects();
				var canvasWidth		= canvas.getWidth() * rescaleValue;
				var canvasHeight	= canvas.getHeight() * rescaleValue;

				canvas.setWidth(canvasWidth);
				canvas.setHeight(canvasHeight);

			//	set html wrapper css
				if($('#canvas-wrapper').length){
					$('#canvas-wrapper').css({
						'width'		: canvasWidth, 
						'height'	: canvasHeight, 
					});
				}

				for(var i in objects){
					objects[i].scaleX	= objects[i].scaleX * rescaleValue;
					objects[i].scaleY	= objects[i].scaleY * rescaleValue;
					objects[i].left		= objects[i].left * rescaleValue;
					objects[i].top		= objects[i].top * rescaleValue;

					objects[i].setCoords();
				}

				canvas.renderAll();

				if(canvas.backgroundImage){
					var img		= canvas.backgroundImage;
					var aspect	= img.width / img.height;

					canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
						scaleX	: canvas.width / img.width,
						scaleY	: canvas.width / (img.height * aspect), 
					});
				}

				console.log(zoomType + ' ==== ' + canvasScale);
			}
		}
		else{
			console.log('scale exeeded [current scale : ' + canvasScale + ']');
		}
	*/
	}
//	init default options
	window.app.setOptions();
}

function getResponseValue(response, identifier, type){
	var value	= '';
	var type	= type && $.inArray(type, ['label', 'value']) > -1 ? type : 'value';
	var isValid	= type == 'label' || (response && typeof response == 'object' && Object.keys(response).length);

	if(isValid){
		switch(identifier){
			case 'prime-property-type' : 
				value = type == 'value' ? response.property_type || '' : 'Jenis Properti';
			break;
			case 'prime-property-action' : 
				value = type == 'value' ? response.property_action.toUpperCase() || '' : 'STATUS';
			break;
			case 'prime-property-id' : 
				value = type == 'value' ? response.property_id || '' : 'ID Properti';
			break;
			case 'prime-property-title' : 
				value = type == 'value' ? response.property_title || ''  : 'Kalimat Promosi';
			break;
			case 'prime-property-keyword' : 
				value = type == 'value' ? response.property_keyword || '' : 'Kata Kunci Properti';
			break;
			case 'prime-property-description' : 
				value = type == 'value' ? response.property_description || '' : 'Deskripsi Properti';
			break;
			case 'prime-property-price' : 
				value = type == 'value' ? response.property_price || '' : 'Harga Properti';
			break;
			case 'prime-property-specification' : 
				value = type == 'value' ? response.property_specification || '' : 'Spesifikasi Properti';
			break;
			case 'prime-property-location' : 
				value = type == 'value' ? response.property_location || '' : 'Lokasi Properti';
			break;
			case 'prime-agent-name' : 
				value = type == 'value' ? response.agent_full_name || '' : 'Nama Lengkap Agen';
			break;
			case 'prime-agent-email' : 
				value = type == 'value' ? response.agent_email || '' : 'Email Agen';
			break;
			case 'prime-agent-phone' : 
				value = type == 'value' ? response.agent_phone || '' : 'Telepon / No. Handphone Agen';
			break;

			case 'prime-company-name' : 
				value = type == 'value' ? response.company_name || '' : 'Nama Perusahaan';
			break;
			case 'prime-company-phone' : 
				value = type == 'value' ? response.company_phone || '' : 'Telepon Perusahaan';
			break;
			case 'prime-company-email' : 
				value = type == 'value' ? response.company_email || '' : 'Email Perusahaan';
			break;
			case 'prime-company-address' : 
				value = type == 'value' ? response.company_address || '' : 'Alamat Perusahaan';
			break;
			case 'prime-company-domain' : 
				value = type == 'value' ? response.company_domain || '' : 'Domain Perusahaan';
			break;
		}
	}

	return value;
}

$(document).ready(function(){
	$('body').click(function(event){
		var objTarget	= $(event.target);
		var hasRole		= objTarget.data('role') ? true : false;

		if(hasRole === false){
			hasRole = objTarget.parent().data('role') ? true : false;
		}

		if(objTarget.closest('.preview').length && hasRole === false && objTarget.is('canvas') === false){
		//	auto deselect when user click outside canvas
			window.app.deselectAll();
		}
	});

//	load json value
	var objEbrochureModal	= $('#ebrochure-modal');
	var objEbrochureForm	= $('#ebrochure-builder-form');

	var objTemplateImage	= $('#template-image-input');
	var objTemplateLayout	= $('#template-layout-input');

	var objEbrochureImage	= $('#ebrochure-image-input');
	var objEbrochureLayout	= $('#ebrochure-layout-input');

	var objUserInput		= $('#ebrochure-user-input');
	var objPropertyInput	= $('#ebrochure-property-input');
	var objImagePreview		= $('#ebrochure-image-preview');

	var objTemplateInputWrapper		= $('div.template-input');
	var objEbrochureInputWrapper	= $('div.ebrochure-input');

	if(objEbrochureLayout.length){
		var canvas = window.app.__canvas;

		if(objEbrochureLayout.val() != ''){
			window.app.loadJSON(objEbrochureLayout.val(), function(app){
				canvas.trigger('canvas:modified');
			});
		}
	//	add copyright and company logo (wait until setdimension complete)
		canvas.on('canvas:modified', function(options){
			var primeCopyright	= canvas.getItemByAttr('prime_id', 'prime-copyright');
			var companyLogo		= canvas.getItemByAttr('prime_id', 'prime-company-logo');
			var maxWidth		= 100;
			var maxHeight		= 30;

			var objectCount	= canvas.getObjects().length;

			// if(!primeCopyright){
			// //	auto add prime copyright on canvas load
			// 	fabric.Image.fromURL('/img/poweredby.png', function(image){
			// 		var scale = getScale(image.width, image.height, maxWidth, maxHeight);

			// 		image.set({
			// 			prime_id	: 'prime-copyright', 
			// 			prime_name	: 'prime-copyright', 
			// 			// selectable	: false, 
			// 		//	opacity		: 0.5, 
			// 			scaleX		: scale, 
			// 			scaleY		: scale, 
			// 			top			: canvas.height - ((image.height * scale) + 10), 
			// 			left		: 10, 
			// 			hoverCursor	: 'default', 
			// 		}).setCoords();

			// 		canvas.add(image);
			// 	}.bind(this), {
			// 		crossOrigin : 'anonymous', 
			// 	});
			// }

			// if(!companyLogo){
			// //	auto add company logo on canvas load
			// 	var objCompanyLogo = $('#design-list-company img.company-logo');

			// 	if(objCompanyLogo.length){
			// 		var source = objCompanyLogo.data('src') || objCompanyLogo.attr('src');

			// 		fabric.Image.fromURL(source, function(image){
			// 			var scale = getScale(image.width, image.height, maxWidth, maxHeight);

			// 			image.set({
			// 				prime_id	: 'prime-company-logo', 
			// 				prime_name	: 'Logo Perusahaan', 
			// 				scaleX		: scale, 
			// 				scaleY		: scale, 
			// 				top			: 10, 
			// 				left		: 10, 
			// 			//	top			: canvas.height - ((image.height * scale) + 10), 
			// 			//	left		: canvas.width - ((image.width * scale) + 10), 
			// 			}).setCoords();

			// 			canvas.add(image);
			// 		}.bind(this), {
			// 			crossOrigin : 'anonymous', 
			// 		});
			// 	}
			// }

			canvas.renderAll();
		});
	}

	$(':input[data-role="text-command"]').on('click change', function(event){
		var self		= $(this);
		var styleName	= self.data('type');
		var styleValue	= self.is('select') ? self.find('option:selected').val() : self.val();

		var isValidSelect = self.is('select') && event.type == 'change';
		var isValidButton = self.is('button') && event.type == 'click';

		if(isValidSelect || isValidButton){
			window.app.toggleFontStyle(styleName, styleValue);
		}
	});

	$(':input[data-role="general-command"]').on('click change', function(event){
		var self = $(this);
		var type = self.data('type');

		var isValidSelect = self.is('select') && event.type == 'change';
		var isValidButton = self.is('button') && event.type == 'click';

		if(isValidSelect || isValidButton){
			if(type == 'remove'){
				window.app.removeSelected();
			}
			else if($.inArray(type, ['sendBackward', 'bringForward', 'bringToFront', 'sendToBack']) > -1){
				window.app.moveObject(type);
			}
			else if($.inArray(type, ['flipX', 'flipY']) > -1){
				window.app.flipObject(type);
			}
		}
	});

	$('.canvas-command, :input[data-role="canvas-command"]').on('init change colorpicker:change', function(event){
		var self	= $(this);
		var type	= self.data('type');
		var value	= self.is('select') ? self.find('option:selected').val() : self.val();
		var fnName	= self.data('call');
		var role	= self.data('role');

		if(fnName && typeof window.app[fnName] && event.type != 'init'){
			var isValidInput		= role == 'canvas-command' && event.type == 'change';
			var isValidColorPicker	= role == 'color-input' && event.type == 'colorpicker:change';

			if(isValidColorPicker || isValidInput){
				if(type){
					window.app[fnName](type, value);
				}
				else{
					window.app[fnName](value);
				}
			}
		}
		else if(type == 'orientation'){
			window.app.setCanvasDimension(value);
		}
	//	else{
	//		window.app.setBackground(type, value);
	//	}
	}).trigger('init');

	var saveLayoutJXHR;

	function postRequest(targetUrl, postData, callback){
		if(targetUrl){
			postData = postData || {};

			if(saveLayoutJXHR){
				saveLayoutJXHR.abort();
			}

			saveLayoutJXHR = $.ajax({
				url		: targetUrl, 
				type	: 'post', 
				data	: postData, 
				success	: function(response){
					if(typeof response == 'string' && response.length){
						try{
							response = $.parseJSON(response);
						}
						catch(e){
						//	invalid json (html result is possible os its not considered as an error)
						//	console.log('error:' + e);
						};
					}

					if(Object.keys(response).length){
						var status		= response.status || 'error';
						var message		= response.message || '';
						var redirect	= response.redirect || null;
						var data		= response.data || {};

						if(status == 'success'){

						}

						if(typeof callback == 'function'){
							callback(response);
						}

						if(redirect){
							window.location = redirect;
						}
					}
				}, 
				error : function(XMLHttpRequest, textStatus, errorThrown){
					if(errorThrown != 'abort'){
						notify('Upss, terjadi kesalahan pada saat pengumpulan data, mohon coba beberapa saat lagi.', 'info');
					}
				}, 
			}).always(function(param1, param2, param3){
			//	each param value is optional, only param2 is fixed (textStatus)
			//	success	: data, textStatus, jqXHR
			//	failed	: jqXHR, textStatus, errorThrown

				if(param2 != 'abort'){
				//	revert back to original layout
				//	window.app.loadJSON(layoutJson, function(){
				//	//	$('.searchbox input[data-role="catcomplete"]').trigger('catcompleteselect:after');
				//	});
				}
			});
		}
	}

	$(document).on('click', 'a[data-role="canvas-command"], button[data-role="canvas-command"]', function(event){
		var self	= $(this);
		var value	= self.is('button') ? self.val() : self.data('value');
		var type	= self.data('type');
		var fnName	= self.data('call');

		if(fnName && typeof window.app[fnName]){
			if(type){
				window.app[fnName](type, value);
			}
			else{
				window.app[fnName](value);
			}
		}
		else{
			if($.inArray(type, ['zoomIn', 'zoomOut', 'zoomReset']) > -1){
				calculateZoom(type);
			}
			else if(type == 'clearCanvas'){
				window.app.removeAll();
			}
			else if(type == 'deselectAll'){
				window.app.deselectAll();
			}
			else if($.inArray(type, ['ebrochure', 'template']) > -1){
			//	set current saving state
				objEbrochureForm.attr('data-type', type);

			//	set current title
				var modalTitle = '';

				if(type == 'ebrochure'){
					modalTitle = 'Simpan Ebrosur';

					objTemplateInputWrapper.hide().find(':input').prop('disabled', true);
					objEbrochureInputWrapper.show().find(':input').prop('disabled', false);

					// var objEbrochureNameInput	= $('#ebrochure-name-input');
					// var objAgentKeyword			= $('#SearchAgentKeyword');

					// if(objEbrochureNameInput.val() == '' && objAgentKeyword.val() != ''){
					// 	var agentInfo	= objAgentKeyword.val().split(' | ');
					// 	var agentName	= agentInfo[0] || '';
					// 	var agentEmail	= agentInfo[1] || '';

					// 	objEbrochureNameInput.val(agentName + ' (' + agentEmail + ')');
					// }
				}
				else{
					modalTitle = 'Simpan Template';

					objTemplateInputWrapper.show().find(':input').prop('disabled', false);
					objEbrochureInputWrapper.hide().find(':input').prop('disabled', true);
				}

				objEbrochureModal.find('.modal-header .modal-title').text(modalTitle);
				return;
			}
			else if($.inArray(type, ['saveCanvas', 'saveTemplate', 'saveLayout']) > -1){
				var currentType = objEbrochureForm.attr('data-type') || 'ebrochure';

				if((currentType == 'ebrochure' && objEbrochureLayout.length) || (currentType == 'template' && objTemplateLayout.length)){
					var layoutJson = window.app.rasterizeJSON();

					if(typeof layoutJson == 'string' && layoutJson.length){
						try{
							layoutJson = $.parseJSON(layoutJson);

							if(Object.keys(layoutJson).length){
								var objCanvasContainer	= $('.canvas-container');
								var orientation			= $('select[data-role="canvas-command"][data-type="orientation"]').val() || defaultOrientation;

							//	append orientation
								layoutJson['orientation'] = orientation;

							//	copy for clone
								var jsonClone = layoutJson;

							//	convert back to string
								layoutJson = JSON.stringify(layoutJson);

								if(type == 'saveTemplate' || currentType == 'template'){
								//	saving template, reset all value for template use (dont use real value)
								//	1. clone template from ebrochure layout
								//	2. clean real value
								//	3. render cloned layout
								//	4. save template
								//	5. revert back to ebrochure layout
									var objects = jsonClone.objects || null;

									if(objects){
										$.each(objects, function(index, object){
											var primeID = object.prime_id || '';

											if($.inArray(object.type, ['text', 'i-text', 'textbox']) > -1){
												var predefinedID	= window.canvasOptions.predefinedID;
												var value			= object.text || '';

												if($.inArray(primeID, predefinedID) > -1){
													value = getResponseValue(null, primeID, 'label');
												}

												object['text'] = value;
											}
											else if($.inArray(object.type, ['image']) > -1){
												if($.inArray(primeID, ['prime-company-logo', 'prime-agent-photo', 'prime-property-photo']) > -1){
												//	var source			= window.location.protocol + '//' + window.location.hostname + '/img/';
													var source			= '/img/';
													var sourceWidth		= 0;//object.width;
													var sourceHeight	= 0;//object.height;

												//	use real source dimension
													if(primeID == 'prime-company-logo'){
														source			= source + 'ebrochure-company-photo.png';
														sourceWidth		= 240;
														sourceHeight	= 96;
													}
													else if(primeID == 'prime-agent-photo'){
														source			= source + 'ebrochure-agent-photo.png';
														sourceWidth		= 300;
														sourceHeight	= 300;
													}
													else{
														source			= source + 'ebrochure-property-photo.png';
														sourceWidth		= 855;
														sourceHeight	= 481;
													}

												//	console.log(object.width + ' =====  ' + sourceWidth);
												//	console.log(object.height + ' =====  ' + sourceHeight);

													var scaledObjectWidth	= object.width * object.scaleX;
													var scaledObjectHeight	= object.height * object.scaleY;

													var newScaleX	= scaledObjectWidth / sourceWidth;
													var newScaleY	= scaledObjectHeight / sourceHeight;

													var newWidth	= sourceWidth * newScaleX;
													var newHeight	= sourceHeight * newScaleY;
													var newScale	= newScaleX;

													if(newWidth <= scaledObjectWidth && newHeight > scaledObjectHeight){
														newScale = newScaleY;
													}

													object['original_width']	= object.width;
													object['original_height']	= object.height;
													object['original_scaleX']	= object.scaleX;
													object['original_scaleY']	= object.scaleY;
													object['crossOrigin']		= 'anonymous';

													object['src']		= source;
													object['scaleX']	= newScale;	// same source, keeping image proportion
													object['scaleY']	= newScale;

													if(primeID == 'prime-company-logo'){
														object['width']		= sourceWidth;
														object['height']	= sourceHeight;

														console.log(object);
													}
												}
											}
										});

									//	append back to jsonClone
										jsonClone['objects'] = objects;
									}

								//	console.log(jsonClone);return;

								//	convert clonned layout to string
									jsonClone = JSON.stringify(jsonClone);

								//	replace canvas with clonned layout
									window.app.loadJSON(jsonClone, function(){
									//	render canvas after clonned layout loaded
										var imageResult = window.app.rasterize(1);

										objTemplateImage.val(imageResult);
										objTemplateLayout.val(jsonClone);

									//	set image preview thumbnail
										objImagePreview.attr('src', imageResult).load(function(){
											if(objCanvasContainer.length){
												objCanvasContainer.addClass('loading');
											}

										//	set loading state on button
											self.button('loading');

											var targetUrl	= objEbrochureForm.attr('action') + '?' + 'template=1';
											var postData	= objEbrochureForm.serialize();

										//	save layout 
											postRequest(targetUrl, postData, function(response){
												objCanvasContainer.removeClass('loading');

												var status	= response.status;
												var message	= response.msg;
												var data	= response.data;
												var errors	= response.validationErrors;

												if(data){
													$('#template-id-input').val(data.EbrochureTemplate.id || '');
													$('#template-name-input').val(data.EbrochureTemplate.name || '');
													$('#template-description-input').val(data.EbrochureTemplate.description || '');
												}

												if(errors){
													renderError(errors);
												}
												else if(message){
													notify(message, status);
												}

											//	revert back to original layout
												window.app.loadJSON(layoutJson, function(){
												//	remove loading state on button
													self.button('reset');

													objEbrochureModal.modal('hide');

												//	load new template list
													postRequest('/Ajax/get_ebrochure_template', {}, function(response){
														var selector				= '#design-list-templates';
														var objTemplatePlaceholder	= $(response).find(selector);
														var objTargetPlaceholder	= $(selector);

														if(objTemplatePlaceholder.length && objTargetPlaceholder.length){
															objTargetPlaceholder.html(objTemplatePlaceholder.html());

														//	trigger lazy load
															lazyImage();
														}
													});
												});
											});
										});
									});
								}
								else{
									var imageResult = window.app.rasterize(1);

									objEbrochureImage.val(imageResult);
									objEbrochureLayout.val(layoutJson);

									objImagePreview.attr('src', imageResult).load(function(){
										if(objCanvasContainer.length){
											objCanvasContainer.addClass('loading');
										}

									//	set loading state on button
										self.button('loading');

										var targetUrl	= objEbrochureForm.attr('action');
										var postData	= objEbrochureForm.serialize();

									//	save layout 
										postRequest(targetUrl, postData, function(response){
										//	remove loading state on button
											self.button('reset');

											objCanvasContainer.removeClass('loading');

											var status	= response.status;
											var message	= response.msg;
											var data	= response.data;
											var errors	= response.validationErrors;

											if(data){
												$('#ebrochure-id-input').val(data.UserCompanyEbrochure.id || '');
												// $('#ebrochure-name-input').val(data.UserCompanyEbrochure.name || '');
												$('#ebrochure-description-input').val(data.UserCompanyEbrochure.description || '');
											}

											if(errors){
												renderError(errors);
											}
											else if(message){
												notify(message, status);
											}
										});
									});
								}
							}
						}
						catch(e){
						//	invalid json
							console.log('error:' + e);
						};
					}

				//	console.log(layoutJson);
				//	objEbrochureLayout.val(layoutJson);

				//	if(type == 'saveTemplate'){
				//		var objJsonViewer = $('#json-viewer');

				//		if(objJsonViewer.length){
				//			objJsonViewer.removeClass('hide').find('textarea').val(layoutJson);
				//		}
				//	}
				}

			//	if(type == 'saveCanvas' && objEbrochureImage.length){
			//		var imageResult = window.app.rasterize(1);

			//		objEbrochureImage.val(imageResult);

			//		objImagePreview.attr('src', imageResult).load(function(){
			//			objEbrochureForm.submit();
			//		});
			//	}
			}
			else if(type == 'switchTemplate'){
				if(confirm('Konten akan dihapus saat Anda mengganti template, apa Anda yakin ingin tetap mengganti template?')){
					window.app.loadJSON(value, function(){
						$('.searchbox input[data-role="catcomplete"]').trigger('catcompleteselect:after');
					});
				}
			}
		}
	});

	$('button[data-role="crop-tool"]').click(function(event){
		var toggle = $(this).data('toggle');

		switch(toggle){
			case 'start'	: window.app.startCrop(); break;
			case 'apply'	: window.app.applyCrop(); break;
			case 'cancel'	: window.app.cancelCrop(); break;
		}
	});

	$(document).on('click', '[data-role="add-object"]', function(event){
		var self	= $(this);
		var value	= self.is(':input') ? self.val() : self.data('value');
		var type	= self.data('type');

		if(self.is('a')){
			event.preventDefault();
		}

		if($.inArray(type, ['text', 'line', 'triangle', 'rectangle', 'circle']) > -1){
			switch(type){
				case 'text'			: window.app.addText();		break;
				case 'line'			: window.app.addLine();		break;
				case 'triangle'		: window.app.addTriange();	break;
				case 'rectangle'	: window.app.addRect();		break;
				case 'circle'		: window.app.addCircle();	break;
			}
		}
		else{
			if(value && value.length){
				if($.inArray(type, ['image', 'images']) > -1){
					var options = {};
					var isProperty	= self.hasClass('property-photo');

					if(self.hasClass('property-photo')){
						options = {'prime_id' : 'prime-property-photo'};
					}
					else if(self.hasClass('agent-photo')){
						options = {'prime_id' : 'prime-agent-photo'};
					}
					else if(self.hasClass('company-logo')){
						options = {'prime_id' : 'prime-company-logo'};
					}

					window.app.addImage(value, options);
				}
				else{
					window.app.addShape(value);
				}
			}
			else if(type == 'qr-image'){
				value = self.attr('data-url');

				if(value){
					window.app.addImage(value, {
						'prime_id' : 'prime-property-qr', 
					});
				}
				else{
					notify('Mohon pilih properti', 'error');
					console.log('Error : invalid QR source');
				}
			}
		}
	});

	$('#canvas-background-image').on('change', function(event){
		var file = event.target.files[0];

		if(/\.(bmp|png|jpg|jpeg|gif)$/i.test(file.name)){
			var reader = new FileReader();
			var canvas = window.app.__canvas;

		//	canvas.setBackgroundColor('', canvas.renderAll.bind(canvas));
			canvas.setBackgroundImage('', canvas.renderAll.bind(canvas));

			reader.onload = function(f){
				var data = f.target.result;

				window.app.setBackgroundImage(null, data);
			};

			reader.readAsDataURL(file);
		}
		else{
			notify('File tidak valid, mohon pilih file dengan format .bmp, .png, .jpg, .jpeg, atau .gif)', 'error');
		}

		$(this).val('');
	});

	$(document).on('click', 'button[data-role="img-cmd-tool"]', function(event){
		var toggle = $(this).data('toggle');

		if(toggle == 'remove'){
			window.app.removeSelected();
		}
		else if($.inArray(toggle, ['bring-backward', 'bring-forward', 'bring-front', 'bring-back']) > -1){
			window.app.moveObject(toggle);
		}
		else if($.inArray(toggle, ['flipX', 'flipY']) > -1){
			window.app.flipObject(toggle);
		}
	});

	$(document).on('click', 'a[data-role="edit-section"]', function(event){
		var self	= $(this);
		var canvas	= window.app.__canvas;
		var objects	= canvas.getObjects();

	//	console.log(objects);
		
		if(objects){
		//	higher index === highest z-index
		//	-1 because top most index is reserved for copyright 
		//	-1 because index is zero based 
			var layersCount		= objects.length - 2;
			var parent			= self.parent('li[data-role="section"]');
			var parentGuid		= parent.data('guid');
			var currentIndex	= parent.index();

		//	console.log(parentGuid + ' --- ' + currentIndex + ' --- ' + parent.find('a').text());
			
			var layerIndex	= layersCount - currentIndex;
		//	var object		= objects[layerIndex] || null;
			var object		= canvas.getItemByAttr('prime_id', parentGuid) || null;

		//	console.log('layer index : ' + layerIndex + ', current index : ' + currentIndex);
		//	console.log(object);

			if(object){
				canvas.setActiveObject(object);
				canvas.renderAll();
			}
		}
	});

	fabric.util.addListener(document.getElementsByClassName('upper-canvas')[0], 'contextmenu', function(event){
		event.preventDefault();
	});

	$(document).on('click', '[data-toggle]', function(event){
		var self		= $(this);
		var target		= $(self.attr('data-toggle'));

		if(target.length){
			var clear		= self.data('clear') || false;
			var toggleClass	= self.data('class');
			var callback	= self.data('callback');

			if(clear === true){
				target.attr('class', '');	
			}

			if(toggleClass){
				target.toggleClass(toggleClass);
			}

			recalculateSimpleBar(target);

			if(callback){
				if(callback.indexOf(';') > -1){
					callback = callback.split(';');
				}
				else{
					callback = { callback };
				}

				$.each(callback, function(index, fnName){
				//	var fn = window[fnName];

				//	if(typeof fn === 'function'){
				//		fnName.apply(null, [ self, event ]);
				//	}

					executeFunction(fnName, window);
				});
			}
		}
	});

	$(document).on('colorpicker:change', ':input.layer-config-input', function(event){
		var self	= $(this);
		var	type	= self.data('type');
		var	value	= self.val();

		if($.inArray(type, ['stroke', 'fill', 'backgroundColor']) > -1){
			var object = window.app.__canvas.getActiveObject();

			if(object){
				object.fill = value;
				window.app.__canvas.renderAll();
			}
		}
	});

//	image scaling
	if(typeof $.fn.imageScale == 'function'){
		$('.sectionBlock.sectionBlock-pick img[data-type="images"]').on('load', function(event){
			$(this).imageScale({
				scale			: 'best-fit', 
				align			: 'center', 
				fadeInDuration	: 500, 
			});
		});

		$('.sectionBlock.sectionBlock-pick img[data-type="shapes"]').imageScale({
			scale			: 'best-fit', 
			align			: 'center', 
			fadeInDuration	: 500, 
		});
	}

//	image lazyload
	lazyImage = function(config){
		config = $.extend({
			obj : $('.lazy-image'), 
		}, config);

		config.obj = $(config.obj);

		if(config.obj.length && typeof $.fn.lazyload == 'function'){
			config.obj.lazyload({
			//	threshold		: 200,
				placeholder		: '', 
				effect			: 'fadeIn', 
			//	skip_invisible	: true, 
			});
		}
	}

	lazyImage();

	function executeFunction(functionName, context){
		var args		= Array.prototype.slice.call(arguments, 2);
		var namespaces	= functionName.split(".");
		var func		= namespaces.pop();

		for(var i = 0; i < namespaces.length; i++){
			context = context[namespaces[i]];
		}

		if(typeof context[func] == 'function'){
			return context[func].apply(context, args);
		}
	}

	document.addEventListener('scroll', function(event){
		var objColorpicker = $(event.target).find('.colorpicker-component');

		if(objColorpicker.length && objColorpicker.is(':visible') && typeof $.fn.colorpicker == 'function'){
			objColorpicker.colorpicker('reposition');
		}
	}, true);

	$.builderColorpicker = function(options){
		var settings = $.extend({
			obj : $('.colorpicker-component'), 
		}, options);

		if(typeof $.fn.colorpicker == 'function' && settings.obj.length){
			$(document).on('scroll', function(event){
				settings.obj.colorpicker('reposition');
			});

			settings.obj.each(function(){
				var self		= $(this);
				var input		= self.find(':input[data-role="color-input"]');
				var container	= input.attr('data-container') || '';

				if(input.length){
					var color = input.val();

				//	set last color
					input.attr('data-last-color', color);

					var options = {
						input			: input, 
						format			: 'rgba',
						component		: '[data-role="color-preview"]', 
						colorSelectors	: {
							'1'		: '#FEE439', 
							'2'		: '#FE9C07', 
							'3'		: '#F35240', 
							'4'		: '#E72762', 
							'5'		: '#9B2CAE', 
							'6'		: '#693BB7', 
							'7'		: '#4254B6', 
							'8'		: '#1E83DF', 
							'9'		: '#2395EE', 
							'10'	: '#48B061', 
							'11'	: '#88BF4A', 
						}, 
					};

					if(color != ''){
					//	set default color
						options = $.extend({ color : color }, options);
					}

					if(container != ''){
						options = $.extend({ container : container }, options);
					}

					return self.colorpicker(options).off('showPicker hidePicker changeColor').on('showPicker hidePicker changeColor', function(event){
						var self	= $(this);
						var input	= self.find(':input[data-role="color-input"]');

						if(event.type == 'showPicker'){
						//	append color code input on first load
							var picker = $('.colorpicker.colorpicker-visible');

							if(picker.length && picker.find('button.colorpicker-reset').length <= 0){
								var template =	'<div class="colorpicker-addon margin-top-1">';
									template+=		'<input type="text" class="colorpicker-code"/>';
									template+= 		'<button type="button" class="colorpicker-reset">';
									template+=			'<i class="fa fa-times"></i>';
									template+=		'</button>';
									template+=	'</div>';

								picker.append(template);

							//	bind event to newly created element
								var colorCode	= picker.find('.colorpicker-code');
								var colorReset	= picker.find('.colorpicker-reset');

								colorCode.val(self.colorpicker('getValue', '')).on('click touchstart mousedown change', function(event){
									if(event.type == 'change'){
										var currentColor = $(this).val();

										self.colorpicker('setValue', currentColor ? currentColor : 'transparent');
										input.val(currentColor).attr('value', currentColor);

									//	input.trigger('colorpicker:change');
									}
									else{
										$(this).focus();
									}
								});

								colorReset.on('click', function(event){
									colorCode.val(null).trigger('change');
								});
							}
						}
						else if(event.type == 'hidePicker'){
						//	desc	: custom "change" event untuk input nya, supaya nembak "change" nya sekali.
						//	note	: plugin color picker ini selalu trigger "change" setiap kali ada penggeseran slider warna.
						//	solusi	: untuk sekarang "change" di ganti dengan "colorpicker:change" yang di trigger saat color picker di tutup

							if(input.length){
								var currentColor	= self.colorpicker('getValue', '');
								var lastColor		= input.attr('data-last-color') || '';

								if(currentColor != lastColor){
									input.attr('data-last-color', currentColor).trigger('colorpicker:change');
								}
							}
						}
						else{
						//	change color code value
							var picker		= $('.colorpicker.colorpicker-visible');
							var colorCode	= picker.find('.colorpicker-code');

							if(picker.length && colorCode.length){
								var currentColor = self.colorpicker('getValue', '');

								colorCode.val(currentColor);
							}
						}
					});
				}
				else{
					return false;
				}
			});
		}
	}

	$.builderColorpicker();

	$('#object-tab-toggle').on('click', function(event){
		event.preventDefault();

		var objObjectNav = $('#object-tab-nav');

		if(objObjectNav.length){
			objObjectNav.trigger('click');
		}
	});

//	autocomplete event
	var propertyJXHR = {};

	function renderDesignList(objTarget, values, options){
		objTarget = $(objTarget);

		if(objTarget.length && values && $.isArray(values)){
			var type = options.type || null;

			if(type && $.inArray(type, ['agent-photo', 'property-photo']) > -1){
				var selector		= 'img.' + type;
				var objRemoveList	= $(objTarget).find(selector).closest('li');

			//	remove old list
				if(objRemoveList.length) objRemoveList.remove();

				$.each(values, function(index, value){
					var text	= typeof value == 'string' ? value : (value.text || null);
					var source	= typeof value == 'string' ? value : (value.url || null);

					if(source){
						var template = [
							'<div class="design-wrapper">', 
								'<div class="thumbnail-wrapper">', 
									'<img src="/img/view/errors/error_xxsm.jpg" class="lazy-image ' + type + '" alt="' + text + '" data-src="' + source + '" data-type="images" title="' + text + '">', 
									'<div class="thumbnail-overlay">',
										'<div class="overlay-action">',
											'<a href="javascript:void(0);" title="Tambah ' + text + '" data-role="add-object" data-type="images" data-value="' + source + '" class="btn btn-default margin-bottom-1 ' + type + '">Tambah</a>',,
											'<a href="javascript:void(0);" title="Jadikan ' + text + ' sebagai latar" data-value="' + source + '" data-type="switch" data-role="canvas-command" data-call="setBackgroundImage" class="btn btn-default ' + type + '">Latar</a>',
										'</div>',
									'</div>', 
								'</div>', 
								'<div class="lil-action">', 
									'<span class="mockup-title">' + text + '</span>', 
								'</div>', 
							'</div>'
						];

						objTarget.append('<li>' + template.join('') + '</li>');
					}
				});

				lazyImage();
			}
		}
	}

	$('.searchbox input[data-role="catcomplete"]').on('init catcompleteselect:after', function(event, ui){
		var self			= $(this);
		var inputID			= self.attr('id');
		var reference		= self.next('input:hidden').val();
		var isConfigTrigger	= $.inArray(self.attr('data-config-trigger'), [1, true, '1', 'true']) > -1;

	//	reset attr
		self.removeAttr('data-config-trigger');

		var objQR		= $('#object-list li a[data-type="qr-image"]');
		var propertyQR	= null;

	//	var item		= ui.item;
	//	var reference	= item.reference;

		var canvas	= window.app.__canvas;
		var objects = isConfigTrigger ? [canvas.getActiveObject()] : canvas.getObjects();

		if(reference){
		//	check if object with predefined id exist
			var predefinedObjects	= [];
			var predefinedSet		= [];

			if(inputID == 'SearchPropertyKeyword'){
				predefinedSet = [
					'prime-property-photo', 'prime-property-type', 'prime-property-action', 'prime-property-id', 
					'prime-property-title', 'prime-property-keyword', 'prime-property-description', 
					'prime-property-price', 'prime-property-specification', 'prime-property-location', 
				];

			//	set property id
				if(objPropertyInput.length){
					objPropertyInput.val(reference);
				}
			}
			else if(inputID == 'SearchAgentKeyword'){
				predefinedSet = [
					'prime-agent-photo', 'prime-agent-name', 'prime-agent-email', 'prime-agent-phone', 
				];

			//	set agent id
				if(objUserInput.length){
					objUserInput.val(reference);
				}
			}

			if(predefinedSet){
				predefinedSet.push('prime-company-logo', 'prime-company-name', 'prime-company-phone', 'prime-company-email', 'prime-company-address', 'prime-company-domain');

				if(objects.length){
					$.each(objects, function(index, object){
						var primeID = object.prime_id || null;

					//	only text object
					//	if($.inArray(object.type, ['text', 'i-text', 'textbox']) > -1 && $.inArray(primeID, predefinedSet) > -1){
						if($.inArray(primeID, predefinedSet) > -1){
							predefinedObjects.push(object);
						}
					});
				}
			}

			var source = self.data('source');

			if(source){
				if(propertyJXHR[inputID]){
					propertyJXHR[inputID].abort();
				}

			//	request property / agent details
				var objCanvasContainer = $('.canvas-container');

				if(objCanvasContainer.length){
					objCanvasContainer.addClass('loading');
				}

				propertyJXHR[inputID] = $.ajax({
					url		: source + '/' + reference, 
					type	: 'post', 
					success	: function(response){
						if(typeof response == 'string' && response.length){
							try{
								response = $.parseJSON(response);
							}
							catch(e){
							//	invalid json
								console.log('error:' + e);
							};
						}

						var responseFields = Object.keys(response);

						if(response && typeof response == 'object' && responseFields.length){
							if(event.type == 'catcompleteselect:after'){
								if(predefinedObjects.length){
									var companyLogos		= response.company_logo || null;
									var agentPhotos			= response.agent_photo || null;
									var propertyPhotos		= response.property_photo || null;
									var propertyPhotoIndex	= 0; // only property using this index

								//	replace layer value based on newly selected property / agent
									$.each(predefinedObjects, function(index, predefinedObject){
										var primeID = predefinedObject.prime_id || null;

										if($.inArray(primeID, ['prime-company-logo', 'prime-property-photo', 'prime-agent-photo']) > -1){
											var sourceIndex	= 0;
											var source		= null;

											if(primeID == 'prime-property-photo'){
												source		= propertyPhotos;
												sourceIndex = propertyPhotoIndex;

												if(typeof propertyPhotos[propertyPhotoIndex + 1] != 'undefined'){
													propertyPhotoIndex++;
												}
												else{
													propertyPhotoIndex = 0;
												}

												source = propertyPhotos[sourceIndex] || {};
											}
											else if(primeID == 'prime-agent-photo'){
												source = agentPhotos[sourceIndex] || {};
											}
											else{
												source = companyLogos[sourceIndex] || {};
											}

											var sourceURL = source.url || null;

											if(sourceURL){
											//	predefinedObject.setSrc(sourceURL, function(){
											//		console.log('size after loading image', predefinedObject.width, predefinedObject.scaleX, predefinedObject.height, predefinedObject.scaleY);
											//		predefinedObject.set({
											//			width: 128,
											//			height: 128,
											//			scaleX: 1,
											//			scaleY: 1
											//		}).setCoords();

											//		console.log('size after resizing i to desired width height', predefinedObject.width, predefinedObject.scaleX, predefinedObject.height, predefinedObject.scaleY);
											//		canvas.renderAll();
											//	});

												var img = new Image();

												img.onload = function(){
												//	this.width	= predefinedObject.width;
												//	this.height	= predefinedObject.height;
													var width		= predefinedObject.width * predefinedObject.scaleX;
													var height		= predefinedObject.height * predefinedObject.scaleY;
													var newScaleX	= width / img.width;
													var newScaleY	= height / img.height;

													predefinedObject.setElement(img).set({
													//	img.width - 0.1 to trigger the image change, if no dimension change, image wont refresh
														width	: img.width - 0.1, 
														height	: img.height - 0.1, 
														scaleX	: newScaleX,
														scaleY	: newScaleX, 
													}).setCoords();

												//	predefinedObject.setSrc(sourceURL);
													canvas.renderAll();
												}

												img.crossOrigin	= 'anonymous';
												img.src			= sourceURL;
											}
										}
										else{
											var value = getResponseValue(response, primeID);

											predefinedObject.set('text', value);
										}

										canvas.renderAll();
									});
								}
							}

							var objCompanyDesignList	= $('#design-list-company ul.design-list');
							var setAgentPhoto			= true;

							if(inputID == 'SearchPropertyKeyword'){
							//	set property qr on autocomplete change / init
								propertyQR = response.property_qr || null;

							//	auto set default agent value if no agent selected ====================================

								var objSiblingFilter = $('#SearchAgentKeyword');

								if(objSiblingFilter.length && objSiblingFilter.next('input:hidden').length){
									var siblingReference = objSiblingFilter.next('input:hidden').val();

									if(siblingReference == '' || objSiblingFilter.is(':visible') === false){
									//	change value when no agent selected or when agent search input is not visible
										var agentID		= response.agent_id || '';
										var agentLabel	= response.agent_label || '';

										objSiblingFilter.val(agentLabel).next('input:hidden').val(agentID);
										objSiblingFilter.trigger('catcompleteselect:after');

									//	console.log(agentID + ' --- ' + agentLabel);
									}
									else{
										setAgentPhoto = false;
									}
								}

							//	======================================================================================

							//	set property photo gallery to panel ==================================================

								var propertyPhotos = response.property_photo || null;

								renderDesignList(objCompanyDesignList, propertyPhotos, { 'type' : 'property-photo' });

							//	======================================================================================
							}
							
							if(setAgentPhoto){
							//	set agent photo gallery to panel =====================================================

								var agentPhotos = response.agent_photo || null;
								//	agentPhotos = agentPhotos ? [agentPhotos] : agentPhotos;

								renderDesignList(objCompanyDesignList, agentPhotos, { 'type' : 'agent-photo' });

							//	======================================================================================
							}
						}
						else{
							console.log('Error : invalid data');
							console.log(response);
						}

						if(objCanvasContainer.length){
							objCanvasContainer.removeClass('loading');
						}
					}, 
					error : function(XMLHttpRequest, textStatus, errorThrown){
						if(errorThrown != 'abort'){
							notify('Upss, terjadi kesalahan pada saat pengumpulan data, mohon coba beberapa saat lagi.', 'info');

							if(objCanvasContainer.length){
								objCanvasContainer.removeClass('loading');
							}
						}
					}, 
				});
			}
		}

	//	show / hide qr code
		if(propertyJXHR[inputID]){
			propertyJXHR[inputID].done(function(){
				if(inputID == 'SearchPropertyKeyword' && objQR.length){
					objQR.attr('data-url', propertyQR);
					objQR.closest('li').toggleClass('hide', !reference);
				}
			});
		}

	//	console.log(event.type);
	//	console.log(ui);
	}).trigger('init');

//	design search
	if(typeof delay == 'undefined'){
		var delay = (function(){
			var timer = 0;

			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();
	}

	$('#design-filter-input').on('keyup', function(event){
		var self = $(this);

		delay(function(){
			var objImagesList	= $('#design-list-images');
			var objShapesList	= $('#design-list-shapes');
			var objActiveList	= objImagesList.is(':visible') ? objImagesList : objShapesList;

		//	console.log(objActiveList.attr('id'));

			if(objActiveList.length){
				var value		= self.val().toLowerCase();
				var objParents	= objActiveList.find('ul');

				if(objParents.length){
					objParents.each(function(parentIndex, objParent){
						var objParent	= $(objParent);
						var objChildren	= objParent.find('li');
						var counter		= 0;

						if(objChildren.length){
						//	add loading class
							objParent.addClass('loading');

							var objActiveChidren = objChildren.hide().filter(function(){
								var list	= $(this);
								var match	= list.find('span.mockup-title').text().toLowerCase().indexOf(value) > -1;

								return match;
							}).fadeIn(500, function(){
								objParent.removeClass('loading');
							});

						//	toggle title
							objParent.prev('.sectionBlock-title').toggleClass('hide', objActiveChidren.length == 0);
						}
					});
				}
			}
		}, 500);
	});

	if(typeof $.fn.tooltip == 'function'){
		$('[data-toggle="tooltip"], [data-tooltip="1"]').tooltip({
			html		: true, 
			container	: 'body', 
			placement	: 'auto', 
		});
	}

//	auto rendering ebrochure
	var objSaveLayoutBtn = $('button[data-role="canvas-command"][data-type="saveLayout"]');

	if(window.regenerateEbrochure && objSaveLayoutBtn.length){
		var objLayoutForm = objSaveLayoutBtn.closest('form');

		if(objLayoutForm.length){
			var app = window.app;

		//	$('.splashscreen-cover').hide();

			window.app.__canvas.on('canvas:rendered', function(options){
				objEbrochureImage.val(window.app.rasterize(1));
			//	objEbrochureLayout.val(window.app.rasterizeJSON());

			//	submit form
				objLayoutForm.submit();
			});
		}
	}
});