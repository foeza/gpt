(function ( $ ) {
    var gmapRku = $('#gmap-rku');
    var rkuAddress = $('#rku-address');
    var rkuAddress2 = $('#rku-address2');
    var rkuAddressNo = $('#rku-no-address');
    var rkuLatitude = $('#rku-latitude');
    var rkuLongitude = $('#rku-longitude');
    var rkuLocation = $('#rku-location');
    var loadJXHR;

//  custom print
    $.rkuPrint = function(options){
        var settings = $.extend({
            obj : $('.rku-print'),
        }, options );

        if(settings.obj.length){
            $(document).delegate('.rku-print', 'click', function(){
                var filename = settings.obj.data('name');
                var docTitle = document.title;

                if(filename){
                    document.title = filename;
                }

                window.print();
                document.title = docTitle;
            });
        }
    }

    $.rkuPrint();

//  add error message to input, or remove error message (if error state === false)
    $.toggleErrorInput = function(options){
        var settings = $.extend({
            obj             : null,
            message         : '',
            isError         : false,
            inputClass      : 'form-error',
            messageClass    : 'error-message', 
            
        }, options );

    //  reset state
        settings.obj.removeClass(settings.inputClass).next('div.' + settings.messageClass).remove();

        if(settings.obj.length){
            if(settings.isError == true){
                var objMessage = '<div class="' + settings.messageClass + '">' + settings.message + '</div>';

            //  append message after targeted object
                settings.obj.addClass(settings.inputClass).after( $(objMessage) );
            }
        }
    }

    $.checkUndefined = function (value, _default) {
        if(typeof value == 'undefined' ) {
            value = _default;
        }

        return value;
    }

    $.replaceTag = function (str, replacement, keyword) {
        keyword = $.checkUndefined(keyword, '[%replacement%]');

        return str.replace(keyword, replacement)
    }

    $.numberToString = function (str, replacement) {
        str = $.checkUndefined(str, replacement) + '';

        return str.replace(/,/g, '');
    }

    $.formatNumber = function( number, decimals, dec_point, thousands_sep ){
        // Set the default values here, instead so we can use them in the replace below.
        thousands_sep   = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        dec_point       = (typeof dec_point === 'undefined') ? '.' : dec_point;
        decimals        = !isFinite(+decimals) ? 0 : Math.abs(decimals);

        // Work out the unicode representation for the decimal place.   
        var u_dec = ('\\u'+('0000'+(dec_point.charCodeAt(0).toString(16))).slice(-4));

        // Fix the number, so that it's an actual number.
        number = (number + '')
            .replace(new RegExp(u_dec,'g'),'.')
            .replace(new RegExp('[^0-9+\-Ee.]','g'),'');

        var n = !isFinite(+number) ? 0 : +number,
            s = '',
            toFixedFix = function (n, decimals) {
                var k = Math.pow(10, decimals);
                return '' + Math.round(n * k) / k;
            };

        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (decimals ? toFixedFix(n, decimals) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, thousands_sep);
        }
        if ((s[1] || '').length < decimals) {
            s[1] = s[1] || '';
            s[1] += new Array(decimals - s[1].length + 1).join('0');
        }
        return s.join(dec_point);
    }

    if( gmapRku.length > 0 ) {
        var iconMarker = new google.maps.MarkerImage('/img/icons/icon_map.png',
            new google.maps.Size(26,32),
            new google.maps.Point(0,0),
            new google.maps.Point(13,32)
        );
        var shadow = new google.maps.MarkerImage('/img/icons/icon_map_shadow.png',
            new google.maps.Size(46,32),
            new google.maps.Point(0,0),
            new google.maps.Point(13,32)
        );
        var shape = {
            coord: [25,0,25,1,25,2,25,3,25,4,25,5,25,6,25,7,25,8,25,9,25,10,25,11,25,12,25,13,25,14,25,15,25,16,25,17,25,18,25,19,25,20,25,21,25,22,25,23,25,24,25,25,25,26,25,27,25,28,21,29,20,30,19,31,6,31,5,30,4,29,0,28,0,27,0,26,0,25,0,24,0,23,0,22,0,21,0,20,0,19,0,18,0,17,0,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,0,4,0,3,0,2,0,1,0,0,25,0],
            type: 'poly'
        };
        var propertyMarker;
    }

    $.oauthpopup = function (options) {
        options.windowName = options.windowName || 'ConnectWithOAuth';
        options.windowOptions = options.windowOptions || 'location=0,status=0,width='+options.width+',height='+options.height+',scrollbars=1';
        options.callback = options.callback || function () {
            window.location.reload();
        };
        var that = this;
        that._oauthWindow = window.open(options.path, options.windowName, options.windowOptions);
        that._oauthInterval = window.setInterval(function () {
            if (that._oauthWindow.closed) {
                window.clearInterval(that._oauthInterval);
                options.callback();
            }
        }, 1000);
    };
 
    // $.fn.fadePage = function( options ) {
    //     var settings = $.extend({
    //         rel: this.attr('rel'),
    //         pageName: 'step-',
    //         curr: '.step-'+this.attr('rel'),
    //         allPage: $('.toggle-page'),
    //         action: 'next',
    //     }, options );
 
    //     settings.allPage.removeClass('active');
    //     settings.allPage.not( settings.curr ).fadeOut( "fast", function() {
    //         $(settings.curr).fadeIn();
    //     });
    // };

    var _callMultipleArea = function ( objCity ) {

        if( $('.multiple-area').length > 0 ) {
            var objMultipeArea = $('.multiple-area a.dropdown-toggle');
            var defaultTitleArea = objMultipeArea.attr('data-empty');
            var defaultTitleAreaParent = objMultipeArea.attr('data-empty-parent');
            var city_id = objCity.val();

            $('.multiple-area input[type="checkbox"]').prop('checked', false);
            $('.multiple-area ul.dropdown-menu').html('');

            if( city_id != '' ) {
                $('.multiple-area a.dropdown-toggle span.title').html(defaultTitleArea);
            } else {
                $('.multiple-area a.dropdown-toggle span.title').html(defaultTitleAreaParent);
            }
        }
    }

    var _callAllArea = function ( objCity ) {
        if( $('.all-area').length > 0 ) {
            var objMultipeArea = $('.all-area a.dropdown-toggle');
            var defaultTitleArea = objMultipeArea.attr('data-empty');
            var defaultTitleAreaParent = objMultipeArea.attr('data-empty-parent');
            var city_id = objCity.val();

            if( city_id != '' ) {
                $('.all-area a.dropdown-toggle span').html(defaultTitleArea);
            } else {
                $('.all-area a.dropdown-toggle span').html(defaultTitleAreaParent);
            }
        }
    }

    var resetLocation = function(settings, parents, param){
        var objCity = parents.find(settings.citySelector);
        var objSubarea = parents.find(settings.subareaSelector);
        var objZip = parents.find(settings.zipSelector);

        var cityChange = $.checkUndefined($(settings.locationRoot).attr('data-city-change'), 'true');

        if( param == 'region' ){
            if ( objCity.is( "select" ) ) {
                objCity.trigger('chosen:updated');

                if( cityChange == 'true' ) {
                    objCity.trigger('change');
                }
            }

            _callMultipleArea(objCity);
            _callAllArea(objCity);
        } else if( param == 'city' ){
            objSubarea.trigger('change');
            objSubarea.trigger('chosen:updated');

            _callMultipleArea(objCity);
            _callAllArea(objCity);
        } else if( param == 'subarea' ){
            objZip.val('');
        }
    }

    function _callGenerateLblLocation ( objLocation, value, empty ) {
        var emptyLabel = '';

        if ( objLocation.is( "select" ) ) {
            if( value != '' ) {
                var emptyLabel = $.checkUndefined(objLocation.attr('data-empty'), empty);
            } else {
                var emptyLabel = $.checkUndefined(objLocation.attr('data-empty-parent'), empty);
            }
            
            emptyLabel = $.replaceTag('<option value="">[%replacement%]</option>', emptyLabel);
        }
        
        return emptyLabel;
    }

    $.generateLocation = function( options ){
        var settings = $.extend({
        	region_empty: 'Pilih Provinsi',
            city_empty: 'Pilih Kota',
            area_empty: 'Pilih Area',
            locationRoot: '.locations-root',
            locationTrigger: '.locations-trigger',
            currentRegionID: '.currRegionID',
            currentCityID: '.currCityID',
            currentSubareaID: '.currSubareaID',
            regionSelector: '.regionId',
            citySelector: '.cityId',
            subareaSelector: '.subareaId',
            zipSelector: '.rku-zip',
            addr: rkuAddress,
            addr2: rkuAddress2,
            no: rkuAddressNo,
        }, options );

		var objLocationRoot	= $(settings.locationRoot);
		var regionChange	= $.checkUndefined(objLocationRoot.attr('data-region-change'), 'true');
		var cityChange		= $.checkUndefined(objLocationRoot.attr('data-city-change'), 'true');
		var areaChange		= $.checkUndefined(objLocationRoot.attr('data-area-change'), 'true');

		if(regionChange == 'true' && $(settings.regionSelector).length){
		//	regions
            $(settings.regionSelector).off('change').change(function(){
				var self				= $(this);
				var objWrapper			= self.closest(settings.locationTrigger);
				var dataRemove			= $.checkUndefined(self.data('remove'), false);
				var dataTarget			= $.checkUndefined(self.data('target'), false);
				var dataEmptyCity		= $.checkUndefined(self.data('empty-city'), true);
				var dataTriggerChange	= $.checkUndefined(self.data('trigger-change'), false);

				if(self.is('select')){
					var selectedOption	= self.find('option:selected');
					var regionID		= selectedOption.val();
					var regionName		= selectedOption.text();
				}
				else{
					var regionID	= self.val();
					var regionName	= regionID;
				}

				if(objWrapper.find(settings.currentRegionID).length){
					objWrapper.find(settings.currentRegionID).val(regionID);
				}

				var objCity = objWrapper.find(settings.citySelector);

				if(objCity.length && objCity.is('select')){
					var cityOptions	= [];
					var firstCityID	= ''; 

					if(dataEmptyCity){
						var emptyLabel = $.checkUndefined(objCity.attr('data-empty'), settings.city_empty);
							emptyLabel = _callGenerateLblLocation(objCity, regionID, emptyLabel);

						cityOptions.push(emptyLabel);
					}

					if(regionID != '' && typeof window.cities == 'object' && window.cities.length){
						var cityList = window.cities[regionID];

						if(cityList.length){
							$.each(cityList, function(cityIndex, cityValue){
								var cityID		= $.checkUndefined(cityValue[0]);
								var cityName	= $.checkUndefined(cityValue[1]);

								if(cityIndex == 0){
									firstCityID = cityID;
								}

							//	build options list
								cityOptions.push('<option value="'+ cityID +'">' + cityName + '</option>');
							});
						}
					}

				//	set default value for city
					objCity.html(cityOptions.join('')).attr('data-text-temp', regionName).val(dataEmptyCity ? '' : firstCityID);

					if(objCity.attr('data-role') == 'chosen-select' || objCity.hasClass('chosen-select')){
						objCity.trigger('chosen:updated');
					}
				}

				if(regionID != '' && typeof gmapRku != 'undefined' && gmapRku.length){
					$.updateGMap({
						map		: gmapRku,
						addr	: $.getAddress(),
					});
				}

				if(dataRemove && objWrapper.find(dataRemove).length){
					objWrapper.find(dataRemove).empty();
				}

				if(dataTarget && objWrapper.find(dataTarget).length){
					objWrapper.find(dataTarget).html(regionName);
				}

				if(dataTriggerChange == 'city' && objCity.length){
					objCity.trigger('change');
				}

				resetLocation(settings, objWrapper, 'region');
			});

			if(cityChange == 'true' && $(settings.citySelector).length){
			//	cities
				$(settings.citySelector).off('change').change(function(){
					var self				= $(this);
					var objWrapper			= self.closest(settings.locationTrigger);
					var dataRemove			= $.checkUndefined(self.data('remove'), false);
					var dataTarget			= $.checkUndefined(self.data('target'), false);
					var dataEmptySubarea	= $.checkUndefined(self.data('empty-subarea'), true);
					var dataTriggerChange	= $.checkUndefined(self.data('trigger-change'), false);

					if(self.is('select')){
						var selectedOption	= self.find('option:selected');
						var cityID			= selectedOption.val();
						var cityName		= selectedOption.text();
					}
					else{
						var cityID		= self.val();
						var cityName	= cityID;
					}

					if(objWrapper.find(settings.currentCityID).length){
						objWrapper.find(settings.currentCityID).val(cityID);
					}

					var objRegion	= objWrapper.find(settings.regionSelector);
					var objSubarea	= objWrapper.find(settings.subareaSelector);
					var regionID	= objRegion.val();
					var regionName	= objRegion.text();

					if(dataTarget && objWrapper.find(dataTarget).length){
						objWrapper.find(dataTarget).html(cityName + ', ' + regionName);
					}

				//	if(objSubarea.length){
						if(cityID == ''){
							if(dataEmptySubarea && objSubarea.is('select')){
								var emptyLabel = $.checkUndefined(objSubarea.attr('data-empty'), settings.area_empty);
									emptyLabel = _callGenerateLblLocation(objSubarea, cityID, emptyLabel);

								objSubarea.empty().html(emptyLabel);
							}
							else{
								objSubarea.val('');
							}

							resetLocation(settings, objWrapper, 'city');
						}
						else{
						//	get subarea list
							var objZip			= objWrapper.find(settings.zipSelector);
							var objMultipleArea	= objWrapper.find('.multiple-area');
							var multipleAreaJXHR;

							if(objMultipleArea.length){
								if(regionID != ''){
									var fieldName = $.checkUndefined(objMultipleArea.attr('data-fieldname'), false);
									var targetURL = '/ajax/get_list_subareas/'+regionID+'/'+cityID+'/';

									if(fieldName){
										targetURL+= fieldName + '/';
									}

									if(multipleAreaJXHR){
										multipleAreaJXHR.abort();
									}

									multipleAreaJXHR = $.ajax({
										url : targetURL,
										type : 'POST',
										beforeSend : function(){
											$.loadingbar_progress('beforeSend');
										},
										success : function(result){
											objMultipleArea.html(result);
											$.dropDownMenu();
										},
										error : function(XMLHttpRequest, textStatus, errorThrown){
											alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
										}
									}).always(function(){
										$.loadingbar_progress('always');
										return false;
									});
								}
							}
							else if(objSubarea.length){
								var targetURL = '/ajax/get_subareas/'+regionID+'/'+cityID+'/';

								$.ajaxUpdateElement(self, objSubarea, targetURL, function(){
									objSubarea.trigger('change');

									if(objSubarea.attr('data-role') == 'chosen-select' || objSubarea.hasClass('chosen-select')){
										objSubarea.trigger('chosen:updated');
									}

								//	https://basecamp.com/1789306/projects/10415456/todos/359349920
								//	if(objZip.length){
								//		objZip.val('');
								//	}
								});
							}

							if(typeof gmapRku != 'undefined' && gmapRku.length){
								$.updateGMap({
									map		: gmapRku,
									addr	: $.getAddress(),
								});
							}
						}
				//	}
				});
			}

			if(areaChange == 'true'){
			//	subareas
				$(settings.subareaSelector).off('change').change(function(){
					var self		= $(this);
					var objWrapper	= self.closest(settings.locationTrigger);
					var objZip		= objWrapper.find(settings.zipSelector);

					if(self.is('select')){
						var selectedOption	= self.find('option:selected');
						var subareaID		= selectedOption.val();
						var subareaName		= selectedOption.text();
					}
					else{
						var subareaID	= self.val();
						var subareaName	= subareaID;
					}

					if(subareaID == ''){
						if(objZip.length && objZip.val() == ''){
						//	https://basecamp.com/1789306/projects/10415456/todos/359349920
							resetLocation(settings, objWrapper, 'subarea');
						}
					}
					else if(objZip.length){
                        if(objZip.val() == ''){
                        //	https://basecamp.com/1789306/projects/10415456/todos/359349920
						//	auto fill only if zip is empty
							$.ajaxUpdateElement(self, objZip, '/ajax/get_zip/'+subareaID+'/');
                        }

						if(typeof gmapRku != 'undefined' && gmapRku.length){
							$.updateGMap({
								map		: gmapRku,
								addr	: $.getAddress(),
							});
						}
					}
                }).trigger('change');
            }

			if($(settings.regionSelector).length){
				$(settings.regionSelector).each(function(){
					var self			= $(this);
					var objWrapper		= self.closest(settings.locationTrigger);
					var objCity			= objWrapper.find(settings.citySelector);
					var currentRegionID	= self.val();

					if(objWrapper.find(settings.currentRegionID).length){
						currentRegionID = objWrapper.find(settings.currentRegionID).val();
					}

					if(self.is('select')){
						var regionOptions	= [];
						var emptyLabel		= $.checkUndefined(self.attr('data-empty'), settings.region_empty);
							emptyLabel		= _callGenerateLblLocation(self, currentRegionID, emptyLabel);

						regionOptions.push(emptyLabel);

						if(typeof window.regions == 'object' && window.regions.length){
							$.each(window.regions, function(regionIndex, regionValue){
								var regionID	= $.checkUndefined(regionValue[0]);
								var regionName	= $.checkUndefined(regionValue[1]);

							//	build options list
								regionOptions.push('<option value="'+ regionID +'">' + regionName + '</option>');
							});
						}

						self.html(regionOptions.join(''));
					}

					self.val(currentRegionID);

					if(self.attr('data-role') == 'chosen-select' || self.hasClass('chosen-select')){
						self.trigger('chosen:updated');
					}

					if(objCity.length){
						var currentCityID = objCity.val();

						if(objWrapper.find(settings.currentCityID).length){
							currentCityID = objWrapper.find(settings.currentCityID).val();
						}

						if(objCity.is('select')){
							var cityOptions	= [];
							var emptyLabel	= $.checkUndefined(objCity.attr('data-empty'), settings.city_empty);
								emptyLabel	= _callGenerateLblLocation(objCity, currentRegionID, emptyLabel);

							cityOptions.push(emptyLabel);

							if(currentRegionID != '' && typeof window.cities == 'object' && window.cities.length){
								var cityList = window.cities[currentRegionID];

								if(cityList.length){
									$.each(cityList, function(cityIndex, cityValue){
										var cityID		= $.checkUndefined(cityValue[0]);
										var cityName	= $.checkUndefined(cityValue[1]);

									//	build options list
										cityOptions.push('<option value="'+ cityID +'">' + cityName + '</option>');
									});
								}
							}

						//	set default value for city
							objCity.html(cityOptions.join(''));
						}

						objCity.val(currentCityID);

						if(objCity.attr('data-role') == 'chosen-select' || objCity.hasClass('chosen-select')){
							objCity.trigger('chosen:updated');
						}
					}
				});
            }
        }

	//	var objAdditionalInputs = $([settings.addr, settings.addr2, settings.no, settings.zipSelector]);
		var objAdditionalInputs = $(settings.addr).add(settings.addr2).add(settings.no).add(settings.zipSelector);

		if(objAdditionalInputs.length){
			objAdditionalInputs.off('blur').blur(function(event){
				if(typeof gmapRku != 'undefined' && gmapRku.length){
					$.updateGMap({
						map		: gmapRku,
						addr	: $.getAddress(),
					});
				}
			});
		}
    }

    $.ajaxUpdateElement = function(el, target, url, callback) {
        $.post(url, function(data) {
            if(target.is("select")) {
                target.html($.trim(data));
            } else {
                target.val($.trim(data));
            }

            if (typeof callback == "function") {
                callback();
            }
        });
    };

    $.gmapLocation = function( options ) {
        var settings = $.extend({
            mapZoom: 16,
            gmap: gmapRku,
            latitude: rkuLatitude.val(),
            longitude: rkuLongitude.val(),
            locations: rkuLocation.val(),
        }, options );

        if( settings.locations != '' ) {
            settings.gmap.gmap3({
                action:'init',
                options:{
                    center: [settings.latitude, settings.longitude],
                    zoom: settings.mapZoom,
                    scrollwheel: false,
                },
                callback: function(results) {
                    $.addGMapMarker({
                        map: gmapRku,
                        locations: [settings.latitude, settings.longitude],
                    });
                }
            });
        } else { 
            settings.gmap.gmap3({
                action:'init',
                options:{
                    zoom: settings.mapZoom,
                    scrollwheel: false,
                },
            });
        }
    };

    $.updateLocationData = function( options ) {
        var settings = $.extend({
            marker: '',
            latitude: rkuLatitude,
            longitude: rkuLongitude,
            locations: rkuLocation,
        }, options );

        if(settings.marker) {
            point = settings.marker.getPosition();
            settings.latitude.val(point.lat());
            settings.longitude.val(point.lng());
            settings.locations.val(point.lat() + ', ' + point.lng());
        }
    };

    $.addGMapMarker = function( options ) {
        var settings = $.extend({
            map: gmapRku,
            locations: '',
            infowindow: '',
            dragendPoin: $('.rku-dragend'),
            mapZoom: 16,
        }, options );
        var content = '<div id="mapwin_title">'+settings.infowindow+'</div>';
        var markerGMap = settings.map.gmap3({
            action:'get', 
            name:'marker',
            first: true
        });

        if(!markerGMap) {
            settings.map.gmap3({
                action: 'addMarker',
                latLng: settings.locations,
                map: {
                    center: true,
                    zoom: settings.mapZoom
                },
                scrollwheel: false,
                marker: {
                    options: {
                        draggable: true,
                        icon: iconMarker,
                        shadow: shadow,
                        shape: shape
                    },
                    events: {
                        dragend: function(marker, event, data){
                            $.updateLocationData({
                                marker: marker, 
                            });

                            settings.dragendPoin.val(1);
                        },
                        click: function(marker, event){
                            if( settings.infowindow != '' && typeof settings.infowindow != "undefined" ) {
                               $(this).gmap3({
                                action: 'addinfowindow',
                                anchor: marker,
                                options: {
                                  content: content
                                }
                              });
                            }
                        },
                    },
                    callback: function(marker) {
                        propertyMarker = marker;

                        $.updateLocationData({
                            marker: marker, 
                        });
                    }
                }
            });
        } else {
            updateGmapMarker({
                map: settings.map,
                marker: markerGMap,
                locations: settings.locations,
            });
        }
    };

    $.updateGmapMarker = function(options) {
        var settings = $.extend({
            map: gmapRku,
            marker: '',
            locations: '',
        }, options );

        settings.marker.setPosition(settings.locations);
        settings.map.gmap3({
            action:'panTo', 
            args:[settings.locations]
        });
        $.updateLocationData({
            marker: settings.marker, 
        });
    }

    $.updateGMap = function( options ) {
        var settings = $.extend({
            map: gmapRku,
            addr: '',
        }, options );

        if( settings.map.length > 0 ) {
            settings.map.gmap3({
                action: 'getlatlng',
                address: settings.addr,
                callback: function (results) {
                    if (results){
                        var location = results[0].geometry.location;

                        $(this).gmap3({
                            action: 'setCenter', 
                            args:[ location ],
                        });

                        if(!propertyMarker) {
                            $.addGMapMarker({
                                map: $(this), 
                                locations: location,
                            });
                        } else {
                            $.updateGmapMarker({
                                map: $(this), 
                                marker: propertyMarker,
                                locations: location,
                            });
                        }
                    }
                }
            });
        }
    };

    $.getAddress = function( options ) {
        var settings = $.extend({
            subarea: $('.subareaId'),
            city: $('.cityId'),
            region: $('.regionId'),
            country: $('#countryId'),
            zip: $('.rku-zip'),
            addr: rkuAddress,
            addr2: rkuAddress2,
            no: rkuAddressNo,
        }, options );

        var locations = [];
        var address = '';

        var objLocationWrapper = $('.subareaId, .cityId, .regionId').closest('div.location-wrapper');

        if(objLocationWrapper.length){
            var objLocationPicker = objLocationWrapper.find('input.location-picker');

            if(objLocationPicker.length){
                locations.push(objLocationPicker.val());
            }
        }
        else{
            var selectedOption;

            if(settings.subarea.is('select')){
                selectedOption = settings.subarea.find('option:selected');

                if(selectedOption.val() != ''){
                    locations.push(selectedOption.val());
                }
            }

            if(settings.city.is('select')){
                selectedOption = settings.city.find('option:selected');

                if(selectedOption.val() != ''){
                    locations.push(selectedOption.val());
                }
            }

            if(settings.region.is('select')){
                selectedOption = settings.region.find('option:selected');

                if(selectedOption.val() != ''){
                    locations.push(selectedOption.val());
                }
            }

            if(settings.country.is('select')){
                selectedOption = settings.country.find('option:selected');

                if(selectedOption.val() != ''){
                    locations.push(selectedOption.val());
                }
            }
        }

    //  convert jadi text
        locations = locations.join(', ');

        if(settings.addr.val()) {
            address = settings.addr.val().replace(/\r\n|\r|\n/g,", ");

            if( typeof settings.addr2.val() != 'undefined' && settings.addr2.val() != '' ) {
                address += ', ' + settings.addr2.val();
            }

            if( typeof settings.no.val() != 'undefined' && settings.no.val() != '' ) {
                address += ' No.' + settings.no.val();
            }

            if( locations != '' ) {
                address += ', ';
            }

            locations = address + locations;
        }

        if(typeof settings.zip.val() != 'undefined' && settings.zip.val()) {
            locations += ' ' + settings.zip.val();
        }

        return locations;
    }

    if( gmapRku.length > 0 ) {
        $.gmapLocation();
    }

    $.directAjaxLink = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-link'),
        }, options );

        var valObj = settings.obj.val();
        var urlDefault = settings.obj.attr('href');
        var url = $.checkUndefined(settings.obj.attr('data-url'), urlDefault);
        var url_form = $.checkUndefined(settings.obj.attr('data-url-form'), false);

        var parents = settings.obj.parents('.ajax-parent');
        var type = settings.obj.attr('data-type');
        var flag_alert = settings.obj.attr('data-alert');
        var data_ajax_type = settings.obj.attr('data-ajax-type');
        var data_wrapper_write = settings.obj.attr('data-wrapper-write');
        var data_wrapper_write_page = $.checkUndefined(settings.obj.attr('data-wrapper-write-page'), false);
        var data_action = settings.obj.attr('data-action');
        var data_pushstate = settings.obj.attr('data-pushstate');
        var data_url_pushstate = settings.obj.attr('data-url-pushstate');
        var data_form = settings.obj.attr('data-form');
        var data_scroll = settings.obj.attr('data-scroll');
        var data_scroll_top = $.convertNumber(settings.obj.attr('data-scroll-top'));
        var data_scroll_time = $.convertNumber(settings.obj.attr('data-scroll-time'), 'int', 2000);
        var data_use_current = settings.obj.attr('data-use-current-value');
        var data_remove = $.checkUndefined(settings.obj.attr('data-remove'), false);
        var data_hide = $.checkUndefined(settings.obj.attr('data-hide'), false);
        var data_focus = $.checkUndefined(settings.obj.attr('data-on-focus'), false);
        var data_location = $.checkUndefined(settings.obj.attr('data-location'), false);
        var data_field_change = $.checkUndefined(settings.obj.attr('data-field-change'), false);
        var data_loadingbar = $.checkUndefined(settings.obj.attr('data-loadingbar'), false);
        var data_abort= $.checkUndefined(settings.obj.attr('data-abort'), false);
        var data_click = $.checkUndefined(settings.obj.attr('data-on-click'), null);
        var data_location_additional = $.checkUndefined(settings.obj.attr('data-location-additional'), false);
        var data_reload_chart = $.checkUndefined(settings.obj.attr('data-reload-chart'), null);
        var data_show_loading_bar = $.checkUndefined(settings.obj.attr('data-show-loading-bar'), null);

        var data_trigger_after_wrapper = $.checkUndefined(settings.obj.attr('data-trigger-after-wrapper'), null);
        var data_trigger_after = $.checkUndefined(settings.obj.attr('data-trigger-after'), null);
        
        if(data_field_change.length > 0){
            var data_field = data_field_change.split(' ');    
        }
        
        var formData = false; 

        if( flag_alert != null ) {
            if ( !confirm(flag_alert) ) { 
                return false;
            }

        //  tambahan untuk fly-button-media (delete button)
            if(settings.obj.hasClass('fly-button-media')){
                settings.obj.hide();
            }
        }

        if(typeof data_ajax_type == 'undefined' ) {
            data_ajax_type = 'html';
        }

        if(typeof data_wrapper_write == 'undefined' ) {
            data_wrapper_write = '#wrapper-write';
        }

        if(typeof data_pushstate == 'undefined' ) {
            data_pushstate = false;
        }

        if(typeof data_url_pushstate == 'undefined' ) {
            data_url_pushstate = url;
        }

        if(typeof data_form != 'undefined' ) {
            var formData = $(data_form).serialize(); 

            if( url_form == 'true' ) {
                url = $(data_form).attr('action');
            }
        }

        if(typeof type == 'undefined' ) {
            type = 'content';
        }

        if(data_action == 'ebrosur-request' || data_use_current == 'true'){
            url += '/'+settings.obj.val()+'/';
        }

        if( data_abort == 'true' && loadJXHR ){
            loadJXHR.abort();
        }

        loadJXHR = $.ajax({
            url: url,
            type: 'POST',
            dataType: data_ajax_type,
            data: formData,
            beforeSend  : function() {
                $.loadingbar_progress('beforeSend', data_loadingbar);
                if(data_show_loading_bar != null || data_show_loading_bar == 'true'){
                    $(data_wrapper_write).addClass('ajax-loading');
                    $(data_wrapper_write).find('.ajax-loading').removeClass('ajax-loading');
                }
            },
            success: function(result) {
                var msg = result.msg;
                var status = result.status;

                if(data_show_loading_bar != null || data_show_loading_bar == 'true'){
                    $(data_wrapper_write).removeClass('ajax-loading');
                }

                if( type == 'content' ) {
                    var contentHtml = $(result).filter(data_wrapper_write).html();
                    var hid_pushstate_url = $.checkUndefined($(result).find('#hid-pushstate-url').val(), data_url_pushstate);

                    if( data_pushstate != false ) {
                        window.history.pushState('data', '', hid_pushstate_url);
                    }

                    if(typeof contentHtml == 'undefined' ) {
                        contentHtml = $(result).find(data_wrapper_write).html();
                    }

                    if( data_field_change != false ) {
                        if(data_field.length > 0){
                            var i;
                            for (i = 0; i < data_field.length; ++i) {
                                fieldChange = $(result).find(data_field[i]).val();
                                data_field[i] = $.checkUndefined(data_field[i], false);

                                if( data_field[i] == '.save-path'){
                                    var save_path = fieldChange;
                                }

                                if(data_field[i] == '.document-imb'){
                                    var split = fieldChange.split('.');
                                    $('.upload-imb').attr('disabled', false);

                                    if(fieldChange != ''){
                                        $('.show-imb').show();
                                        $('.preview-img').remove();
                                        if(split[1] == 'pdf'){
                                            $(data_field[i]).attr('src', "/img/pdf.png");
                                        }else{
                                            $(data_field[i]).attr('src', "/img/view/"+save_path+"/m"+fieldChange);
                                        } 
                                    }
                                   
                                }else{
                                    $(data_field[i]).val(fieldChange);
                                }
                            }
                        }
                        
                        
                    }

                    if(typeof data_scroll != 'undefined' && data_scroll != 'false' ) {
                        var theOffset = $(data_scroll).offset();
                        $('html, body').animate({
                            scrollTop: theOffset.top + data_scroll_top,
                        }, data_scroll_time);
                    }

                    if( data_remove != false ) {
                        $(data_remove).remove();
                    }
                    if( data_hide != false ) {
                        $(data_hide).hide();
                    }
                    if( data_focus != false ) {
                        $(data_focus).focus();
                    }
                    
                    if( data_click != null ) {
                        if( data_click.indexOf('[[') >= 0 ) {
                            var dataClickArr = eval(data_click);

                            if( $.isArray(dataClickArr) ) {
                                $.each( dataClickArr, function( i, val ) {
                                    targetWrapper = $.checkUndefined(val[0], null);
                                    targetClick = $.checkUndefined(val[1], null);

                                    var targetFound = $.checkUndefined($(result).filter(targetWrapper).html(), null);

                                    if( targetFound == null ) {
                                        targetFound = $.checkUndefined($(result).find(targetWrapper).html(), null);
                                    }

                                    if( targetFound != null ) {
                                        $(targetClick).trigger('click');
                                    }
                                });
                            }
                        } else if( $(data_click).length > 0 ) {
                            $(data_click).trigger('click');
                        }
                    }

                    if( data_wrapper_write_page != false ) {
                        var data_wrapper_arr = data_wrapper_write_page.split(',');
                        
                        $.each(data_wrapper_arr, function(index, identifier){
                            var targetWrapper = $.trim(identifier);
                            var sourceWrapper = $(result).filter(targetWrapper);

                            if(sourceWrapper.length <= 0){
                                sourceWrapper = $(result).find(targetWrapper);
                            }

                            if(sourceWrapper.length){
                                if( $(targetWrapper).length > 0 ) {
                                    $(targetWrapper).html(sourceWrapper.html());
                                    $.rebuildFunctionAjax($(targetWrapper));
                                }
                            }
                        });
                    } else if( $(data_wrapper_write).length > 0 ) {
                        $(data_wrapper_write).html(contentHtml);
                        $.rebuildFunctionAjax( $(data_wrapper_write) );

                        if( data_location == 'true' || data_action == 'input-file' ) {
                            if(data_location_additional == 'true'){
                                $.generateLocation({
                                    currentRegionID: $('#currRegionIDaditionals').val(),
                                    currentCityID: $('#currCityIDaditionals').val(),
                                    regionSelector: $('#regionIdAdditional'),
                                    citySelector: $('#cityIdAdditional'),
                                    subareaSelector: $('#subareaIdAdditional'),
                                    zipSelector: $('#rku-zip-Additional'),
                                });
                            }else{
                                $.generateLocation();
                            }
                        }

                        if(data_action == 'input-file' ) {
                            $.handle_input_file();
                            $.option_image_ebrosur();
                            $.updateLiveBanner('trigger-handle-agent-ebrosur', 'onload');
                            $.limit_word_package();
                            $.limit_word_package({
                                obj: $('.desc-info-cls'),
                                objCounter: $('.limit-character2'),
                                objBody: false
                            });
                        }
                    }
                }
                $.inputPrice();
                $.actionPopover();
                $('.error-full.alert').remove();

                if(data_trigger_after != null && data_trigger_after_wrapper != null){
                    var path_class = data_wrapper_write+' '+data_trigger_after_wrapper;

                    if($(path_class).length > 0){
                        $(path_class).trigger(data_trigger_after);
                    }
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if(errorThrown != 'abort'){
                    console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            }
		}).always(function(param1, param2, param3){
		//	each param value is optional, only param2 is fixed (textStatus)
		//	success	: data, textStatus, jqXHR
		//	failed	: jqXHR, textStatus, errorThrown

		//	console.log(param2);

			if(param2 != 'abort'){
				$.loadingbar_progress('always', data_loadingbar);
			}
		}).done(function(data){
            if(data_reload_chart != null){
                $.formatDateChart(settings.obj, valObj);

                var obj_chart = $(data_wrapper_write).find('.reload[data-load="infinity"]');
                $.callGenerateChart(obj_chart, 1, true);
            }
        });
    }

    $.flashNotice = function (type, message, autoRender, addclass) {
        var types = ['info', 'success', 'warning', 'error'];
        var flash = null;
        autoRender = $.checkUndefined(autoRender, true);

        if($.inArray(type, types) !== -1){
            addclass = $.checkUndefined(addclass, 'notice ' + type + ' margin-bottom-3');

        //  generate element
            flash = $('<div></div>').addClass(addclass);

        //  append message
            flash.html($('<p></p>').html(message)); 

            if(autoRender){
            //  modal
                var modal = $('#myModal').clone();
                    modal = $(modal);

            //  cleanup modal
                modal.attr('id', 'flash-modal').find('.modal-title, .modal-bottom').remove();

            //  append flash to modal
                modal.find('.modal-body').html(flash);

            //  append modal to body
                $('body').append(modal);
                $('#flash-modal').modal('show');
            }
            else{
                return flash;
            }
        }
    };

    $.option_image_ebrosur = function(){
        $('ul.box-list-medias li').click(function(){
            var self = $(this);
            var url_image = self.find('img').attr('src');

            $('ul.box-list-medias li').removeClass('active');

            self.addClass('active');

            var media_id = self.attr('data-media-id');

            if(typeof media_id != 'undefined'){
                $('#property-media-id').val(media_id);
                $('.file-image-live, #filename-hide-ebrosur').val('');
            }else{
                $('#property-media-id').val('');
            }

            $('.live.ebanner .property-image img').attr('src', url_image);
        });
    }

    $.ajaxLink = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-link'),
            objChange: $('.ajax-change, .form-table-search table td select.form-control'),
            objBlur: $('.ajax-blur'),
            objKeyup: $('.ajax-keyup, .form-table-search table td .form-control, .table-header .dropdown-group #sorted'),
            objAttribute: $('.ajax-attribute'),
        }, options );

        if( $('.ajax-link').length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(){
                var self = $(this);

                if(self.hasClass('check-multiple-delete')){
                    return false;
                }

                $.directAjaxLink({
                    obj: self,
                });

                return false;
            });
        }

        if( settings.objChange.length > 0 ) {
            settings.objChange.off('change');
            settings.objChange.change(function(){
                var self = $(this);
                
                $.directAjaxLink({
                    obj: self,
                });

                return false;
            });
        }

        if( $('.ajax-blur').length > 0 ) {
            settings.objBlur.off('blur').blur(function(){
                var self = $(this);
                
                $.directAjaxLink({
                    obj: self,
                });

                return false;
            });
        }

        if( settings.objKeyup.length > 0 ) {
            settings.objKeyup.off('keyup');
            settings.objKeyup.keyup(function(){
                var self = $(this);

			//	setTimeout( function(){
			//		$.directAjaxLink({
			//			obj : self,
			//		});
			//	}, 1000);

				delay(function(){
					$.directAjaxLink({
						obj: self,
					});
				}, 500);

                return false;
            });
        }

        if( $('.ajax-attribute').length > 0 ) {
            settings.objAttribute.off('change');
            settings.objAttribute.change(function(){
                var self = $(this);
                var addParam = $.checkUndefined(self.attr('data-params'), '');
                var current_data = $.checkUndefined(self.attr('data-use-current-value'), 'true');
                var href = $.checkUndefined(self.attr('data-href'), '/admin/ajax/attributes');

                if( addParam != '' ) {
                    addParam = '/' + addParam;
                }
                if( current_data == 'true' ) {
                    sel_val = self.val();
                } else {
                    sel_val = 0;
                }
                
                self.attr('href', href + '/' + sel_val + addParam + '/');
                
                $.directAjaxLink({
                    obj: self,
                });

                return false;
            });
        }
    }

    $.disabledSubmit = function( options ) {
        var settings = $.extend({
            obj: $('form'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('submit');
            settings.obj.submit(function(){
                var self = $(this);
                var button = self.find('button[type="submit"],button[type="button"]');
                var idAttr = self.attr('id');
                var autoDisable = true;

            //  For some browsers, `attr` is undefined; for others,
            //  `attr` is false.  Check for both.
                if(typeof self.attr('data-auto-disable') !== typeof undefined && self.attr('data-auto-disable') !== false){
                    autoDisable = self.attr('data-auto-disable');
                }

                if( idAttr != 'form-report' && (autoDisable === true || autoDisable === 'true') ) {
                    button.attr('disabled', true);
                }
            });
        }
    }

    var ajaxFormJXHR;
    $.getAjaxForm = function( self ) {
        if(ajaxFormJXHR){
            ajaxFormJXHR.abort();
        }

        var url = self.attr('action');
        var type = self.attr('data-type');
        var flag_alert = self.attr('data-alert');
        var data_ajax_type = self.attr('data-ajax-type');
        var formData = self.serialize(); 
        var data_wrapper_write = self.attr('data-wrapper-write');
        var data_wrapper_success = self.attr('data-wrapper-success');
        var data_pushstate = self.attr('data-pushstate');
        var data_url_pushstate = self.attr('data-url-pushstate');
        var data_reload = self.attr('data-reload');
        var data_reload_url = self.attr('data-reload-url');
        var data_close_modal = self.attr('data-close-modal');
        var data_scroll = self.attr('data-scroll');
        var data_scroll_top = $.convertNumber(self.attr('data-scroll-top'));
        var data_scroll_time = $.convertNumber(self.attr('data-scroll-time'), 'int', 2000);
        var data_location = $.checkUndefined(self.attr('data-location'), false);
        var data_location_additional = $.checkUndefined(self.attr('data-location-additional'), false);
        var data_to_top = $.checkUndefined(self.attr('data-to-top'), false);
        var data_to_url = $.checkUndefined(self.attr('data-to-url'), false);

        if( flag_alert != null ) {
            if ( !confirm(flag_alert) ) { 
                return false;
            }
        }

        if(typeof data_ajax_type == 'undefined' ) {
            data_ajax_type = 'html';
        }

        if(typeof data_wrapper_write == 'undefined' ) {
            data_wrapper_write = '#wrapper-write';
        }

        if(typeof data_pushstate == 'undefined' ) {
            data_pushstate = false;
        }

        if(typeof data_url_pushstate != 'undefined' ) {
            data_url_pushstate = url;
        }
        var button_submit = self.find('button[type="submit"]');
        var flag_button_submit = $.checkUndefined(button_submit.attr('data-auto-disable'), 'true');

        ajaxFormJXHR = $.ajax({
            url: url,
            type: 'POST',
            dataType: data_ajax_type,
            data: formData,
            beforeSend:function(){
                if( flag_button_submit == 'true' ) {
                    button_submit.attr('disabled', 'disabled');
                }
            },
            success: function(result) {
                if( type == 'content' ) {
                    var content = result;
                    var status = $(content).find('#msg-status').html();
                    var msg = $(content).find('#msg-text').html();
                    var to_url = $(content).find('#to-url').html();
                //  console.log(data_wrapper_write);
                    var contentHtml = $(content).filter(data_wrapper_write).html();

                    if(typeof contentHtml == 'undefined' ) {
                        contentHtml = $(content).find(data_wrapper_write).html();
                    }

                    if(typeof data_scroll != 'undefined' && data_scroll != 'false' ) {
                        var theOffset = $(data_scroll).offset();
                        $('html, body').animate({
                            scrollTop: theOffset.top + data_scroll_top,
                        }, data_scroll_time);
                    }

                    var objModal        = $('.modal:visible');
                    var objModalDialog  = objModal.find('.modal-dialog');

                    if(objModal.length){
                        var modalTitle  = $.checkUndefined(self.attr('title'), '');
                        var modalSize   = $.checkUndefined(self.attr('data-size'), '');

                        if(modalSize != ''){
                            objModalDialog.removeClass('modal-xs modal-sm modal-md modal-lg modal-xl modal-fluid').addClass(modalSize);
                        }

                        if(modalTitle != ''){
                            var objModalTitle = objModal.find('.modal-title');

                            if(objModalTitle.length){
                                objModalTitle.html(modalTitle).show();
                            }
                        }
                    }

                    // UNDER DEVELOPMENT
                //  if( status == 'success' && data_reload == 'true' ) {
                    if( ( status != 'error' && status != 'undefined' ) && data_reload == 'true' ) { // ??????
                //  if(typeof status != 'undefined' && status != 'error' && data_reload == 'true'){
                        if(data_to_top == 'true' || data_to_top == true){
                            $('html').animate({scrollTop:0}, 1);
                            $('body').animate({scrollTop:0}, 1);
                        }

                        if(typeof data_reload_url == 'undefined' ) {
                            window.location.reload();
                        } else {
                            location.href = data_reload_url;
                        }
                    } else if( $(data_wrapper_write).length > 0 ) {
                         if(status == 'success' && typeof data_wrapper_success != 'undefined' && $(data_wrapper_success).length > 0 ) {
                            contentHtml = $(content).filter(data_wrapper_success).html();

                            if(typeof contentHtml == 'undefined' ) {
                                contentHtml = $(content).find(data_wrapper_success).html();
                            }

                            $(data_wrapper_success).html(contentHtml);
                            $.rebuildFunctionAjax( $(data_wrapper_success) );

                            if( data_pushstate != false ) {
                                window.history.pushState('data', '', data_url_pushstate);

                            }

                            if( data_close_modal == 'true' ) {
                                $('#myModal .close.btn').trigger("click");
                            }
                        } else {
                            $(data_wrapper_write).html(contentHtml);
                            $.rebuildFunctionAjax( $(data_wrapper_write) );
                        }

                        if(status == 'success' && data_to_url != false && ( to_url != '' && to_url != 'undefined' )){
                            
                            window.location.href = to_url;
                        }

                        if( data_location == 'true' ) {
                            if(data_location_additional == 'true'){
                                $.generateLocation({
                                    currentRegionID: $('#currRegionIDaditionals').val(),
                                    currentCityID: $('#currCityIDaditionals').val(),
                                    regionSelector: $('#regionIdAdditional'),
                                    citySelector: $('#cityIdAdditional'),
                                    subareaSelector: $('#subareaIdAdditional'),
                                    zipSelector: $('#rku-zip-Additional'),
                                });
                            }else{
                                $.generateLocation();
                            }
                        }
                    }
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        }).always(function() {
            button_submit.attr('disabled', false);
        });

        return false;
    }

    $.ajaxForm = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-form'),
        }, options );

        settings.obj.submit(function(){
            var self = $(this);

            $.getAjaxForm ( self );

            return false;
        });
    }

    $.ajaxChangeForm = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-change-form'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.change(function(e){
                var parent = $(this).parents('form');
                parent.submit();
            });
        }
    }

    $.submitCustomForm = function( options ) {
        var settings = $.extend({
            obj: $('.submit-custom-form'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(e){
                var parent = $(this).attr('data-form');
                var url = $(this).attr('href');
                var flag_alert = $(this).attr('data-alert');

                if( flag_alert != null ) {
                    if ( !confirm(flag_alert) ) { 
                        return false;
                    }
                }

                $(parent).attr('action', url);
                $(parent).attr("method", "post");
                $(parent).submit();

                return false;
            });
        }
    }

    var autocompleteJXHR;

    $.Autocomplete = function( options ) {
        var settings = $.extend({
            obj: $('#autocomplete, [data-role="autocomplete"]'),
            obj2: $('#autocomplete2'),
        }, options );

        function _callAutocomplete ( objTarget ) {
            var url = objTarget.attr('data-ajax-url');
            var data_change = objTarget.attr('data-change');

            var autocomplete = objTarget.typeahead({
                minLength: 3,
                highlighter: function (item) {
                    var is_highlighter = objTarget.attr('data-highlighter');

                    var regex = new RegExp( '(' + this.query + ')', 'gi' );
                    
                    if(!is_highlighter || is_highlighter == 'false'){
                        result = "$1";
                    }else{
                        result = "<strong>$1</strong>";
                    }

                    return item.replace( regex, result );
                },
                afterSelect: function(item){
					var item_value = typeof item == 'string' ? encodeURI(item.split(/,(.+)?/)[0]) : item.id;

					if(objTarget.hasClass('location-picker') && typeof item == 'object'){
						var objWrapper = objTarget.closest('.locations-trigger');

						if(objWrapper.length){
							var regionID		= item.region_id ? item.region_id : null;
							var cityID			= item.city_id ? item.city_id : null;
							var subareaID		= item.subarea_id ? item.subarea_id : null;
							var zipCode			= item.zip ? item.zip : null;
							var locationName	= item.name ? item.name : null;

							if(objWrapper.find('input.regionId, input.currRegionID').length){
								objWrapper.find('input.regionId, input.currRegionID').val(regionID);
							}

							if(objWrapper.find('input.cityId, input.currCityID').length){
								objWrapper.find('input.cityId, input.currCityID').val(cityID);
							}

							if(objWrapper.find('input.subareaId').length){
								objWrapper.find('input.subareaId').val(subareaID);
							}

							if(objWrapper.find('input.rku-zip').length){
								objWrapper.find('input.rku-zip').val(zipCode);
							}

							if(typeof gmapRku != 'undefined' && gmapRku.length){
							//	update map marker position
								$.updateGMap({
									map		: gmapRku,
									addr	: $.getAddress(),
								});
							}

							objTarget.attr('data-selected', true);
						}
						else{
							return false;
						}
					}
                    else{
                    	var data_find_property = $.checkUndefined(objTarget.attr('data-find-property'), 'true');

                    	if(data_find_property == 'true'){
	                        if($('#handle-ajax-medias').length > 0){
	                            var target_ajax = $('#handle-ajax-medias');
	                            var url = target_ajax.attr('href');
	                            
	                            url_target = url+'/'+item_value+'/'+$('.file-image-ebrosur').val();

	                            target_ajax.attr('href', url_target);

	                            target_ajax.trigger('click');

	                            target_ajax.attr('href', url);
	                        }

	                        if( data_change == 'true' || data_change == 'trigger' ) {
	                            var url = $.checkUndefined(objTarget.attr('href'), '');

	                            if( data_change == 'true' ) {
	                                href = url;

	                                if(href != ''){
	                                    href+= '/';
	                                }

	                                href+= item_value + '/';

	                                objTarget.attr('href', href);
	                            }

	                            objTarget.attr('is_selected', true);

	                            $.directAjaxLink({
	                                obj: objTarget,
	                            });

	                            objTarget.attr('href', url);
	                        }
	                    }
	                    else{
	                        var data_ebrosur = $.checkUndefined(objTarget.attr('data-ebrosur'), 'false');

	                        if(data_ebrosur == 'true'){
	                            get_data_client_ebrosur(item_value);
	                        }
	                    }
                    }
                }
            });

            objTarget.on('keydown', function(e){
				var code = e.keyCode || e.which;

				if(code != 9 && code != 20 && code != 16 & code != 17) {
					var self = $(this);

					if(self.hasClass('location-picker')){
						var objWrapper			= self.closest('.locations-trigger');
						var objAdditionalInputs = objWrapper.find('input.regionId, input.cityId, input.subareaId, input.rku-zip');

						if(objAdditionalInputs.length){
						//	force clear zip code if user search other location
							objAdditionalInputs.val('');
						}
					}
					else{
						var has_been_selected = self.attr('is_selected');

						if( has_been_selected !== undefined ) {
							self.val('').removeAttr('is_selected');

							$.directAjaxLink({
								obj: self,
							});

							return false;
						}
					}
				}
			}).on('blur', function(e){
				var self		= $(this);
                var isSelected  = self.data('selected');
                var data_clear  = self.data('clear');
				var data_write	= self.data('wrapper-write');

				if(isSelected && self.hasClass('location-picker')){
					var objWrapper		= self.closest('.locations-trigger');
					var objSubareaInput	= objWrapper.find('input.subareaId');
					var objZipCode		= objWrapper.find('input.rku-zip');

				//	https://basecamp.com/1789306/projects/10415456/todos/359349920
				//	if(objSubareaInput.length && objSubareaInput.val() == '' || objZipCode.length && objZipCode.val() == ''){
				//		var objAdditionalInputs = objWrapper.find('input.regionId, input.cityId, input.subareaId, input.rku-zip');

					if(objSubareaInput.length && objSubareaInput.val() == ''){
						var objAdditionalInputs = objWrapper.find('input.regionId, input.cityId, input.subareaId');

						self.add(objAdditionalInputs).val('').removeAttr('data-selected');
					}
				}

                if( data_clear === true && $(data_write).length > 0 ) {
                    $(data_write).find('input,select').val('');
                }
			});

            autocomplete.data('typeahead').source = function (query, process) {
                delay(function(){
                    if(autocompleteJXHR){
                        autocompleteJXHR.abort();
                    }

                    var loadingClass = 'loading-circle';
                    objTarget.addClass(loadingClass);

                    autocompleteJXHR = $.ajax({
                        url             : url,
                        type            : 'POST',
                        data            : { query: query },
                        loadingClass    : loadingClass,
                        dataType        : 'json',
                        success         : function(json){
                            objTarget.removeClass(loadingClass);
                            return process(json);
                        }, 
                    });

                    return autocompleteJXHR;
                }, 500);
            };
        }

        settings.obj = $(settings.obj);

		if(settings.obj.length){
			settings.obj.each(function(index, object){
				_callAutocomplete($(object));
			});
		}

        settings.obj2 = $(settings.obj2);

		if(settings.obj2.length){
			settings.obj2.each(function(index, object){
				_callAutocomplete($(object));
				$.generateLocation();
			});
		}
    }

    $.closePopover = function( obj ) {
        $('.close-popup').click(function (e) {
            e.preventDefault();

            var self = $(this);
            var target = self.attr('data-target');

            $(target).html('');
        });
    }

    $.rebuildFunction = function() {
        if( $('.jscroll').length > 0 ) {
            $('.jscroll').jscroll();
        }

        $.disabledSubmit();
        $.ajaxLink();

        if( $('.ajax-form').length > 0 ) {
            $('.ajax-form').off('submit');
            $.ajaxForm();
        }
        
        if( $("#read-inbox").length > 0 ) {
            $("#read-inbox").off('animate');
            $("#read-inbox").animate({ scrollTop: $("#read-inbox")[0].scrollHeight}, 500);
        }
        if( $("#inbox .wrapper-left .list-inbox").length > 0 && $("#inbox .wrapper-left .list-inbox li.active") > 0 ) {
            $("#inbox .wrapper-left .list-inbox").off('animate');
            $("#inbox .wrapper-left .list-inbox").animate({ scrollTop: $("#inbox .wrapper-left .list-inbox li.active").offset().top-200}, 0);
        }

        $.tag_input();
        $.checkboxInput();
        $.ajaxChangeForm();
        $.rowAdded();
        $.draggableSorting();
        $.uploadMedias();
        $.loadFileUpload();
        $.ajaxModal();
        $.ajaxMediaTitleChange();
        $.calcKPR();
        // $.checkAll();
        $.triggerDisabled();
        $.closePopover();
        $.alert_close();
        $.scrollingTo();
        $.popupWindow();
        $.callResetFilter();
        $.callShowHideColumn();
        $.badword_filter();
        $.catcomplete();
        $.show_less_description();

        /*BEGIN - tentang fast KPR*/
        // $.KprSoldPrice();
        /*END - tentang fast KPR*/
        
        // if( $('#read-inbox').length > 0 ) {
        //     var frameScroll    = $('#read-inbox');
        //     var height = frameScroll[0].scrollHeight;
        //     frameScroll.scrollTop(height);
        // }

        if(typeof $.fn.popover == 'function' && $('[data-toggle="popover"]').length > 0){
            $.actionPopover();
        }

        $._MT_widgets();
        $.inputCounter();

    //  EXPORT PDF REPORT
        if(typeof $.screenShot == 'function'){
            $.screenShot();
        }

        renderRecaptcha();

        /*Booking Unit*/
        $.form_click_option();
        $.paymentChannelHandler();
        $.voucherHandler();
        $.callPreloader();
        $.handle_toggle();

        if($('#lot-unit-id').length){
            $('#lot-unit-id').trigger('init');
        }

        $.increment();
        $.reposition();
        $.phoneInput();
        $.multiselect();

        if(typeof gapi != 'undefined' && typeof handleClientLoad != 'undefined' && $('#signInButton').length){
            handleClientLoad();
        }
    }

    window.lastModalTop = null;

    $.reposition = function(options){
        var options = $.extend({
            obj     : $('#property_media_wrapper .fly-button-media'), 
            delay   : 100, 
        }, options);

        var object = $(options.obj);

        if(object.length){
            var parent  = object.closest('.modal').length ? object.closest('.modal') : window;
            var isModal = parent.hasClass('modal');
            var timeout = null;
            var delay   = isNaN(options.delay) ? 300 : options.delay;

            $(parent).on('scroll', function(){
                var parent = $(this);

                clearTimeout(timeout);

                timeout = setTimeout(function(){
                    var parentTop       = parent.scrollTop();
                    var windowHeight    = $(window).height();

                    object.each(function(){
                        var self        = $(this);
                        var selfTop     = self.offset().top;
                        var selfHeight  = self.height();
                        var newPosition = parseFloat(parentTop) + (parseFloat(windowHeight) / 2);
                            newPosition = newPosition - (parseFloat(selfHeight) / 2);
                            newPosition = parseInt(newPosition);

                        if(isModal){
                            var modalTop = parent.find('.modal-content').offset().top;

                            if(modalTop > 0){
                                if(window.lastModalTop === null || Math.abs(modalTop) < window.lastModalTop){
                                    window.lastModalTop = Math.abs(modalTop);
                                }
                            }

                            if(parseInt(window.lastModalTop) > 0){
                                newPosition = newPosition - parseInt(window.lastModalTop);
                            }
                        }

                        self.css({
                        //  'display'   : 'block', 
                            'position'  : 'absolute', 
                            'top'       : parseInt(newPosition) + 'px', 
                        });
                    });
                }, delay);
            });
        }
    };

    $.popoverClose = function(){
        $('.popover a.close').off('click');
        $('.popover a.close').on('click', function (e) {
            var parent = $(this).parents('.popover');
            var id = parent.attr('id');
            var describedby = $.checkUndefined($(this).attr('aria-describedby'), null);

            $('[aria-describedby="'+id+'"]').popover('hide');
            return false;
        });
    };

    $.actionPopover = function(options){
        var options = $.extend({
            obj : $('a[data-role="popover-action"], button[data-role="popover-action"]'), 
        }, options);

        var popover_trigger = function ( obj_name ) {
            obj_name.off('click');
            obj_name.on('click', function (e) {
                var describedby = $.checkUndefined($(this).attr('aria-describedby'), null);

                if( describedby == null ) {
                    $(this).popover('show');
                    obj_name.not(this).popover('hide');

                    $.popoverClose();
                    return false;
                } else {
                    $(this).popover('hide');
                }
            });
        }

        if(typeof $.fn.popover == 'function'){
            $('[data-toggle="popover"]').popover({
                html : true,
                container: 'body',
                'template': '<div class="popover" role="tooltip"><div class="arrow"></div><a href="javascript:void();" class="close"><i class="rv4-cross "></i></a><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
            });
        }

        popover_trigger($('[data-toggle="popover"]'));

        if(typeof $.fn.popover == 'function' && options.obj.length){
            options.obj.popover({
                'container' : 'body',
                'content'   : function(){
                    var self = $(this);
                    return self.next('[data-role="popover-content"]').clone().removeClass('hide');
                }, 
                'placement' : 'bottom', 
                'html'      : true, 
            });

            popover_trigger(options.obj);
        }

        options.obj.on('inserted.bs.popover', function () {
            $.rebuildFunction();
        });
    }
    
    $.rebuildFunctionAjax = function( obj, options ) {
        var options = $.checkUndefined(options, {});
        var rebuildFunction = $.checkUndefined(options.rebuild_function, true);

        if(rebuildFunction){
            $.rebuildFunction();
        }

        $.inputPrice({
            obj: obj.find('.input_price'),
        });
        $.inputNumber({
            obj: obj.find('.input_number'),
        });
        $.datePicker({
            obj: obj.find('.datepicker'),
        });
        $.Autocomplete({
            obj: obj.find('#autocomplete, [data-role="autocomplete"]'),
        });
        if (obj.find('.icheckbox').length > 0) {
            obj.find('.icheckbox').iCheck();
        }
        $.daterangepicker({
            obj: obj.find('.date-range'),
            objCustom: obj.find('.date-range-custom'),
            objCalendar: obj.find('.date-range-calendar'),
        });
        $.rebuild_toggle_form();
        $.handle_toggle_content();
        $.triggerComponentPoint();
        $.component_range();
        $.dropdownFix({
            obj: obj.find('.columnDropdown'),
        });

        if(obj.find('.tryClose').length > 0){
            $.trigger_popup();
        }

        $.editable({
            obj : obj.find('.editable'),
        });

        if( obj.find('.chosen-select').length > 0 ) {
            $.callChoosen({
                obj: obj.find('.chosen-select'),
            });
        }
    }

    $.addCustomTextField = function(){
        $('.add-custom-field').click(function (e) {
            e.preventDefault();

            var self = $(this);
            var action_type = self.attr('action_type');

            if( action_type == 'career' ) {
                var length = parseInt( $('#career-requirement-list > ul > li').length );
                var index = length;
                $('#career-requirement-list').children('ul').append('<li><input name="data[CareerRequirement][name]['+index+']" class="form-control" type="text" id="CareerRequirementName'+index+'"></li>');
            }
        });
    }

    $.convertNumeric = function(num, type, empty){
        type    = typeof type == 'undefined' ? 'int' : type;
        empty   = typeof empty == 'undefined' ? 0 : empty;

        if(typeof num != 'undefined' && (type == 'int' || type == 'float')){
            num = num.toString();
            num = num.replace(/[^0-9.]/gi, '');

            num = type == 'int' ? num * 1 : parseFloat(num);
            num = isNaN(num) ? 0 : num;
        }
        else{
            num = empty;
        }

        return num;
    }

    $.convertNumber = function(num, type, empty){
        if( typeof empty == 'undefined' ) {
            empty = 0;
        }

        if( typeof num != 'undefined' ) {
            num = num.replace(/,/gi, "").replace(/ /gi, "").replace(/IDR/gi, "").replace(/Rp./gi, "").replace(/Rp/gi, "");

            if( typeof type == 'undefined' ) {
                type = 'int';
            }

            if( type == 'int' ) {
                num = num*1;
            } else if( type == 'float' ) {
                num = parseFloat(num);
            }

            if( type == 'int' || type == 'float' ) {
                if( isNaN(num) ) {
                    num = 0;
                }
            }
        } else {
            num = empty;
        }

        return num;
    }

    $.convertDecimal = function(self, decimal){
        var val = $.convertNumber(self.val(), 'string');
        var decimal = $.checkUndefined(decimal, 0);
        
        return $.formatNumber(val, decimal);
    }

    $.inputPrice = function(options){
        var settings = $.extend({
            obj: $('.input_price'),
        }, options );
        if( settings.obj.length > 0 ) {
            // settings.obj.priceFormat({
            //     prefix: '',
            //     centsSeparator: '',
            //     thousandsSeparator: ',',
            //     centsLimit: 0,
            //     doneFunc: function(obj, val) {
            //         currencyVal = val;
            //         currencyVal = currencyVal.replace(/,/gi, "")
            //         obj.next(".input_hidden").val(currencyVal);
            //     }
            // });

            settings.obj.off('blur keyup').on('blur keyup', function(event){
                var self = $(this);

                if(event.type == 'keyup'){
                    delay(function(){
                        self.trigger('blur').focus();
                    }, 100 );
                }
                else{
                    var places  = 0;
                    var decimal  = $.checkUndefined(self.data('decimal'), null);
                    var target  = $.checkUndefined(self.data('target'), null);
                    var target_type  = $.checkUndefined(self.data('target-type'), null);

                    if(decimal != null){
                        places = decimal;

                    } else if(self.val().indexOf('.') > -1){
                    //  places = self.val().split('.')[1];
                    //  places = places.length;
                        places = 2;
                    }

                    self.val($.convertDecimal(self, places));

                    if( target != null && target_type != null ) {
                        $(target).trigger(target_type);
                    }
                }
            });

            settings.obj.each(function(index, object){
				var self		= $(this);
				var places		= 0;
				var allowNull	= $.checkUndefined(self.attr('data-allow-null'), false);
				var decimal		= $.checkUndefined(self.attr('data-decimal'), null);

                if(decimal != null){
                    places = decimal;
                } else if(self.val().indexOf('.') > -1){
                //  places = self.val().split('.')[1];
                //  places = places.length;
                    places = 2;
                }

                if(self.val() != '' || !allowNull){
	                self.val($.convertDecimal(self, places));
                }
            });
        }
    }

    $.inputNumber = function(options){
        var settings = $.extend({
            obj: $('.input_number'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('keypress').keypress(function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;
                var dotAllow = $.checkUndefined($(this).data('dot'), 'true');

                if( dotAllow == 'true' ) {
                    allow = (this.value.length == 0 && charCode == 46) || charCode == 33;
                    allowAlt = (charCode == 46);  /*point*/
                    disallow = (charCode != 46 || ($(this).val().indexOf('.') != -1));
                } else {
                    allow = false;
                    allowAlt = false;
                    disallow = false;
                }

                if( allow || charCode == 33 || charCode == 64 || charCode == 35 || charCode == 36 || charCode == 37 || charCode == 94 || charCode == 38 || charCode == 42 || charCode == 40 || charCode == 41
                    ){
                    return false;
                } else {
                    if (
                        charCode == 8 ||  /*backspace*/
                        allowAlt || /*point*/
                        charCode == 9 || /*Tab*/
                        charCode == 27 || /*esc*/
                        charCode == 13 || /*enter*/
                        // charCode == 97 || 
                        // Allow: Ctrl+A
                        // (charCode == 65 && event.ctrlKey === true) ||
                        // Allow: home, end, left, right
                        (charCode >= 35 && charCode < 39) || ( charCode >= 48 && charCode <= 57 )
                        ) 
                    {
                        return true;
                    }else if (          
                        disallow || 
                        (charCode < 48 || charCode > 57)) 
                    {
                        event.preventDefault();
                    }
                }
            }).blur(function(){
                var self    = $(this);
                var places  = 0;

                if(self.val().indexOf('.') > -1){
                //  places = self.val().split('.')[1];
                //  places = places.length;
                    places = 2;
                }

                // self.val($.convertDecimal(self, places));
            });     
        }
    }

    $.fn.hasAttr = function(strAttribute){
        var attribute = this.attr(strAttribute);

        if(typeof attribute !== typeof undefined && attribute !== false){
            return true;
        }

        return false;
    };

    $.reindexList = function(options){
        var settings = $.extend({
            obj     : null, 
            child   : null, 
        }, options);

        var target = $(settings.obj);

        if(target.length){
            var rules = target.data('rule');
            var child = settings.child ? settings.child : target.data('child');
                child = $(child);

            if(typeof rules == 'undefined'){
                rules = [
                    ['label.control-label', ['for']], 
                    [':input', ['id', 'name']], 
                ];
            }
            else if(typeof rules == 'string'){
                rules = eval(rules);
            }

            if(child && child.length && rules && $.isArray(rules)){
                child.each(function(childIndex, childObj){
                    childObj = $(childObj);

                    $.each(rules, function(ruleIndex, rule){
                        var rule        = eval(rule);
                        var selector    = $.checkUndefined(rule[0]);
                        var attributes  = $.checkUndefined(rule[1]);
                            attributes  = $.isArray(attributes) ? attributes : [attribute];

                        var reindexTarget = childObj.find(selector);

                        if(reindexTarget.length){
                            reindexTarget.each(function(targetIndex, reindexTargetObj){
                                var reindexTargetObj = $(reindexTargetObj);
                                var targetAttributes = {};

                                $.each(attributes, function(attributeIndex, attributeName){
                                    if(reindexTargetObj.hasAttr(attributeName)){
                                        var newValue = reindexTargetObj.attr(attributeName).toString();
                                        //  newValue = newValue.replace(/[0-9]/g, childIndex);
                                            newValue = newValue.replace(/\d+/g, childIndex);

                                        targetAttributes[attributeName] = newValue;
                                    }
                                });

                                if(targetAttributes){
                                    reindexTargetObj.attr(targetAttributes);

                                    if(reindexTargetObj.hasClass('editor')){
                                        $.editor({ obj : reindexTargetObj });
                                    }
                                }
                            });

                            if(reindexTarget.is(':input') && reindexTarget.hasClass('handle-toggle')){
                                reindexTarget.trigger('init');
                            }
                        }
                    });
                });

                $.rebuildFunction();
            }
        }
    }

    $.rowAdded = function(options){
        var settings = $.extend({
            obj: $('.field-added'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click').click(function(e){
                var self = $(this);
                var parentAdded = self.parents('.form-added');
                var type = self.data('type');
                var parentUl = parentAdded.children('ul');
                var temp = parentAdded.find('.field-copy');
                var value = temp.html();

                var fieldCopy = parentUl.find('.field-copy');

                if(fieldCopy.length == 0){
                    fieldCopy = parentUl.children('li').eq(0);
                }

                var template    = fieldCopy.clone();
                var editables   = template.find('.editable');
                var rowIndex    = parentUl.find('li').length;

                if(editables.length){
                    editables.each(function(index, editable){
                        var editableID      = $(editable).attr('id');
                        var editableName    = $(editable).attr('data-name');
                        var attributes      = {
                            'data-value' : '', 
                        };

                        if(typeof editableID != 'undefined'){
                            editableID  = editableID.replace(/\d+/g, rowIndex);
                            attributes  = $.extend(attributes, { 'id' : editableID });
                        }

                        if(typeof editableName != 'undefined'){
                            editableName    = editableName.replace(/\d+/g, rowIndex);
                            attributes      = $.extend(attributes, { 'data-name' : editableName });
                        }

                        $(editable).attr(attributes).html('').addClass('editable-empty');
                    });
                }

                template.find('.data-empty').empty();
                parentUl.append(template.removeClass('field-copy'));

                var lastChild = parentUl.find('li:last-child');

                lastChild.find('input.trigger-active').val('');
                lastChild.find('.error-message').remove();

                switch (type) { 
                    case 'component-type':
                        var objComponent = lastChild.find('.component-type');

                        var name_copy = objComponent.attr('name').toString();
                        var rel_copy = objComponent.attr('data-rel').toString();

                        name_copy = name_copy.replace('['+rel_copy+']', '['+rowIndex+']');
                        rel_copy = rel_copy.replace(rel_copy, rowIndex);

                        objComponent.attr('name', name_copy);
                        objComponent.attr('data-rel', rel_copy);
                        parentUl.find('li:last-child .component-type').val('');
                    break;
                }

                $.rebuildFunctionAjax( parentUl.find('li:last-child') );
                $.rowRemoved();

                var reindex = $.checkUndefined(parentAdded.attr('data-reindex'), false);

                if(reindex === 'true' || reindex === true){
                    $.reindexList({
                        obj     : parentUl, 
                        child   : parentUl.find('li'), 
                    });
                }

                return false;
            });

            $.rowRemoved();
        }
    }

    $.rowRemoved = function(options){
        var settings = $.extend({
            obj: $('.form-added .removed'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(e){
                var self = $(this);

                var parentLi    = self.parents('li');
                var parentUl    = parentLi.parents('ul');
                var parentAdded = parentUl.parents('.form-added');
                var reindex     = $.checkUndefined(parentAdded.attr('data-reindex'), false);

                parentLi.remove();

                if(reindex === 'true' || reindex === true){
                    $.reindexList({
                        obj     : parentUl, 
                        child   : parentUl.find('li'), 
                    });
                }

                return false;
            });
        }
    }

    $.increment = function(options){
        var settings = $.extend({
            obj: $('.op-min,.op-plus'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            $('.op-min,.op-plus').click(function(){
                var self = $(this);
                var action = self.attr('data-action');
                var target = self.attr('data-target');
                target = self.parents('.increment').find('.'+target);
                var val = parseInt(target.val());

                if( isNaN(val) ) {
                    val = 0;
                }

                if( action == 'min' ) {
                    if( val <= 0 ) {
                        val = 0;
                    } else {
                        val -= 1;
                    }
                } else {
                    val += 1;
                }

                target.val(val);

                return false;
            });
        }
    }

    $.checkboxInput = function(options){
        var settings = $.extend({
            obj: $('.cb-checkmark label'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            $('.cb-checkmark').removeClass('checked');

            settings.obj.click(function(e){
                var self = $(this);

                var data_show = self.attr('data-show');
                var rdLine = self.parents('.rd-line');
                var parent = self.parents('.cb-checkmark');


                if( parent.hasClass('radio') ) {
                    var input = parent.children('input[type="radio"]');
                    rdLine.find('input[type="checkbox"]').attr('checked', false).prop('checked', false);
                    
                    input.attr('checked', true).prop('checked', true);
                } else {
                    var input = parent.children('input[type="checkbox"]');

                    if( input.is(':checked') == false && typeof data_show != 'undefined' ) {
                        $(data_show).show();
                    } else if( typeof data_show != 'undefined' ) {
                        $(data_show).hide();
                    }
                }

                // if ( ( parent.hasClass('checked') || parent.find('input[type="checkbox"]').attr('checked') == 'checked' ) && !parent.hasClass('radio') ) {
                //     parent.removeClass('checked');

                //     if(typeof data_show != 'undefined' && !$('.cb-checkmark').hasClass('checked') ) {
                //         $(data_show).hide();
                //     }
                // } else {
                //     if( parent.hasClass('radio') ) {
                //         rdLine.find('.cb-checkmark').removeClass('checked');
                //         rdLine.find('.cb-checkmark input[type="radio"]').prop('checked', false);
                //         parent.find('input[type="radio"]').prop('checked', true);
                //     }
                //     parent.addClass('checked');

                //     if(typeof data_show != 'undefined' ) {
                //         $(data_show).show();
                //     }
                // }
            });
        }
    }

    $.draggableSorting = function(options){
        var settings = $.extend({
            obj: $("ul.drag"),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.sortable({
                placeholder: '<li class="placeholder col-sm-4"></li>',
                nested: false,
                vertical: false,
                onDrop: function ($item, container, _super) {
                    var parentObj = $("ul.drag");
                    var obj = $("ul.drag li");
                    var url = parentObj.attr('data-url');
                    var leng = obj.length;
                    var media_id = '';
                    var data_wrapper_write = settings.obj.data('wrapper-write') ? settings.obj.data('wrapper-write') : '#wrapper-write';

                    for (i = 0; i < leng; i++) {
                        media_id += obj[i].getAttribute("rel")+',';
                    };

                    if(typeof url != 'undefined' ) {
                        $.ajax({
                            url: url+'/media_id:' + media_id,
                            type: 'POST',
                            success: function(result) {
                                var contentHtml = $(result).filter(data_wrapper_write).html();

                                if(typeof contentHtml == 'undefined' ) {
                                    contentHtml = $(result).find(data_wrapper_write).html();
                                }

                                if( $(data_wrapper_write).length > 0 ) {
                                    $(data_wrapper_write).html(contentHtml);
                                    $.rebuildFunction();
                                }

                                return false;
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                                return false;
                            }
                        });
                    }

                    _super($item, container);
                },
            });

            $('.disable-drag').hover(function() {
                doOnSizeChange("disable");
            }, function() {
                doOnSizeChange();
            });

            function doOnSizeChange( titleName ) {
                if( titleName == 'disable' ) {
                    settings.obj.sortable("disable");
                } else {
                    var doc_w = $( document ).width();

                    if( doc_w <= 767 ) {
                        settings.obj.sortable("disable");
                    } else {
                        settings.obj.sortable("enable");
                    }
                }
            }

            $( window ).resize(function() {
                doOnSizeChange();
            });

            doOnSizeChange();
        }
    };

    $.loadFileUpload = function(options){
        if( $('#fileupload').length > 0 ) {
            $('#fileupload').fileupload();
        }

        if( $('#single-fileupload').length > 0 ) {
            $('#single-fileupload').fileupload();
        }
    };

    $.uploadMedias = function(options){
        var settings = $.extend({
            obj: $('.wrapper-upload-medias.upload-photo .btn.uploads,.user-photo .pick-file,.fileupload-buttonbar .btn.uploads'),
            objTrigger: $('.fileupload-buttonbar input[type="file"]'),
            objFile: $('.file-uploads'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(e){
                settings.objTrigger.trigger("click");
                return false;
            });
        }

        // if( settings.objFile.length > 0 ) {
        //     settings.objFile.off('click');
        //     settings.objFile.click(function(e){
        //         var data_url = $(this).attr('data-url');
        //         var data_wrapper = $(this).attr('data-wrapper-write');

        //         $(data_wrapper).attr('action', data_url);

        //         settings.objTrigger.trigger("click");
        //         return false;
        //     });
        // }
    };

    var directAjaxModalJXHR;

    $.directAjaxModal = function(options){
        var settings = $.extend({
            obj: $('.ajaxModal'),
            objId: $('#myModal'),
        }, options );

        var vthis = settings.obj;
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var title = vthis.attr('title');
        var data_location = $.checkUndefined(vthis.attr('data-location'), false);
        var data_location_additional = $.checkUndefined(vthis.attr('data-location-additional'), false);
        var modalSize = $.checkUndefined(vthis.attr('data-size'), '');
        var target_template = $.checkUndefined(vthis.attr('data-target-template'), '');
        var just_body = $.checkUndefined(vthis.attr('data-just-body'), false);
        var data_form = $.checkUndefined(vthis.data('form'), null);

        if(target_template != ''){
            settings.objId = $(target_template);
        }

    //  jangan langsung apus, grab dulu informasinya
    //  $('.modal-body').html('');

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        var params_form = false;
        var dataRequest = false;

        if( data_form != null ) {
            dataRequest = $(data_form).serializeArray();
        } else if( $('.form-target').length ) {
            params_form = [];

            $.each($('.form-target').serializeArray(), function(index) {
                var cur = this;
                if( cur.name != '_method' && cur.name.indexOf('checkbox_all') == -1 ) {
                    params_form.push(cur);
                }
            });

            dataRequest = { 'params' : params_form };
        }

        if(directAjaxModalJXHR){
            directAjaxModalJXHR.abort();
        }

        var objModal        = $(settings.objId);
        var objModalHeader  = objModal.find('.modal-header');
        var objModalTitle   = objModal.find('.modal-title');
        var objModalDialog  = objModal.find('.modal-dialog');
        var objModalBody    = objModal.find('.modal-body');

    //  flush modal content
    //  objModalBody.html('');

        directAjaxModalJXHR = $.ajax({
            url: url,
            type: 'POST',
            data: dataRequest,
            success: function(response, status) {
                if(objModal.length){
                    objModalHeader.show();

                    if(objModalTitle.length && just_body == false){
                        if(title !== undefined){
                            objModalTitle.html(title).show();
                        }
                        else{
                            objModalTitle.html('').hide();
                        }
                    }

                    if(modalSize != ''){
                        objModalDialog.removeClass('modal-xs modal-sm modal-md modal-lg modal-xl modal-fluid').addClass(modalSize);
                    }

                    objModalBody.html(response);
                    objModal.modal('show');

                    $.rebuildFunctionAjax(objModal);
                }

                if( data_location == 'true' ) {
                    if(data_location_additional == 'true'){
                        $.generateLocation({
                            currentRegionID: $('#currRegionIDaditionals').val(),
                            currentCityID: $('#currCityIDaditionals').val(),
                            regionSelector: $('#regionIdAdditional'),
                            citySelector: $('#cityIdAdditional'),
                            subareaSelector: $('#subareaIdAdditional'),
                            zipSelector: $('#rku-zip-Additional'),
                        });
                    }else{
                        $.generateLocation();
                    }
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    $.ajaxModal = function(options){
        var settings = $.extend({
            obj: $('.ajaxModal'),
            objId: $('#myModal'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(msg) {
                var self = $(this);
                var data_reload = self.attr('data-close');

                $.directAjaxModal({
                    obj: self,
                    objId: settings.objId,
                });

                settings.objId.on('hidden.bs.modal', function () {
                    var self = $(this);
                    var body = self.find('.modal-body');

                    body.empty();

                    if( data_reload == 'reload' ) {
                        window.location.reload();
                    }
                });

                return false;
            });

            $('.close-modal').off('click');
            $('.close-modal').click(function(){
                $('#myModal').modal('hide');
                return false;
            });
        }
    }

    $.getDates = function(dt, reverse){
        reverse = $.checkUndefined(reverse, false);
        dtString = '';

        if( reverse == true ) {
            dtArr = dt.split('-');

            if( dtArr.length == 3 ) {
                y = dtArr[0];
                m = dtArr[1];
                d = dtArr[2];

                dtString = d + '/' + m + '/' + y;
            }
        } else {
            dtArr = dt.split('/');

            if( dtArr.length == 3 ) {
                d = dtArr[0];
                m = dtArr[1];
                y = dtArr[2];

                dtString = y + '-' + m + '-' + d;
            }
        }

        return dtString;
    }

    $.datePicker = function(options){
        var settings = $.extend({
            obj: $('.datepicker'),
            objMaxToday: $('.max-today-datepicker'),
            objBirthday: $('.birthdaypicker'),
            objRange: $('.to-datepicker'),
            objTime: $('.timepicker'),
            objMonth: $('.monthpicker'),
            objYear: $('.yearpicker'),
            objPeriode: $('.date-periode'),
            up: 'rv4-angle-up',
            down: 'rv4-angle-down',
            next: 'rv4-angle-right',
            previous: 'rv4-angle-left',
            clear: 'rv4-trash', 
            close: 'rv4-cross',
            time: 'rv4-time small-icon',
            calender: 'rv4-calendar2 small-icon',
        }, options );

        if( settings.objMaxToday.length > 0 ) {
            settings.objMaxToday.datetimepicker({
                format: 'DD/MM/YYYY HH:mm',
                maxDate: moment().add(0, 'days'),
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close,
                    time: settings.time,
                    date: settings.calender,
                },
            });
        }

        if( settings.obj.length > 0 ) {
            settings.obj.datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close
                },
            });
        }

        if( settings.objRange.length > 0 ) {
            settings.objRange.datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    up: 'rv4-angle-up',
                    down: 'rv4-angle-down',
                    previous: 'rv4-angle-left',
                    next: 'rv4-angle-right',
                    clear: 'rv4-trash',
                    close: 'rv4-cross'
                },
                useCurrent: false,
            });
            settings.obj.on("dp.change", function (e) {
                settings.objRange.data("DateTimePicker").minDate(e.date);

                var self = $(this);
                var currentDate = $.getDates(self.closest('div.row').find('.to-datepicker').val());
                var startDate = e.date.format("YYYY-MM-DD");
                var setDate = e.date.format("DD/MM/YYYY");

                if( startDate > currentDate ) {
                    self.closest('div.row').find('.to-datepicker').val(setDate);
                }
            });
            // settings.objRange.on("dp.change", function (e) {
            //     settings.obj.data("DateTimePicker").maxDate(e.date);
            // });
        }

        if( settings.objTime.length > 0 ) {
            settings.objTime.datetimepicker({
                format: 'HH:mm',
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close
                },
            });
        }

        if( settings.objMonth.length > 0 ) {
            settings.objMonth.datetimepicker({
                format: 'MMM YYYY',
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close
                },
            });
        }

        if( settings.objYear.length > 0 ) {
            settings.objYear.datetimepicker({
                format: 'YYYY',
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close
                },
            });
        }

        if( settings.objBirthday.length > 0 ) {
            settings.objBirthday.datetimepicker({
                format: 'DD/MM/YYYY',
                viewMode: 'years',
                icons: {
                    up: settings.up,
                    down: settings.down,
                    previous: settings.previous,
                    next: settings.next,
                    clear: settings.clear,
                    close: settings.close
                },
            });
        }

        if( settings.objPeriode.length > 0 ) {
            var dateMin = settings.objPeriode.attr('data-min');
            var dateMax = settings.objPeriode.attr('data-max');
            var dateVal = settings.objPeriode.attr('data-value');

            if(typeof dateMin != 'undefined' ) {
                dateMin = eval(dateMin);
            }
            if(typeof dateMax != 'undefined' ) {
                dateMax = eval(dateMax);
            }
            if(typeof dateMax != 'undefined' ) {
                dateVal = eval(dateVal);
            } else {
                dateVal = null;
            }

            settings.objPeriode.rangePicker({
                closeOnSelect:true,
                presets: false,
                months: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                minDate : dateMin,
                maxDate : dateMax,
                setDate : dateVal
            });
        }
    }

    var _callApplyDateRange = function ( picker, self, flag_trigger ) {
        var trigger = $.checkUndefined(self.attr('data-trigger'), false);
        
        startDate = picker.startDate.format('DD/MM/YYYY');
        endDate = picker.endDate.format('DD/MM/YYYY');

        var dt = startDate + ' - ' + endDate;
        self.val(dt);

        if( flag_trigger == true && trigger != false ) {
            self.trigger(trigger);
        }

        if( !self.hasClass('calendar-chart') && $('.date-range-calendar.calendar-chart').length > 0 ) {
            $('.date-range-calendar.calendar-chart').val(dt);
            $('.date-range-calendar.calendar-chart').trigger(trigger);
        }
    }

    $.daterangepicker = function( options ) {
        var settings = $.extend({
            obj: $('.date-range'),
            objMonth: $('.date-range-month'),
            objCustom: $('.date-range-custom'),
            objCalendar: $('.date-range-calendar'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                },
                "linkedCalendars": false,
            });
            settings.obj.on('apply.daterangepicker', function(ev, picker) {
                var dataEvent = settings.obj.attr('data-event');
                var dataForm = settings.obj.parents('form');
                
                if( dataEvent == 'submit' ) {
                    dataForm.submit();
                }
            });
            $('.icon-picker').click(function(e) {
                settings.obj.trigger('click');
            });
        }

        if( settings.objCustom.length > 0 ) {
            settings.objCustom.each(function(){
                var self = $(this);
                var limitday = $.convertNumber(self.attr('data-limit'), 'int');

                if( limitday == 0 ) {
                    limitday = false;
                }

                self.daterangepicker({
                    locale: {
                        format: 'DD/MM/YYYY',
                    },
                    "linkedCalendars": false,
                    dateLimit: { days: limitday },
                });
            });
        }

        if( settings.objCalendar.length > 0 ) {
            settings.objCalendar.daterangepicker({
                ranges: {
                   'Hari ini': [moment(), moment()],
                   'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                   '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                   'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                   'Bulan lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: 'DD/MM/YYYY',
                },
                autoUpdateInput: false,
            });

            settings.objCalendar.on('apply.daterangepicker', function(ev, picker) {
                _callApplyDateRange( picker, $(this), true );
                // var trigger = $.checkUndefined($(this).attr('data-trigger'), false);
                
                // startDate = picker.startDate.format('DD/MM/YYYY');
                // endDate = picker.endDate.format('DD/MM/YYYY');

                // $(this).val(startDate + ' - ' + endDate);

                // if( trigger != false ) {
                //     $(this).trigger(trigger);
                // }
            });

            settings.objCalendar.each(function(i, selected){ 
                var tmp = $(selected).val();

                $(selected).val(tmp)
            });
        }

        if( settings.objMonth.length > 0 ) {
            settings.objMonth.daterangepicker({
                locale: {
                    format: 'MM YYYY',
                },
                "linkedCalendars": false,
            });
        }
    }

    $.checkAll = function(options){
        var settings = $.extend({
            obj: '.checkAll',
            objTarget: '.check-option',
            objClick: '.check-multiple-delete',
            objTargetCount: $('.check-count-target'),
            objForm: $('.form-target'),
        }, options );

        var countChecked = function () {
            var count = 0;
            var count_all = $(settings.objTarget).length;
            var data_show = '.delete-overflow';

            $.each( $(settings.objTarget), function( i, val ) {
                var data_val = $(val);
                var parent = data_val.parents('.cb-checkmark');
                var parent_tr = data_val.parents('tr');
                var custom_data_show = parent.children('label').attr('data-show');

                parent_tr.removeClass('checked');

                if( $(custom_data_show).length > 0 ) {
                    data_show = custom_data_show;
                }

                if( parent.find('input[type="checkbox"]').is(':checked') ) {
                    count += 1;

                    if( data_val.hasClass('trigger-bg') ) {
                        parent_tr.addClass('checked');
                    }
                }
            });

            var text = '';

            if( count > 0 ) {
                text = ' ('+count+')';

                $(settings.objClick).removeClass('hide');

                if( $(data_show).length > 0 ) {
                    $(data_show).removeClass('hide').show();
                }
            }else{
                $(settings.objClick).addClass('hide');
                
                if( $(data_show).length > 0 ) {
                    $(data_show).addClass('hide').hide();
                }
            }

            if(count_all > 0 && count_all == count){
                $(settings.obj).prop('checked', true);
            }else{
                $(settings.obj).prop('checked', false);
            }

            settings.objTargetCount.html(text);
            $('.delete-overflow .counter span').html(count);
        }

        if( $(settings.obj).is(':checked') ) {
            $(settings.objTarget).prop('checked', true);
        }

        function checkDisabledMorgageBackend(obj_checkall_target){
            var data_bank = obj_checkall_target.data('bank');
            var data_setting = obj_checkall_target.data('setting');

            if( obj_checkall_target.is(':checked') ) {
                // $('.mortgageBank.check-bank'+data_bank).removeClass('check-bank'+data_bank);
                $('.mortgageBank[data-setting='+data_setting+']').find('.input-note').fadeIn();
                $('.mortgageBank[data-setting='+data_setting+']').find('input[type=text].noted').removeAttr('disabled');
                $('.mortgageBank[data-setting='+data_setting+']').removeClass('check-bank'+data_bank);

                $('.mortgageBank.check-bank'+data_bank).each(function(){
                    var self = $(this);
                    var checkbox = self.find('input:checkbox');
                    var setting_id = checkbox.val();

                    checkbox.prop('disabled', true);
                    self.addClass('disabled').find('.toggle-display').removeAttr('data-display');
                    self.find('.detail-info.open').css('display', 'none');

                });
            } else {
                $('.mortgageBank[data-setting='+data_setting+']').addClass('check-bank'+data_bank);
                $('.mortgageBank[data-setting='+data_setting+']').find('.input-note').fadeOut();
                $('.mortgageBank[data-setting='+data_setting+']').find('input[type=text].noted').attr('disabled', true);

                $('.mortgageBank.check-bank'+data_bank).each(function(){
                    var self = $(this);
                    var checkbox = self.find('input:checkbox');
                    var setting_id = checkbox.val();

                    checkbox.removeAttr('disabled');
                    self.removeClass('disabled').find('.toggle-display').attr('data-display', "#kpr-info-detail[rel='"+setting_id+"']");
                });
            }
        }

        function checkDisabledMorgage(obj_checkall_target){
            var data_bank = obj_checkall_target.data('bank');

            if( obj_checkall_target.is(':checked') ) {
                obj_checkall_target.parents('.mortgageBank.check-bank'+data_bank).removeClass('check-bank'+data_bank);

                $('.mortgageBank.check-bank'+data_bank).each(function(){
                    var self = $(this);
                    var checkbox = self.find('input:checkbox');
                    var setting_id = checkbox.val();

                    checkbox.prop('disabled', true);
                    self.addClass('disabled').find('.toggle-display').removeAttr('data-display');
                    self.find('.detail-info.open').css('display', 'none');

                });
            } else {
                obj_checkall_target.parents('.mortgageBank').addClass('check-bank'+data_bank);

                $('.mortgageBank.check-bank'+data_bank).each(function(){
                    var self = $(this);
                    var checkbox = self.find('input:checkbox');
                    var setting_id = checkbox.val();

                    checkbox.removeAttr('disabled');
                    self.removeClass('disabled').find('.toggle-display').attr('data-display', '#detail-info'+setting_id);

                });
            }
        }

        function checkAllFun(obj_checkall_target){
            if( obj_checkall_target.is(':checked') ) {
                $(settings.objTarget).prop('checked', true);
            } else {
                $(settings.objTarget).prop('checked', false);
            }

            countChecked();
        }

        $(document).delegate( settings.obj, "init click", function(e) {
            checkAllFun($(this));
        });


        $(document).delegate( settings.objTarget, "init click", function(e) {
            var data_bank = $(this).attr('data-bank');
            var backend = $(this).attr('data-backend');

            if(data_bank){
                if(backend){
                    checkDisabledMorgageBackend($(this));
                }else{
                   checkDisabledMorgage($(this));                
                }
            }

            countChecked();
        });

        $(document).delegate( settings.objClick, "init click", function(e) {
            var self = $(this);
            var url = self.attr('href');
            var msg = self.attr('data-alert');
            var flagChecked = false;

            $.each( $('.check-option'), function( i, val ) {
                var selfEach = $(this);
                if( selfEach.is(':checked') ) {
                    flagChecked = true;
                }
            });

            if( flagChecked == true ) {
                if(typeof msg != 'undefined' ) {
                    if ( !confirm(msg) ) { 
                        return false;
                    }
                }
                $('.form-target').attr('action', url);
                $('.form-target').submit();
            } else {
                alert('Mohon centang salah satu data yang ada di table');
            }

            return false;
        });

        $.ajaxModal();
    }

//  CUSTOM AUTOCOMPLETE WITH CATEGORY AS ITEM GROUP ////////////////////////////////////////////////////////////////////////////////////////////////////////

    if (typeof $.ui != 'undefined') {
        if(typeof $.ui.autocomplete == 'function'){
            $.widget('custom.catcomplete', $.ui.autocomplete, {
                _create     : function(){
                    this._super();
                    this.widget().menu('option', 'items', '> :not(.ui-autocomplete-category)');
                },
                _renderMenu : function(ul, items){
                    var that            = this;
                    var currentCategory = '';

                    $.each(items, function(index, item){
                        var li;

                        if(typeof item.category != 'undefined' && item.category != currentCategory){
                            ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
                            currentCategory = item.category;
                        }

                        li = that._renderItemData(ul, item);
                        if(item.category){
                            li.attr('aria-label', item.category + ' : ' + item.label);
                        }
                    });
                }, 
                _renderItem : function (ul, item){
                //  highlight matched chars
                    var caption = item.label.replace(
                        new RegExp('(?![^&;]+;)(?!<[^<>]*)(' + $.ui.autocomplete.escapeRegex(this.term) + ')(?![^<>]*>)(?![^&;]+;)', 'gi'), '<b>$1</b>'
                    );

                    return $('<li></li>').data('item.autocomplete', item).append('<a>' + caption + '</a>').appendTo(ul);
                }
            });
        }
    }

//  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    var catCompleteJXHR;

    $.catcomplete = function(options){
        var settings = $.extend({
            obj : $('input[data-role="catcomplete"]'),
            objForm : $('#auto-submit'),
        }, options);

        if(typeof settings.obj != 'undefined' && settings.obj.length && typeof $.ui.autocomplete == 'function'){
            settings.obj.each(function(){
                var selfobj = $(this);
                var mustMatch = $.checkUndefined(selfobj.data('must-match'), false);
                var autoSubmit = $.checkUndefined(selfobj.data('auto-submit'), false);
                var additional = $.checkUndefined(selfobj.data('additional'), false);

                selfobj.catcomplete({
                    autoFocus   : true, 
                    appendTo    : $('body'), 
                    delay       : 0, 
                    minLength   : 3, 
                    select      : function(event, ui){
                        var self        = $(this);
                        var label       = $.checkUndefined(ui.item.label);
                        var urlChange   = $.checkUndefined(selfobj.data('select-source'), null);

                        self.trigger('catcompleteselect:before', [ui]);

                        self.val(label);

                        if(autoSubmit){
                            settings.objForm.submit();
                        }

                        if(self.next('input:hidden').length){
                            var reference   = $.checkUndefined(ui.item.reference);
                            var value       = $.checkUndefined(ui.item.value, reference);

                            self.next('input:hidden').val(reference);
                        }

                        if( urlChange != null ) {
                            urlChange += '/id:'+reference+'/';
                            $.directAjaxLink({
                                obj: selfobj,
                                url: urlChange,
                            });
                        }

                        self.trigger('catcompleteselect:after', [ui]);
                    }, 
                    source      : function(request, response){
                        var data_parent;
                        var self        = $(this);
                        var extraParams = $._extractParams(selfobj);
                        var dataSource  = $.checkUndefined(selfobj.attr('data-source'), false);
                        var dataParent  = $.checkUndefined(selfobj.attr('data-parent'), false);
                        var data_additional = $(additional).val();

                        if(dataParent){
                            var data_parent = $(dataParent).val();
                        }

                        var postData    = {
                            'Search' : {
                                'keyword' : request.term,
                                'additional':data_additional,
                                'parent' : data_parent,
                            }, 
                        };

                        if(catCompleteJXHR){
                            catCompleteJXHR.abort();
                        //  self.removeClass('ui-autocomplete-loading').addClass('ui-autocomplete-input');
                        }

                        if(extraParams){
                        //  append additional params ========================================================                   

                            if($.objKeys(extraParams.post).length){
                                postData = $.extend(true, {
                                    'Search' : extraParams.post, 
                                }, postData);
                            }

                            if($.objKeys(extraParams.named).length){
                                var nonFieldParams  = [];
                                var fieldParams     = [];
                                var namedParams     = '';

                                $.each(extraParams.named, function(paramKey, paramValue){
                                    var regex = new RegExp('^\\d+$');

                                    if(regex.test(paramKey)){
                                    //  numeric field name
                                        nonFieldParams.push(paramValue);
                                    }
                                    else{
                                        fieldParams.push(paramKey + ':' + paramValue);
                                    }
                                });

                            //  alter url
                                namedParams+= dataSource.substr(dataSource.length - 1) == '/' ? '' : '/';
                                namedParams+= nonFieldParams.length ? nonFieldParams.join('/') + '/' : '';
                                namedParams+= fieldParams.length ? fieldParams.join('/') : '';

                                if(dataSource.indexOf('?') > -1){
                                    dataSource = dataSource.split('?').join(namedParams + '?');
                                }
                                else{
                                    dataSource+= namedParams;
                                }
                            }

                            if($.objKeys(extraParams.query).length){
                                var queryParams = [];

                                $.each(extraParams.query, function(paramKey, paramValue){
                                    queryParams.push(paramKey + '=' + paramValue);
                                });

                            //  alter url
                                dataSource+= dataSource.indexOf('?') > -1 ? '&' : '?';
                                dataSource+= queryParams.join('&');
                            }

                        //  =================================================================================
                        }

                        catCompleteJXHR = $.post(dataSource, postData, function(data){
                        //  catCompleteCache[term] = data;
                        //  self.removeClass('ui-autocomplete-loading').addClass('ui-autocomplete-input');
                            selfobj.removeClass('ui-autocomplete-loading');
                            response(data);
                        }, 'json');
                    }, 
                    open        : function(event, ui){
                        var self            = $(this);
                        var target          = $(event.target);
                        var targetHeight    = target.height();
                        var dropdown        = $('.ui-autocomplete:visible');

                        if(dropdown.length){
                            var dropdownTop     = dropdown.offset().top - $(window).scrollTop();
                            var dropdownHeight  = dropdown.height();
                            var viewportHeight  = $(window).height();
                            var buffer          = 50;

                            dropdown.css({
                                'width' : target.outerWidth() + 'px',  
                            });     
                        }

                    //  self.removeClass('ui-autocomplete-loading').addClass('ui-autocomplete-input');
                    }, 
                    change      : function(event, ui){
                        var self = $(this);

                        self.trigger('catcompletechange:before', [ui]);

                    //  untuk hapus textbox jika isi tidak sesuai dengan result ajax
                        if(mustMatch && !ui.item){
                            self.val('');

                            if(self.next('input:hidden').length){
                                self.next('input:hidden').val('');
                            }
                        }

                        self.trigger('catcompletechange:after', [ui]);
                    }, 
                }).click(function(){
                //  $(this).select();
                }).keyup(function(){
                //  reset hidden input when user done typing
                    if($(this).next('input:hidden').length){
                        $(this).next('input:hidden').val('');
                    }
                });
            });
        }
    }

    $.objKeys = function(obj){
        var keys = [];

        if(obj){
            for(key in obj){
                if(obj.hasOwnProperty(key)){
                    keys.push(key);
                }
            }
        }

        return keys;
    };

    $.getObjVal = function(strSelector){
        var object  = $(strSelector);
        var value   = '';

        if(object.length){
            if(object.is('input') || object.is('select') || object.is('textarea') || object.is('button')){
                if(object.is('input:radio')){
                    value = object.filter(':checked').val();
                }
                else{
                    value = object.val();
                }
            }
            else{
                value = object.html();
            }
        }
        else{
            value = strSelector;
        }

        return $.trim(value);
    }

//  additional params extractor
    $._extractParams = function(objElement){
        objElement = $(objElement);

        if(objElement.length){
            var objDataKeys = $.objKeys(objElement.data());
            var paramKeys   = ['post', 'named', 'query'];//$.arrayIntersect(objDataKeys, ['post', 'named', 'query']);
            var objParams   = {
                post    : {}, 
                named   : {}, 
                query   : {}, 
            };

            if(paramKeys.length){
                $.each(paramKeys, function(index, paramType){
                //  var params = objElement.data(paramType);
                    var params = objElement.attr('data-' + paramType);

                //  console.log(params);

                    if(typeof params != 'undefined' && params != ''){
                    //  convert to array
                        if(params.indexOf(',') > -1){
                            params = params.split(',');
                        }
                        else{
                            params = [params];
                        }

                    //  trim array params after split (like array_filter())
                        params = $.grep(params, function(v, i){
                            v = $.trim(v);
                            return (typeof v != 'undefined' && v != '');
                        });

                    //  loop params
                        if(params.length){
                            var counter = 0;
                            $.each(params, function(paramIndex, paramValue){
                                var fieldName   = '';
                                var fieldValue  = '';

                            //  check if params contain CLASS or ID selector
                                if(paramValue.match(/[#|.|:]/)){
                                    if(paramValue.indexOf(':') > -1){
                                        fieldName   = paramValue.split(':')[0];
                                        fieldValue  = paramValue.split(':')[1];

                                        fieldName   = $.getObjVal(fieldName);
                                        fieldValue  = $.getObjVal(fieldValue);
                                    }
                                    else{
                                        fieldName   = counter;
                                        fieldValue  = $.getObjVal(paramValue);

                                        counter++;
                                    }
                                }
                                else{
                                    fieldName   = counter;
                                    fieldValue  = paramValue;

                                    counter++;
                                }

                                objParams[paramType][fieldName] = fieldValue;
                            });
                        }
                    }   
                });
            }

            return objParams;
        }
        else{
            return null;
        }
    }

    $.ajaxMediaTitleChange = function( options ) {
        var settings = $.extend({
            obj: $('.label-image'),
            objShareFile: $('.share-file'),
            objTitleFile: $('.change-file-title'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('change').change(function(){
                var self = $(this);
                var val = self.val();
                var parent = self.parents('.ajax-parent');
                var rel = parent.attr('rel');
                var url = self.attr('data-url');
                var isEasyMode = self.closest('div#property_media_wrapper').length;

                url += '/' + rel + '/';

                if( val == 0 && val != '' ) {
                    self.attr('href', url);

                    $.directAjaxModal({
                        obj: self,
                    });
                } else {
                    val = val == '' ? 0 : val;

                    url += val + '/';

                    var attributes = { 'data-url' : url };
                    if(isEasyMode){
                        attributes = $.extend(attributes, {
                            'data-wrapper-write'    : '', 
                            'data-type'             : 'json', 
                        });
                    }

                    self.attr(attributes);

                    $.directAjaxLink({
                        obj: self,
                    });
                }
            });
        }

        function _callBeforeSave( self, objShare, objTitle ) {
            var parent = self.parents('.ajax-parent');
            var rel = parent.attr('rel');
            var url = '/ajax/admin_project_document_change';
            var title = $.checkUndefined(objTitle.val(), '')

            title = encodeURIComponent(title);

            if( objShare.is(':checked') ) {
                isShare = 1;
            } else {
                isShare = 0;
            }

            url += '/' + rel + '/' + isShare + '/' + title + '/';

            self.attr('href', url);

            $.directAjaxLink({
                obj: self,
            });
        }

        if( settings.objShareFile.length > 0 ) {
            settings.objShareFile.off('click');
            settings.objShareFile.click(function(){
                var self = $(this);
                var parent = self.parents('.ajax-parent');
                var title = parent.find('.change-file-title');

                _callBeforeSave(self, self, title);
            });
        }

        if( settings.objTitleFile.length > 0 ) {
            settings.objTitleFile.off('blur');
            settings.objTitleFile.blur(function(){
                var self = $(this);
                var parent = self.parents('.ajax-parent');
                var share = parent.find('.share-file');
                
                _callBeforeSave(self, share, self);
            });
        }
    }

//	utility function for setting dropdown value (objselect only)
	$.setDropdownValue = function(objDropdown, event){
		var eventType = '';

		if(typeof event == 'string'){
			eventType = event;
		}
		else{
			eventType = event.type || 'click';
		}

		var objDropdown		= $(objDropdown);
		var objInputText	= objDropdown.find('a.dropdown-toggle span.title');
		var objHiddenInput	= objDropdown.find('input.input-dropdown');

		if($.inArray(eventType, ['click', 'change']) > -1){
			var objDropdownMenu	= objDropdown.find('.dropdown-menu');
			var objOptions		= objDropdownMenu.find('li > a');
			var objTextInputs	= objDropdownMenu.find('.dropdown-text-input input:text');
			var objMinValue		= objTextInputs.filter('input[role="min-value"]');
			var objMaxValue		= objTextInputs.filter('input[role="max-value"]');

			var inputText	= 'Semua';
			var inputValue	= '';
			var minValue	= null;
			var maxValue	= null;

			if(eventType == 'click'){
			//	anchor clicked
				objTextInputs.val('');

				var objSelected	= objOptions.filter('.selected');

				if(objSelected.length){
					inputText	= objSelected.text();
					inputValue	= objSelected.attr('data-value') || '';

					var tempValue = $.trim(inputValue);

					if(tempValue != ''){
						var operand = inputValue.match(/\D/g);
                        // console.log(inputValue)
                        // console.log(operand)
						if(operand){
							operand		= $.isArray(operand) ? operand.join('') : operand;
							tempValue	= tempValue.split(operand);

							minValue = $.checkUndefined(tempValue[0], '');
							maxValue = $.checkUndefined(tempValue[1], '');

							minValue = $.trim(minValue);
							maxValue = $.trim(maxValue);

							var bothIsEmpty = minValue == '' && maxValue == '';

							if(bothIsEmpty == false){
								if($.inArray(operand, ['<', '<=']) > -1){
									minValue = null;
								}
								else if($.inArray(operand, ['>', '>=']) > -1){
									minValue = maxValue;
									maxValue = null;
								}
								else if(operand == '-' && parseInt(minValue) > 0 && (parseInt(minValue) > parseInt(maxValue))){
									var temp = minValue;
									minValue = maxValue;
									maxValue = temp;
								}
							}
						}
						else{
							minValue = inputValue;
						}
					}
				}
				else{
					objOptions.removeClass('selected');
				}
			}
			else{
			//	input value changed
				objOptions.removeClass('selected');

				var minValue	= objMinValue.val();
				var maxValue	= objMaxValue.val();
				var divider		= '';

				minValue = minValue.split(',').join('');
				maxValue = maxValue.split(',').join('');

				var tmpMinValue = $.formatNumber(minValue, 0);
				var tmpMaxValue = $.formatNumber(maxValue, 0);

				if(parseInt(tmpMinValue) > 0 || parseInt(tmpMaxValue) > 0 ){
					if(tmpMaxValue == 0){
						tmpMaxValue = ' atau lebih';
					}
					else if(tmpMinValue == 0){
						tmpMinValue = 'Dibawah ';
					}
					else{
						divider = '-';
					}

					inputText	= tmpMinValue + divider + tmpMaxValue;
					inputValue	= minValue + divider + maxValue;
				}
				else{
					objTextInputs.val('');
				}
			}

			if(objInputText.length){
				objInputText.text(inputText);
			}

			if(objHiddenInput.length){
				objHiddenInput.val(inputValue);	
			}

			objMinValue.val(minValue);
			objMaxValue.val(maxValue);

			if(parseInt(minValue) > 0) objMinValue.trigger('blur');
			if(parseInt(maxValue) > 0) objMaxValue.trigger('blur');
		}
	}

    $.dropDownMenu = function( options ) {
        var settings = $.extend({
            obj: $('.dropdown-menu'),
            objSelect: $('.dropdown-menu-select a'),
            objForm: $('.dropdown-menu-form input[type=checkbox]'),
        }, options );

        settings.obj.off('click');
        settings.objSelect.off('click');
        settings.objForm.off('click');

        settings.obj.click(function(e){
            var self = $(this);

            if( self.find('input[type=checkbox]').length > 0 ){
                e.stopPropagation();
            }
        });

        settings.objSelect.on('click', function(e){
			var self			= $(this);
			var objDropdown		= self.parents('.dropdown-group');
			var objDropdownMenu	= objDropdown.find('.dropdown-menu');

            e.preventDefault();

			if(objDropdownMenu.find('li > a').length){
				objDropdownMenu.find('li > a').removeClass('selected');
			}

			self.addClass('selected');

			if(typeof $.setDropdownValue == 'function'){
				$.setDropdownValue(objDropdown, e);
			}
			else{
				var objInputText	= objDropdown.find('a.dropdown-toggle span.title');
				var objHiddenInput	= objDropdown.find('input.input-dropdown');

				objInputText.html(self.html());
				objHiddenInput.val(self.attr('data-value'));
			}
        });

	//	init
		settings.objSelect.parents('.dropdown-group').each(function(index, parent){
			var parent		= $(parent);
			var selected	= parent.find('ul.dropdown-menu > li > a.selected');

			if(selected.length){
				selected.trigger('click');
			}
		});

        settings.objForm.click(function(event){
            var self = $(this);
            var parentGroup = self.parents('.dropdown-group');
            var titleHeader = parentGroup.find('a.dropdown-toggle');
            var defaultTitleArea = titleHeader.attr('data-empty');
            var title = parentGroup.find('a.dropdown-toggle span.title');
            var parent = self.parents('.dropdown-menu-form');
            var result = '';
            var val = self.attr('rel');

            if( val != '' ) {
                $('li', parent).each(function() {
                    var inputChk = $(this).find('.cb-custom input[type="checkbox"]');
                    var label = $(this).find('.cb-custom label').html();

                    if( inputChk.is(':checked') ) {
                        if( result == '' ) {
                            result += label;
                        } else {
                            result += ', '+label;
                        }
                    }
                });

                if( result != '' ) {
                    if( parent.find('.cb-checkmark input[rel=""]').is(':checked') ) {
                        parent.find('.cb-checkmark input[rel=""]').prop('checked', false);
                    }

                    resultTitle = result.substr(0,18);

                    if( result.length > 18 ) {
                        resultTitle += '..';
                    }


                    title.html(resultTitle);
                } else {
                    title.html(defaultTitleArea);
                }
            } else {
                var objLabelChk = parent.find('input:checked+label');
                objLabelChk.trigger('click');

                title.html(defaultTitleArea);
            }
        });

		$('body').off('change').on('change', '.dropdown-text-input input:text', function(event){
			var objDropdown = $(this).parents('.dropdown-group');

			if(typeof $.setDropdownValue == 'function'){
				$.setDropdownValue(objDropdown, event);
			}
		});
    }

    $.colorPicker = function(options){
        var settings = $.extend({
            obj: $('input.colorPicker'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.minicolors({
                control: settings.obj.attr('data-control') || 'hue',
                defaultValue: settings.obj.attr('data-defaultValue') || '',
                format: settings.obj.attr('data-format') || 'rgb',
                keywords: settings.obj.attr('data-keywords') || '',
                inline: settings.obj.attr('data-inline') === 'true',
                letterCase: settings.obj.attr('data-letterCase') || 'lowercase',
                opacity: settings.obj.attr('data-opacity') || '0.5',
                position: settings.obj.attr('data-position') || 'bottom left',
                change: function(value, opacity) {
                    if( !value ) return;
                    if( opacity ) value += ', ' + opacity;
                    if( typeof console === 'object' ) {
                        console.log(value);
                    }
                },
                theme: 'bootstrap'
            });
        }
    }

    $.handle_input_file = function(options){
        var settings = $.extend({
            obj: $('.handle-input-file'),
            objLiveHandle: '.file-image-live',
            objLivePreview: '#live-image-preview img'
        }, options );

        var target_live_preview = settings.obj.attr('data-live-preview');

        if(typeof target_live_preview == 'undefined' ) {
            target_live_preview = settings.objLivePreview;
        }

        settings.obj.click(function(e){
            var target = settings.obj.attr('data-target');

            if(typeof target == 'undefined' ) {
                target = settings.objLiveHandle;
            }

            $(target).trigger('click');
        });
        
        $(settings.objLiveHandle).change(function(){
            readURL(this, target_live_preview);
        });
        
        function readURL(input, target_live) {
            var fileTypes = ['jpeg', 'jpg', 'gif', 'png'];

            if (input.files && input.files[0]) {
                var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
                isSuccess = fileTypes.indexOf(extension) > -1;

                if(isSuccess){
                    if(window.FileReader) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            $(target_live).attr('src', e.target.result);
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        alert('Browser ini tidak mendukung untuk melakukan preview atas foto yg Anda Unggah, Namun dapat muncul di Preview selanjutnya setelah Anda selesai melakukan pengisian.');
                    }
                }else{
                    $(input).val('');
                    $(target_live).attr('src', 'http://www.rumahku.com/images/view/properties/l/2014/09/54192a5e-6b60-43c8-8d34-2e82ca2ba9b3.jpg?efb44234f772bb9d7da32293ac47527a');
                    alert('Harap hanya memasukkan file berekstensi jpeg, jpg, gif atau png');
                }
            }
        }
    }

    $.isMobile = function(){
        var mobile = false; //initiate as false
    
        // device detection
        if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
            mobile = true;
        }

        return mobile;
    };

    $.initCarouselSwipe = function(options){
        var settings = $.extend({
            objTarget: null,
        }, options );

        settings.objTarget.swiperight(function(){
            $(this).find('.carousel').carousel('prev');
        });

        settings.objTarget.swipeleft(function(){
            $(this).find('.carousel').carousel('next');
        });
    }

    $.carouselSwipe = function(options){
        // if($.isMobile()){
        //  load jquery mobile if the script has not been loaded
            $.initCarouselSwipe(options);
        // }
    }

    $.carousel = function(options){
        var settings = $.extend({
            obj: $('.carousel')
        }, options );

        if(settings.obj.length > 0){
            settings.obj.carousel({
                interval: false,
            });
        }
    }

	$.updateLiveBanner = function(options, type){
		var obj_agent_wrapper = $('.agent-info');

		if(obj_agent_wrapper.length && obj_agent_wrapper.html() != ''){
			var action_banner = $('#action-banner').val();
			var property_action = $('#property-option').val();

			if(action_banner == 2){
				$('#property-action-container').removeClass('dijual').addClass('disewakan');
			} else {
				$('#property-action-container').removeClass('disewakan').addClass('dijual');
				$('#period-property-banner').val('');
			}

			var property_tag	= $('#in-property-title').val();
			var property_area	= '';
			var property_city	= '';
			var property_region	= '';

			var obj_form			= obj_agent_wrapper.closest('#form-ebrochure');
			var obj_location_picker	= obj_form.find('input.location-picker');

			if(obj_location_picker.length){
				var location_name = obj_location_picker.val();

				location_name	= location_name.split(', ');
				property_area	= location_name[0] ? location_name[0] : '';
				property_city	= location_name[1] ? location_name[1] : '';
				property_region = location_name[2] ? location_name[2] : '';
			}
			else{
				property_area	= $('.subareaId').find('option:selected').text();
				property_city	= $('.cityId').find('option:selected').text();
				property_region	= $('.regionId').find('option:selected').text();
			}

            var property_title = $('#type-banner').find('option:selected').text();
            var period = $('#period-property-banner').val();
            var lot_unit = $('#ebrosur-lot-unit').val();
            var note_price = $('#ebrosur-note-price').val();

            if(lot_unit != ''){
                lot_unit = ' / '+$('#ebrosur-lot-unit').find('option:selected').text();
            }

            if(period != ''){
                period = $('#period-property-banner').find('option:selected').text();
            }

            if($('.potrait-bg').length > 0){
                property_title = property_title+' '+property_tag+', Di '+property_area+', '+property_city;
            }else{
                property_title = property_title+' '+property_tag+'<br/> Di '+property_area+', '+property_city;
            }
            
            $('#property-title').html(property_title);
            
            var property_price = $('#in-property-price').val();
            var format_price = $('.currency-handle option:selected').text();

            if(format_price == 'IDR'){
                format_price = 'Rp.';
            }

            var property_price_format = format_price+' '+property_price+',- '+lot_unit+' '+period+' '+note_price;

            $('#property-price').html('<p>'+property_price_format+'</p>');

            if(type == 'onload'){
                var desc_info = $('#desc-info').val();
                $('#desc-body').html('<p>'+desc_info+'</p>');
            }
        }

		$.action_banner();
	//	$.ebrosur_lot_unit();
		$.EbrosurListAgent(options);
    }

    $.action_banner = function(){
        $('#action-banner').off('change');        
        $('#action-banner').change(function(){
            var self = $(this);
            
            if(self.val() == 1){
                $('#action-ebrosur').hide();
            }else{
                $('#action-ebrosur').show();
            }

            $.updateLiveBanner();
        });
    }

    /*$.ebrosur_lot_unit = function(){
        $('#type-banner').change(function(){
            $.ajaxUpdateElement($(this), $('.box-lot-unit-ebrosur #ebrosur-lot-unit'), '/ajax/lot_unit_ebrosur/'+$(this).val()+'/'+$('#action-banner').val()+'/');
        });
    }*/

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $.limit_word_package = function(options){
        var settings = $.extend({
            obj: $('#desc-info'),
            objCounter: $('.limit-character'),
            objBody: $('.desc-body')
        }, options );

        if( settings.obj.length > 0 ) {
            function getLengthChar ( obj ) {
                var val = obj.val();
                var leng = val.length;

                return leng;
            }

            var maxCharLength = settings.obj.attr('maxlength');
            var currentLeng = getLengthChar(settings.obj);

            function cutText ( obj ) {
                var leng = getLengthChar(obj);
                var val = obj.val();

                var maxCharLength = obj.attr('maxlength');

                if( leng > maxCharLength ) {
                    var cutText = val.substr(0, maxCharLength);
                    var result = 0;

                    obj.val(cutText);
                    settings.objCounter.html(result);
                }else{
                    var desc_info = settings.obj.val();

                    desc_info = desc_info.replace(/(?:\r\n|\r|\n)/g, '<br />');
                    
                    if(settings.objBody != false || settings.objBody == 'false'){
                        settings.objBody.html('<p>'+desc_info+'</p>');
                    }
                }
            }

            settings.obj.keydown(function(event){
                var self = $(this);
                var leng = getLengthChar(self) + 1;
                var charCode = (event.which) ? event.which : event.keyCode;

                if( leng > maxCharLength && charCode != 8 && charCode != 46 ) {
                    event.preventDefault();
                }
            });

            settings.obj.keyup(function(){
                var self = $(this);
                var leng = getLengthChar(self);
                var result = maxCharLength-leng;

                settings.objCounter.html(result);
                cutText(self);
            });

            settings.obj.blur(function(){
                var self = $(this);
                cutText(self);
            });

            settings.objCounter.html(maxCharLength-currentLeng);

            $('.cb-custom .ebrosur-desc').off();
            $('.cb-custom .ebrosur-desc').click(function(){
                var id_desc = $('#ebrosur-is-description').is(':checked');
                var id_spec = $('#ebrosur-is-specification').is(':checked');

                var val_desc = $('#ebrosur-description-property').val();
                var val_spec = $('#ebrosur-specification-property').val();

                var text = '';

                if(id_spec && val_spec != null){
                    text = text+val_spec;
                }

                if(id_desc && val_desc != null){
                    if(id_spec && val_spec != null){
                        text = text+' ';
                    }

                    text = text+val_desc;
                }

                $('#desc-info').val(text);
                cutText($('#desc-info'));
                cutText($('#desc-info'));
            });
        }

        $('.changes-event-ebrosur, #form-ebrochure .location-wrapper input.location-picker').change(function(){
            $.updateLiveBanner();
        });

        $('.key-event-ebrosur').keyup(function(){
            delay(function(){
                $.updateLiveBanner();
            }, 1000 );
        });

        $.action_banner();
        /*$.ebrosur_lot_unit();*/
        $.EbrosurListAgent();

        $('.color-banner').click(function(){
            var self = $(this);
            var id = self.attr('id');
            var background = self.attr('template-img');
            var textcolor = self.attr('text-color');
            var property_action_id = $('#action-banner').val();
            var activeClass = $('.color-banner.active').attr('template-img');

            $('.color-banner').removeClass('active');
            self.addClass('active');
            $('#background_color').val(id);
            $('#property-action-container').removeClass(activeClass).addClass(background);
            
            if(property_action_id == 2){
                $('#property-action-container').removeClass('dijual').addClass('disewakan');
            } else {
                $('#property-action-container').removeClass('disewakan').addClass('dijual');
            }
        });
    }


    var isNewContent = false;
    var refresh_ebrosur = function(){
        disableAjax = true;
        
        $.ajax({
            type:'POST',
            url: '/ajax/get_ebrochure/'+$('#maxid').val()+'/',
            success: function(result) {
                var output = $(result);
                var content = output.filter("#container-fancybox").html();
                var maxid = output.filter("#maxid").val();
                var oldMaxId = $('#maxid').val();
                
                if(oldMaxId < maxid && maxid !== undefined){
                    $('#maxid').val(maxid);
                    $("#container-fancybox").prepend(content);
                    isNewContent = true;
                }

                disableAjax = false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                disableAjax = false;
                return false;
            }
        });
    }

    $.initFancybox = function(){
        if($('.fancybox-buttons').length > 0){
            var autoplay = $.checkUndefined($('#ebrosur-autoplay').val(), false);

            if( autoplay == 1 ) {
                autoPlay = true;
            } else {
                autoPlay = false;
            }

            $('.fancybox-buttons').fancybox({
                openEffect  : 'elastic',
                closeEffect : 'elastic',

                prevEffect : 'elastic',
                nextEffect : 'elastic',

                closeBtn  : false,
                autoPlay  : autoPlay,

                helpers : {
                    title : {
                        type : 'inside'
                    },
                    buttons : {}
                },
                playSpeed: 5000,
                minHeight: ($.isMobile())?false:($(window).height() - 150),
                beforeLoad : function() {
                    if(isNewContent){
                        isNewContent = false;
                        var newobj = [];
                        var cur = this.group;
                        $('.fancybox-buttons').each(function(){
                            var isexists = false;
                            var imagehref = $(this).attr('href');
                            for(var i in cur){
                                var current = cur[i];
                                if(current.href == imagehref){
                                    isexists = true;
                                    break;
                                }
                            }
                            if(!isexists){
                                newobj.push({ 
                                    element: $(this),
                                    href: imagehref, 
                                    type: "image", 
                                    isDom: false,
                                    title: "" 
                                });
                            }
                        });
                        for(var i = newobj.length-1; i >= 0; i--){
                            this.group.unshift(newobj[i]);
                        }
                        this.index = (this.group.length)-1;
                    }
                },
                'onComplete': function() {
                    $('.fancybox-buttons').css({'top':'20px', 'bottom':'auto'});
                }
            });

            if( $('.start-interval').length > 0 ) {
                setInterval(function(){
                    refresh_ebrosur();
                }, 15000);
            }

            if( autoplay == 1 ) {
                $(".fancybox-buttons").trigger('click');
            }
        }
    }

    $.handle_toggle_content = function(options){
        var settings = $.extend({
            obj: $('.handle-toggle-content')
        }, options );

        settings.obj.on('init change', function(event){
            var self = $(this);
            var target = self.attr('data-target');
            var action_type = $.checkUndefined(self.attr('data-type'), 'display');
            var data_reverse = $.checkUndefined(self.attr('data-reverse'), '');
            var data_disable = $.checkUndefined(self.attr('data-disabled'), '');
            var checked = false;
            var unchecked = true;

            if( data_reverse == 'true' ) {
                checked = true;
                unchecked = false;
            }

            if( self.is(':checked') ) {
                if( action_type == 'disabled' ) {
                    $(target).prop( "disabled", checked );
                } else {
                    if( data_reverse == 'true' ) {
                        $(target).slideUp();
                    } else {
                        $(target).slideDown();
                    }
                }

                if(data_disable != ''){
                    $(data_disable).prop( "disabled", true );
                }
            } else {
                if( action_type == 'disabled' ) {
                    $(target).prop( "disabled", unchecked );
                } else {
                    if( data_reverse == 'true' ) {
                        $(target).slideDown();
                    } else {
                        $(target).slideUp();
                    }
                }

                if(data_disable != ''){
                    $(data_disable).prop( "disabled", false );
                }
            }
        }).trigger('init');
    }


    // $.calcDp = function(options) {
    //     var settings = $.extend({
    //         objPrice: $('.KPR-price'),
    //         objDpPercent: $('.kpr-dp-percent'),
    //         objDpPrice: $('.kpr-dp-price'),
    //     }, options );
        
    //     var price = $('.KPR-price').val();

    //     if($('.kpr-dp-price').length > 0){
    //          settings.objDpPrice.change(function(){
    //             var self = $(this);
    //             var downPayment = self.val();
    //             downPayment = downPayment.split(',').join('');
    //             var percent = getDpPercent(downPayment, price);
    //             $('.kpr-dp-percent').val(percent.toFixed(2));

    //             $.directAjaxLink({
    //                 obj: self,
    //             });

    //          });
    //     }

    //     if($('.kpr-dp-percent').length > 0){
    //          settings.objDpPercent.change(function(){
    //             var self = $(this);
    //             var percent = self.val();
    //             var downPayment = getDpPrice(percent, price);
    //             $('.kpr-dp-price').val(downPayment);

    //             $.directAjaxLink({
    //                 obj: self,
    //             });

    //          });
    //     }

    //     function getDpPercent( dp, price){
    //         var percent = ( dp / price )*100;
    //         return percent;
    //     }

    //     function getDpPrice( percent, price){
    //         var downPayment = ( percent / 100 )*price;
    //         return downPayment;
    //     }
    // }

    $.calcKPR = function(options){
        function SameAddress() {
            var obj = $('#same-as-address-ktp');
            if( obj.length > 0 ) {
                obj.off('click');
                obj.click(function(){
                    if( $(this).is(':checked') ) {
                        $('#address-domisili textarea').val('');
                        $('#address-domisili').slideUp();
                    } else {
                        $('#address-domisili').slideDown();
                    }
                });
            }
        }

        function count_percent_down_payment(property_price, down_payment){
            var total = (down_payment / property_price) * 100;
            return total;
        }

        function count_loan_price(property_price, down_payment){
            var total = property_price - down_payment;
            return total;
        }

        function count_down_payment(property_price, percent_down_payment){
            return property_price * percent_down_payment;
        }

        function countManualPercentDownPayment(property_price, total_loan){
            return ((property_price - total_loan)  / property_price) * 100;
        }

        var trigger_detail_angsuran = function () {
            $('.trigger-installment-payment').off('click');
            $('.trigger-installment-payment').click(function(e){
                $('.tab-installment-payment').trigger('click');
            });
        }

        function keyUpFloatingRate(cek){
            var floating = $('.floating_rate_id').val();
            if(isNaN(floating)){
                floating = 0;
            }
            if(floating != ''){
                if(cek == 0){
                    $('.floating_rate_id').val('');
                    $('.credit_total').val('');
                    $('.credit_total').attr('disabled', true);
                    $('.credit_total').attr('required', false).removeClass('required');
                }else{
                    $('.credit_total').attr('disabled', false);
                    $('.credit_total').attr('required', true).addClass('required');
                }
            }else{
                $('.floating_rate_id').val('');
                $('.credit_total').val('');
                $('.credit_total').attr('disabled', true);
                $('.credit_total').attr('required', false).removeClass('required');
            }
        }

        function update_down_payment(parent, action){
            var price_text = parent.find('.KPR-price').val();
            var obj_loan_price = parent.find('.loan-price-id');
            var obj_persen_loan = parent.find('.persen-loan-id');
            var obj_down_payment = parent.find('.down-payment-id');

            var down_payment = obj_down_payment.val();
            down_payment = parseInt(down_payment.replace(/,/g, ''));
            if(isNaN(down_payment)){
                down_payment = 0;
            }

            var property_price = price_text.replace(/,/g, '');
            if(isNaN(property_price)){
                property_price = 0;
            }
            
            var persen_loan = obj_persen_loan.val();
            if(isNaN(persen_loan)){
                persen_loan = 0;
            }

            var loan_price = $.numberToString(obj_loan_price.val(), 0);
            var for_persen = persen_loan / 100;
            var total_down_payment;
            var total_percent_down_payment;
            var total_loan;

            if(action == 'find_percent'){
                total_percent_down_payment = count_percent_down_payment(property_price, down_payment);
                if(total_percent_down_payment > 100 || total_percent_down_payment < 0){
                    alert('Uang muka tidak boleh lebih dari 100% dan kurang dari 0%');
                    if( property_price == 0 ) {
                        uang_muka = 0;
                    } else {
                        uang_muka = 20;
                    }
                    obj_loan_price.val($.formatNumber( uang_muka, 0 ));              
                    obj_down_payment.val(0);               
                    return false;
                }else{
                    obj_persen_loan.val($.formatNumber( total_percent_down_payment, 2));

                    total_loan = count_loan_price(property_price, down_payment);
                    obj_loan_price.val($.formatNumber( total_loan, 0 ));             
                }
            }else if(action == 'find_down_payment'){
                total_down_payment = count_down_payment(property_price, for_persen);
                obj_down_payment.val($.formatNumber( total_down_payment, 0 ));

                total_loan = count_loan_price(property_price, total_down_payment);
                format_loan_price = $.formatNumber( total_loan, 0 );

                // if ( obj_loan_price.is( 'div' ) || obj_loan_price.is( 'span' ) ) {
                    obj_loan_price.html(format_loan_price).val(format_loan_price);
                // } else {
                    // obj_loan_price.val(format_loan_price);
                // }
            }else if(action == 'find_loan'){
                var loan = obj_loan_price.val();
                loan = loan.replace(/,/g, '');

                if(isNaN(loan)){
                    loan = 0;
                } else {
                    loan = parseInt(loan);
                }
                
                if(loan > property_price){
                    alert('Jumlah pinjaman tidak boleh lebih besar dari harga properti');
                    update_down_payment(parent, 'find_percent');
                    update_down_payment(parent, 'find_down_payment');
                }else{
                    total_percent_down_payment = countManualPercentDownPayment(property_price, loan);
                    if(isNaN(total_percent_down_payment)){
                        total_percent_down_payment = 0;
                    }
                    obj_persen_loan.val($.formatNumber( total_percent_down_payment, 2));

                    total_down_payment = count_down_payment(property_price, (total_percent_down_payment / 100));                
                    obj_down_payment.val($.formatNumber( total_down_payment, 0 ));
                }
            }
        }

        var calculate_kpr = function( parent ){
            var obj_interest_rate = parent.find('.interest_rate');
            var obj_loan_amount = parent.find('.loan-amount');
            var obj_credit_fix = parent.find('.credit_fix');

            var interest_rate = $.checkUndefined(obj_interest_rate.val(), '');
            
            if( interest_rate == '' ){
                alert('Bunga KPR Harap diisi');
            } else {
                var target = parent.attr('data-target');
                var obj_target = parent.find(target);
                var property_price = parent.attr('data-price');
                var loan_amount = $.checkUndefined(obj_loan_amount.val(), 0);
                var credit_fix = $.checkUndefined(obj_credit_fix.val(), 0);

                loan_amount = $.numberToString(loan_amount, 0);
                property_price = $.numberToString(property_price, 0);

                $.ajax({
                    url: '/ajax/get_kpr_installment_payment/'+property_price+'/'+loan_amount+'/'+credit_fix+'/'+interest_rate+'/',
                    type: 'GET',
                    success: function(response) {
                        if( response != '' ) {
                            obj_target.replaceWith(response);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });
            }
        }

        if($("#kpr-btn-form").length > 0){
            $('.ajukan-kpr-button a').off('click');
            $('.ajukan-kpr-button a').click(function(){
                var theOffset = $("#kpr-btn-form").offset();
                $('html, body').animate({
                    scrollTop: theOffset.top - 1200
                }, 2000);
            });
        }

        if( $('.credit_fix').length ) {
            $('.credit_fix').off('change');
            $('.credit_fix').change(function(){
                var parent = $(this).parents('.calculator-kpr-credit');
                calculate_kpr(parent);
            });
        }

        if( $('.interest_rate').length ) {
            $('.interest_rate').off('blur');
            $('.interest_rate').blur(function(){
                var parent = $(this).parents('.calculator-kpr-credit');
                calculate_kpr(parent);
            });
        }

        if( $('.kpr-nav-tab').length ) {
            $('.kpr-nav-tab').off('click');
            $('.kpr-nav-tab').click(function(e){
                e.preventDefault();

                var self = $(this);
                $('.nav-kpr ul li').removeClass('active');
                $('.tab-content-kpr').addClass('hide');

                if( self.attr('link') ) {
                    var link = self.attr('link');
                } else {
                    var parent = self.parents('li');
                    var link = parent.attr('link');
                    parent.addClass('active');              
                }

                if( link == 'kpr-btn-form' ) {
                    $('#botton-action-kpr').hide();
                } else {
                    $('#botton-action-kpr').show();
                }

                $('#'+link).removeClass('hide');
            });

            trigger_detail_angsuran();
            $('.kpr-nav-tab.active').trigger('click');
        }

        if( $('.btn-link-kpr-form').length ) {
            $('.btn-link-kpr-form').off('click');
            $('.btn-link-kpr-form').click(function(e){
                e.preventDefault();
                $('li[link="kpr-btn-form"]').children().click();
            });
        }

        var validateKpr = function( parent ){
            var obj_price = parent.find('.KPR-price');
            var obj_down_payment = parent.find('.down-payment-id');
            var obj_interest_rate = parent.find('.interest_rate');
            var obj_credit_total = parent.find('.credit_total');
            var obj_credit_fix_id = parent.find('.credit_fix_id');
            var obj_floating_rate_id = parent.find('.floating_rate_id');
            var status = true;
            var offset = 0;

            if( parseInt( obj_price.val() ) == '' || obj_price.val() == 0 ) {
                alert('Harga beli properti harap diisi');
                offset = obj_price.offset();
                status = false;
            } else if( parseInt( obj_down_payment.val() ) == '' || obj_down_payment.val() == 0 ){
                alert('Uang muka harap diisi');
                offset = obj_down_payment.offset();
                status = false;
            } else if( obj_interest_rate.val() == '' || parseFloat( obj_interest_rate.val() ) == 0 ){
                alert('Suku bunga harap diisi');
                offset = obj_interest_rate.offset();
                status = false;
            } else if( obj_credit_total.val() == '' ){
                alert('Lama pinjaman harap dipilih');
                offset = obj_credit_total.offset();
                status = false;
            } else if( parseInt(obj_credit_fix_id.val()) > parseInt(obj_credit_total.val()) ){
                alert('Masa Kredit Fix harus lebih kecil dari Lama Pinjaman');
                offset = obj_credit_fix_id.offset();
                status = false;
            } else if( obj_credit_fix_id.val() != '' && obj_floating_rate_id.val() == '' ){
                alert('Suku bunga floating harap diisi');
                offset = obj_floating_rate_id.offset();
                status = false;
            }

            if( status == false ) {
                $('html, body').animate({
                    scrollTop: offset.top - 150
                }, 2000);
            }
            return status;
        }

        if( $('.btn-save-kpr').length ) {
            $('.btn-save-kpr').closest('form').off('submit');
            $('.btn-save-kpr').closest('form').submit(function(e){
                e.preventDefault();
                var parent = $('.calculator-kpr-credit');

                if( validateKpr( parent ) ) {
                    var obj_loan_price = parent.find('.loan-price-id');

                    var loan_price = obj_loan_price.val();
                    loan_price = loan_price.replace(/,/g, '');
                    $('#kpr-application-loan-price-id').val($.formatNumber( loan_price, 0));

                    $("#kpr-appraisal").attr('disabled', false);
                    $("#kpr-administration").attr('disabled', false);

                    var state_loan_price = $('#kpr-application-loan-price-id').attr('disabled');

                    // FIELD NILAI PENGAJUAN DI FORM PENGAJUAN KPR
                    $('#kpr-application-loan-price-id').attr('disabled', false);

                    var formData = new FormData(this);
                    var KprFormData = $('#KPRMainForm').serializeArray();
                    var url = $('.btn-save-kpr').closest('form').attr('action');
                    var self = $('#kpr-btn-form form.kpr');

                    for (var i = 0; i < KprFormData.length; i++) {
                         formData.append(KprFormData[i].name, KprFormData[i].value);
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function(response) {

                            var status = $(response).filter('#status').text();
                            var output = $(response).find('#content-form').html();
                            
                            if( status == 'success' ){
                                // UNDER DEVELOPMENT
                                // var modal_title = $(response).find('.modal-title').html();
                                // var modal_content = $(response).find('.modal-content').html();

                                // $('#myModal #myModalLabel').html(modal_title);
                                // $('.modal-body').html(modal_content);
                                // closeModal();

                                // $('#myModal').modal({
                                //     show: true,
                                // });
                                $("#kpr-btn-form").find('#content-form').html(output);
                            } else {
                                var message = $(response).find('#message').html();
                                $("#kpr-btn-form").find('#content-form').html(message+output);
                            }

                            $.inputPrice();
                            $.datePicker({
                                up: 'fa fa-angle-up',
                                down: 'fa fa-angle-down',
                                next: 'fa fa-angle-right',
                                previous: 'fa fa-angle-left',
                                clear: 'fa fa-trash-o',
                                close: 'fa fa-times',
                            });
                            $.generateLocation();
                            $('.tooltip-note').tooltip();
                            SameAddress();

                            $("#kpr-appraisal").attr('disabled', true);
                            $("#kpr-administration").attr('disabled', true);
                            
                            // FIELD NILAI PENGAJUAN DI FORM PENGAJUAN KPR
                            $('#kpr-application-loan-price-id').attr('disabled', state_loan_price);
                            $(window).scrollTop($('#kpr-content').offset().top);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                            return false;
                        }
                    });

                    return false;
                } else {
                    $('#kpr-application-loan-price-id').val(0);
                }
            });
        }

        if( $('#btn-calculate-kpr').length ) {
            $('#btn-share-kpr').css('visibility', 'hidden');
            $('#btn-calculate-kpr').off('click');
            $('#btn-calculate-kpr').click(function(e){
                e.preventDefault();
                var parent = $('.calculator-kpr-credit');

                if( validateKpr( parent ) ) {
                    var self = $(this);
                    var obj_loan_price = parent.find('.loan-price-id');
                    var obj_down_payment = parent.find('.down-payment-id');
                    var obj_credit_fix_id = parent.find('.credit_fix_id');

                    $("#kpr-appraisal").attr('disabled', false);
                    $("#kpr-administration").attr('disabled', false);

                    // FIELD NILAI PENGAJUAN DI FORM PENGAJUAN KPR
                    $('#kpr-application-loan-price-id').attr('disabled', false);

                    var url_ajax = self.attr('href');
                    var data = $('#KPRMainForm').serialize();

                    $.ajax({
                        data: data,
                        url: url_ajax,
                        type: 'POST',
                        success: function(response) {
                            var output_loan_summary = $(response).find('#loan-summary-content').html();
                            var output_installment_payment = $(response).find('#installment-payment-content').html();
                            var output_url_kpr_share = $(response).find('#url-kpr-share').html();
                            var output_url_kpr_excel = $(response).find('#url-kpr-excel').html();

                            var loan_price = obj_loan_price.val();
                            var down_payment = obj_down_payment.val();
                            var credit_fix = ( obj_credit_fix_id.val() != '' ) ? obj_credit_fix_id.val() : 0;
                            var credit_float = ( $('#KPRCreditFloat').val() != '' ) ? $('#KPRCreditFloat').val() : 0;
                            var credit_total = parseInt(credit_fix) + parseInt(credit_float);
                            
                            $('#loan-summary').html(output_loan_summary);
                            $('#installment-payment').html(output_installment_payment);
                            $('#btn-share-kpr').attr('href', output_url_kpr_share).css('visibility', 'visible');
                            $('.excel-anchor').attr('href', output_url_kpr_excel);

                            // FIELD NILAI PENGAJUAN DI FORM PENGAJUAN KPR
                            loan_price = loan_price.replace(/,/g, '');
                            $('#kpr-application-loan-price-id').val($.formatNumber( loan_price, 0));

                            // REBIND
                            $('.tooltip-note').tooltip();

                            $("#kpr-appraisal").attr('disabled', true);
                            $("#kpr-administration").attr('disabled', true);

                            // FIELD NILAI PENGAJUAN DI FORM PENGAJUAN KPR
                            $('#kpr-application-loan-price-id').attr('disabled', true);
                            $('#kpr-application-credit-total-id').val(credit_total);
                            $('#kpr-application-down-payment-id').val(down_payment);

                            // ajaxModal();
                            $('.additional-kpr-button').css('visibility','visible');
                            trigger_detail_angsuran();
                        }
                    });
                } else {
                    $('#kpr-application-loan-price-id').val(0);
                }
            });
        }

        if( $('.link-kpr-detail-simulation').length ){
            $('.link-kpr-detail-simulation').off('click');
            $('.link-kpr-detail-simulation').click(function(e){
                e.preventDefault();
                var self = $(this);
                var extended_href = self.attr('href') + '/'+  $('#credit_fix').val() + '/' + parseFloat($('#interest_rate').val());
                window.location = extended_href;
            });
        }

        if( $('.persen-loan-id').length ) {
            var value = $('.persen-loan-id').val();
            var has_tootip = $('.persen-loan-id').closest('div.form-group').find('a.tooltip-note');
            if( has_tootip.length ) {
                has_tootip.attr('data-original-title', 'Uang muka diambil dari '+value+'% harga properti yang Anda inginkan.');
            }
        }

        if( $('#loan-summary').find('p.alert-warning').length ) {
            $('.additional-kpr-button').css('visibility','hidden');
        }

        if( $('#kpr-content').length > 0 ) {
            $('.floating_rate_id').off('keyup');
            $('.floating_rate_id').keyup(function(){
                var cek = $(this).val();

                keyUpFloatingRate(cek);
            });

            $('.floating_rate_id').off('ready');
            $('.floating_rate_id').ready(function(){
                var cek = $('.floating_rate_id').val();

                if(cek != ''){
                    $('.credit_total').attr('disabled', false);
                }
            });
        }

        if( $('.persen-loan-id').length ) {
            $('.persen-loan-id').off('blur');
            $('.persen-loan-id').blur(function(){
                var self = $(this);
                var value = self.val();
                var _calc = self.attr('data-calc');
                var parent = self.parents('.calculator-kpr-credit');

                var price = parent.find('.KPR-price').val();
                var obj_loan_price = parent.find('.loan-price-id');
                var obj_persen_loan = parent.find('.persen-loan-id');

                value = value.replace(/,/g, '');
                value = parseFloat(value);

                price = price.replace(/,/g, '');
                price = parseInt(price);

                var uang_muka = 0;
                var tooltip = self.closest('div.form-group').find('a.tooltip-note');

                if( isNaN(price) ) {
                    price = 0;
                }

                if(value < 0 || isNaN(value)){
                    if( price != 0 ) {
                        uang_muka = 20;
                    } else {
                        uang_muka = 0;
                    }
                    
                    obj_loan_price.val($.formatNumber( uang_muka, 0 ));              
                    update_down_payment(parent, 'find_down_payment');
                }else if(value > 100){
                    alert('Uang muka tidak boleh lebih dari 100%');

                    if( price != 0 ) {
                        uang_muka = 20;
                    } else {
                        uang_muka = 0;
                    }
                    
                    obj_persen_loan.val($.formatNumber( uang_muka, 0 ));             
                }else{
                    update_down_payment(parent, 'find_down_payment');
                }

                if( tooltip.length ) {
                    tooltip.attr('data-original-title', 'Uang muka diambil dari '+value+'% harga properti yang Anda inginkan.');
                }

                if( _calc == 'first-installment' ) {
                    calculate_kpr(parent);
                }
            });
        }

        $('.credit_fix_id').off('change');
        $('.credit_fix_id').change(function(){
            var parent = self.parents('.calculator-kpr-credit');
            var obj_interest_rate = parent.find('.interest_rate');
            var obj_floating_rate_id = parent.find('.floating_rate_id');

            var value = $(this).val();
            if( value == '' ) {
                obj_floating_rate_id.val('');
                obj_floating_rate_id.attr('disabled', true);
                $('.tooltip-note', obj_floating_rate_id.closest('div.form-group')).hide();
            } else {
                obj_floating_rate_id.attr('disabled', false);
                $('.tooltip-note', obj_floating_rate_id.closest('div.form-group')).show();
            }

            var tooltip_interest_rate = obj_interest_rate.closest('div.form-group').find('a.tooltip-note');
            var tooltip_floating_rate = obj_floating_rate_id.closest('div.form-group').find('a.tooltip-note');

            if( tooltip_interest_rate.length ) {
                tooltip_interest_rate.attr('data-original-title', 'Suku Bunga Bank Fix selama '+value+' Tahun.');
            }
            if( tooltip_floating_rate.length ) {
                tooltip_floating_rate.attr('data-original-title', 'Setelah '+value+' Tahun akan mengikuti Suku Bunga Bank BI yang berlaku pada saat itu.');
            }
        });

        $('.KPR-price').off('blur');
        $('.KPR-price').blur(function(){
            var self = $(this);
            var parent = self.parents('.calculator-kpr-credit');
            var price = self.val();

            var obj_persen_loan = parent.find('.persen-loan-id');

            price = price.replace(/,/g, '');
            price = parseInt(price);

            var uang_muka = obj_persen_loan.val();
            uang_muka = uang_muka.replace(/,/g, '');
            uang_muka = parseInt(uang_muka);

            if( isNaN(uang_muka) ) {
                uang_muka = 0;
            }
            
            if( isNaN(price) ){
                price = 0;
                self.val($.formatNumber( price, 0));
            } else {
                if( price != 0 && uang_muka == 0 ) {
                    obj_persen_loan.val($.formatNumber( 20, 2));
                }
            }

            obj_persen_loan.trigger("blur");
        });

        $('.loan-price-id').off('blur');
        $('.loan-price-id').blur(function(){
            var self = $(this);
            var parent = self.parents('.calculator-kpr-credit');
            var jumlah_pinjaman = self.val();

            jumlah_pinjaman = jumlah_pinjaman.replace(/,/g, '');
            jumlah_pinjaman = parseInt(jumlah_pinjaman);

            if( isNaN(jumlah_pinjaman) ){
                jumlah_pinjaman = 0;
            }

            if(jumlah_pinjaman < 0){
                jumlah_pinjaman = 0;
            }else{
                update_down_payment(parent, 'find_loan');
            }
        });

        $('.interest_rate').off('blur');
        $('.interest_rate').blur(function(){
            var bunga_kpr = $(this).val();
            bunga_kpr = bunga_kpr.replace(/,/g, '');
            bunga_kpr = parseFloat(bunga_kpr);

            if( isNaN(bunga_kpr) ){
                bunga_kpr = 0;
            }

            $(this).val($.formatNumber( bunga_kpr, 2 ));
        });

        $('.floating_rate_id').off('blur');
        $('.floating_rate_id').blur(function(){
            var bunga_floating = $(this).val();
            bunga_floating = bunga_floating.replace(/,/g, '');
            bunga_floating = parseFloat(bunga_floating);

            if( isNaN(bunga_floating) ){
                bunga_floating = 0;
            }

            $(this).val($.formatNumber( bunga_floating, 2 ));
        });
        /*
        ** END KPR
        */

        $('.down-payment-id').off('blur');
        $('.down-payment-id').blur(function(){
            var self = $(this);
            var parent = self.parents('.calculator-kpr-credit');
            var value = self.val();

            var price = parent.find('.KPR-price').val();
            var obj_persen_loan = parent.find('.persen-loan-id');

            value = value.replace(/,/g, '');
            value = parseInt(value);

            price = price.replace(/,/g, '');
            price = parseInt(price);
        
            var tooltip = obj_persen_loan.closest('div.form-group').find('a.tooltip-note');

            if( isNaN(value) ) {
                value = 0;
            }

            if( isNaN(price) ) {
                price = 0;
            }

            if(value < 0){
                alert('uang muka tidak boleh angka minus');

                if( price != 0 ) {
                    uang_muka = 20;
                } else {
                    uang_muka = 0;
                }
                obj_persen_loan.val($.formatNumber( uang_muka, 0 ));             
                self.val(0);               
            }else{
                update_down_payment(parent, 'find_percent');
            }

            if( tooltip.length ) {
                var value_persen_loan = obj_persen_loan.val();
                tooltip.attr('data-original-title', 'Uang muka diambil dari '+value_persen_loan+'% harga properti yang Anda inginkan.');
            }
        });

        SameAddress();

        $('.generate-calculator-kpr').click(function(){
            var self = $(this);
            var parent = self.parents('.calculator-kpr-credit');
            var credit_total = parent.find('.credit_total').val();
            var persen_loan = parent.find('.persen-loan-id').val();
            var interest_rate = parent.find('.interest_rate').val();
            var url = self.attr('href') + '/dp:' + persen_loan + '/' + credit_total + '/' + interest_rate + '/';

            window.open(url, '_blank');
        });

        if($('.kpr-dp-nominal, .kpr-dp-percent').length){
            $(document).undelegate('.kpr-dp-nominal', 'blur');
            $(document).delegate('.kpr-dp-nominal', 'blur', function(){
                var self = $(this);

                var target_change   = self.attr('data-target');
                var rate            = self.attr('data-rate');
                
                var parent_target   = self.parents('.mortgageBank');
                var setting_id      = parent_target.attr('data-setting');
                var credit_total    = parent_target.find('.credit-total-bank').val();
                var sold_price      = $('.kpr-property-sold-price').val();
                sold_price          = $.numberToString(sold_price);
                
                target_change       = parent_target.find(target_change);
                

                var val_target = self.val();                
                val_target = $.numberToString(val_target);

                var loan_price = count_loan_price(sold_price, val_target);
                var pinjaman_target = 100-((loan_price/sold_price)*100);
                pinjaman_target = parseFloat(Math.round(pinjaman_target * 100) / 100).toFixed(2);

                parent_target.find(target_change).val(pinjaman_target);

                var objTarget = $('.font-table-bank-list[data-setting="'+setting_id+'"]');

                updateKprRowList(self, objTarget, loan_price, rate, credit_total, val_target);

                val_target = $.formatNumber(val_target);
                self.val(val_target);
            });

            $(document).undelegate('.kpr-dp-percent', 'blur');
            $(document).delegate('.kpr-dp-percent', 'blur', function(){
                var self = $(this);

                var target_change   = self.attr('data-target');
                var rate            = self.attr('data-rate');
                
                var parent_target   = self.parents('.mortgageBank');
                var setting_id      = parent_target.attr('data-setting');
                var credit_total    = parent_target.find('.credit-total-bank').val();
                var sold_price      = $('.kpr-property-sold-price').val();
                sold_price          = $.numberToString(sold_price);
                
                target_change       = parent_target.find(target_change);
                
                var val_target = self.val();                
                val_target = $.numberToString(val_target);

                var loan_price = sold_price - (sold_price*(val_target/100));
                var pinjaman_target = sold_price*(val_target/100);
               
                parent_target.find(target_change).val($.formatNumber(pinjaman_target));

                var objTarget = $('.font-table-bank-list[data-setting="'+setting_id+'"]');

                updateKprRowList(self, objTarget, loan_price, rate, credit_total, pinjaman_target);
            });

            $(document).undelegate('.credit-total-bank', 'change');
            $(document).delegate('.credit-total-bank', 'change', function(){
                var self = $(this);

                var sold_price      = $('.kpr-property-sold-price').val();

                var rate            = self.attr('data-rate');
                var parent_target   = self.parents('.mortgageBank');
                var dpnominal       = parent_target.find('.kpr-dp-nominal').val();
                var setting_id      = parent_target.attr('data-setting');
                var credit_total    = self.val();
                
                dpnominal           = $.numberToString(dpnominal);
                sold_price          = $.numberToString(sold_price);

                var loan_price = count_loan_price(sold_price, dpnominal);

                var objTarget = $('.font-table-bank-list[data-setting="'+setting_id+'"]');

                updateKprRowList(self, objTarget, loan_price, rate, credit_total);
            });
        }

        function updateKprRowList(self, objTarget, loan_price, rate, credit_total, pinjaman_target){
            var credit_fix = creditFix(loan_price, rate, credit_total);

            var total_pinjaman  = 'Rp. '+$.formatNumber(loan_price);
            var first_credit    = 'Rp. '+$.formatNumber(credit_fix);

            if( $('.first-payment-field').length > 0 ) {
                var oldInstallment = $.convertNumber(objTarget.find('.first-credit-field').html());
                var oldDP = $.convertNumber(objTarget.find('.dp-kpr-field').html());
                var oldFirstPayment = $.convertNumber(objTarget.find('.first-payment-field').html());

                oldFirstPayment = oldFirstPayment - oldInstallment - oldDP;
                oldFirstPayment = oldFirstPayment + pinjaman_target + credit_fix;
                objTarget.find('.first-payment-field').text('Rp. '+$.formatNumber(oldFirstPayment));
            }

            objTarget.find('.total-pinjaman-field').text(total_pinjaman);
            objTarget.find('.first-credit-field').text(first_credit);

            if(typeof pinjaman_target != 'undefined'){
                objTarget.find('.dp-kpr-field').text('Rp. '+$.formatNumber(pinjaman_target));
            }

            var data_trigger = $.convertNumber(self.attr('data-trigger'), null);
            var data_trigger_type = $.convertNumber(self.attr('data-trigger-type'), 'click');

            if( data_trigger != null ) {
               $(data_trigger).trigger(data_trigger_type);
            }
        }

        function creditFix(amount, rate, year=20){
            if( rate == 0 ) {
                return 0;
            } else {
                rate = (rate/100)/12;

                var rateYear = Math.pow((1+rate), (year*12));
                var rateMin = (Math.pow((1+rate), (year*12))-1);

                if( rateMin != 0 ) {
                    rateYear = rateYear / rateMin;
                }

                var mortgage = rateYear * amount * rate; // rumus angsuran fix baru 

                return mortgage;
            }
        }
    }

    $('.multiple-options').click(function(){
        var self = $(this);
        var dataTarget = self.attr('data-target');
        var dataDefault = self.attr('data-default');
        var objDefault = $(dataDefault + ' :selected');

        objDefault.each(function(i, selected){ 
            var objSelect = $(selected);

            $(dataTarget)
            .append($("<option></option>")
            .attr("value", objSelect.val())
            .text(objSelect.text()));

            objSelect.remove();
        });

        return false;
    });

    $('.btn-multiple-select').click(function(){
        var self = $(this);
        var dataTarget = self.attr('data-target');

        $(dataTarget + ' option').attr('selected', 'selected');

        return true;
    });

    $.tag_input = function( options ) {
        var settings = $.extend({
            objTarget: $('.tag_input'),
        }, options );

        var url = settings.objTarget.attr('data-url');

        function extractor(query) {
            var result = /([^,]+)$/.exec(query);
            if(result && result[1])
                return result[1].trim();
            return '';
        }
        
        if(settings.objTarget.length > 0){
            settings.objTarget.typeahead({
                source: function (query, process) {
                    return $.ajax({
                        url: url,
                        type     : 'POST',
                        data: {query: query},
                        loadingClass: "loading-circle",
                        dataType: 'json',
                        success: function(json) {
                            return process(json);
                        }
                    });
                },
                updater: function(item) {
                    return this.$element.val().replace(/[^,]*$/,'')+item+',';
                },
                matcher: function (item) {
                  var tquery = extractor(this.query);
                  if(!tquery) return false;
                  return ~item.toLowerCase().indexOf(tquery.toLowerCase())
                },
                highlighter: function (item) {
                  
                  var query = extractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
                  return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
                    return '<strong>' + match + '</strong>'
                  })
                }
            });
        }
    }

    $.triggerDisabled = function( options ) {
        var settings = $.extend({
            obj: $('.trigger-disabled .rd-line .cb-checkmark'),
        }, options );

        if(settings.obj.length > 0){
            settings.obj.off('click');
            settings.obj.click(function() {
                var self = $(this);
                var radio = self.children('input[type="radio"]');
                var target = radio.attr('data-target');
                var value = radio.attr('data-value');
                var autocomplete = radio.attr('data-autocomplete');

                if( $(target).find('input[type="text"]').length > 0 ) {
                    $(target).find('input[type="text"]').val('');
                    $(autocomplete).val('');

                    if( radio.val() == value ) {
                        $(target).find('input[type="text"]').attr('readonly', true);
                        $(target).find('.select select').prop('disabled', true);
                        $(target).find('.input-group').removeClass('disable').addClass('disable');

                        $.Autocomplete({
                            obj: $(autocomplete),
                        });
                    } else {
                        $(target).find('input[type="text"]').attr('readonly', false);
                        $(target).find('.select select').removeAttr('disabled');
                        $(target).find('.input-group').removeClass('disable');

                        $(autocomplete).typeahead('destroy');
                    }
                }
            });
        }
    }

    function get_data_client_ebrosur(val){
        var url = '/ajax/get_ebrosur_data_agent/email:'+val+'/';

        $.ajax({
            url: url,
            type: 'POST',
            success: function(result) {
                var photo = $(result).filter('#agent-photo-ebrosur').html();
                var url_photo = $(result).filter('#agent-url-photo-ebrosur').html();
                var name = $(result).filter('#agent-name-ebrosur').html();
                var phone = $(result).filter('#agent-phone-ebrosur').html();

                $('#agent-photo-ebrosur').html(photo);
                $('#agent-id-photo-ebrosur').val(url_photo);
                $('#agent-name-ebrosur').html(name);
                $('#agent-phone-ebrosur').html(phone);                
                $('#agent-id-name-ebrosur').val(name);
                $('#agent-id-phone-ebrosur').val(phone);                
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    $.EbrosurListAgent = function(trigger){
        $('#handle-agent-ebrosur, .handle-agent-ebrosur').off('change');
        $('#handle-agent-ebrosur, .handle-agent-ebrosur').change(function(){
            get_data_client_ebrosur($(this).val());
        });

        if(typeof trigger != 'undefined' && trigger != null && $('#handle-agent-ebrosur, .handle-agent-ebrosur').length > 0){
            $('#handle-agent-ebrosur, .handle-agent-ebrosur').trigger('change');
        }
    }

    $.load_more_paginate = function( options ) {
        var settings = $.extend({
            obj: $('.load-more-ul'),
            nextSelector: 'span.next-load a',
            contentSelector: 'div.infinite-loop',
            loading: '<div class="tacenter">Sedang memuat...</div>'
        }, options );

        if(settings.obj.length > 0){
            settings.obj.jscroll({
                loadingHtml: settings.loading,
                padding: 20,
                nextSelector: settings.nextSelector,
                contentSelector: settings.contentSelector,
                // callback: $.checkAll
            });
        }
    }

    $.toggle_display = function( options ) {
        $(document).delegate( '.toggle-display', "init click", function(e) {
            e.preventDefault();
            var self = $(this);
            var divShow = self.attr('data-display');
            var type = self.attr('data-type');
            var arrow = $.checkUndefined(self.attr('data-arrow'), false);
            var label = $.checkUndefined(self.attr('label-detail'), false);

            if( type == 'slide' ) {
                if( arrow == 'true' ) {
                    var objArrow = self.find('i').removeAttr('class');
                    var visible = $(divShow).is(':visible');

                    if (visible) {
                        if(label){
                            self.html('Buka Detail <i class="rv4-angle-down"></i>');
                        }
                        objArrow.addClass('rv4-angle-down');
                        
                    } else {
                        if(label){
                            self.html('Tutup Detail <i class="rv4-angle-up"></i>');
                        }
                        objArrow.addClass('rv4-angle-up');
                    }
                }
                
                $(divShow).slideToggle();
            } else {
                $(divShow).toggle();
            }
        });
    }

    $.sameHeightItem = function(obj){
        var data_type = obj.attr('data-type');
        var label_layer = obj.find('.label-layer');
        var wrapper_layer = obj.find('.wrapper-layer');
        var absolute_botton = obj.find('.absolute-botton');
        var elementHeights = wrapper_layer.map(function() {
            return $(this).height();
        }).get();

        var maxHeight = Math.max.apply(null, elementHeights);

        if( data_type == 'fix-height' ) {
            wrapper_layer.css('height', maxHeight);
        } else {
            wrapper_layer.css('min-height', maxHeight);
        }
        
        //  console.log(maxHeight);

        if( absolute_botton.length > 0 ) {
            absolute_botton.css({'position': 'absolute', 'bottom': 0, 'width': '100%'});
        }
    }

    $.sameHeight = function( options ) {
        var settings = $.extend({
            obj: $('.same-height'),
        }, options );

        if(settings.obj.length > 0){
            settings.obj.each(function(index, object){
                var self = $(this);
                
                $.sameHeightItem(self);
            });
        }
    }

    $.alert_close = function(){
        $('.alert.close').click(function(){
            var self = $(this);
            var parent = self.parent('.alert');

            parent.remove();

            return false;
        });
    }

    $.scrollingTo = function(){
        $('.scrollto').off('click');
        $('.scrollto').click(function(){
            var self = $(this);
            var data_scroll = self.attr('href');
            var data_remove = self.attr('data-remove');
            var top = $.checkUndefined(self.attr('data-top'), 10);
            var theOffset = $(data_scroll).offset();

            $('html, body').animate({
                scrollTop: theOffset.top - top
            }, 1000);
            $(data_remove).remove();

            if( self.hasClass('ajax-link') ) {
                $.directAjaxLink({
                    obj: self,
                });
            }
        });
    }

    $.popupWindow = function (options) {
        var settings = $.extend({
            obj: $('.popup-window'),
        }, options );
        
        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(){
                var self = $(this);
                var type = $.checkUndefined(self.attr('data-type'), 'popup');
                var width = $.checkUndefined(self.attr('data-width'), 700);
                var height = $.checkUndefined(self.attr('data-height'), 500);
                var url = $.checkUndefined(self.attr('href'), false);
                var title = $.checkUndefined(self.attr('title'), '');
                var ajax_url = $.checkUndefined(self.attr('data-url'), false);

                var top = ($(window).height() / 2) - (height / 2);
                var left = ($(window).width() / 2) - (width / 2);

                if( url != false ) {
                    if( type == 'redirect' ) {
                        window.location.href = url;
                    } else {
                        window.open(url, title, 'left='+left+',top='+top+',height='+height+',width='+width);
                    }
                }

                if( ajax_url != false ) {
                    if( loadJXHR ){
                        loadJXHR.abort();
                    }

                    loadJXHR = $.ajax({
                        url: ajax_url,
                        type: 'POST',
                        success: function(result) {
                            console.log(result);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                            return false;
                        }
                    });
                }

                return false;
            });
        }
    }

    $.rebuild_toggle_form = function(options){
        var settings = $.extend({
            obj: $('.toggle-input'),
        }, options );

        if(settings.obj.length > 0){
            settings.obj.bootstrapToggle()
        }
    }
    
    $.callChoosen = function(options){
        var settings = $.extend({
            obj: $('.chosen-select'),
            init: false,
        }, options );

        if( settings.obj.length > 0 ) {
            if( settings.init == false ) {
                settings.obj.select2();
            } else {
                settings.obj.select2(settings.init);
            }
        }
    }
    
    $.callInterval = function(options){
        var settings = $.extend({
            obj: $('.call-interval'),
            init: false,
        }, options );

        if( settings.obj.length > 0 ) {
            var self = settings.obj;
            var interval = $.checkUndefined(self.attr('data-interval'), 15000);
            var trigger = $.checkUndefined($('.trigger-interval').val(), 'true');

            setInterval(function(){
                if( trigger == 'true' ) {
                     $.directAjaxLink({
                        obj: self,
                    });
                 }
            }, interval);
        }
    }
    
    $.callResetFilter = function(options){
        var settings = $.extend({
            obj: $('.form-reset-filter'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(){
                var objInput = settings.obj.parents('tr').find('.form-control');
                objInput.val('');
                objInput.trigger('keyup');
            });
        }
    }

    $.loadingbar_progress = function( options, is_loadingbar ){
        is_loadingbar = $.checkUndefined(is_loadingbar, false);

        if( is_loadingbar === true || is_loadingbar == 'true' ) {
            switch (options) { 
                case 'beforeSend':
                    $("body").append("<div id='loadingbar'></div>");
                    $("#loadingbar").addClass("waiting").append($("<dt/><dd/>"));

                    $("#loadingbar").width((50 + Math.random() * 30) + "%");
                break;
                case 'always':
                     $("#loadingbar").width("101%").delay(200).fadeOut(400, function() {
                         $(this).remove();
                     });
                break;
            }
        }
    };

    $.callShowHideColumn = function(options){
        var settings = $.extend({
            obj: $('.columnDropdown input[type="checkbox"]'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click').click(function(){
                var self = $(this);
                var data_url = $.checkUndefined(self.attr('data-url'), false);
                var data_form = $.checkUndefined(self.attr('data-form'), false);

                if( self.attr('rel') == 'all' ) {
                    settings.obj.prop('checked', self.prop('checked'));
                }

                var activeColumns = settings.obj.filter(function(){
                    return $(this).prop('checked') && $(this).attr('rel') != 'all';
                });

                if(activeColumns.length == 0 && $('.colview-default').length){
                    activeColumns = $('.colview-default').val();
                    activeColumns = $(activeColumns);

                    if(activeColumns.length){
                        activeColumns.prop('checked', true);
                    }
                }

                var inActiveColumns = settings.obj.filter(function(){
                    return !$(this).prop('checked') && $(this).attr('rel') != 'all';
                });

            //  checkall otomatis nyala kalo semua siblings di centang
                var isCheckAll = activeColumns.length >= (activeColumns.length + inActiveColumns.length);

                $('.columnDropdown .cb-checkmark input[rel="all"]').prop('checked', isCheckAll);

                activeColumns.map(function(){ 
                    $('table .' + $(this).attr('rel')).show();
                });

                inActiveColumns.map(function(){
                    $('table .' + $(this).attr('rel')).hide();
                });

                if( data_url != false ) {
                    var formData = false;

                    if( data_form != false ) {
                        formData = $(data_form).serialize(); 
                    }

                    if( loadJXHR ){
                        loadJXHR.abort();
                    }

                    loadJXHR = $.ajax({
                        url: data_url,
                        data: formData,
                        type: 'POST',
                        success: function(result) {
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                            return false;
                        }
                    });
                }
            });

            var activeColumns = settings.obj.filter(function(){
                return $(this).prop('checked') && $(this).attr('rel') != 'all';
            });
            var inActiveColumns = settings.obj.filter(function(){
                return !$(this).prop('checked') && $(this).attr('rel') != 'all';
            });
            var isCheckAll = activeColumns.length >= (activeColumns.length + inActiveColumns.length);

            $('.columnDropdown .cb-checkmark input[rel="all"]').prop('checked', isCheckAll);
        }
    }

    $.badword_filter = function(){
        var badwords = /\b(ass |asshole|bitch|bastard|cunt|dick|dike|dildo|fuck|gay|hoe|nigger|pussy|slut|whore|god damn|goddamn)\b/gi;
        var second_badword = /\b(dijual|jual|disewakan|disewa|sewa|dikontrakan|kontrakan|kontrak| di |murahan|murah|harga)\b/gi;

        $('input[type="text"][data-role="word-filter"], textarea[data-role="word-filter"]').keyup(function (event){
            var charCode = (event.which) ? event.which : event.keyCode;
            var bool_detect = false;

            if(charCode <= 40 && charCode >= 37){
                event.preventDefault();
            }else{
                var filter = $(this).val().replace(badwords, function (fullmatch, badword) {
                    alert('Dilarang menggunakan kata '+fullmatch);

                    bool_detect = true;

                    return '';
                });

                if(bool_detect){
                    $(this).val(filter);
                }
            }
        });

        $('input.title-property-filter').keyup(function (event){
            var charCode = (event.which) ? event.which : event.keyCode;
            var bool_detect = false;

            if(charCode <= 40 && charCode >= 37){
                event.preventDefault();
            }else{
                var filter = $(this).val().replace(badwords, function (fullmatch, badword) {
                    alert('Dilarang menggunakan kata '+fullmatch);

                    bool_detect = true;

                    return '';
                });

                if(bool_detect){
                    $(this).val(filter);
                }
            }
        });
    }

    $.dropdownFix = function(options){
        var settings = $.extend({
            obj: $('.columnDropdown'),
        }, options );

        settings.obj.on({
            "shown.bs.dropdown": function() { this.closable = false; },
            "click":             function(e) { 
                var target = $(e.target);

                if( target.parents('ul.dropdown-menu').length == 0 ) 
                    this.closable = true;
                else 
                    this.closable = false; 
            },
            "hide.bs.dropdown":  function() { return this.closable; }
        });
    }

    $.jobCompany = function(val, mandatory){
        var change = false;

        if(val == 1){
           change = 'Perusahaan/Usaha'; 
        
        }else if(val == 2){
            change = 'Departemen'; 

        }else if(val == 3){
            change = 'Perusahaan';
            
        }else if(val == 4){
            change = 'Bidang Usaha'; 
            
        }

        return change;
    }

    $.changeLabel = function(self){
        var change = false;
        var val = self.val();
        var target = self.data('target');
        var category = self.data('category');
        var mandatory = self.data('mandatory');
        var placeholder = self.data('placeholder');
        category = $.checkUndefined(category, false);
        placeholder = $.checkUndefined(placeholder, false);
        mandatory = $.checkUndefined(mandatory, '*');

        var data_placehoder = $(placeholder).data('placeholder');

        if(category != false){
            switch(category){
                case 'job_company' :
                        changed = change = $.jobCompany(val, mandatory);
                    break;
            }
        }

        if(change != false){

            if(mandatory && (val != 4)){
                change = change+' <span class="color-red">'+mandatory+'</span>';
            }

            $( target ).html( change );
            
            if(placeholder){
                $( placeholder ).attr( 'placeholder', changed+' '+data_placehoder );
            }
        }
    }

    $.handle_toggle = function(options){
        var settings = $.extend({
            obj: '.handle-toggle',
            objToggleClick: '.handle-toggle-click',
            objToggleContent: '.handle-toggle-content',
            objDisplayToggle: '.display-toggle', 
        }, options );

        function _callHandleToggle ( self, value, event ) {
            var match = $.checkUndefined(self.attr('data-match'));
            var resetTarget = $.checkUndefined(self.data('reset-target'), true);
            var targetDisabled = $.checkUndefined(self.data('target-disabled'), null);
            var result = false;
            match = eval(match);

            if($.isArray(match) ) {
                $.each( match, function( i, val ) {
                    target = $.checkUndefined(val[0]);
                    dataMatch = $.checkUndefined(val[1]);
                    type = $.checkUndefined(val[2]);
                    reset = $.checkUndefined(val[3], 'true');

                    if($(target).length){
                        var key = $.inArray( value, dataMatch );

						if(self.is(':checkbox') && value === false){
						//	input checkox will return false as value when unchecked
						//	search if data match contain zero value

							key = $.inArray('0', dataMatch);
						}

                        if( key >= 0 ) {
                            result = true;
                            $(target).removeClass('hide');
                        } else {
                            result = false;
                        }

                        switch (type) { 
                            case 'fade':
                                if( result ) {
                                    $(target).fadeIn().removeClass('hide');
                                } else {
                                    $(target).fadeOut().addClass('hide');
                                }
                            break;
                            case 'slide':
                                if( result ) {
                                    $(target).slideDown().removeClass('hide');
                                } else {
                                    $(target).slideUp().addClass('hide');
                                }
                            break;
                            default : 
                                if( result ) {
                                    $(target).show();
                                } else {
                                    $(target).hide();
                                }
                            break;
                        }

                    //  force clear target value on change event
                        var inputs = $(target).find(':input');

                        if( reset == 'false' ) {
                          resetTarget = false;
                        } else {
                          // resetTarget = $.inArray( event.type, ['change', 'click'] ) && resetTarget === true;
                        }

                        if(inputs.length){
                            if( !result && resetTarget == true){
                                inputs.each(function(index, element){
                                    var type = $(this).attr('type');

                                    if( $.inArray(type, ['checkbox', 'radio']) !== -1 ) {
                                        $(this).prop('checked', false);
                                    } else {
                                        $(this).val('').prop('checked', false);
                                    }
                                });
                            }

                            inputs.prop('disabled', !result);
                            $('[data-match-type="remove"]').remove();

                            if( targetDisabled != null ) {
                                $(targetDisabled).prop('disabled', !result);
                                
                                if( result && resetTarget && inputs.val() == '' && targetDisabled != null ) {
                                    $(targetDisabled).each(function(index, element){
                                        var type = $(this).attr('type');

                                        if( type == 'checkbox' ) {
                                            $(this).prop('checked', false);
                                        } else {
                                            $(this).val('').prop('checked', false);
                                        }
                                    });
                                }
                            }
                        }
                        var innerToggles = $(target).find('.handle-toggle:visible, .handle-toggle-click:visible');
                        if(resetTarget && innerToggles.length){
                            innerToggles.trigger('init');
                        }
                    }
                });
            }
        }

        // $(document).on('init change', settings.obj, function(event){
        //     var self        = $(this);
        //     var inputType   = $.checkUndefined(self.attr('type'));
        //     var value       = self.val();

        //     if($.inArray(inputType, ['radio', 'checkbox']) > -1){
        //         if(!self.is(':checked')){
        //             value = false;
        //         }
        //     }

        //     _callHandleToggle(self, value, event);
        // });

        // $(settings.obj).on('init change', function(event){
        //     var self        = $(this);
        //     var inputType   = $.checkUndefined(self.attr('type'));
        //     var value       = self.val();

        //     if($.inArray(inputType, ['radio', 'checkbox']) > -1){
        //         if(!self.is(':checked')){
        //             value = false;
        //         }
        //     }

        //     _callHandleToggle(self, value, event);
        // });

        $(document).undelegate(settings.obj, 'init change');
        $(document).delegate( settings.obj, "init change", function(event) {
            var self        = $(this);
            var inputType   = $.checkUndefined(self.attr('type'));
            var value       = self.val();

            if($.inArray(inputType, ['radio', 'checkbox']) > -1){
                if(!self.is(':checked')){
                    value = false;
                }
            }

            _callHandleToggle(self, value, event);
        });

        $(document).undelegate(settings.objToggleClick, 'init click');
        $(document).delegate( settings.objToggleClick, "init click", function(event) {
            var self = $(this);
            var input_type = $.checkUndefined(self.attr('type'));
            var value = self.val();

            if( $.inArray(input_type, ['radio', 'checkbox']) !== -1 ) {
                if( !self.is(':checked') ) {
                    value = false;
                }
            }

            _callHandleToggle(self, value, event);
        });

        $(document).undelegate(settings.objDisplayToggle, 'click');
        $(document).delegate(settings.objDisplayToggle, 'click', function(){
            var self = $(this);
            var target = $(self.data('target'));
            var clear = $.checkUndefined(self.data('clear'), false);

            if(target.length){
                var toggleClass = $.checkUndefined(self.data('class'), 'hide');

                if(clear === true){
                    target.attr('class', '');   
                }

                target.toggleClass(toggleClass);
            }
        });

        $(document).undelegate(settings.objToggleContent, 'click');
        $(document).delegate( settings.objToggleContent, "click", function() {
            var self = $(this);
            
            var target = self.attr('data-target');
            var reverse = self.attr('data-reverse');
            var type = self.attr('data-type');
            
            if(target != ''){
                if( self.is(':checked') ) {
                    if(type == 'disabled-input'){
                        if(reverse == 'true'){
                            $(target+' input').attr('disabled', false);
                        }else{
                            $(target+' input').attr('disabled', true);
                        }
                    }else{
                        if(reverse == 'true'){
                            $(target).fadeOut();
                        }else{
                            $(target).fadeIn();
                        }
                    }
                } else {
                    if(type == 'disabled-input'){
                        if(reverse == 'true'){
                            $(target+' input').attr('disabled', true);
                        }else{
                            $(target+' input').attr('disabled', false);
                        }
                    }else{
                        if(reverse == 'true'){
                            $(target).fadeIn();
                        }else{
                            $(target).fadeOut();
                        }
                    }
                }

                if(gmapRku.length > 0){
                    map = gmapRku.gmap3('get');
                    google.maps.event.trigger(map, 'resize');
                }
            }
        });

    //  trigger init event for first page load
        if($(settings.obj).length){
            $(settings.obj).trigger('init');
        }

        if($(settings.objToggleClick+'[type="radio"]:checked').length){
            $(settings.objToggleClick+'[type="radio"]:checked').trigger('init');
        } else if($(settings.objToggleClick).length){
            $(settings.objToggleClick).trigger('init');
        }
    }

//  market trend widget
    $._MT_widgets = function(options){
        var settings = $.extend({
            obj : $('div[data-role="market-trend-widget"]'),
        }, options);

        settings.obj = $(settings.obj);

        if(settings.obj.length){
        //  render widget
            function drawWidget(object, response){
                var object = $(object);

                if(object.length && typeof response == 'object'){
                    var summaries = $.checkUndefined(response.summary, null);

                    if(summaries){
                        var objSummaryItems = object.find('.summary-item');
                        var currency        = $.checkUndefined(summaries.currency, 'Rp. ');
                        var totalCount      = $.checkUndefined(summaries.total_property_count, 0);

                        if(objSummaryItems.length){
                            objSummaryItems.each(function(index, object){
                                var objSummaryItem  = $(object);
                                var typeID          = objSummaryItem.data('typeid');
                                var placeholders    = {
                                    '.data-count'       : 'avg_price_measure', 
                                    '.data-progress'    : 'property_count' , 
                                };

                                $.each(placeholders, function(selector, fieldName){
                                    var objPlaceholder = objSummaryItem.find(selector);

                                    if(objPlaceholder.length){
                                        var actionID        = objPlaceholder.data('actionid');
                                        var detailValues    = $.checkUndefined(summaries[fieldName], null);
                                        var rowIndex        = typeID + '-' + actionID;

                                        var value = $.checkUndefined(detailValues[rowIndex], 0);

                                        if(objPlaceholder.hasClass('data-count')){
                                            var cssClass    = '';
                                            var arrowIcon   = '';

                                            var counts      = $.checkUndefined(summaries['property_count'], null);
                                            var percentages = $.checkUndefined(summaries['avg_price_measure_percentage'], null);

                                            var count       = $.checkUndefined(counts[rowIndex], 0);
                                            var percentage  = $.checkUndefined(percentages[rowIndex], 0);

                                            count       = parseInt(count);
                                            percentage  = parseFloat(percentage);

                                            if(count > 0){
                                                if(percentage != 0){
                                                    cssClass    = percentage > 0 ? 'green' : 'red';
                                                    arrowIcon   = percentage > 0 ? 'up' : 'down';
                                                    arrowIcon   = '<i class="fa fa-angle-' + arrowIcon + '"></i>';
                                                }

                                                var valueDecimal        = value.toString().split('.');
                                                var percentageDecimal   = percentage.toString().split('.');

                                                valueDecimal        = $.checkUndefined(valueDecimal[1], 0);
                                                percentageDecimal   = $.checkUndefined(percentageDecimal[1], 0);

                                                count       = $.formatNumber(count, 0);
                                                value       = currency + $.formatNumber(value, 0);
                                                percentage  = $.formatNumber(percentage, percentageDecimal > 0 ? 2 : 0) + '%';
                                            }
                                            else{
                                                value       = '';
                                                percentage  = 'N/A';
                                            }

                                            value       = count + ' Unit &bull; ' + value;
                                            arrowIcon   = '<span class="' + cssClass + ' text">' + arrowIcon + ' ' + percentage + '</span>';

                                            objPlaceholder.html(value + ' ' + arrowIcon);
                                        }
                                        else{
                                        //  progress bar
                                            value = (100 / parseFloat(totalCount)) * parseFloat(value);

                                            objPlaceholder.removeAttr('style').animate({
                                                width : value + '%', 
                                            }, 1000);
                                        }
                                    }
                                });
                            });
                        }
                        else{
                            console.log('Invalid placeholder');
                            return false;
                        }
                    }
                    else{
                        console.log('Invalid response');
                        return false;
                    }
                }
            }

        //  prepare widget data
            var mtJXHR = [];

            settings.obj.each(function(index, object){
                var self        = $(object);
                var dataSource  = $.checkUndefined(self.attr('data-source'));

                if(typeof dataSource != 'undefined'){
                    if(validURL(dataSource) === false){
                        if(typeof dataSource == 'string' && dataSource.length){
                            try{
                                dataSource = $.parseJSON(dataSource);
                            }
                            catch(e){
                            //  invalid json
                                console.log('error:' + e);
                                return false;
                            };
                        }

                        if(typeof dataSource == 'object'){
                            drawWidget(self, dataSource);
                        }
                        else{
                        //  console.log('Invalid json');
                            return false;
                        }
                    }
                    else{
                        if(mtJXHR[index]){
                            mtJXHR[index].abort();
                        }

                        mtJXHR[index] = $.ajax({
                            url         : dataSource, 
                            method      : 'post', 
                            dataType    : 'json', 
                            success     : function(response){
                                drawWidget(self, response);
                            }, 
                            error       : function(jqXHR, textStatus, errorThrown){
                                alert('Gagal melakukan proses. Silakan coba beberapa saat lagi.');
                                return false;
                            }
                        });
                    }
                }
                else{
                    console.log('Invalid data source for widget ' + index);
                    return false;
                }
            });
        }
    }

    $.inputCounter = function(options){
        var settings = $.extend({
            obj         : $(':input.input-counter'), 
            container   : '',
            text        : '%left dari %length karakter', // %length %maxlength %left
        }, options);


        if(settings.obj.length){
            function updateCounter(obj, container, direction){
                var maxlength   = $(obj).attr('maxlength');
                var value       = $(obj).val();
                var length      = value.length;

                if(container.length){
                    var counter = 0;

                    if(direction == 'increment'){
                        counter = length;
                    }
                    else{
                        counter = maxlength - length;   
                    }

                    container.text(counter);

                    if(maxlength && length > maxlength){
                        $(obj).val(value.substr(0, maxlength));
                    }

                    return (counter <= 0) ? false : true;
                }
            }

            settings.obj.each(function(index, object){
                var self        = $(object);
                var direction   = $.checkUndefined(self.data('direction'), 'decrement');
                var container   = $.checkUndefined(self.data('container'));

                if(!container){
                    container = self.closest('[data-role="input-counter-wrapper"]').find('[data-role="counter-wrapper"]');
                }

                container = $(container);

                if(container.length){
                    self.bind({
                        'keyup change init': function(e){
                            var result = updateCounter(this, container, direction);

                            if(!result){
                                e.stopPropagation();
                            }
                        },
                        'cut paste drop': function(e){
                            setTimeout($.proxy(function(){
                                var result = updateCounter(this, container, direction);

                                if(!result){
                                    e.stopPropagation();
                                }
                            }, this), 1);
                        }
                    }).trigger('init');
                }
            });
        }
    }

    // if( $('.chart-change, .ajax-change').length > 0 ) {
    //     $('.chart-change').off('change').change(function(){
    //         var self = $(this);
    //         var value = self.val();

    //         _callRemoveTips();
    //         _callGenerateChart($('.reload[data-load="infinity"]'), 1, true);

    //         if(value){
    //             $.formatDateChart(self, value);
    //             $._callgoogleVisitor();
    //         }
    //     });

    //     $.formatDateChart = function(self, value){
    //         var date_arr =value.split('-');
    //         var date_from = date_arr[0];
    //         var date_to = date_arr[1];

    //         date_from = $.splitDateChart(date_from);
    //         date_to = $.splitDateChart(date_to);

    //         var result = '['+date_from+','+date_to+']';
    //         // set to html
    //         self.val(value);
    //         self.attr('data-value', result);
    //     }

    //     $.splitDateChart = function(date = null){
    //         date = $.trim(date);
    //         var date_arr = date.split(' ');

    //         var month = date_arr[0];
    //         var year = date_arr[1];

    //         switch(month){
    //             case 'Jan' :
    //                 month = '1';
    //                 break;
    //             case 'Feb' :
    //                 month = '2';
    //                 break;
    //             case 'Mar' :
    //                 month = '3';
    //                 break;
    //             case 'Apr' :
    //                 month = '4';
    //                 break;
    //             case 'May' :
    //                 month = '5';
    //                 break;
    //             case 'Jun' :
    //                 month = '6';
    //                 break;
    //             case 'Jul' :
    //                 month = '7';
    //                 break;
    //             case 'Aug' :
    //                 month = '8';
    //                 break;
    //             case 'Sep' :
    //                 month = '9';
    //                 break;
    //             case 'Oct' :
    //                 month = '10';
    //                 break;
    //             case 'Nov' :
    //                 month = '11';
    //                 break;
    //             case 'Dec' :
    //                 month = '12';
    //                 break;
    //         }

    //         return '['+month+','+year+']';
    //     }
    // }




//  EXPORT REPORT PDF
    $.screenShot = function(options){
        var settings = $.extend(true, {
            obj         : $('[role="capture-button"]'), 
            title       : document.title, 
            export      : true, 
            onInit      : null, 
            onComplete  : null, 
            exportOpts  : {
                logging         : false, 
                backgroundColor : null, 
                imageTimeout    : 60000, 
            //  width           : objTarget.width() + 40,
            //  height          : objTarget.height() + 40,
            }
        }, options);

        settings.obj = $(settings.obj);

        if(settings.obj.length){
        //  trigger init event
            settings.obj.prop('disabled', false).trigger('screenshot:init');

            if(typeof settings.onInit == 'function'){
                settings.onInit(settings.obj, settings);
            }

            settings.obj.off('click').on('click', function(event){
                var ajaxState   = $('body').attr('data-ajax');
                var self        = $(this);
                var buttonState = self.attr('data-state');

                if($.inArray(ajaxState, ['ready', 'completed']) > -1 && buttonState != 'processing'){
                //  can be processed after all ajax request completed
                    event.preventDefault();

                    var caption         = self.text();
                    var documentTitle   = self.data('title');
                    var targetSelector  = self.data('target');
                    var objTarget       = $(targetSelector);

                    var documentPeriod  = $(':input[data-role="report-period-input"]').val();
                //  loading
                    self.text('Mohon tunggu...').attr('data-state', 'processing');

                    if(objTarget.length){
                        var wrapperID   = 'screenshot-wrapper';
                        var objWrapper  = $('#' + wrapperID);

                        if(objWrapper.length <= 0){
                            var protocol    = window.location.protocol;
                            var host        = window.location.host;
                            var baseURL     = protocol + '//' + host;
                            var targetURL   = baseURL + '/backprocess/reports/export';

                            objWrapper = $('<form></form>').attr({
                                'id'        : wrapperID, 
                                'action'    : targetURL, 
                                'method'    : 'post', 
                            });
                        }

                        objWrapper.html('');

                        var pages       = objTarget.find('[data-page],[data-page="*"]');
                        var pageIndex   = [];

                        if(pages.length <= 0){
                            pages = objTarget;

                            pageIndex.push(0);
                        }
                        else{
                            pages.each(function(index, page){
                                pageIndex.push($(this).data('page'));
                            });

                            pageIndex = $.unique(pageIndex.sort());
                        }

                        $.each(pageIndex, function(index, pageNumber){
                        //  hanya bisa 1 item, dan ga boleh pisah wrapper
                            var page = objTarget.find('[data-page="' + pageNumber + '"]');

                            if(page.length < 1 && pageIndex.length == 1){
                                page = objTarget;
                            }

                            if(page.length){
                                var usingTemp = false;

                                if(page.length > 1){
                                    usingTemp = true;

                                    $('body').append($('<div></div>', {
                                        'id' : 'screenshot-temp-wrapper', 
                                    }).html(page.clone()));

                                    page = $('#screenshot-temp-wrapper');
                                }

                                if(index == 0){
                                    if(typeof documentTitle != 'undefined'){
                                        var inputID     = 'ExportTitle';
                                        var inputName   = 'data[Export][title]';

                                        objWrapper.append($('<input />', {
                                            'id'    : inputID, 
                                            'name'  : inputName, 
                                            'value' : documentTitle, 
                                            'type'  : 'hidden', 
                                        }));
                                    }

                                    if(typeof documentPeriod != 'undefined'){
                                        var inputID     = 'ExportPeriod';
                                        var inputName   = 'data[Export][period]';

                                        objWrapper.append($('<input />', {
                                            'id'    : inputID, 
                                            'name'  : inputName, 
                                            'value' : documentPeriod, 
                                            'type'  : 'hidden', 
                                        }));
                                    }
                                }

                                var inputID     = 'ExportPage' + index;
                                var inputName   = 'data[Export][page][' + index + ']';

                                var paddingLeft     = page.css('padding-left');
                                var paddingRight    = page.css('padding-right');

                                page.css({
                                    'padding-left'  : '50px', 
                                    'padding-right' : '60px',
                                });

                                $('body').find('.report-wrapper').css('overflow', 'hidden');

                                html2canvas(page.get(0), settings.exportOpts).then(canvas => {
                                    var base64 = canvas.toDataURL();

                                    objWrapper.append($('<input />', {
                                        'id'    : inputID, 
                                        'name'  : inputName, 
                                        'value' : base64, 
                                        'type'  : 'hidden', 
                                    }));

                                    if(usingTemp){
                                        $('#screenshot-temp-wrapper').remove();
                                    }

                                    if(settings.export === true && index == pageIndex.length - 1){
                                    //  reset caption
                                    //  self.text(caption);
                                        self.text('Export').removeAttr('data-state');

                                    //  process export
                                        objWrapper.submit();
                                    }

                                    $('body').find('.report-wrapper').css('overflow', 'initial');
                                    page.css({
                                        'padding-left'  : paddingLeft, 
                                        'padding-right' : paddingRight, 
                                    });
                                });
                            }
                        });

                        objTarget.after(objWrapper);
                    }
                    else{
                    //  reset caption
                        self.text(caption);
                    }
                }
                else{
                    console.log('still processing some ajax request');
                }
            });

            return settings.obj;
        }
        else{
            return false;
        }
    }

    $(document).ajaxStart(function(){
        $('body').attr('data-ajax', 'processing');

        if($('[role="capture-button"]').length){
            $('[role="capture-button"]').addClass('disabled');
        }
    }).ajaxStop(function(){
        $('body').attr('data-ajax', 'completed');

        if($('[role="capture-button"]').length){
            $('[role="capture-button"]').removeClass('disabled');
        }
    });

    /* report */
        $.tooltipBar = function( options ){
        var settings = $.extend({
            obj: $('[data-toggle="tooltip"]'),
            page: 1,
        }, options );

        settings.obj.tooltip({
            'html': true,
        });
    }

    if( $('[data-load="infinity"]').length > 0 ) {
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(function () {
        });
    }

	function gradientChart(objChartContainer, objChart){
	//	if(typeof objChart == 'object' && typeof google == 'object'){
	//		google.visualization.events.addListener(objChart, 'ready', function(){
	//			var observer = new MutationObserver(function(){
	//				objChartContainer.getElementsByTagName('svg')[0].setAttribute('xmlns', 'http://www.w3.org/2000/svg');

	//				Array.prototype.forEach.call(objChartContainer.getElementsByTagName('rect'), function(rect){
	//				//	if(rect.getAttribute('fill') === '#447799'){
	//						rect.setAttribute('fill', 'url(#my-gradient) #447799');
	//				//	}
	//				});
	//			});

	//			observer.observe(objChartContainer, {
	//				childList	: true,
	//				subtree		: true, 
	//			});
	//		});
	//	}
	}

    function _callChartLine ( self, result, rel, type, options ) {
        var type = $.checkUndefined(type, 'line');

        var width = $.checkUndefined(options.resolution.width, false);
        var height = $.checkUndefined(options.resolution.height, false);
        var attributes = $.checkUndefined(options.attributes, []);

        var chartContainer = document.getElementById(rel);
        var chart;

        switch(type) {
            case 'histogram':
                result = JSON.parse(result);

                var salesData = new google.visualization.arrayToDataTable(result.rows);

                var chartOptions = {
                    title: $.checkUndefined(options.title, 'Lengths of dinosaurs, in meters'),
                    legend: {
                        position: 'top',
                        maxLines: 2
                    },
                    hAxis: {
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                    },
                    vAxis: {
                        baselineColor: '#e6e6e6',
                        gridlines: {
                            color: '#e6e6e6',
                            count: 9
                        },
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                        minValue: 0,
                        maxValue: 5,
                        // ticks: [0,3,6,9,12,15,18,21,24,27,30]
                    },
                    color: ['#5C3292', '#1A8763', '#871B47', '#999999'],
                    interpolateNulls: false,
                };

               	chart = new google.visualization.Histogram(chartContainer);

				gradientChart(chartContainer, chart);

                chart.draw(salesData, chartOptions);
                break;
            case 'bar':
                var salesData = new google.visualization.DataTable(result);
                var chartOptions = {
                    width:'100%',
                    focusTarget: 'category',
                    backgroundColor: 'transparent',
                    colors: ['#4593e2', '#31dd95', '#fbd481', '#f9a9c5', '#9F509E', '#F2713E'],
                    chartArea: {
                        left: 20,
                        top: 10,
                        width: '100%',
                        height: '70%'
                    },
                    bar: {
                        groupWidth: '70%'
                    },
                    hAxis: {
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        }
                    },
                    vAxis: {
                        baselineColor: '#e6e6e6',
                        gridlines: {
                            color: '#e6e6e6',
                            count: 9
                        },
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                        minValue: 0,
                        maxValue: 5,
                        // ticks: [0,3,6,9,12,15,18,21,24,27,30]
                    },
                    legend: {
                        position: 'bottom',
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        }
                    },
                    animation: {
                        duration: 300,
                        easing: 'out',
                        startup: true
                    },
                };

                chart = new google.visualization.ColumnChart(chartContainer);

				gradientChart(chartContainer, chart);

				chart.draw(salesData, $.extend(chartOptions, attributes));
            break;
            case 'bars':
                result = JSON.parse(result);
                var salesData = new google.visualization.arrayToDataTable(result.rows);
                
                var chartOptions = {
                    width:'100%',
                    focusTarget: 'category',
                    backgroundColor: 'transparent',
                    colors: ['#4593e2', '#31dd95', '#fbd481', '#f9a9c5', '#9F509E', '#F2713E'],
                    chartArea: {
                        left: 20,
                        top: 10,
                        width: '100%',
                        height: '70%'
                    },
                    bar: {
                        groupWidth: '70%'
                    },
                    hAxis: {
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                    },
                    vAxis: {
                        baselineColor: '#e6e6e6',
                        gridlines: {
                            color: '#e6e6e6',
                            count: 9
                        },
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                        minValue: 0,
                        maxValue: 5,
                        // ticks: [0,3,6,9,12,15,18,21,24,27,30]
                    },
                    legend: {
                        position: 'bottom',
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        }
                    },
                    animation: {
                        duration: 300,
                        easing: 'out',
                        startup: true
                    }
                };
                
                chart = new google.charts.Bar(chartContainer);

				gradientChart(chartContainer, chart);

				chart.draw(salesData, google.charts.Bar.convertOptions( $.extend(chartOptions, attributes) ));
            break;
            case 'line':
                var salesData = new google.visualization.DataTable(result);
                var chartOptions = {
                    backgroundColor: 'transparent',
                    colors: ['#4593e2', '#31dd95', '#fbd481', '#f9a9c5', '#9F509E', '#F2713E'],
                    areaOpacity:'0.1',
                    pointSize: 3,
                    pointShape: {
                        type: 'circle'
                    },
                    chartArea: {
                        left: 30,
                        top: 10,
                        right: 30,
                        width: '100%',
                        height: '70%'
                    },
                    bar: {
                        groupWidth: '70%'
                    },
                    hAxis: {
                        textStyle: {
                            fontSize: 10,
                            color:'#929496'
                        }
                    },
                    vAxis: {
                        baselineColor: '#e6e6e6',
                        gridlines: {
                            color: '#e6e6e6',
                            count: 4
                        },
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        },
                        minValue: 0,
                        maxValue: 5,
                        // ticks: [0,5,10,15,20]
                    },
                    legend: {
                        position: 'bottom',
                        textStyle: {
                            fontSize: 9,
                            color:'#929496'
                        }
                    },
                    animation: {
                        duration: 300,
                        easing: 'out',
                        startup: true
                    },
                    tooltip: {
                        isHtml: true
                    },
                };

				chart = new google.visualization.AreaChart(chartContainer);
                
				gradientChart(chartContainer, chart);

				chart.draw(salesData, $.extend(chartOptions, attributes) );
            break;
            case 'pie':
                var parseResult = jQuery.parseJSON( result );
                parseResult = $.checkUndefined( parseResult['rows'], [] );

                var salesData = google.visualization.arrayToDataTable(parseResult);
                var pickColor = ['#4593e2', '#31dd95', '#fbd481', '#f9a9c5', '#9F509E', '#F2713E'];
                var chartOptions = {
                    backgroundColor: {
                        strokeWidth: 0,
                    },
                    colors: pickColor,
                    pieStartAngle: 135,
                    chartArea: {
                        left: 0,
                        top: 0,
                        width: width,
                        height: height
                    },
                    pieHole: 0.8,
                    legend: 'none',
                    pieSliceText: 'none',
                };

               	chart = new google.visualization.PieChart(chartContainer);
                
				gradientChart(chartContainer, chart);

				chart.draw(salesData, $.extend(chartOptions, attributes) );
            break;
        }
        
        // $('.dashboard-chart-loading').remove();

        if( self.find('.chart-legend-color').length > 0 ) {
            self.find('.chart-legend-color').each(function(i, obj){
                var thisColor = $(this);
                var get_color = $.checkUndefined(pickColor[i], pickColor[0]);

                thisColor.css('background-color', get_color);
            });
        }
    }

    $.infinityLoad = function( options ) {
        var settings = $.extend({
            obj: $('[data-load="infinity"]'),
            page: 1,
            reset: true,
        }, options );

        if( settings.obj.length > 0 ) {
            $.callGenerateChart(settings.obj, settings.page, settings.reset);
        }
    }

    function _callInfinityLoad ( self, page, loadingBar ) {
        var urlRender = self.attr('data-render-url');
        var template = self.attr('wrapper-write-dashboard-template');
        var template_parent = $.checkUndefined(self.attr('wrapper-write-dashboard-parent'), null);
        var data_append = $.checkUndefined(self.attr('data-append'), null);
        var url = self.attr('data-url') + '/page:'+ page + '/';
        var rel = $.checkUndefined(self.attr('rel'), 'default');

        var date_type = $.checkUndefined(self.attr('data-type'), null);
        var date_title = $.checkUndefined(self.attr('data-title'), null);
        var data_form = $.checkUndefined(self.attr('data-form'), null);
        var data_abort = $.checkUndefined(self.attr('data-abort'), null);
        var data_loading = $.checkUndefined(self.attr('data-loading'), null);
        var data_height = $.checkUndefined(self.attr('data-height'), '50%');
        var data_width = $.checkUndefined(self.attr('data-width'), '100%');
        var data_remove = $.checkUndefined(self.attr('data-remove'), null);
        var formData = null;

        // attributes
        var data_legend = $.checkUndefined(self.attr('data-legend'), null);

        var loadingBar = $.checkUndefined(loadingBar, 'true');

        if( data_form != null ) {
            formData = $(data_form).serialize(); 
        }

        if( data_abort == 'true' && loadJXHR ){
            loadJXHR.abort();
        }
        if( data_loading != null ) {
        //  remove loading bar
            var objLoading  = $(data_loading).html();
                objLoading  = $(objLoading);
            var cssClass    = objLoading.attr('class');
                cssClass    = cssClass.length ? '.' + cssClass.split(' ').join('.') : cssClass;

            if( typeof cssClass == 'undefined' || $(template).parent().find(cssClass).length <= 0 ){
                $(template).after(objLoading);
            }

        //  console.log(cssClass);
        //  console.log($(template).parent().find(cssClass).length);
        }
        
        loadJXHR = $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success:function(result){
                if( typeof result == 'string' && result.length ) {
                    try{
                        result = jQuery.parseJSON( result );
                        var result_rows = $.checkUndefined(result.rows, []);

                        delete result.rows;

                        eval('$.resultjson'+rel+'.rows = $.checkUndefined($.resultjson'+rel+'.rows, []);');
                        eval('$.resultjson'+rel+'.cols = $.checkUndefined($.resultjson'+rel+'.cols, []);');

                        eval('$.extend(true, $.resultjson'+rel+', result);');

                        if( result_rows.length > 0 ) {
                            $.each(result_rows, function(i, row){ 
                                eval('$.resultjson'+rel+'.rows.push(row);');
                            });
                        }

                        // eval('$.extend(true, $.resultjson'+rel+', result);');
                        // console.log(eval('$.resultjson'+rel));

                        $.infinityLoad({
                            obj: self,
                            page: page + 1,
                            reset: false,
                        });
                    }
                    catch(e){
                        console.log(e);
                    }
                } else {
                    var resultjson = eval('$.resultjson'+rel);
                    var resultjsonRows = $.checkUndefined(resultjson.rows, []);
                    var resultJsonType = $.checkUndefined(resultjson.JsonType, null);

                    if ( resultjsonRows.length == 0 && ( resultJsonType != 'calendar' && resultJsonType != 'content' ) ) {
                        resultjson.rows = {};
                    }

                    if( template_parent != null ) {
                        template = template_parent;
                    }

                    $.ajax({
                        url: urlRender,
                        data: { json: JSON.stringify(resultjson) },
                        type: 'POST',
                        beforeSend  : function() {
                            $.loadingbar_progress('beforeSend', loadingBar);
                        },
                        success:function(resultRender){
                            // var content = $(resultRender).find(template).html();
                            var header_content = $(template).find('.header-chart').length;

                            var data_wrapper_arr = template.split(',');
                            
                            $.each(data_wrapper_arr, function(index, identifier){
                                var targetWrapper = $.trim(identifier);
                                var contentPage = $(resultRender).find(targetWrapper).html();

                                $(targetWrapper).html(contentPage);
                            });

                            if( data_append != null ) {
                                var data_append_arr = data_append.split(',');

                                $.each(data_append_arr, function(index, identifier){
                                    var targetWrapper = $.trim(identifier);
                                    var contentPage = $(resultRender).find(targetWrapper).html();

                                    if( $(resultRender).find(targetWrapper).length > 0 ) {

                                        if($('#wrapper-dashboard-parent-tips .tips-content > .loader').length > 0){
                                            $('#wrapper-dashboard-parent-tips .tips-content > .loader').remove();
                                        }

                                        $('#wrapper-dashboard-parent-tips .tips-content').append(contentPage);
                                        $('#wrapper-dashboard-parent-tips').slideDown();
                                    }
                                });
                            }

                            // if($('#wrapper-dashboard-parent-tips .tips-content > .loader').length > 0){

                            //     var data_empty = $('#wrapper-dashboard-parent-tips').data('empty');

                            //     $('#wrapper-dashboard-parent-tips .tips-content > .loader').remove();

                            //     if($('#wrapper-dashboard-parent-tips .tips-content li').length == 0){
                            //         $('#wrapper-dashboard-parent-tips .tips-content').append('<div class="message-content tacenter padding-4"><p class="mb0">'+data_empty+'</p></div>');
                            //     }
                            // }

                            if( data_remove != null ) {
                                var data_remove_arr = data_remove.split(',');

                                $.each(data_remove_arr, function(index, identifier){
                                    var targetWrapper = $.trim(identifier);

                                    $(targetWrapper).remove();
                                });
                            }

                            if( header_content == 0 ) {
                                $(template+' .header-chart').remove();
                                $(template+' .detail-report').remove();
                            }

                            $.tooltipBar();
                            $.infinityClick();
                            $.rebuildFunction();

                            var dataRows = $.checkUndefined(resultjson.rows, null);
                            var options = {
                                'resolution' : {
                                    'width' : data_width,
                                    'height' : data_height,
                                },
                                'title': date_title
                            };

                            if(data_legend == 'false'){
                                options = $.extend(options, {
                                    'attributes' : {
                                        'legend': false,
                                    }
                                });
                            }

                            if ( dataRows.length > 0 ) {
                                switch (date_type) {
                                    case 'chart-histogram':                                        
                                        google.charts.setOnLoadCallback( _callChartLine( $(template), eval('JSON.stringify($.resultjson'+rel+')'), rel, 'histogram', options ) );
                                    break;
                                    case 'chart-line':
                                        google.charts.setOnLoadCallback( _callChartLine( $(template), eval('JSON.stringify($.resultjson'+rel+')'), rel, 'line', options ) );
                                    break;
                                    case 'chart-bar':
                                        google.charts.setOnLoadCallback( _callChartLine( $(template), eval('JSON.stringify($.resultjson'+rel+')'), rel, 'bar', options ) );
                                    break;
                                    case 'chart-bars':
                                        google.charts.setOnLoadCallback( _callChartLine( $(template), eval('JSON.stringify($.resultjson'+rel+')'), rel, 'bars', options ) );
                                    break;
                                    case 'chart-pie':
                                        google.charts.setOnLoadCallback( _callChartLine( $(template), eval('JSON.stringify($.resultjson'+rel+')'), rel, 'pie', options ) );
                                    break;
                                }
                            }
                            
                            eval('$.resultjson'+rel+' = {};')
                            $.sameHeight();
                        },
                        error: function(){
                            console.log('Server timeout');
                        },
                    }).always(function() {
                        $.loadingbar_progress('always', loadingBar);
                    });
                }
            },
            error: function(){
                console.log('Server timeout');
            },
            timeout: 30000,
        });
    }

    $.callGenerateChart = function (obj, page, reset) {
        obj.each(function(){
            var self = $(this);
            var rel = $.checkUndefined(self.attr('rel'), 'default');

            if( reset == true ) {
                eval('$.resultjson'+rel+' = {};');
            }
            
            _callInfinityLoad(self, page, 'false');
        });
    }

    $.infinityClick = function( options ) {
        var settings = $.extend({
            objNav: $('[data-navigation="infinity"]'),
            objNavChange: $('[data-navigation-change="infinity"]'),
            objNavKeyup: $('[data-navigation-keyup="infinity"]'),
        }, options );

        if( settings.objNav.length > 0 ) {
            settings.objNav.off('click').click(function(){
                var self = $(this);
                var rel = $.checkUndefined(self.attr('rel'), 'default');

                eval('$.resultjson'+rel+' = {};');
                
                _callInfinityLoad(self, 1, 'true');

                return false;
            });
        }

        if( settings.objNavChange.length > 0 ) {
            settings.objNavChange.off('change').change(function(){
                var self = $(this);
                var rel = $.checkUndefined(self.attr('rel'), 'default');

                eval('$.resultjson'+rel+' = {};');
                
                _callInfinityLoad(self, 1, 'true');

                return false;
            });
        }

        if( settings.objNavKeyup.length > 0 ) {
            settings.objNavKeyup.off('keyup').keyup(function(){
                var self = $(this);
                var rel = $.checkUndefined(self.attr('rel'), 'default');
                var data_table = $.checkUndefined(self.attr('data-table'), null);

                eval('$.resultjson'+rel+' = {};');
                
                _callInfinityLoad(self, 1, 'true');

                if( data_table == 'true' ) {
                    var objSearchDate = $('.form-table-search #SearchDate');
                    var valSearchDate = objSearchDate.val();

                    if( valSearchDate != self.val() ) {
                        objSearchDate.val( self.val() );
                        objSearchDate.trigger('keyup');
                    }
                }

                return false;
            });
        }
    }

    if($('.select-periode-picker').length > 0){
        $('.select-periode-picker').off('change').change(function(){
            var self = $(this);
            var value = self.val();
            var target = self.data('target');
            var trigger = self.data('trigger');

            var data_value = $(target).attr('data-value');
            data_value = $.parseJSON(data_value);

            var date_from = data_value[0];
            var date_to = data_value[1];

            var month_to = date_to[0];

            var margin = month_to - (value -1);
            date_from[0] = margin;

            if(margin < 1){
                margin = Math.abs(margin);
                var margin = 12 - margin;
                date_from[0] = margin;
                date_from[1] = date_to[1] - 1;
            } else {
                date_from[1] = date_to[1];
            }

            var setDateFrom = '['+date_from[0]+','+date_from[1]+']';
            var setDateTo = '['+date_to[0]+','+date_to[1]+']';

            var result = '['+setDateFrom+','+setDateTo+']';

            // return html;
            setFrom = $.setMonth(date_from);
            setEnd = $.setMonth(date_to);

            $(target).val(setFrom+' - '+setEnd);
            $(target).attr('data-value', result);
            

            if(typeof trigger == 'undefined'){
                _callRemoveTips();
                $.callGenerateChart($('.reload[data-load="infinity"]'), 1, true);
            } else if(trigger == 'off'){
                return false;
            } else {
                $.directAjaxLink({
                    obj: $(target),
                });
            }
        });

        $.setMonth = function(date){
            var month = date[0];
            var result = false;

            switch(month){
                case 1 : result = 'Jan'; break;
                case 2 : result = 'Feb'; break;
                case 3 : result = 'Mar'; break;
                case 4 : result = 'Apr'; break;
                case 5 : result = 'May'; break;
                case 6 : result = 'Jun'; break;
                case 7 : result = 'Jul'; break;
                case 8 : result = 'Aug'; break;
                case 9 : result = 'Sep'; break;
                case 10 : result = 'Oct'; break;
                case 11 : result = 'Nov'; break;
                case 12 : result = 'Dec'; break;
            }

            return result+' '+date[1];
        }
    }

    function _callRemoveTips(){
        if( $('#wrapper-dashboard-parent-tips .tips-content').length > 0 ) {
            $('#wrapper-dashboard-parent-tips .tips-content .loader').remove();

            $('#wrapper-dashboard-parent-tips .tips-content').html('<div class="loader padding-2"><div class="loader-4"></div></div>');
        }
    }

    if( $('.chart-change, .ajax-change').length > 0 ) {
        $('.chart-change').off('change').change(function(){

            var self = $(this);
            var value = self.val();

            _callRemoveTips();
            $.callGenerateChart($('.reload[data-load="infinity"]'), 1, true);

            if(value){
                $.formatDateChart(self, value);
            }
        });

        $.formatDateChart = function(self, value){
            var date_arr =value.split('-');
            var date_from = date_arr[0];
            var date_to = date_arr[1];

            date_from = $.splitDateChart(date_from);
            date_to = $.splitDateChart(date_to);

            var result = '['+date_from+','+date_to+']';
            // set to html
            self.val(value);
            self.attr('data-value', result);
        }

        $.splitDateChart = function(date = null){
            date = $.trim(date);
            var date_arr = date.split(' ');

            var month = date_arr[0];
            var year = date_arr[1];

            switch(month){
                case 'Jan' :
                    month = '1';
                    break;
                case 'Feb' :
                    month = '2';
                    break;
                case 'Mar' :
                    month = '3';
                    break;
                case 'Apr' :
                    month = '4';
                    break;
                case 'May' :
                    month = '5';
                    break;
                case 'Jun' :
                    month = '6';
                    break;
                case 'Jul' :
                    month = '7';
                    break;
                case 'Aug' :
                    month = '8';
                    break;
                case 'Sep' :
                    month = '9';
                    break;
                case 'Oct' :
                    month = '10';
                    break;
                case 'Nov' :
                    month = '11';
                    break;
                case 'Dec' :
                    month = '12';
                    break;
            }

            return '['+month+','+year+']';
        }
    }
    
    $.infinityLoad();
    /* End Report */

    // $.KprSoldPrice = function(options){
    //     var settings = $.extend({
    //         obj : $('.kpr-property-sold-price'),
    //     }, options );

    //     if(settings.obj.length){
    //         $(document).undelegate('.kpr-property-sold-price', 'blur');
    //         $(document).delegate('.kpr-property-sold-price', 'blur', function(){
    //             if($('.link-bank-list').length > 0){
    //                 $('.link-bank-list').trigger('click');
    //             }
    //         });
    //     }
    // }

    $.editable = function(options){
        if(typeof $.fn.editable == 'function'){
        //  var buttonTemplate = '<button type="submit" class="btn green editable-submit"><i class="rv4-bold-check"></i></button>';
        //      buttonTemplate+= '<button type="button" class="btn red editable-cancel"><i class="rv4-bold-cross"></i></button>';

            var buttonTemplate = '';

            $.fn.editableform.buttons = buttonTemplate;

            var options     = typeof options == 'object' ? options : {};
            var settings    = $.extend({
                obj     : $('.editable'), 
                options : {
                //  mode            : 'inline', 
                //  selector        : 'a', 
                    onblur          : 'submit', 
                    send            : false, 
                    showbuttons     : false, 
                    savenochange    : true, 
                    emptytext       : 'N/A',
                //  onblur          : 'submit',  
                //  sourceCache     : false, 
                }, 
            }, options);

            settings.obj.off('init shown hidden').on('init shown hidden', function(event, data){
                var self = $(this);

                if(event.type == 'shown'){
                    var wrapper = self.parent().find('.editable-container');

                    if(wrapper.length){
                        if(typeof $.rebuildFunctionAjax == 'function'){
                            $.rebuildFunctionAjax(wrapper, {
                                rebuild_function : false,
                            });
                        }

                        if(typeof $.handle_toggle == 'function'){
                            $.handle_toggle();
                        }

                        if(typeof $.generateLocation == 'function'){
                            $.generateLocation({
                                regionSelector  : $('select[data-role="region-input"]'),
                                citySelector    : $('select[data-role="city-input"]'),
                                subareaSelector : $('select[data-role="subarea-input"]'),
                                zipSelector     : $('input[data-role="zip-input"]'),
                            });
                        }
                    }
                }
                else{
                //  init / hidden
                    var value       = '';
                    var type        = $.checkUndefined(self.attr('data-type'), 'text');
                    var placeholder = $.checkUndefined(self.attr('data-placeholder'), '');
                    var emptytext   = settings.options.emptytext;

                    if(event.type == 'hidden'){
                        value = self.editable('getValue', true);
                    }
                    else{
                        value = $.checkUndefined(self.attr('data-value'), '');

                        if(type == 'text' && (value == null || value.length <= 0)){
                            if(self.text().length && self.text() != placeholder && self.text() != emptytext){
                                value = self.text();
                            }
                        }
                    }

                    if(value == null || value.length <= 0){
                        emptytext = placeholder.length ? placeholder : emptytext;
                        self.text(emptytext).addClass('editable-empty');
                    }   
                }
                
            //  console.log(event.type);
            });

        //  INITIALIZE ==================================================================================

            settings.obj.editable(settings.options);

            if(settings.obj.length){
            //  force init event on predefined editable elements
                settings.obj.trigger('init');
            }

        //  =============================================================================================

        //  FORM SUBMIT HANDLER =========================================================================

            $('body').off('click').on('click', '[data-role="editable-submit"]', function(event){
                var self        = $(this);
                var targetForm  = $(self.attr('data-target'));

                if(targetForm.length){
                    var editableInputs = targetForm.find('.editable');
                    
                    if(editableInputs.length){
                        var targetURL           = targetForm.attr('action');
                        var nonEditableInputs   = targetForm.find(':input');
                        var additionalData      = {};

                        if(nonEditableInputs.length){
                            var serializeArray = nonEditableInputs.serializeArray();

                            $.each(serializeArray, function(arrayIndex, arrayValue){
                                additionalData[arrayValue['name']] = arrayValue['value'];
                            });
                        }

                    //  ajax request handler
                        editableInputs.editable('submit', { 
                            url         : targetURL, 
                            ajaxOptions : { 
                                dataType : 'json', 
                                beforeSend  : function(){
                                    $.loadingbar_progress('beforeSend', 'true');
                                },
                            }, 
                            data        : additionalData,
                            success     : function(response, config){
                                $.loadingbar_progress('always', 'true');

                            //  remove error message (if any)
                                targetForm.find('.error-message').remove();

                                var self        = $(this);
                                var redirect    = $.checkUndefined(response.redirect);

                                var status      = $.checkUndefined(response.status, 'error');
                                var message     = $.checkUndefined(response.msg);
                                var data        = $.checkUndefined(response.data);
                                var recordID    = $.checkUndefined(response.id);

                                if(recordID){
                                //  refresh primary key
                                    self.editable('option', 'pk', recordID);
                                }

                                if(status == 'success'){
                                //  remove unsaved class
                                    self.removeClass('editable-unsaved').off('save.newuser');
                                }
                                else{
                                    var validationErrors    = $.checkUndefined(response.validationErrors);
                                    var inputErrors         = $.checkUndefined(response.inputErrors);

                                    if(inputErrors.length){
                                        $.each(inputErrors, function(index, errorDetail){
                                            var inputID         = $.checkUndefined(errorDetail.id, false);
                                            var inputName       = $.checkUndefined(errorDetail.name, false);
                                            var errorMessage    = $.checkUndefined(errorDetail.message, false);

                                            if((inputID || inputName) && errorMessage){
                                                var objInput = $('#' + inputID);

                                                if(objInput.length < 1){
                                                    objInput = $('[name="' + inputName + '"], [data-name="' + inputName + '"]');
                                                }

                                                if(objInput.length){
                                                    objInput.after('<small class="error-message"><p>' + errorMessage + '</p></small>');
                                                }
                                            }
                                        });
                                    }
                                }

                                if(validURL(redirect)){
                                    window.location.href = redirect;
                                }

                                var objAlertTarget = $('#wrapper-write');

                                if(objAlertTarget.length){
                                    var addClass    = status + '-full alert mb20';
                                    var objNotice   = $.flashNotice(status, message, false, addClass);

                                    $('body').find('.alert').remove();
                                    objAlertTarget.find('.editable-notice').remove();
                                    objAlertTarget.prepend(objNotice.addClass('editable-notice'));

                                    $('html, body').animate({
                                        scrollTop: objAlertTarget.offset().top - 50,
                                    }, 500);

                                    setTimeout(function(){
                                        var notices = objAlertTarget.find('.editable-notice');

                                        if(notices.length){
                                            notices.remove();
                                        }
                                    }, 15000);
                                }
                                else{
                                //  no message placeholder, show alert
                                    alert(message);
                                }
                            },
                            error       : function(errors){
                                alert('Upss, sepertinya terjadi kesalahan, silakan coba lagi.');

                                var msg = '';
                                if(errors && errors.responseText) {
                                //  ajax error, errors = xhr object
                                    msg = errors.responseText;

                                    console.log(errors.responseText);
                                }
                                else{
                                //  validation error (client-side or server-side)
                                    $.each(errors, function(k, v){
                                        msg += k+": "+v+"<br>";

                                        console.log(k + ' : ' + v);
                                    });
                                } 

                            //  $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
                            }
                        });
                    }
                }
            });

        //  =============================================================================================
        }
    };

    $.show_less_description = function(options){
        $('.open-more').click(function(){
            var self = $(this);
            var url_async = $.checkUndefined(self.attr('data-url'), '');
            var parent = self.parents('.open-more-box');

            if(url_async != ''){
                $.ajax({
                    url : url_async,
                    type : "post",
                    async : true,
                    success : function(data) {}
                });
            }

            parent.find('.text-editor-hide').remove();
            parent.find('.text-load-more.text-editor').css('height', 'initial');
            parent.find('.text-load-more.text-editor').css('max-height', 'initial');
            self.remove();
        });
    }

    $.form_click_option = function(){
        $('.form-click-option').off('click');

        $('.form-click-option').click(function(){
            var self = $(this);
            var target = $.checkUndefined(self.attr('data-target'), '');
            var value = $.checkUndefined(self.attr('data-value'), '');
            var text_before = $.checkUndefined(self.attr('data-text-before'), '');
            var text_after = $.checkUndefined(self.attr('data-text-after'), '');

            var label_target = $.checkUndefined(self.attr('data-label-target'), '');

            if(label_target != ''){
                var parent_target = self.parents('tr');

                var stock_name = parent_target.find('.field-name').text();
                var blok_name = parent_target.find('.field-blok').text();

                var text = stock_name+' - '+blok_name;

                $(label_target).text(text);

                $('.input-stock-name').val(stock_name);
                $('.input-stock-block').val(blok_name);
            }

            $('.form-click-option').text(text_before);

            self.text(text_after);

            $(target).val(value);

            $.directAjaxLink({
                obj:self
            });
        });
    }

    $.paymentChannelHandler = function(){
        if($('.payment-channel-handle').length > 0){
            $('.payment-channel-handle').off('change');
            $('.payment-channel-handle').change(function(){
                var self = $(this);
                var val = self.val();

                var target_payment_channel_transfer = $('.payment-channel-transfer');
                var target_payment_channel_transfer_other = $('.payment-channel-transfer-other');

                if(val == '98' || val == 98){
                    target_payment_channel_transfer.show();
                }else{
                    target_payment_channel_transfer.hide();

                    $('.payment-channel-transfer select').val('');
                    $('.payment-channel-transfer-other input').val('');
                }

                target_payment_channel_transfer_other.hide();
            });
        }
    }

    $.voucherHandler = function(){
        //  checkout payment handler
        var btnValidateVoucher = $('#validate-voucher-button');

        if(btnValidateVoucher.length){
            var txtVoucherCode          = $('#voucher-code');
            var txtDocumentType         = $('#document-type');
            var txtDocumentCode         = $('#document-code');
            var txtDocumentPrefix       = $('#document-prefix');
            var selMembershipPackage    = $('#document-membership-package');
            var txtTriggerVoucher       = $('#trigger-voucher');

            var baseAmount      = $('#document-amount');
            var discountAmount  = $('#document-discount');
            var totalAmount     = $('#document-grandtotal');

            var data_default = $.checkUndefined(btnValidateVoucher.data('default'), false);
            var data_parent = $.checkUndefined(btnValidateVoucher.data('parent'), 'div.content-input');
            var data_class = $.checkUndefined(btnValidateVoucher.data('class'), 'padding-top-2');

            var inputParent = txtVoucherCode.closest( data_parent );

            /*diskusi sama surya*/
            // if(txtVoucherCode.length > 0 && data_default == false ){
            //     txtVoucherCode.val('');
            // }

            if(selMembershipPackage.length){
                selMembershipPackage.on('ajax-link:changed', function(event){
                    txtVoucherCode.val('');
                    inputParent.find('div.error-message, div.success-message').remove();
                });
            }

            var checkVoucherJXHR;
            btnValidateVoucher.off('click');
            btnValidateVoucher.click(function(){
                var self = $(this);

                inputParent.find('div.error-message, div.success-message').remove();

                if(txtVoucherCode.val() != ''){
                    if(checkVoucherJXHR){
                        checkVoucherJXHR.abort();
                    }

                    var protocol    = window.location.protocol;
                    var host        = window.location.host;
                    var baseURL     = protocol + '//' + host;

                    checkVoucherJXHR = $.ajax({
                        url     : baseURL + '/backprocess/transaction/validateVoucher',
                        type    : 'post',
                        data    : {
                            'code'                  : txtVoucherCode.val(), 
                            'document_type'         : txtDocumentType.val(), 
                            'document_code'         : txtDocumentCode.val(), 
                            'membership_package'    : selMembershipPackage.val(), 
                            'refer_prefix'          : txtDocumentPrefix.val(),
                            // 'price'                 : self.data('price'),
                        },
                        success : function(data){
                            var objResult = $.parseJSON(data);

                            if(typeof(objResult) == 'object'){
                                var status  = typeof(objResult.status) != 'undefined' ? objResult.status : 'error';
                                var message = typeof(objResult.msg) != 'undefined' ? objResult.msg : 'Error';

                                if(status == 'success'){
                                    var voucher         = typeof(objResult.data) != 'undefined' ? objResult.data : false;
                                    var currencySymbol  = typeof(voucher.discount_currency_symbol) != 'undefined' ? voucher.discount_currency_symbol : '';
                                    var discountType    = typeof(voucher.discount_type) != 'undefined' ? voucher.discount_type : 'nominal';
                                    var discountValue   = typeof(voucher.discount_value) != 'undefined' ? voucher.discount_value : 0;
                                    var subTotal        = $.checkUndefined(baseAmount.data('value'), 0);
                                    var grandTotal      = subTotal;

                                    if(discountType == 'nominal'){
                                        discountValue = typeof(voucher.discount_convert_value) != 'undefined' ? voucher.discount_convert_value : 0;
                                    }
                                    else{
                                        discountValue = (parseFloat(subTotal) / 100) * parseFloat(discountValue);
                                    }

                                    grandTotal = parseFloat(subTotal) - parseFloat(discountValue);
                                    grandTotal = parseFloat(grandTotal) < 0 ? 0 : grandTotal;

                                    discountValue   = formatMoney(discountValue, 0);
                                    grandTotal      = formatMoney(grandTotal, 0);

                                    discountAmount.html(currencySymbol+' '+discountValue);
                                    totalAmount.html(currencySymbol+' '+grandTotal);

                                    inputParent.append('<div class="success-message '+data_class+'">'+message+'</div>');
                                    inputParent.append('<input type="hidden" name="data[VoucherCode][status]" value="1">');
                                }
                                else{
                                    discountAmount.html('-');
                                    totalAmount.html(baseAmount.html());

                                    inputParent.append('<div class="error-message '+data_class+'">'+message+'</div>');
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
                else{
                    discountAmount.html('-');
                    totalAmount.html(baseAmount.html());
                }

                return false;
            });

            if(txtTriggerVoucher.length > 0){
                btnValidateVoucher.trigger('click');
                $('#trigger-voucher').remove();
            }
        }
    }

    $.callPreloader = function( options ) {
        if( $('#render_tbl').length > 0 ) {
            $('.table-responsive, .pagination, .filter-footer').hide();
            var url = $('#render_tbl').attr('data-reload');
            var template = $('#render_tbl').attr('data-template');

            $.ajax({
                url: url,
                dataType: 'html',
                type: 'POST',
                success:function(result){
                    var header_number = '.main-action .data-number';
                    var contentHtml = $(result).find(template).html();
                    var header = $(result).find(header_number).html();

                    $(template).html(contentHtml);
                    $(header_number).html(header);
                    $('#render_tbl').remove();
                    
                    $.rebuildFunction();
                    $.rebuildFunctionAjax( $(template) );
                },
                error: function(){
                    console.log('Server timeout');
                },
                timeout: 30000,
            });
        }
    }

    $.triggerSubmitReport = function(){
        if($('.trigger-submit-report').length > 0){
            $('.trigger-submit-report').click(function(){
                _callRemoveTips();
                $.callGenerateChart($('.reload[data-load="infinity"]'), 1, true);
            });
        }      
    }

    $.triggerComponentPoint = function(){
        if($('.trigger-conditions').length > 0){
            $('.trigger-conditions').click(function(){
                var self = $(this);
                var checked = self.is(':checked');

                var data_parent = self.attr('data-parent');

                var data_child_component = self.attr('data-child-component');
                var parent = self.parents(data_parent);

                if(checked == true){
                    $(parent).find('input[type="text"],input.trigger-active').removeAttr('disabled');
                    $(parent).find(data_child_component).fadeIn();
                } else {
                    $(parent).find('input[type="text"],input.trigger-active').attr('disabled', true);
                    $(parent).find(data_child_component).fadeOut();
                }
            });
        }

        if($('.component-type').length > 0){
            $('.component-type').off('change').change(function(){
                var self = $(this);
                var val = $.checkUndefined(self.val(), false);

                var data_target = $.checkUndefined(self.attr('data-target'), false);
                var data_parent = $.checkUndefined(self.attr('data-parent'), false);
                var data_url = $.checkUndefined(self.attr('data-url'), false);
                var data_type = $.checkUndefined(self.attr('data-type'), 0);
                var data_rel = $.checkUndefined(self.attr('data-rel'), 0);

                var parent = self.parents(data_parent);

                if(val) {
                    data_url = data_url + '/' + val+ '/'+ data_type+ '/'+ data_rel;
                }

                 $.ajax({
                    url: data_url,
                    type: 'POST',
                    success: function(result) {
                        parent.find(data_target).html(result);   
                        $.rebuildFunctionAjax($(data_parent));
                        return false;
                    },  
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });

            });
        }
    }

    $.component_range = function(){
        if($('.component-range').length > 0){
            $('.component-range').off('change').change(function(){
                var self = $(this);
                var value = self.val();

                var parentLi = self.parents('li');

                var val_obj_first = parentLi.find('.val_first');
                var val_obj_end = parentLi.find('.val_end');
                var wrapper_val_end = parentLi.find('.wrapper-value-end');

                switch(value){
                    case 'between':
                        val_obj_first.removeAttr('readonly');
                        val_obj_end.removeAttr('readonly');
                        
                        wrapper_val_end.fadeIn();
                        break;

                    case 'less_than':
                    case 'equal':
                        val_obj_first.removeAttr('readonly');
                        val_obj_end.attr('readonly', true).val('');
                        
                        wrapper_val_end.fadeOut();
                        break;

                    case 'more_than':
                        val_obj_end.removeAttr('readonly');
                        val_obj_first.attr('readonly', true).val('');
                        
                        wrapper_val_end.fadeIn();
                        break;
                }
            });
        }
    }

    $.phoneInput = function(options){
        options = $.extend({
            obj : ':input[data-role="phone-input"]', 
        }, options);

        options.obj = $(options.obj);

        if(options.obj.length && typeof libphonenumber == 'object'){
        /*  available formats
            National                                — e.g. (213) 373-4253
            International                           — e.g. +1 213 373 4253
            E.164                                   — e.g. +12133734253
            RFC3966 (the phone number URI)          — e.g. tel:+12133734253;ext=123
            IDD — "Out-of-country" dialing format   - e.g. 01178005553535 for +7 800 555 35 35 being called out of options.fromCountry === US. If no options.fromCountry was passed or if there's no default IDD prefix for options.fromCountry then returns undefined. Pass options.humanReadable: true for a human-readable output (same output as Google's formatOutOfCountryCallingNumber()).
        */

            options.obj.on('init blur', function(event){
                var self            = $(this);
                var dataCountry     = $.checkUndefined(self.data('country'), 'ID');
                var dataFormat      = $.checkUndefined(self.data('format'), 'National');
                var dataAllowed     = $.checkUndefined(self.data('allowed'));
                var dataHaltOnError = $.checkUndefined(self.data('halt_on_error'), false);

                var value   = self.val();
                var isError = false;
                var message = null;

                if(value){
                    var asYouType   = new libphonenumber.AsYouType(dataCountry);
                    var formatted   = libphonenumber.formatNumber(asYouType.input(value), dataFormat);
                    var numberType  = libphonenumber.getNumberType(formatted, dataCountry);

                    if(dataAllowed.length){
                        dataAllowed = dataAllowed.toString().replace(' ', '');
                        dataAllowed = dataAllowed.split(',');

                        if(numberType && $.inArray(numberType, dataAllowed) > -1){
                        //  do nothing
                        }
                        else{
                            isError = true;
                            message = 'Format nomor salah';
                        }
                    }

                    if(!isError){
                        self.val(formatted);
                    }
                }

            //  toggle error message
                $.toggleErrorInput({
                    obj     : self,
                    isError : isError,
                    message : message,
                });

                if(dataHaltOnError === true || dataHaltOnError === 'true'){
                    var btnSubmit = self.closest('form').find(':input:submit,button[type="submit"]');
                    btnSubmit.prop('disabled', isError);
                }
            }).trigger('init');
        }
    }

//  multiple check autocomplete
//  https://github.com/davidstutz/bootstrap-multiselect

    $.multiselect = function(settings){
        if(typeof $.fn.multiselect == 'function'){
            var settings = $.extend({
                obj : $('select.multiselect'), 
            }, settings);

            settings.obj = $(settings.obj);

            if(settings.obj.length){
                var multiselectJXHR;
                var defaultOpts = {
                    enableHTML                      : true, 
                    includeResetOption              : true, 
                    includeResetDivider             : true, 
                    enableFiltering                 : true,
                    enableCaseInsensitiveFiltering  : true, 
                    maxHeight                       : 250, 
                    numberDisplayed                 : 1, 
                    filterBehavior                  : 'text', // text, value or both, 
                    filterPlaceholder               : 'Cari', 
                    buttonWidth                     : '100%', 
                    buttonText                      : function(options, select){
                        return options.length ? options.length + ' Terpilih' : 'Pilih';
                    }, 
                };

                function buildItemList(objSelect, objOption, state){
                    var self    = $(objSelect);
                    var values  = self.val();
                    var state   = state ? state : 'append';

                    if(values.length){
                    //  append list item
                        values = values.split(',');

                        $.each(values, function(index, value){

                        });
                    }
                    else{
                    //  remove all items
                    }
                }

                function getData(obj){
                    var self        = $(obj); 
                    var inputName   = self.attr('name');
                    var dataSource  = self.data('source');

                    if(dataSource){
                        if(validURL(dataSource)){
                            var objWrapper  = self.parent('.multiselect-native-select');
                            var objFilter   = objWrapper.find('.multiselect-filter .multiselect-search');
                            var postData    = {
                                'Search' : {
                                    'keyword' : objFilter.val(), 
                                }, 
                            };

                            var extraParams = $._extractParams(self);

                            if(extraParams){
                            //  append additional params ========================================================

                                if($.objKeys(extraParams.post).length){
                                    postData = $.extend(true, extraParams.post, postData);
                                }

                                if($.objKeys(extraParams.named).length){
                                    var nonFieldParams  = [];
                                    var fieldParams     = [];
                                    var namedParams     = '';

                                    $.each(extraParams.named, function(paramKey, paramValue){
                                        var regex = new RegExp('^\\d+$');

                                        if(regex.test(paramKey)){
                                        //  numeric field name
                                            nonFieldParams.push(paramValue);
                                        }
                                        else{
                                            fieldParams.push(paramKey + ':' + paramValue);
                                        }
                                    });

                                //  alter url
                                    namedParams+= dataSource.substr(dataSource.length - 1) == '/' ? '' : '/';
                                    namedParams+= nonFieldParams.length ? nonFieldParams.join('/') + '/' : '';
                                    namedParams+= fieldParams.length ? fieldParams.join('/') : '';

                                    if(dataSource.indexOf('?') > -1){
                                        dataSource = dataSource.split('?').join(namedParams + '?');
                                    }
                                    else{
                                        dataSource+= namedParams;
                                    }
                                }

                                if($.objKeys(extraParams.query).length){
                                    var queryParams = [];

                                    $.each(extraParams.query, function(paramKey, paramValue){
                                        queryParams.push(paramKey + '=' + paramValue);
                                    });

                                //  alter url
                                    dataSource+= dataSource.indexOf('?') > -1 ? '&' : '?';
                                    dataSource+= queryParams.join('&');
                                }

                            //  =================================================================================
                            }

                            if(multiselectJXHR){
                                multiselectJXHR.abort();
                            }

                            $.loadingbar_progress('beforeSend', true);

                            multiselectJXHR = $.post(dataSource, postData, function(response){
                            //  set new data
                                self.multiselect('dataprovider', response);
                                $.loadingbar_progress('always', true);
                            //  buildItemList(self);
                            }, 'json');
                        }
                        else{
                        //  json formatted data source
                            self.multiselect('dataprovider', dataSource);
                        //  buildItemList(self);
                        }
                    }
                }

                settings.obj.on('init', function(event){
                    var self            = $(this);
                    var inputName       = $.checkUndefined(self.attr('name'), null);
                    var multiple        = $.checkUndefined(self.attr('multiple'), null);
                    var placeholder     = $.checkUndefined(self.attr('placeholder'), defaultOpts.filterPlaceholder);
                    var displaySelected = $.checkUndefined(self.data('display-selected'), false);

                    defaultOpts.filterPlaceholder = placeholder;

                    if(inputName && multiple){
                        if(inputName.indexOf('[]') < 0){
                            inputName = inputName + '[]';
                        }

                        defaultOpts = $.extend(defaultOpts, {
                            checkboxName : function(option){
                                return inputName;
                            }
                        });
                    }

                //  bind search functionality
                    if(defaultOpts.enableFiltering){
                        defaultOpts = $.extend(defaultOpts, {
                            onFiltering : function(filter){
                                getData(self);
                            }, 
                        });
                    }

                //  display item selected as list (onchange event)
                    if(displaySelected){
                        defaultOpts = $.extend(defaultOpts, {
                            onChange : function(option, checked, select){
                                var optionValue = $(option).val();
                                var optionLabel = $(option).text();
                                var objWrapper  = self.parent('.multiselect-native-select');
                                var objList     = objWrapper.next('.multiselect-item-list');

                                if(checked){
                                //  append item
                                    if(objList.length <= 0){
                                        objWrapper.after('<ul class="multiselect-item-list"></ul>');

                                        objList = objWrapper.next('.multiselect-item-list');
                                    }

                                    var template = '<a href="javascript:void();" class="remove" data-ref="' + optionValue + '">';
                                        template+= '<i class="rv4-cross color-red"></i>';
                                        template+= '</a>';

                                    objList.append('<li class="multiselect-item" data-ref="' + optionValue + '" title="' + optionLabel + '">' + template + optionLabel + '</li>');
                                }
                                else{
                                //  remove item
                                    var target = objList.find('.multiselect-item[data-ref="' + optionValue + '"]');

                                    if(target.length){
                                        target.remove();
                                    }
                                }

                            //  buildItemList(self);
                            }, 
                        });
                    }

                //  self.multiselect('setOptions', defaultOpts);
                //  self.multiselect('rebuild');
                    self.multiselect(defaultOpts);
                    getData(self);
                }).trigger('init');

                $(document).on('click', '.multiselect-item .remove', function(event){
                    var self        = $(this);
                    var objWrapper  = self.closest('.multiselect-item-list');
                    var objSelect   = objWrapper.prev('.multiselect-native-select');

                    if(objSelect.length){
                        var objCheckbox = objSelect.find('ul.multiselect-container li.active :input[value="' + self.data('ref') + '"]');

                        if(objCheckbox.length){
                            objCheckbox.trigger('click');
                        }
                    }
                });
            }
        }
    }
}( jQuery ));

function validURL(str){
    var pattern = new RegExp(/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/, 'i');
    return pattern.test(str);
}

function l_pad(string, pad_length, pad_string){
    return (Array(pad_length).join(pad_string||' ') + string).slice(-pad_length);
}

function r_pad(string, pad_length, pad_string){
    return (string + Array(pad_length).join(pad_string||' ')).slice(0, pad_length);
}

//  google recaptcha
var renderRecaptcha = function(){
    if(typeof grecaptcha == 'object' && typeof grecaptcha.render == 'function'){
        for(var i = 0; i < document.forms.length; ++i){
            var form    = document.forms[i];
            var holder  = form.querySelector('.recaptcha-holder');

            if(null === holder){ continue; }

            var siteKey = holder.dataset.key;

            if(typeof siteKey != 'undefined'){
                (function(frm){
                    var holderId = grecaptcha.render(holder, {
                        'sitekey'   : siteKey,
                        'size'      : 'invisible',
                        'badge'     : 'inline', 
                        'callback'  : function(recaptchaToken){
                            $.getAjaxForm ( $(frm) );
                            // HTMLFormElement.prototype.submit.call(frm);
                        }
                    });

                    frm.onsubmit = function(event){
                        event.preventDefault();
                        grecaptcha.execute(holderId);
                        $('button[type="submit"],button[type="button"]').attr('disabled', false);
                    };
                })(form);
            }
            else{
                console.log('please provide recaptcha key');
            }
        }
    }
};

function setCookie(cname, cvalue, exminute) {
    var d = new Date();
    d.setTime(d.getTime() + (exminute * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    
    return "";
}

function deleteCookie(cname) {
    document.cookie = cname + "=;path=/";
}

function checkCookie(cname) {
    var username = getCookie(cname);
    if (username != "") {
        return true;
    } else {
        return false;
    }
}