<?php
		$user_principle_id = Configure::read('Principle.id');
		$dataCompany = !empty($dataCompany)?$dataCompany:false;

		$total_artikel	= Common::hashEmptyField($resume, 'total_artikel', false);
		$total_banner	= Common::hashEmptyField($resume, 'total_banner', false);

		$urlPropertyActive = false;
		$urlPropertySold = false;
		$urlEbrosur = false;

		if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
			$urlAgent = array(
				'controller' => 'reports',
				'action' => 'generate',
				'agents',
            );
			$urlPropertyActive = array_merge($urlAgent, array(
				'title' => 'Agen Berdasarkan Properti terbanyak',
				'sort' => 'total_property',
				'direction' => 'desc',
				'admin' => true,
            ));
            $urlPropertySold = array_merge($urlAgent, array(
				'title' => 'Agen Berdasarkan Properti Terjual terbanyak',
				'sort' => 'total_property_sold',
				'direction' => 'desc',
            ));
		}

?>
<div class="row">
	<div class="col-sm-8">
		<?php 	

				echo $this->element('blocks/users/dashboards/multiple_chart', array(
					'point_tooltip' => 'Total Pengunjung',
				));

				$total_listing_per_agent = empty($total_listing_per_agent) ? array() : $total_listing_per_agent;
				$template = '';

				// if($total_listing_per_agent){
				// 	$template.= $this->element('blocks/users/dashboards/table', array(
				// 		'title' => __('5 Agen dengan properti terbanyak'),
				// 		'wrapperClass' => 'wrapper-dashboard-table-property-active',
				// 		'values' => $total_listing_per_agent,
				// 		'daterangeClass' => 'daterange-dasboard-table-property-active',
				// 		'url' => $urlPropertyActive,
				// 		'urlTitle' => __('Lihat semua'),
				// 	));
				// }

				echo(empty($template) ? $template : $this->Html->tag('div', $template, array(
					'class' => 'row', 
				)));

		?>
	</div>
	<div class="col-sm-4">
		<div class="">
			<div class="row">
				<?php
						// echo $this->element('blocks/users/dashboards/box', array(
						// 	'frameClass' => 'col-sm-12 mb10',
						// 	'class' => 'blueclr',
						// 	'icon' => 'rv4-user',
						// 	'value' => $percentage['total_percentage'].'%',
						// 	'title' => __('Kelengkapan Profil'),
						// 	'url' => array(
						// 		'controller' => 'users',
						// 		'action' => 'edit',
						// 		'admin' => true,
						// 	),
						// 	'urlTitle' => __('Lengkapi Sekarang'),
						// ));
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'greenclr',
							'icon' => 'rv4-newspaper',
							'value' => $total_artikel,
							'title' => __('Artikel Tayang'),
							'url' => array(
								'controller' => 'blogs',
								'action' => 'index',
								'status' => 'status-active',
								'admin' => true,
							),
							'urlTitle' => __('Lihat Semua'),
						));
						echo $this->element('blocks/users/dashboards/box', array(
							'frameClass' => 'col-sm-12 mb10',
							'class' => 'orangeclr',
							'icon' => 'rv4-newspaper',
							'value' => $total_banner,
							'title' => __('Banner Tayang'),
							'url' => array(
								'controller' => 'pages',
								'action' => 'slides',
								'admin' => true,
							),
							'urlTitle' => __('Lihat Semua'),
						));
				?>
			</div>
		</div>
	</div>
</div>