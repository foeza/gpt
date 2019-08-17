<?php 
		$bankKpr = !empty($bankKpr)?$bankKpr:false;
		$property = !empty($property)?$property:false;
		$promo_text = $this->Rumahku->filterEmptyField($bankKpr, 'Bank', 'promo_text');
		$_allowKpr = $this->Rumahku->_allowKpr($property);

		if( !empty($apply_kpr) ) {
			$loadSummaryActive = '';
			$kprBtnActive = 'active';
			
			$loadSummary = 'hide';
			$kprBtn = '';
		} else {
			$loadSummaryActive = 'active';
			$kprBtnActive = '';

			$loadSummary = '';
			$kprBtn = 'hide';
		}

		if( $this->theme == 'EasyLiving' ) {
			echo $this->element('blocks/common/sub_header', array(
	            'title' => __('Kalkulator KPR'),
	        ));
		}

		echo $this->Form->create('Kpr', array(
			'id' => 'KPRMainForm',
			'type' => 'file',
			// 'url' => array(
			// 	'controller' => 'kpr',
			// 	'action' => 'bank_calculator',
			// 	'apply' => 'kpr'
			// ),
		));
?>
<div class="content">
    <div class="container">
        <div class="row">
        	<div class="col-sm-12">
				<div id="wrapper-kpr">
					<?php 
							echo $this->Html->tag('h2', __('Hitung angsuran dan pinjaman KPR Anda.'), array(
								'class' => 'hidden-print'
							));
							echo $this->Html->tag('h2', __('Simulasi KPR.'), array(
								'class' => 'print-visible'
							));
							echo $this->Html->tag('p', __('Gunakan kalkulator KPR %s untuk memperkirakan angsuran per bulan cicilan KPR Anda, termasuk pajak dan asuransi. Cukup dengan memasukkan informasi detail pengajuan pada kolom dibawah ini, untuk menghitung angsuran dan pinjaman KPR Anda.', Configure::read('__Site.site_name')), array(
								'class' => 'hidden-print'
							));

							if(!empty($property)) {
								$mls_id = $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');
								$propertyName = $this->Property->getNameCustom($property);
								$propertySlug = $this->Rumahku->toSlug($propertyName);

								echo $this->Html->tag('label', sprintf(__('<strong>Simulasi Angsuran Properti</strong> #%s'), $this->Html->link($mls_id, FULL_BASE_URL.$this->Html->url(array(
									'controller'=> 'properties', 
									'action' => 'detail',
									'mlsid' => $mls_id,
									'slug'=> $propertySlug, 
									'admin'=> false
								)))), array(
									'class' => 'title hidden-print',
								));
								echo $this->Html->tag('label', sprintf(__('<strong>Simulasi Angsuran Properti</strong> #%s'), $mls_id), array(
									'class' => 'title visible-print',
								));
							}

					?>
					<div class="row">
						<!-- LEFT CONTENT -->
						<div class="col-lg-9 print-left">

							<div id="kpr-content">
								<div class="row">

									<!-- LEFT SIDEBAR -->
									<div class="col-sm-4 hidden-print calculator-kpr-credit">
									
										<?php
												echo $this->element('blocks/kpr/left_sidebar');
										?>	   
									</div>

									<!-- CENTER TABS -->
									<div class="col-sm-8">
										<div class="content-description">
											<div class="property-map kpr hidden-print">
												<div class="nav-kpr">
													<ul class="nav nav-pills kpr">
														<?php 
																echo $this->Html->tag('li', $this->Html->link(__('Rincian Pinjaman'), 'javascript:void(0);', array(
																	'class' => 'kpr-nav-tab',
																)), array(
																	'class' => $loadSummaryActive,
																	'role' => 'presentation',
																	'link' => 'loan-summary',
																));
																echo $this->Html->tag('li', $this->Html->link(__('Detil Angsuran'), 'javascript:void(0);', array(
																	'class' => 'kpr-nav-tab tab-installment-payment',
																)), array(
																	'role' => 'presentation',
																	'link' => 'installment-payment',
																));

																if( empty($property) || !empty($_allowKpr) ){
																	echo $this->Html->link(__('Ajukan Sekarang'), '#', array(
																		'id'	=> 'bnt-kpr', 
																		'class'	=> 'btn btn-orange pull-right kpr-nav-tab ajukan-btn '.$kprBtnActive,
																		'link'	=> 'kpr-btn-form',
																	));
																}
														?>
													</ul>
												</div>
											</div>
											<?php
													if( !empty($promo_text) ) {
														$promo_text = str_replace(PHP_EOL, '<br>', $promo_text);
														$promoContent = $this->Html->tag('label','<i class="fa fa-volume-down"></i>', array(
															'class' => 'kpr-icon-title'
														));
														$promoContent .= $this->Html->tag('div', $promo_text, array(
															'class' => 'kpr-note-description'
														));
														echo $this->Html->tag('div', $promoContent, array(
															'class' => 'promo-content',
														));
													}

													echo $this->Html->tag('div', $this->element('blocks/kpr/loan_summary', array(
														'params' => $loan_summary
													)), array(
														'id' => 'loan-summary',
														'class' => 'tab-content-kpr '.$loadSummary,
													));
													echo $this->Html->tag('div', $this->element('blocks/kpr/installment_payment', array(
														'params' => $kpr_data
													)), array(
														'id' => 'installment-payment',
														'class' => 'tab-content-kpr hide hidden-print',
													));
													echo $this->Html->tag('div', $this->element('blocks/kpr/kpr_btn_form'), array(
														'id' => 'kpr-btn-form',
														'class' => 'tab-content-kpr '.$kprBtn,
													));

													echo $this->element('blocks/kpr/action_kpr');
											?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- RIGHT CONTENT -->
						<div class="col-lg-3 sidebar-right-kpr print-right" id="sidebar">
							<?php 
									echo $this->element('blocks/kpr/contact_info');
							?>
						</div>
					</div>
					<?php 
							echo $this->Html->tag('div', $this->element('blocks/kpr/installment_payment', array(
								'params' => $kpr_data,
								'_print' => true,
							)), array(
								'id' => 'installment-payment',
								'class' => 'visible-print pagebreak',
							));
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>