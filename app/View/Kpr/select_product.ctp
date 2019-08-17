<!-- BEGIN PAGE TITLE/BREADCRUMB -->
<?php
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
	            'title' => __('Promo KPR'),
	        ));
		}else{
			$this->Html->addCrumb(__('Properti'), array(
				'controller' => 'properties',
				'action' => 'find',
				'admin' => false,
			));
			$this->Html->addCrumb($label, $urlProperty);
			$this->Html->addCrumb(__('Promo KPR'));
		}

		// echo $this->element('blocks/kpr/forms/product/breadcrumb');
		echo $this->Form->create('Kpr', array(
			'type' => 'file',
            'class' => 'form-group bank-kpr-form',
		));
?>
<!-- END PAGE TITLE/BREADCRUMB -->


<!-- BEGIN CONTENT WRAPPER -->
<div class="content <?php echo $classSection; ?>" style="margin-bottom: 30px;">
	<div class="container">
		<?php 
				echo $this->element('blocks/common/flash').PHP_EOL;
		?>
		<div class="app-setup">
			<?php
					echo $this->Html->tag('h2', __('Hitung dan ajukan aplikasi KPR Anda.'), array(
						'class' => 'hidden-print',
					));
					echo $this->Html->tag('p', __('Hanya dengan 2 langkah mudah, Anda dapat mengajukan aplikasi KPR melalui website kami.'), array(
						'class' => 'hidden-print',
					));
					echo $this->element('blocks/kpr/forms/product/tab_action');
			?>

			<div class="tab-content">
				<div id="appDetail" class="tab-pane fade active in" aria-labelledby="appTab">
					<?php
							echo $this->element('blocks/kpr/forms/product/appSettings');
					?>
					<div class="bank-stack">
						<?php
								$h5 = $this->Html->tag('h5', __('Pilih bank penyelenggara KPR'));
										$p = $this->Html->tag('p', __('Anda hanya dapat mengajukan 1 promo dari setiap bank.'));
								echo $this->element('blocks/kpr/forms/product/bank_table', array(
									'modelName' => 'Kpr',
									'left_header' => sprintf('%s %s', $h5, $p),
								));
						?>
						<div class="app-control">
							<div class="row">
								<?php
										// echo $this->Html->tag('div', false, array(
										// 	'class' => 'col-sm-9',
										// ));
										//button 
										$button = $this->Html->link($this->Html->tag('span', __('Bandingkan')), array(
											'controller' => 'kpr',
											'action' => 'compare',
											'admin' => false,
											'plugin' => false,
										), array(
											'id' => 'btn-submit-comparison',
											'data-role' => '.bank-kpr-form',
											'class' => 'btn compare',
											'url-target-detail' => $this->Html->url(array(
												'controller' => 'kpr',
												'action' => 'ajax_compare_detail',
												'admin' => false,
											)),
											'escape' => FALSE, 
										)).
										$this->Form->button(__('Lanjut'), array(
											'type' => 'submit',
											'class' => 'btn next-step',
										)).
										$this->Html->link($this->Html->tag('span', __('Lihat Properti')), $urlProperty, array(
											'class' => 'btn default float-left',
											'escape' => FALSE, 
										));

										echo $this->Html->tag('div', $this->Html->tag('div', $button, array(
											'class' => 'button-group'
										)), array(
											'class' => 'col-sm-12 text-right',
										));
								?>
							</div>
						</div>
					</div>
				</div>
				<div id="formDetail" class="tab-pane fade" aria-labelledby="formTab">
					Detail Form
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END CONTENT WRAPPER -->
<?php
		echo $this->Form->hidden('view_name_product', array(
			'value' => true,
		));
		echo $this->Form->end();
?>