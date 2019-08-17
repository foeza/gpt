(function ( $ ) {
    //  GOOGLE CHART HANDLER
    if(typeof google == 'object' && typeof google.charts == 'object'){
    //	global chart instance
    	window.GCharts = [];

    //	load chart libs
        google.charts.load('current', {
        	packages : ['controls', 'corechart', 'bar'], 
        	language : 'id', 
       	});

        $.googleChartDraw = function(googleCharts, data, options){
            if(typeof googleCharts == 'undefined'){
                googleCharts = $('[data-role="google-chart"]');
            }

            if(googleCharts.length){
            	googleCharts.each(function(){
					var self	= $(this);
					var chartID	= self.attr('id');

            		if(typeof window.GCharts[chartID] == 'object'){
						var chart = $.checkUndefined(window.GCharts[chartID].chart);
					}
					else{
						var chart = self.data('chart');
					}

					if(typeof chart != 'undefined'){
						if(typeof data == 'undefined' && typeof options == 'undefined'){
							if(typeof window.GCharts[chartID] == 'object'){					
								data	= $.checkUndefined(window.GCharts[chartID].data);
								options	= $.checkUndefined(window.GCharts[chartID].options);
							}
							else{			               
								data	= self.data('data');
								options	= self.data('options');
			                }
	            		}

						chart.draw(data, options);
	                }
	            });
            }
        }

        $.googleChartOption = function(options){
        	var defaultTextOpts = { color : '#666666', fontSize : 10, bold : false, italic : false };
            var chartOptions    = $.extend(true, {
                title           : '', 
                titleTextStyle  : '',
                width           : '100%',
                height          : '100%', 
            //  curveType       : 'function',
                backgroundColor : 'transparent',
                chartArea       : {
                    top     : 20,
                    left    : 50,
                    right   : 50, 
                    bottom  : 60, 
                    width   : '100%',
                    height  : '80%', 
                }, 
                legend      : 'none', 
            //  legend      : {
            //      position    : 'bottom', 
            //      textStyle   : defaultTextOpts
            //  }, 
                animation   : {
                    startup     : true, 
                    duration    : 500, 
                    easing      : 'out', 
                }, 
                vAxis       : {
                    title           : '', 
                    titleTextStyle  : '',
                    textPosition    : 'out', 
                    textStyle       : defaultTextOpts, 
                    gridlines       : {
                        count   : -1, // auto count the number of grid to be displayed, paksa manual, suka error rendernya ini pake auto
                        color   : '#DEDEDE', 
                        units   : {
                            years   : { format : ['yyyy'] },
                            months  : { format : ['MMM yy'] },
                            days    : { format : ['dd MMM yy'] },
                            hours   : { format : ['HH:mm:ss'] },
                            minutes : { format : ['HH:mm'] }, 
                        }, 
                    }, 
                }, 
                hAxis       : {
                    title           : '', 
                    titleTextStyle  : '',
                    textPosition    : 'out', 
                    textStyle       : defaultTextOpts, 
                    gridlines       : {
                        count   : -1, // auto count the number of grid to be displayed, paksa manual, suka error rendernya ini pake auto
                        color   : '#DEDEDE', 
                        units   : {
                            years   : { format : ['yyyy'] },
                            months  : { format : ['MMM yy'] },
                            days    : { format : ['dd MMM yy'] },
                            hours   : { format : ['HH:mm:ss'] },
                            minutes : { format : ['HH:mm'] }, 
                        }, 
                    }, 
                },
            }, options);

            return chartOptions;
        }

        $.googleChart = function(options){
            var settings    = $.extend({
                elementID   : 'google-chart', 
                type        : 'line', 
                dataSource  : null, 
                postData    : null, 
                options     : null, 
                onComplete  : null, 
            }, options);

            var chartCanvas = document.getElementById(settings.elementID); // can't use $('#' + elementID)

            if(chartCanvas){
                var chartPlaceholder	= $(chartCanvas).closest('.stat-graph-container');
                var chartDataSource		= typeof settings.dataSource != 'undefined' ? settings.dataSource : null;

                if(chartDataSource == null || validURL(chartDataSource) == false){
                    console.log('invalid data source.');
                    return false;
                }
                else{
                    var validTypes  = ['line', 'area', 'bar', 'material_bar', 'column', 'pie', 'donut'];
                    var chartType   = typeof settings.type != 'undefined' ? settings.type : 'line';
                    var chartLang   = typeof settings.options.lang != 'undefined' ? settings.options.lang : 'id';

                    if($.inArray(chartType, validTypes) < 0){
                        chartType = 'line';
                    }

                //  B : PREPARE THE CHART ====================================================================

                //  library load listener
                    google.charts.setOnLoadCallback(initChart);

                    function initChart(){
                        var chartOptions    = $.googleChartOption(settings.options);
                        var chartData		= null;

                        if(validURL(chartDataSource)){
                            chartDataSource = $.ajax({
                                type        : 'post', 
                                url         : chartDataSource,
                                data        : settings.postData, 
                                async       : false, 
                                beforeSend  : function(){
                                    $.loadingbar_progress('beforeSend');
                                },
                            }).always(function(){
                                $.loadingbar_progress('always');
                            }).responseText;

                            try{
                                chartDataSource = $.parseJSON(chartDataSource);

                            //  ada 3 key
                            //  data    : data hasil query (belum dimodifikasi)
                            //  chart   : data chart (formatted)
                            //  summary : data summary (hasil pengolahan data)

                                chartData = typeof chartDataSource.chart != 'undefined' ? chartDataSource.chart : chartDataSource;
                                chartData = $.map(chartData, function(value, index){
                                    var temp = $.map(value, function(subValue, subIndex){
                                        return subValue;
                                    });

                                    return [temp];
                                });
                            }
                            catch(e){
                                console.log('error:' + e);
                            };
                        }

                        if(typeof chartData != 'undefined' && $.isEmptyObject(chartData) === false){
                            var data = google.visualization.arrayToDataTable(chartData);
                            var chart;

                            switch(chartType){
                                case 'area' :
                                    chart = new google.visualization.AreaChart(chartCanvas);
                                    chartOptions.hAxis.gridlines.count = chartData.length;
                                    break;
                                case 'bar' :
									chart = new google.visualization.BarChart(chartCanvas); // ga bisa vertical
                                    chartOptions.hAxis.gridlines.count = chartData.length;
                                    break;
                                case 'material_bar' :
									chart = new google.charts.Bar(chartCanvas);
									chartOptions.hAxis.gridlines.count = chartData.length;

									chartOptions = google.charts.Bar.convertOptions(chartOptions);
								break;
                                case 'column' : 
                                    chart = new google.visualization.ColumnChart(chartCanvas);
                                    chartOptions.hAxis.gridlines.count = chartData.length;
                                    break;
                                case 'pie' :
                                    chart           = new google.visualization.PieChart(chartCanvas);
                                    chartOptions    = $.extend(true, {
                                        pieHole             : 0, 
                                        pieSliceTextStyle   : {
                                            color       : '#FFFFFF',
                                            fontSize    : 10, 
                                            bold        : true, 
                                        },
                                    }, chartOptions);
                                    break;
                                case 'donut' :
                                    chart           = new google.visualization.PieChart(chartCanvas);
                                    chartOptions    = $.extend(true, {
                                        pieHole             : 0.5, 
                                        pieSliceTextStyle   : {
                                            color       : '#333333',
                                            fontSize    : 10, 
                                            bold        : true, 
                                        },
                                    }, chartOptions);
                                    break;
                                default :
                                    chart = new google.visualization.LineChart(chartCanvas);
                                    chartOptions    = $.extend(true, {
                                        hAxis   : {
                                            gridlines : {
                                                count : chartData.length
                                            }
                                        },
                                        vAxis   : {
                                            gridlines : {
                                                count : -1
                                            }
                                        }, 
                                    }, chartOptions);
                                    break;
                            }

                        //  for chart redraw function, we need to store the options to each initialized chart
                        //  so we dont need to query/request the data source all over again
                        	var element = $('#' + settings.elementID);

                            if(element.length){
                            	window.GCharts[settings.elementID] = {
                            		'element'	: element, 
                            		'chart'		: chart, 
                            		'type'		: chartType,
                                    'data'			: data, 
                                    'options'		: chartOptions, 
                            	};

                                element.data({
                                    'chart'     : chart, 
                                    'data'      : data, 
                                    'options'   : chartOptions, 
                                });

                        	//	render
								$.googleChartDraw(element);
                            }

                            if($.isFunction(settings.onComplete)){
                            //  custom function, let that function decide what to do with the result
                            //	chartObject
                            //	response

                                settings.onComplete(chart, data, chartDataSource);
                            }
                        }
                        else{
                            console.log('Tidak ada data untuk ditampilkan.');
                            return false;
                        }
                    }

                //  E : PREPARE THE CHART ====================================================================
                }
            }
            else{
                console.log('no chart placeholder exist.');
                return false;
            }
        };
    }
}( jQuery ));