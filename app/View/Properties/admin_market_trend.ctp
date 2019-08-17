<?php

	$location	= empty($location) ? array() : $location;
	$summaries	= empty($summaries) ? array() : $summaries;

	$lastSummaries		= Hash::get($summaries, 'last_period', array());
	$currentSummaries	= Hash::get($summaries, 'current_period', array());

	$regionID		= Hash::get($location, 'Region.id');
	$regionName		= Hash::get($location, 'Region.name');
	$cityID			= Hash::get($location, 'City.id');
	$cityName		= Hash::get($location, 'City.name');
	$subareaID		= Hash::get($location, 'Subarea.id');
	$subareaName	= Hash::get($location, 'Subarea.name');

	$period		= Hash::get($summaries, 'period');
	$periodFrom	= Hash::get($summaries, 'period_from');
	$periodTo	= Hash::get($summaries, 'period_to');
	$periodDate	= false;

	if($periodFrom || $periodTo){
		$periodDate = $this->Rumahku->getCombineDate($periodFrom, $periodTo);
	}

	echo($this->element('blocks/market_trend/stat'));

?>
<div id="market-chart">
	<div class="row">
		<div class="col-md-4">
			<div class="wrapper-selector mb30">
				<div class="dashbox">
					<div class="mtchart chart">
						<div class="chart-head">
							<h3 class="title">
								<i class="fa fa-lightbulb-o"></i> Ringkasan
							</h3>
						</div>
						<div class="chart-body">
							<div class="market-guide-style">
								<?php

									$fieldSelectors	= array(
										'price_movement' => '{n}.PropertyType.detail.jual.detail.terjual.property_price_percent', 
										'property_count' => '{n}.PropertyType.detail.jual.detail.terjual.property_count', 
									);
									
								//	bikin variable baru, soalnya key bakal di reindex, jadi patokan harus balik lagi ke master data
								//	cari max tinggal tarik pake array_shift, cari min tinggal pake array_pop (karena sorting desc)

								//	logic supaya bisa angkat 1 best tipe properti
								//	cek dulu semua data saingannya, minimmal ada 1 yang punya data (sesuaikan dengan field yang akan ditarik)

									$lastRawData		= Hash::get($lastSummaries, 'Summary.property_count.data', array());
									$currentRawData		= Hash::get($currentSummaries, 'Summary.property_count.data', array());
									$propertyComparison	= Common::statisticCompare($lastRawData, $currentRawData, $propertyTypes);
									$conclusions		= array();

									foreach($fieldSelectors as $fieldAffix => $fieldPath){
										$sortValues = Hash::extract($propertyComparison, $fieldPath);
										$sortValues = array_sum($sortValues);

										if($sortValues){
										//	movement
											$sortValues	= Hash::sort($propertyComparison, $fieldPath, 'desc', 'numeric');
											$maxValue	= array_shift($sortValues);
											$minValue	= array_pop($sortValues);

											$tempMax	= Hash::extract(array($maxValue), $fieldPath);
											$tempMin	= Hash::extract(array($minValue), $fieldPath);

											if(array_sum($tempMax) > 0){
												$conclusions[sprintf('max_%s', $fieldAffix)] = $maxValue;
											}

											$conclusions[sprintf('min_%s', $fieldAffix)] = $minValue;
										}
									}

									if(empty($conclusions)){
										echo($this->Html->tag('p', __('Tidak ada pergerakan harga pada periode ini.'), array(
											'align' => 'center', 
										)));
									}
									else{
									//	movement count
										$maxPriceMovement	= Hash::get($conclusions, 'max_price_movement', array());
										$maxPropertyCount	= Hash::get($conclusions, 'max_property_count', array());
										$message			= '';

										if($maxPriceMovement){
											$message = 'Berdasarkan data pergerakan harga di daerah %s, tipe properti %s menunjukkan';
										}

										if($maxPropertyCount){
											$typeName		= Hash::get($maxPropertyCount, 'PropertyType.name');
											$propertyCount	= Hash::get($maxPropertyCount, 'PropertyType.detail.jual.detail.terjual.property_count', 0);
											$totalProperty	= Hash::get($maxPropertyCount, 'PropertyType.detail.jual.property_count', 0);
											$percentage		= (100 / $totalProperty) * $propertyCount;

											$countAllProperty = Hash::extract($propertyComparison, '{n}.PropertyType.detail.jual.detail.{s}.property_count');
											$countAllProperty = array_sum($countAllProperty);

											$propertyCount		= number_format($propertyCount, 0, ',', '.');
											$totalProperty		= number_format($totalProperty, 0, ',', '.');
											$countAllProperty	= number_format($countAllProperty, 0, ',', '.');
											$percentageDecimal	= Hash::get(explode('.', $percentage), 1, 0);
											$percentageDecimal	= $percentageDecimal > 0.01 ? 2 : 0;
											$percentage			= number_format($percentage, $percentageDecimal, ',', '.');

											$message = '%s menjadi properti yang paling diminati di area ini, ';
											$message.= 'dengan penjualan sebanyak %s unit dari %s (%s%%), dengan total keseluruhan properti di area ini sebanyak %s unit.';

											$message = __($message, $typeName, $propertyCount, $totalProperty, $percentage, $countAllProperty);
										}

									//	echo conclusion
										echo($this->Html->tag('p', __($message)));
									}

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="wrapper-selector mb30">
				<div class="dashbox">
					<div class="mtchart chart">
						<div class="chart-head">
							<h3 class="title">
								<i class="fa fa-calendar"></i> <?php echo($periodDate); ?>
							</h3>
						</div>
						<div class="chart-body">
							<div class="market-guide-style">
								<div id="movement-detail-holder" class="property-data">
									<?php

										echo($this->element('blocks/market_trend/widget', array(
											'propertyTypes' => $propertyTypes, 
											'options'		=> array(
												'property_action_id'	=> 1, 
												'show_action_name'		=> false, 
												'data_source'			=> $this->Html->url(array(
													'admin'			=> false, 
													'controller'	=> 'properties', 
													'action'		=> 'property_statistic', 
													3,	// terjual
													'movement', 
													'period'	=> $period, 
													'region'	=> $regionID, 
													'city'		=> $cityID, 
													'subarea'	=> $subareaID, 
												), true), 
											), 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="market-chart-wrapper">
				<div class="market-chart-title">
					<div class="row">
						<div class="col-sm">
							<?php

								$currentArea = $subareaName ?: $cityName ?: $regionName;

								if($currentArea){
									$currentArea = $this->Html->tag('strong', $currentArea);

									echo($this->Html->tag('h1', __('Pergerakan Harga Properti %s', $currentArea), array(
										'class' => 'title', 
									)));
								}

							?>
						</div>
						<div class="col-sm">
							<div class="market-chart-periode">
								<div class="periode">
									<label for="">Periode</label>
									<div class="select-box disinblock">
										<select name="data[Chart][period]" class="small with-border" data-role="chart-period">
											<option value="6">6 Bulan</option>
											<option value="12">1 Tahun</option>
											<option value="36">3 Tahun</option>
											<option value="60">5 Tahun</option>
										</select>
									</div>
								</div>
								<div class="auto-toggle">
									<label for="">Auto View</label>
									<div class="toggle-button">
										<input type="checkbox" id="trend-carousel-toggle" checked="checked">
										<label for="trend-carousel-toggle"></label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php

					$propertyTypes	= empty($propertyTypes) ? array() : $propertyTypes;
					$dataSource		= array_merge(array(
						'admin'			=> false, 
						'controller'	=> 'properties', 
						'action'		=> 'property_statistic', 
					), $this->params->named);

					echo($this->element('blocks/market_trend/admin_carousel', array(
						'propertyTypes'	=> $propertyTypes, 
					)));

				?>
				<div class="market-chart-content">
					<?php

						echo($this->Html->tag('div', false, array(
							'id'			=> 'google-chart-1', 
							'class'			=> 'google-chart', 
							'data-role'		=> 'google-chart', 
							'data-type'		=> 'material_bar', 
							'data-source'	=> $this->Html->url($dataSource, true), 
						)));

					?>
				</div>
				<div class="market-chart-setup">
					<div class="chart-setup">
						<?php 

							if($propertyTypes){
								$typeList	= Hash::extract($propertyTypes, '{n}.PropertyType.name');
								$classes	= array('first', 'second', 'third', 'fourth');

								foreach($typeList as $key => $typeName){
									$class = sprintf('property-label-text %s', $classes[$key]);
									echo($this->Html->div('property-label-list', $this->Html->div($class, __($typeName))));
								}
							}

						?>
					</div>
					<div class="see-other-prop">
						<?php

							$linkOpts = array(
								'escape'	=> false, 
								'target'	=> '__blank', 
								'class'		=> 'green-link', 
							);

							$infoURL = $this->Html->url(array(
								'admin'				=> false, 
								'controller'		=> 'properties', 
								'action'			=> 'find', 
								'property_action'	=> 1, 
								'region'			=> $regionID, 
								'city'				=> $cityID, 
								'subarea'			=> $subareaID, 
							), true);

							echo($this->Html->link(__('Lihat properti lainnya'), $infoURL, $linkOpts));

						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>