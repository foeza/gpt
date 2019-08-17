$(document).ready(function(){
	if(typeof $.fn.lazyload == 'function'){
		var lazyImage = $('.lazy-image');

		if(lazyImage.length){
			lazyImage.lazyload({
				threshold	: 200,
				placeholder	: '', 
			});
		}
	}

	if(typeof $.carousel == 'function'){
		var objTrendCarousel	= $('#trend-carousel');
		var objTrendToggle		= $('#trend-carousel-toggle');

		if(objTrendCarousel.length){
		//	initiate carousel
			objTrendCarousel.carousel({
				interval	: 20000,
				pause		: true, 
			});

			if(objTrendToggle.length){
				objTrendToggle.prop('checked', true).on('click init', function(event){
					var state = $(this).is(':checked') ? 'cycle' : 'pause';

				//	set new carousel's state based on toggle state
					objTrendCarousel.carousel(state);
				}).trigger('init');
			}
		}
	}

//	google chart handler
	$.initChart = function(options){
		var settings = $.extend({
			'obj' : $('body'), 
		}, options);

		var chartContainer = $('body');

		if(chartContainer.length){
			var chartPeriod		= chartContainer.find('[data-role="chart-period"]');
			var chartType		= chartContainer.find('[data-role="chart-type"]');
			var googleCharts	= chartContainer.find('div[data-role="google-chart"]');

			var chartTabContainer	= $('ul[role="tablist"]');
			var chartTabs			= chartTabContainer.find('a[role="tab"][data-toggle="tab"]');

			if(chartTabs.length){
				chartTabs.on('shown.bs.tab', function(e){
					var self	= $(this);
					var href	= self.attr('href');
					var tabPane	= $('div.tab-pane.active' + href);

					if(tabPane.length){
						var googleChart	= tabPane.find('div[data-role="google-chart"]:visible');

						if(googleChart.html() != ''){
							googleChart.html('');
						}

						__renderChart(googleChart);
					}
				});

				var googleChart	= chartContainer.find('div.tab-pane.active div[data-role="google-chart"]');

				__renderChart(googleChart);
			}

		//	chart input
			chartPeriod.add(chartType).on('change init', function(event){
				var self		= $(this);
				var inputRole	= self.data('role');
				var googleChart = null;

				if(event.type == 'init' && inputRole == 'chart-period' || event.type == 'change'){
				//	if(inputRole == 'chart-period'){
					//	chart period : global
				//		googleChart = chartContainer.find('div[data-role="google-chart"]:visible');
				//	}
				//	else{
					//	chart type : per wrapper
						var wrapper = self.closest('div.market-chart-wrapper');

						if(wrapper.length){
							googleChart = wrapper.find('div[data-role="google-chart"]:visible');	
						}
				//	}

					__renderChart(googleChart);
				}
			}).trigger('init');

			var googleChart = $('div[data-role="google-chart"][data-autorun="true"]:visible');
			if(googleChart.length){
				__renderChart(googleChart);
			}

			function setTooltipContent(dataTable, row){
				if(row !== null){
				//	var content = '<div class="custom-tooltip" ><h1>' + dataTable.getValue(row, 0) + '</h1><div>' + dataTable.getValue(row, 1) + '</div></div>'; //generate tooltip content
				//	var tooltip = document.getElementsByClassName("google-visualization-tooltip")[0];
				
				//	tooltip.innerHTML = content;

					var tooltip	= $('.google-visualization-tooltip:eq(0)');
					var content	= tooltip.html();

					content = content.split('&lt;').join('<').split('&gt;').join('>');
					content = content.split('{*-*}').join('<i class="fa fa-angle-down"></i>');
					content = content.split('{*+*}').join('<i class="fa fa-angle-up"></i>');

					tooltip.html(content);
				//	console.log(row);
				//	console.log(dataTable.toSource());
				}
			}

			function __renderChart(googleChart){
				googleChart = $(googleChart);

				if(googleChart.length){
					var transparentAxesOpts = {
						gridlines : { color : 'transparent' },
						textStyle : {
							color		: 'transparent', 
							fontSize	: 0, 
						 },
					};

					var chartOptions = {
						'material_bar' : {
							title		: googleChart.data('title'), 
							chart		: { marginTop : 20 }, 
							legend		: { position : 'none' }, 
							bar			: { groupWidth : '25%' },
							tooltip		: { isHtml	: true, trigger : 'hover' }, 
							isStacked	: true, 
							vAxis		: {
								format		: 'short',
								textStyle	: { bold : false, fontSize: 10,  },
							},
							hAxis		: {
								format		: 'MMM yy',
								gridlines	: {
									color	: 'transparent',
									units	: null, 
								},
							},
							axes		: {
								y : {
									0 : { side : 'right' }, 
								}, 
							}, 
							series		: {
								0 : { targetAxisIndex: 0, color	: '#005F9C' },
								1 : { targetAxisIndex: 0, color : '#009EFF' },

								2 : { targetAxisIndex: 1, color : '#87022D' },
								3 : { targetAxisIndex: 1, color : '#DF064D' },

								4 : { targetAxisIndex: 2, color : '#9A691A' },
								5 : { targetAxisIndex: 2, color : '#FFAF34' },

								6 : { targetAxisIndex: 3, color : '#5A2D16' },
								7 : { targetAxisIndex: 3, color : '#97542A' },
							}, 
							vAxes		: {
								1 : transparentAxesOpts,
								2 : transparentAxesOpts,
								3 : transparentAxesOpts,
							},
						}, 
						'line' : {
							title		: googleChart.data('title'), 
							tooltip		: { isHtml : true, trigger : 'hover' }, 
							pointSize	: 8, 
							legend		: { position : 'none', }, 
							colors		: ['#009CFF', '#DE1850', '#FFAF48', '#955433'],
							hAxis		: {
								format		: 'MMM yy',
								gridlines	: {
									color	: 'transparent',
									units	: null, 
								},
							},
							vAxis		: {
								format		: 'short',
								units		: null,
								textStyle	: {
									bold : false, 
								},
							},
						}
					};

					googleChart.each(function(index, objChart){
						var objChart		= $(objChart);
						var chartID			= $.checkUndefined(objChart.attr('id'), null);
						var source			= $.checkUndefined(objChart.data('source'), null);
						var chartType		= $.checkUndefined(objChart.data('type'), 'line');
						var tabPane			= objChart.closest('div.tab-pane.active');
						var propertyTypes	= tabPane.find('[data-role="chart-type"]');

					//	filter inputs
						var period			= chartContainer.find('[data-role="chart-period"]');
						var postData		= period.add(propertyTypes).serialize();
						var legendColors	= [];

						if(propertyTypes.length){
							propertyTypes.each(function(index, elem){
								if($(elem).prop('checked')){
									legendColors.push($(elem).data('color'));
								}
							});
						}
						else{
						//	default colors
							legendColors = ['#009CFF', '#DE1850', '#FFAF48', '#955433'];
						}

						if(source && typeof source != 'undefined' && typeof $.googleChart == 'function'){
						//	generate google chart
							$.googleChart({
								elementID	: chartID,
								dataSource	: source,
								postData	: postData,
								type		: chartType,
								options		: chartOptions[chartType],
								onComplete	: function(chartObject, chartDataTable, results){
									var chartData	= $.checkUndefined(results.chart, null);
									var rawData		= $.checkUndefined(results.data, null);
									var summaries	= $.checkUndefined(results.summary, null);
									var currency	= $.checkUndefined(summaries.currency, 'Rp. ');
									var totalCount	= $.checkUndefined(summaries.total_property_count, 0);

									var dataSync		= objChart.data('sync');
									var dataSyncType	= objChart.data('sync-type');

									var objTarget = $(dataSync);

									if(objTarget.length){
									//	widget
										if(typeof $._MT_widgets == 'function'){
											objTarget.attr('data-source', JSON.stringify(results));

											$._MT_widgets({ obj : objTarget });
										}

									//	custom listener
										google.visualization.events.addListener(chartObject, 'onmouseover', function(event){
											setTooltipContent(chartDataTable, event.row);
										});

										google.visualization.events.addListener(chartObject, 'select', function(){
											if(typeof chartObject == 'object' && chartObject && typeof chartObject.getSelection() == 'object'){
											//	var selection = chartObject.getChart().getSelection();
												var selection = chartObject.getSelection()[0].row;

												setTooltipContent(chartDataTable, selection);
											}
										});
									}
									else{ //if(chartID == 'google-chart-1'){
										var $_carousel = $('#trend-carousel');

										if($_carousel.length && typeof summaries == 'object'){
											var $_carouselItems	= $_carousel.find('.carousel-item, .carousel-inner .item');

											var placeholders	= {
												'price'		: 'avg_price_measure' , 
												'average'	: 'avg_lot_price', 
												'minimum'	: 'min_price', 
												'maximum'	: 'max_price', 
											};

											$_carouselItems.each(function(elemIndex, elemObject){
												var $_carouselItem	= $(elemObject);
												var typeID			= $_carouselItem.data('typeid');

												if(typeID){
													$.each(placeholders, function(prefix, fieldName){
													//	loop actionid
														$.each([1, 2], function(index, actionID){
															var $_price		= $_carouselItem.find('[data-actionid="' + actionID + '"][data-role="' + prefix + '-holder"]');
															var $_compare	= $_carouselItem.find('[data-actionid="' + actionID + '"][data-role="' + prefix + '-compare-holder"]');

															if($_price.length){
																var value = $.checkUndefined(summaries[fieldName][typeID + '-' + actionID], 0);

																if(parseFloat(value) > 0){
																	value = currency + ' ' + $.formatNumber(value, 0);

																	if($_compare.length){
																		var percentage		= $.checkUndefined(summaries[fieldName + '_percentage'][typeID + '-' + actionID], 0);
																		var cssClass		= '';
																		var arrowIcon		= '';
																		var decimalPlace	= 2;

																		percentage = parseFloat(percentage);

																		if(percentage != 0){
																			cssClass	= percentage > 0 ? 'green' : 'red';
																			arrowIcon	= percentage > 0 ? 'up' : 'down';
																			arrowIcon	= '<i class="fa fa-angle-' + arrowIcon + '"></i>';
																		}
																		else{
																			decimalPlace = 0;
																		}

																		$_compare.removeClass('red green').addClass(cssClass);
																		$_compare.html(arrowIcon + ' <strong>' + $.formatNumber(percentage, decimalPlace) + '%</strong>');
																	}
																}
																else{
																	value = 'N/A';
																}

																$_price.html(value);
															}
														});
													});
												}
											});

										//	redraw for stacked column chart so all axis get same value for min and max
											if(typeof window.GCharts[chartID] == 'object'){
												var max = 0;
												var min = 0;

												if(chartData.length){
													var counter		= 1;
													var lastIndex	= null;

													$.each(chartData, function(rowIndex, rowDetail){
														if(rowIndex > 0){
															$.each(rowDetail, function(colIndex, colDetail){
															//	col index 0 used by col name
																if(colIndex != 0){
																	if(counter > 1 && counter % 2 == 0){
																		var stackValue = parseFloat(colDetail) + parseFloat(rowDetail[lastIndex]);

																		max = stackValue > max ? stackValue : max;
																	}

																	counter++;
																	lastIndex = colIndex;
																}
															});
														}
													});
												}

											//	method 1
											//	var roundup = 1;

											//	if(max > 0){
											//		var	roundup = Math.round(parseInt(max) / 1000) * 1000;
											//	}

											//	method 2
												var roundup		= max.toString();
													roundup		= parseInt(roundup.substr(0, 1)) + 1;
												var padlength	= max.toString().length;

												if(roundup == 10){
													padlength++;
												}

												roundup = r_pad(roundup.toString(), padlength, '0');

												var options = $.googleChartOption($.extend(true, {
													vAxis : {
														viewWindow : {
															max	: roundup, 
															min	: 0, 
														},
													},
												}, chartOptions[chartType]));

												options		= google.charts.Bar.convertOptions(options);
												chartData	= google.visualization.arrayToDataTable($.map(chartData, function(value, index){
													var temp = $.map(value, function(subValue, subIndex){
														return subValue;
													});

													return [temp];
												}));

												$.googleChartDraw($('#' + chartID), chartData, options);
											}
										}
									}
								},
							});
						}
					});
				}
			}
		}
	}

//	render chart
	$.initChart();

	var autoRequestElements = $('[data-role="auto-request"]');

	if(autoRequestElements.length){
		autoRequestElements = autoRequestElements.filter(function(){
			return $(this).is('[data-url]') && $(this).data('url').length;
		});

		if(autoRequestElements.length){
			autoRequestElements.each(function(elemIndex, elemObject){
				var self		= $(this);
				var selfID		= $(this).attr('id');
				var selfClass	= $(this).attr('class');
				var dataUrl		= self.data('url');

			//	var targetSelector = '';

			//	if(selfID && selfID.length){
			//		targetSelector = '#' + selfID;
			//	}
			//	else if(selfClass && selfClass.length){
			//		targetSelector = '.' + selfClass.split(' ').join('.');
			//	}

			//	send as direct ajax link
				$.directAjaxLink({
					obj : self, 
				});
			});
		}
	}
});