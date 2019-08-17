<?php
		$user_principle_id = Configure::read('Principle.id');
		$dataCompany = !empty($dataCompany)?$dataCompany:false;
		$isPersonalPage = Configure::read('Config.Company.is_personal_page');

		$total_listing_per_agent = !empty($total_listing_per_agent)?$total_listing_per_agent:false;
		$total_ebrosur = !empty($total_ebrosur)?$total_ebrosur:false;
		$total_listing = !empty($total_listing)?$total_listing:false;

		$company_group_id = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'group_id');
		$target_commission = $this->Rumahku->filterEmptyField($user, 'UserConfig', 'commission');
		$custom_target_commission = $this->Rumahku->getCurrencyPrice($target_commission);

		$urlPropertyActive = false;
		$urlPropertySold = false;
		$urlEbrosur = false;

		if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
			if( $company_group_id == 4 ) {
	            $urlEbrosur = array(
					'controller' => 'users',
					'action' => 'info_agents',
					$user_principle_id,
					'sort' => 'total_ebrosur',
					'direction' => 'desc',
					'admin' => true,
	            );
				$urlPropertyActive = array(
					'controller' => 'users',
					'action' => 'info_agents',
					$user_principle_id,
					'sort' => 'total_property',
					'direction' => 'desc',
					'admin' => true,
	            );
	            $urlPropertySold = array(
					'controller' => 'users',
					'action' => 'agents',
					'admin' => true,
					'sort' => 'total_property_sold',
					'direction' => 'desc',
	            );
				$urlPrimary = array(
					'controller' => 'users',
					'action' => 'agents',
					'sort' => 'total_primary',
					'direction' => 'DESC',
					'admin' => true,
				);
			} else {
				$urlAgent = array(
					'controller' => 'reports',
					'action' => 'generate',
					'agents',
	            );
	            $urlEbrosur = array_merge($urlAgent, array(
					'title' => 'Agen Berdasarkan eBrosur terbanyak',
					'sort' => 'total_ebrosur',
					'direction' => 'desc',
					'admin' => true,
	            ));
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
		}

		$urlUnPaidKPR = array(
			'controller' => 'kpr',
			'action' => 'index',
			'status' => 'unpaid',
			'admin' => true,
			'sort' => 'Kpr.created',
			'direction' => 'desc',
        );
?>
<div class="row">
	<div class="col-sm-8">
		<?php 	

				echo $this->element('blocks/users/dashboards/multiple_chart', array(
					'point_tooltip' => 'Total Pengunjung',
				));

				$top_ebrosurs = empty($top_ebrosurs) ? array() : $top_ebrosurs;
				$total_listing_per_agent = empty($total_listing_per_agent) ? array() : $total_listing_per_agent;
				$template = '';

				if($top_ebrosurs){
					$template.= $this->element('blocks/users/dashboards/table', array(
						'title' => __('5 Agen dengan eBrosur terbanyak'),
						'wrapperClass' => 'wrapper-dashboard-table-ebrosur',
						'values' => $top_ebrosurs,
						'daterangeClass' => 'daterange-dasboard-table-ebrosur',
						'url' => $urlEbrosur,
						'urlAjax' => array(
							'controller' => 'ajax',
							'action' => 'get_dashboard_ebrosurs',
							'admin' => true,
						),
						'urlTitle' => __('Lihat semua'),
						'labelName' => __('eBrosur'),
						'modelName' => 'UserCompanyEbrochure',
						'fieldName' => 'total',
					));
				}

				if($total_listing_per_agent){
					$template.= $this->element('blocks/users/dashboards/table', array(
						'title' => __('5 Agen dengan properti terbanyak'),
						'wrapperClass' => 'wrapper-dashboard-table-property-active',
						'values' => $total_listing_per_agent,
						'daterangeClass' => 'daterange-dasboard-table-property-active',
						'url' => $urlPropertyActive,
						'urlTitle' => __('Lihat semua'),
					));
				}

				echo(empty($template) ? $template : $this->Html->tag('div', $template, array(
					'class' => 'row', 
				)));

		?>
	</div>
	<div class="col-sm-4">
		<?php

			$authGroupID	= Configure::read('User.grup_id');
			$isAgent		= Common::validateRole('agent', $authGroupID);

			if($isAgent){
				echo($this->Html->tag('div', $this->element('blocks/users/dashboards/personal_page_info'), array(
					'class' => 'mb30', 
				)));
			}

			if(empty($isPersonalPage)){
				echo $this->element('blocks/users/dashboards/commissions');

				if(!empty($list_unpaid_provision)){

					?>
					<div class=row>
						<?php
								echo $this->element('blocks/users/dashboards/table', array(
									'title' => __('Provisi belum dibayarkan bank'),
									'wrapperClass' => 'wrapper-dashboard-table-ebrosur',
									'colClass' => 'col-sm-12',
									'label' => __('Klien'),
									'values' => $list_unpaid_provision,
									'daterangeClass' => 'daterange-dasboard-table-ebrosur',
									'url' => $urlUnPaidKPR,
									'is_calender' => false,
				                    'urlTitle' => __('Lihat semua'),
				                    'labelName' => __('Provisi'),
				                    'thClass' => 'taright',
				                    'modelName' => 'Kpr',
				                    'fieldName' => 'commission',
				                    'currency' => true,
				                    'optionLink' => array(
				                    	'link' => array(
				                    		'controller' => 'kpr',
				                    		'action' => 'application_detail',
				                    	),
				                    	'modelName' => 'KprBank',
				                    	'fieldName' => 'id',
				                    	
				                    ),
								));
						?>
					</div>
					<?php

				}

				echo $this->element('blocks/users/dashboards/chart_kpr', array(
					'controller' => 'kpr',
					'action' => 'index',
					'frameClass' => 'col-sm-12 mb10',
					'urlTitle' => __('Lihat Semua'),
				));
			}

		?>
		<div class="mt30">
			<div class="row">
				<?php

						if(empty($isPersonalPage)){
							echo $this->element('blocks/users/dashboards/box', array(
								'frameClass' => 'col-sm-12 mb10',
								'class' => 'blueclr',
								'icon' => 'rv4-stack',
								'value' => $custom_target_commission,
								'title' => __('Target Komisi'),
							));
						}

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