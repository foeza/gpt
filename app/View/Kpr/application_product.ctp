<!-- BEGIN PAGE TITLE/BREADCRUMB -->
<?php
		$named = $this->Rumahku->filterEmptyField($this->params, 'named');
		$slugTheme = $this->Rumahku->filterEmptyField($dataCompany, 'Theme', 'slug');
		$mls_id		= $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');
		$classSection = $this->Kpr->widgetClass('section', $dataCompany);

		$label	= $this->Property->getNameCustom($property);
		$slug		= $this->Rumahku->toSlug($label);

		$urlProperty = array(
			'controller' => 'properties', 
			'action'	 => 'detail',
			'mlsid'		 => $mls_id,
			'slug'		 => $slug, 
			'admin'		 => FALSE,
		);

		if(in_array($slugTheme, array('EasyLiving'))){
			echo $this->element('blocks/common/sub_header', array(
	            'title' => __('Form Aplikasi'),
	        ));
		}else{
			$this->Html->addCrumb(__('Properti'), array(
				'controller' => 'properties',
				'action' => 'find',
				'admin' => false,
			));
			$this->Html->addCrumb($label, $urlProperty);
			$this->Html->addCrumb(__('Promo KPR'), array_merge(
				array(
				'controller' => 'kpr',
				'action' => 'select_product',
			), $named));
			$this->Html->addCrumb(__('Form Aplikasi'));
		}

		$modelName = 'KprApplication';
		echo $this->Form->create($modelName, array(
			'type' => 'file',
            'class' => 'form-group bank-kpr-form',
		));
		
?>
<!-- END PAGE TITLE/BREADCRUMB -->

<div class="content <?php echo $classSection; ?>" style="margin-bottom: 30px;">
	<div class="container">
		<?php 
				echo $this->element('blocks/common/flash').PHP_EOL;
		?>
	</div>
	<div class="container">
		<div class="app-setup">
			<?php
					echo $this->Html->tag('h2', __('Form aplikasi KPR'), array(
						'class' => 'hidden-print',
					));

					echo $this->Html->tag('p', __('Hanya dengan 2 langkah mudah, Anda dapat mengajukan aplikasi KPR melalui website kami.'), array(
						'class' => 'hidden-print',
					));
					echo $this->element('blocks/kpr/forms/product/tab_action');
			?>
			<div class="tab-content">
				<div id="appDetail" class="tab-pane fade active in" aria-labelledby="appTab">
					<hr>
					<div class="row">
						<div class="col-sm-8">
							<div class="content-description">
								<?php
									echo $this->Rumahku->setFormAddress( 'KprApplication' );
									
									echo $this->Html->tag('div', $this->element('blocks/kpr/kpr_btn_form', array(
										'modelName' => $modelName,
									)), array(
										'id' => 'kpr-btn-form',
										'class' => 'tab-content-kpr ',
									));
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
		$currRegionID = false;
		$currCityID = false;

		if( !empty($this->request->data[$modelName]['region_id']) ) {
			$currRegionID = $this->request->data[$modelName]['region_id'];
		}
		if( !empty($this->request->data[$modelName]['city_id']) ) {
			$currCityID = $this->request->data[$modelName]['city_id'];
		}

		echo $this->Form->hidden(sprintf('%s.current_region_id', $modelName), array(
			'id' => 'currRegionID',
			'value' => $currRegionID
		));
		echo $this->Form->hidden(sprintf('%s.current_city_id', $modelName), array(
			'id' => 'currCityID',
			'value' => $currCityID
		));
		echo $this->Form->end();
?>
