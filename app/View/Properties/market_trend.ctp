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

	echo($this->element('blocks/market_trend/stat'));

?>
<div id="market-chart" class="market-chart-style">
	<div class="container">
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

				echo($this->element('blocks/market_trend/carousel', array(
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
<?php

	$propertyFilters = empty($propertyFilters) ? array() : $propertyFilters;

	echo($this->element('blocks/market_trend/filter'));

	echo($this->element('blocks/market_trend/summary', array(
		'summaries' => $summaries, 
	)));

	echo($this->element('blocks/market_trend/collection', array(
		'title'				=> 'Temukan Properti Menarik Di Area Ini', 
		'location'			=> $location, 
		'propertyFilters'	=> $propertyFilters, 
	)));

?>