<?php 
		$type 		 = !empty($type) ? $type : 'default';
		$type        = !empty($type)?$type:false;
		$_config     = !empty($_config)?$_config:false;
		$dataCompany = !empty($dataCompany)?$dataCompany:false;

		$isAdmin		= $this->Rumahku->_isAdmin();
		$isCompanyAdmin	= $this->Rumahku->_isCompanyAdmin();

		$isExpired 		= Configure::read('__Site.is_expired');
		$authGroupID	= Configure::read('User.group_id');
		$isAgent		= Common::validateRole('agent', $authGroupID);

		$urlDashboard = array(
			'plugin' => false, 
			'controller' => 'users',
			'action' => 'account',
			'admin' => true,
		);

		$data_arr = array(
			// Dashboard
			array(
				'icon' => 'rv4-dashboard',
				'name' => 'Dashboard',
				'url' => $urlDashboard,
				'active' => 'dashboard',
				'allow' => true,
			),
			// User
			array(
				'icon' => 'rv4-user',
				'name' => 'User',
				'url' => 'javaScript:void(0);',
				'data_submenu' => 'User',
				'class' => 'main-menu-user take-tour',
				'child' => array(
					array(
						// 'icon' => 'rv4-user-group',
						'name' => 'Admin',
						'url' => array(
							'plugin' => false, 
							'controller' => 'users',
							'action' => 'principles',
						),
						'active' => 'principal',
						'forbidden_allow' => ($isAdmin),
					),
				),
			),
			// Property
			array(
				'icon' => 'rv4-compose',
				'name' => 'Produk',
				'url' => 'javaScript:void(0);',
				'data_submenu' => 'Property',
				'child' => array(
					array(
						'name' => __('Daftar Kategori'),
						'url' => array(
							'plugin' => false, 
							'controller' => 'properties',
							'action' => 'product_category',
							'admin' => true,
						),
						'active' => 'property_category',
					),
					array(
						'name' => __('Daftar Produk'),
						'url' => array(
							'plugin' => false, 
							'controller' => 'properties',
							'action' => 'index',
							'admin' => true,
						),
						'active' => 'property_list',
					),
					// array(
					// 	'name' => __('Draft Properti'),
					// 	'url' => array(
					// 		'plugin' => false, 
					// 		'controller' => 'properties',
					// 		'action' => 'drafts',
					// 		'admin' => true,
					// 	),
					// 	'active' => 'property_draft',
					// ),

				),
			),
			
			// Finance
			// array(
			// 	'icon' => 'rv4-cash',
			// 	'name' => 'Finance',
			// 	'url' => 'javaScript:void(0);',
			// 	'data_submenu' => 'Finance',
			// 	'child' => array(
			// 		array(
			// 			'name' => __('Invoice Membership'),
			// 			'url' => array(
			// 				'plugin'		=> FALSE, 
			// 				'controller'	=> 'payments',
			// 				'action'		=> 'index',
			// 				'admin'			=> TRUE,
			// 			),
			// 			'active' => 'finance',
			// 		),
			// 		array(
			// 			'name' => __('Invoice Booking'),
			// 			'url' => array(
			// 				'plugin'		=> FALSE, 
			// 				'controller'	=> 'transactions',
			// 				'action'		=> 'invoice_booking',
			// 				'admin'			=> TRUE,
			// 			),
			// 			'active' => 'invoice_booking',
			// 		),
			// 		array(
			// 			'name' => __('Invoice User Integrasi'),
			// 			'url' => array(
			// 				'plugin' => false, 
			// 				'controller' => 'users',
			// 				'action' => 'list_registrant',
			// 				'admin' => true,
			// 			),
			// 			'active' => 'list_registrant',
			// 		),
			// 	),
			// ),
			// ContentWeb
			array(
				'forbidden_allow' => (!$isAgent), 
				'icon' => 'rv4-doc-list',
				'name' => 'Konten Web',
				'url' => 'javaScript:void(0);',
				'data_submenu' => 'ContentWeb',
				'child' => array(
					array(
						'name' => __('Artikel'),
						'url' => 'javaScript:void(0);',
						'data_submenu' => 'Advice',
						'child' => array(
							array(
								'name' => __('Daftar Artikel'),
								'url' => array(
									'plugin' => false, 
									'controller' => 'blogs',
									'action' => 'index',
									'admin' => true,
								),
								'active' => 'advice',
							),
							array(
								'name' => __('Kategori Artikel'),
								'url' => array(
									'plugin' => false, 
									'controller' => 'blogs',
									'action' => 'advice_categories',
									'admin' => true,
								),
								'active' => 'advice_category',
							),
						),
					),
					array(
						'name' => __('Banner'),
						'url' => array(
							'plugin' => false, 
							'controller' => 'pages',
							'action' => 'slides',
							'admin' => true,
						),
						'active' => 'slide',
					),
					// array(
					// 	'name' => __('Partnership'),
					// 	'url' => array(
					// 		'plugin' => false, 
					// 		'controller' => 'pages',
					// 		'action' => 'partnerships',
					// 		'admin' => true,
					// 	),
					// 	'active' => 'partnership',
					// ),
					// array(
					// 	'name' => __('Karir'),
					// 	'url' => array(
					// 		'plugin' => false, 
					// 		'controller' => 'pages',
					// 		'action' => 'careers',
					// 		'admin' => true,
					// 	),
					// 	'active' => 'career',
					// ),
					// // Faq
					// array(
					// 	// 'icon' => 'rv4-doc-list',
					// 	'name' => 'Faq',
					// 	'url' => 'javaScript:void(0);',
					// 	'data_submenu' => 'Faq',
					// 	'child' => array(
					// 		array(
					// 			'name' => __('FAQ'),
					// 			'url' => array(
					// 				'plugin' => false, 
					// 				'controller' => 'pages',
					// 				'action' => 'faqs',
					// 				'admin' => true,
					// 			),
					// 			'active' => 'faq',
					// 		),
					// 		array(
					// 			'name' => __('Kategori FAQ'),
					// 			'url' => array(
					// 				'plugin' => false, 
					// 				'controller' => 'pages',
					// 				'action' => 'faq_categories',
					// 				'admin' => true,
					// 			),
					// 			'active' => 'category_faq',
					// 		),
					// 	),
					// ),
					// // Catalog
					// array(
					// 	// 'icon' => 'rv4-doc',
					// 	'name' => 'Catalog',
					// 	'url' => 'javaScript:void(0);',
					// 	'data_submenu' => 'Catalog',
					// 	'child' => array(
					// 		array(
					// 			'name' => __('Attributes'),
					// 			'url' => array(
					// 				'plugin' => false, 
					// 				'controller' => 'settings',
					// 				'action' => 'attributes',
					// 				'admin' => true,
					// 			),
					// 			'active' => 'catalog',
					// 		),
					// 		array(
					// 			'name' => __('Attributes Set'),
					// 			'url' => array(
					// 				'plugin' => false, 
					// 				'controller' => 'settings',
					// 				'action' => 'attribute_sets',
					// 				'admin' => true,
					// 			),
					// 			'active' => 'catalog_set',
					// 		),
					// 	),
					// ),
				),
			),
			// Setting
			array(
				'icon' => 'rv4-gear',
				'name' => 'Pengaturan',
				'url' => 'javaScript:void(0);',
				'data_submenu' => 'Setting',
				'child' => array(
					array(
						'forbidden_allow' => (empty($isExpired)),
						'name' => __('Umum (Admin)'),
						'url' => array(
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'general',
							'admin' => true,
						),
						'active' => 'general',
					),
					array(
						// 'forbidden_allow' => ( empty($isExpired) && !in_array($company_group_id, array( 4 )) ),
						'name' => __('Umum'),
						'url' => array(
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'general_company',
							'admin' => true,
						),
						'active' => 'general_company',
					),
					array(
						'forbidden_allow' => ( empty($isExpired) ),
						'name' => __('Tampilan Website'),
						'url' => array(
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'theme_selection',
							'admin' => true,
						),
						'active' => 'theme_selection',
					),
					array(
						'name' => __('Keamanan'),
						'url' => array(
							'plugin' => false,
							'controller' => 'users',
							'action' => 'security',
							'admin' => true,
						),
						'active' => 'security',
					),
					array(
						'name' => __('Edit Profil'),
						'url' => array(
							'plugin' => false,
							'controller' => 'users',
							'action' => 'edit',
							'admin' => true,
						),
						'active' => 'profile'
					),
					array(
						'name' => __('Cache'),
						'url' => array(
							'plugin' => false,
							'controller' => 'settings',
							'action' => 'cache',
							'admin' => true,
						),
						'active' => 'setting_cache'
					),
				),
			),
		);
	
		switch ($type) {
			case 'default':
				$element = 'default';
				break;
			case 'header':
				$element = 'header';
				break;
		}
		echo $this->element(sprintf('blocks/common/menu/%s', $element), array(
			'data_arr' => $data_arr,
		));
?>