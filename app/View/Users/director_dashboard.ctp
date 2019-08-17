<?php 		
		$total_listing_per_agent = !empty($total_listing_per_agent)?$total_listing_per_agent:false;
		$total_ebrosur = !empty($total_ebrosur)?$total_ebrosur:false;
		$total_listing = !empty($total_listing)?$total_listing:false;

		$target_commission = $this->Rumahku->filterEmptyField($user, 'UserConfig', 'commission');
		$custom_target_commission = $this->Rumahku->getCurrencyPrice($target_commission);

		$urlPropertyActive = false;
		$urlPropertySold = false;
		$urlEbrosur = false;
		// $urlPrimary = array(
		// 	'controller' => 'properties',
		// 	'action' => 'index',
		// 	'status' => 'premium',
		// 	'admin' => true,
		// );

		if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
			$urlPropertyActive = array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
				'sort' => 'total_property',
				'direction' => 'desc',
            );
            $urlPropertySold = array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
				'sort' => 'total_property_sold',
				'direction' => 'desc',
            );
            $urlEbrosur = array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
				'sort' => 'total_ebrosur',
				'direction' => 'desc',
            );
			$urlPrimary = array(
				'controller' => 'users',
				'action' => 'agents',
				'sort' => 'total_primary',
				'direction' => 'DESC',
				'admin' => true,
			);
		}

		$urlUnPaidKPR = array(
			'controller' => 'kpr',
			'action' => 'index',
			'status' => 'unpaid',
			'admin' => true,
			'direction' => 'desc',
        );
?>
<div class="row">
	<div class="col-sm-8">
		<?php 	

				echo $this->element('blocks/users/dashboards/multiple_chart', array(
					'point_tooltip' => 'Total Pengunjung',
				));
		?>
	</div>
	<div class="col-sm-4">
		<?php
				echo $this->element('blocks/users/dashboards/chart_kpr', array(
					'controller' => 'kpr',
					'action' => 'index',
					'frameClass' => 'col-sm-12 mb10',
					'urlTitle' => __('Lihat Semua'),
					'wrapperClass' => 'wrapper-selector mb30',
				));
		?>
		<div class="mt30">
			<div class="row">
				<?php 
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'blueclr',
							'icon' => 'rv4-stack',
							'value' => $custom_target_commission,
							'title' => __('Target Komisi'),
						));
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'greenclr',
							'icon' => 'rv4-user',
							'value' => $percentage['total_percentage'].'%',
							'title' => __('Kelengkapan Profil'),
							'url' => array(
								'controller' => 'users',
								'action' => 'edit',
								'admin' => true,
							),
							'urlTitle' => __('Lengkapi Sekarang'),
						));
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'yellowclr',
							'icon' => 'rv4-ribbon',
							'value' => $total_ebrosur,
							'title' => __('eBrosur dibuat'),
							'url' => array(
								'controller' => 'ebrosurs',
								'action' => 'index',
								'admin' => true,
							),
							'urlTitle' => __('Lihat Semua'),
						));
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'pinkclr',
							'icon' => 'rv4-building',
							'value' => $total_listing,
							'title' => __('Properti Tayang'),
							'url' => array(
								'controller' => 'properties',
								'action' => 'index',
								'status' => 'active-pending',
								'admin' => true,
							),
							'urlTitle' => __('Lihat Semua'),
						));
						// echo $this->element('blocks/users/dashboards/box', array(
						// 	'frameClass' => 'col-sm-12 mb10',
						// 	'class' => 'orangeclr',
						// 	'icon' => 'rv4-home',
						// 	'value' => $total_premium_listing,
						// 	'title' => __('Total Premium Listing Aktif'),
						// 	'url' => $urlPrimary,
						// 	'urlTitle' => __('Lihat Semua'),
						// ));

						// echo $this->element('blocks/users/dashboards/kpr/box_kpr', array(
						// 	'controller' => 'kpr',
						// 	'action' => 'index',
						// 	'frameClass' => 'col-sm-12 mb10',
						// 	'urlTitle' => __('Lihat Semua'),
						// ));
				?>
			</div>
		</div>
	</div>
</div>