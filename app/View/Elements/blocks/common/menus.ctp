<?php 
		$_config = !empty($_config)?$_config:false;
		$dataCompany = !empty($dataCompany)?$dataCompany:false;
		$type = !empty($type)?$type:false;
		$active_menu = !empty($active_menu)?$active_menu:false;

		$is_launcher = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_launcher');
		$company_group_id = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'group_id');
		$is_admin_approval_cobroke = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_admin_approval_cobroke');
		$is_co_broke = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_co_broke');

		$isAdmin		= $this->Rumahku->_isAdmin();
		$isCompanyAdmin	= $this->Rumahku->_isCompanyAdmin();
		$isExpired		= Configure::read('__Site.is_expired');

		if( $company_group_id == 4 ) {
			$urlDashboard = array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'dashboard',
				'admin' => true,
			);
		} else {
			$urlDashboard = array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'account',
				'admin' => true,
			);
		}

		$contentDashboard = $this->Rumahku->link(__('Dashboard'), $urlDashboard, array(
			'data-active' => $active_menu,
			'data-icon' => 'rv4-dashboard',
		));

		// Profile
		// $labelMenuProfil = __('Profil');
		// $contentLiProfile = $this->Html->tag('li', $this->Rumahku->link(__('Informasi Dasar'), array(
		// 	'plugin' => false, 
		// 	'controller' => 'users',
		// 	'action' => 'edit',
		// 	'admin' => true,
		// )));

		// if( in_array($logged_group, array( 2 )) ) {
		// 	$contentLiProfile .= $this->Html->tag('li', $this->Rumahku->link(__('Informasi Profesi'), array(
		// 		'plugin' => false, 
		// 		'controller' => 'users',
		// 		'action' => 'profession',
		// 		'admin' => true,
		// 	)));

		// 	$contentLiProfile .= $this->Html->tag('li', $this->Rumahku->link(__('Media Sosial'), array(
		// 		'plugin' => false, 
		// 		'controller' => 'users',
		// 		'action' => 'social_media',
		// 		'admin' => true,
		// 	)));
		// }

		// Admin Primesystem
		$labelMenuAdminRku = __('User');
		$contentLiAdminRku = '';

		if( $logged_group == 20 ) {
			$contentLiAdminRku .= $this->Html->tag('li', $this->Rumahku->link(__('Admin Prime System'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'rku_admins',
				'admin' => true,
			)));
		}

		// API Developer
		$labelMenuDeveloper = __('Developer');
		$contentLiDeveloper = '';

		if( in_array($logged_group, array( 3,4,5 )) || !empty($isAdmin) ) {
			$contentLiDeveloper = $this->Html->tag('li', $this->Rumahku->link(__('Daftar Project'), array(
				'plugin' => false, 
				'controller' => 'projects',
				'action' => 'index',
				'admin' => true,
			)));

			$contentLiDeveloper .= $this->Html->tag('li', $this->Rumahku->link(__('My Request'), array(
				'plugin' => false, 
				'controller' => 'projects',
				'action' => 'list_request',
				'admin' => true,
			)));

			$contentLiDeveloper .= $this->Html->tag('li', $this->Rumahku->link(__('Developer'), array(
				'plugin' => false, 
				'controller' => 'projects',
				'action' => 'developers',
				'admin' => true,
			)));

		}

		// Group
		$labelMenuGroup = __('Group');
		$contentLiGroup = '';

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 11,19,20 )) ) {
			$contentLiGroup = $this->Html->tag('li', $this->Rumahku->link(__('Direktur'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'directors',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 4,11,19,20 )) ) {
			$contentLiGroup .= $this->Html->tag('li', $this->Rumahku->link(__('Admin'), array(
				'controller' => 'users',
				'action' => 'admins',
				'slug' => 'director',
				'admin' => true,
				'plugin' => false, 
			)));
		}

		// User
		$labelMenuUser = __('Company');
		$contentLiUser = '';

		if( $isAdmin || ( empty($isExpired) && (in_array($logged_group, array( 4,11,19,20 )) || in_array($company_group_id, array( 4 ))) ) ) {
			$contentLiUser = $this->Html->tag('li', $this->Rumahku->link(__('Principal'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'principles',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 3,11,19,20 )) ) {
			$contentLiUser .= $this->Html->tag('li', $this->Rumahku->link(__('Admin'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'admins',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 4,3,5,11,19,20 )) ) {
			$contentLiUser .= $this->Html->tag('li', $this->Rumahku->link(__('Agen'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
			)));
		}

		if( $logged_group == 20 ) {
			$contentLiUser .= $this->Html->tag('li', $this->Rumahku->link(__('Non Company'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => 'non_companies',
				'admin' => true,
			)));
		}

		// Properti
		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 2,3,5,11,19,20 )) && !in_array($company_group_id, array( 4 )) ) {
			$labelMenuProperti = __('Properti');
			$contentLiProperti = '';

			$contentLiProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Daftar Properti'), array(
				'plugin' => false, 
				'controller' => 'properties',
				'action' => 'index',
				'admin' => true,
			)));
			$contentLiProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Draft Properti'), array(
				'plugin' => false, 
				'controller' => 'properties',
				'action' => 'drafts',
				'admin' => true,
			)));
			$contentLiProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Kategori Properti'), array(
				'plugin' => false, 
				'controller' => 'properties',
				'action' => 'status_listing_categories',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && !in_array($company_group_id, array( 4 )) && in_array($logged_group, array( 2,3,5,11,19,20 )) && !empty($is_co_broke) ) {
			$labelMenuCoBroke = __('Co-Broke');
			$contentLiCoBroke = $this->Html->tag('li', $this->Rumahku->link(__('Co-Broke Channel'), array(
				'plugin' => false, 
				'controller' => 'co_brokes',
				'action' => 'index',
				'admin' => true,
			)));
			$contentLiCoBroke .= $this->Html->tag('li', $this->Rumahku->link(__('Listing saya'), array(
				'plugin' => false, 
				'controller' => 'co_brokes',
				'action' => 'me',
				'admin' => true
			)));
			$contentLiCoBroke .= $this->Html->tag('li', $this->Rumahku->link(__('Listing Co-Broke'), array(
				'plugin' => false, 
				'controller' => 'co_brokes',
				'action' => 'listing',
				'admin' => true
			)));
			
			if(!empty($is_admin_approval_cobroke) && in_array($logged_group, array(3,5,11,19,20))){
				$contentLiCoBroke .= $this->Html->tag('li', $this->Rumahku->link(__('Approval'), array(
					'plugin' => false, 
					'controller' => 'co_brokes',
					'action' => 'approval',
					'admin' => true
				)));
			}
		}

		// Laporan
		$labelMenuLaporanProperti = __('Laporan');
		$contentLiLaporanProperti = '';

		if( $isAdmin || ( in_array($company_group_id, array( 4 )) || in_array($logged_group, array( 11,19,20 )) ) ) {
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Growth'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'growth_add',
				'admin' => true,
			)));
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Performa'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'performance_add',
				'admin' => true,
			)));
		}
			
		if($isAdmin || empty($isExpired) && ( in_array($company_group_id, array( 4 )) || in_array($logged_group, array( 11,19,20 )) ) ) {
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Summary'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'summary_add',
				'admin' => true,
			)));
		}

		if($isAdmin || empty($isExpired) && in_array($logged_group, array( 3,4,5,11,19,20 )) ) {
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Agen'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'agent_add',
				'admin' => true,
			)));
		}
		
		if(empty($isExpired)){
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Klien'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'client_add',
				'admin' => true,
			)));
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Property'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'property_add',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && ( !in_array($company_group_id, array( 4 )) || in_array($logged_group, array( 11,19,20 )) ) ) {
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Pengajuan KPR'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'kpr_add',
				'admin' => true,
			)));
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Komisi'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'commission_add',
				'admin' => true,
			)));
		}

		if(empty($isExpired)){
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Pengunjung'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'visitor_add',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && ( !in_array($company_group_id, array( 4 )) || in_array($logged_group, array( 11,19,20 )) ) ) {
			$contentLiLaporanProperti .= $this->Html->tag('li', $this->Rumahku->link(__('Laporan Pesan'), array(
				'plugin' => false, 
				'controller' => 'reports',
				'action' => 'message_add',
				'admin' => true,
			)));
		}

		// Ebrosur
		$labelMenuEbrosur = __('eBrosur');
		$contentLiEbrosur = '';

		$isBrochure = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_brochure');

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 2,3,5,11,19,20 )) && $isBrochure && !in_array($company_group_id, array( 4 )) ){
			$contentLiEbrosur .= $this->Html->tag('li', $this->Rumahku->link(__('Daftar eBrosur'), array(
				'plugin' => false, 
				'controller' => 'ebrosurs',
				'action' => 'index',
				'admin' => true,
			)));
		}

		if($isAdmin || empty($isExpired) && !in_array($company_group_id, array( 4 )) && $isBrochure ){
			$contentLiEbrosur .= $this->Html->tag('li', $this->Rumahku->link(__('Request eBrosur'), array(
				'plugin' => false, 
				'controller' => 'ebrosurs',
				'action' => 'request_ebrosurs',
				'admin' => true,
			)));
		}

		// CRM
		$labelMenuCrm = __('Agenda & Kegiatan');
		$contentLiCrm = '';

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 2,3,5,11,19,20 )) && !in_array($company_group_id, array( 4 )) ) {
			$contentLiCrm.= $this->Html->tag('li', $this->Rumahku->link(__('Daftar Klien'), array(
				'plugin' => false, 
				'controller' => 'users',
				'action' => sprintf('%sclients', in_array($logged_group, array( 3,5,11,19,20 )) ? '' : 'agent_'),
				'admin' => true,
			)));
		}

		if($isAdmin || empty($isExpired) && !in_array($company_group_id, array( 4 )) ){
			$contentLiCrm .= $this->Html->tag('li', $this->Rumahku->link(__('Kegiatan (CRM)'), array(
				'plugin' => false, 
				'controller' => 'crm',
				'action' => 'projects',
				'admin' => true,
			)));
		}

		if($isAdmin || empty($isExpired) && in_array($logged_group, array( 3,4,5,11,19,20 )) ) {
			$contentLiCrm .= $this->Html->tag('li', $this->Rumahku->link(__('Group Email'), array(
				'plugin' => false, 
				'controller' => 'newsletters',
				'action' => 'lists',
				'admin' => true,
			)));
			$contentLiCrm .= $this->Html->tag('li', $this->Rumahku->link(__('Email'), array(
				'plugin' => false, 
				'controller' => 'newsletters',
				'action' => 'campaigns',
				'admin' => true,
			)));
		}
		
		if($isAdmin || empty($isExpired) && in_array($logged_group, array( 2,3,5,19,20 )) && !in_array($company_group_id, array( 4 )) ) {
			// KPR
			if( date('Y-m-d') <= '2016-08-01' ) {
				$newFeature = $this->Html->image('/img/new-feature.png');
			} else {
				$newFeature = false;	
			}

			$labelMenuKpr = sprintf('%s %s', __('KPR'), $newFeature);
			$contentLiKpr = false;
			
			if( in_array($logged_group, array( 2,3,5,19,20 )) ) {
				$contentLiKpr .= $this->Html->tag('li', $this->Rumahku->link(__('Pengajuan KPR'), array(
					'controller' => 'kpr',
					'action' => 'add',
					'admin' => true,
					'plugin' => false, 
				)));
			}

			$contentLiKpr .= $this->Html->tag('li', $this->Rumahku->link(__('Daftar Aplikasi KPR'), array(
				'controller' => 'kpr',
				'action' => 'index',
				'admin' => true,
				'plugin' => false, 
			)));
		}

		// ContentWeb
		if($isAdmin || empty($isExpired) && in_array($logged_group, array( 3,4,5,11,19,20 )) ) {
			$labelMenuContentWeb = __('Konten Web');
			$contentLiContentWeb = '';

			if( !empty($_config['UserCompanyConfig']['is_blog']) ){
				$contentLiContentWeb.= $this->Html->tag('li', $this->Rumahku->link(__('Daftar %s', Configure::read('Global.Data.translates.id.blog')), array(
					'plugin' => false, 
					'controller' => 'advices',
					'action' => 'index',
					'admin' => true,
				)));

				$contentLiContentWeb.= $this->Html->tag('li', $this->Rumahku->link(__('Kategori %s', Configure::read('Global.Data.translates.id.blog')), array(
					'plugin' => false, 
					'controller' => 'advices',
					'action' => 'advice_categories',
					'admin' => true,
				)));
			}

			$contentLiContentWeb.= $this->Html->tag('li', $this->Rumahku->link(__('Slide Utama'), array(
				'plugin' => false, 
				'controller' => 'pages',
				'action' => 'slides',
				'admin' => true,
			)));

			$contentLiContentWeb.= $this->Html->tag('li', $this->Rumahku->link(__('Partnership'), array(
				'plugin' => false, 
				'controller' => 'pages',
				'action' => 'partnerships',
				'admin' => true,
			)));

			if(!empty($_config['UserCompanyConfig']['is_career'])){
				$contentLiContentWeb .= $this->Html->tag('li', $this->Rumahku->link(__('Karir'), array(
					'plugin' => false, 
					'controller' => 'pages',
					'action' => 'careers',
					'admin' => true,
				)));
			}

			$labelMenuContentFaq = __('FAQ');
			$contentLiContentFaq = '';

			if(!empty($_config['UserCompanyConfig']['is_faq'])){
				$contentLiContentFaq.= $this->Html->tag('li', $this->Rumahku->link(__('FAQ'), array(
					'plugin' => false, 
					'controller' => 'pages',
					'action' => 'faqs',
					'admin' => true,
				)));

				$contentLiContentFaq.= $this->Html->tag('li', $this->Rumahku->link(__('Kategori FAQ'), array(
					'plugin' => false, 
					'controller' => 'pages',
					'action' => 'faq_categories',
					'admin' => true,
				)));
			}
		}

	//	if( in_array($logged_group, array( 20 )) ) {
		if( $isAdmin ){
			// Catalog
			$labelMenuCatalog = __('Catalog');
			$contentLiCatalog = $this->Html->tag('li', $this->Rumahku->link(__('Attributes'), array(
				'plugin' => false, 
				'controller' => 'settings',
				'action' => 'attributes',
				'admin' => true,
			)));
			$contentLiCatalog .= $this->Html->tag('li', $this->Rumahku->link(__('Attributes Set'), array(
				'plugin' => false, 
				'controller' => 'settings',
				'action' => 'attribute_sets',
				'admin' => true,
			)));
		}

		// Membership
		$labelMenuMembership = __('Membership');
		$contentLiMembership = FALSE;

		if(in_array($logged_group, array(3, 5, 19, 20))){
			$principleID	= Configure::read('Principle.id');
			$packageID		= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'membership_package_id');

		//	hanya yang sudah pernah membayar membership keluar menu renewal
			if($principleID && $packageID){
				$contentLiMembership.= $this->Html->tag('li', $this->Rumahku->link(__('Renewal'), array(
					'plugin'		=> FALSE, 
					'controller'	=> 'membership_orders',
					'action'		=> 'add',
					'admin'			=> TRUE,
					$principleID, 
				)));
			}

			$contentLiMembership.= $this->Html->tag('li', $this->Rumahku->link(__('Kontak PRIME'), array(
				'plugin'		=> FALSE, 
				'controller'	=> 'membership_orders',
				'action'		=> 'index',
				'admin'			=> TRUE,
			)));
		}

		if(in_array($logged_group, array(19, 20))){
			$contentLiMembership .= $this->Html->tag('li', $this->Rumahku->link(__('Voucher'), array(
				'plugin'		=> FALSE, 
				'controller'	=> 'vouchers',
				'action'		=> 'index',
				'admin'			=> TRUE,
			)));
			$contentLiMembership .= $this->Html->tag('li', $this->Rumahku->link(__('Paket Membership'), array(
				'plugin'		=> FALSE, 
				'controller'	=> 'memberships',
				'action'		=> 'index',
				'admin'			=> TRUE,
			)));

			$contentLiMembership .= $this->Html->tag('li', $this->Rumahku->link(__('Fitur Membership'), array(
				'plugin'		=> FALSE, 
				'controller'	=> 'membership_features',
				'action'		=> 'index',
				'admin'			=> TRUE,
			)));
		}

		// Finance
		$labelMenuFinance = __('Finance');
		$contentLiFinance = FALSE;

		if( in_array($logged_group, array(3, 5, 19, 20)) ){
			$contentLiFinance .= $this->Html->tag('li', $this->Rumahku->link(__('Daftar Invoice'), array(
				'plugin'		=> FALSE, 
				'controller'	=> 'payments',
				'action'		=> 'index',
				'admin'			=> TRUE,
			)));
		}

		// Pengaturan
		$labelMenuPengaturan = __('Pengaturan');
		$contentLiPengaturan = false;

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 11,19,20 )) ) {
			$contentLiPengaturan = $this->Html->tag('li', $this->Rumahku->link(__('Umum (Admin)'), array(
				'plugin' => false,
				'controller' => 'settings',
				'action' => 'general',
				'admin' => true,
			)));

			if( !empty($is_launcher) ) {
				$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Launcher'), array(
					'plugin' => false,
					'controller' => 'settings',
					'action' => 'launcher',
					'admin' => true,
				)));
			}
		}

		if( empty($isExpired) && in_array($logged_group, array( 3,5 )) && !in_array($company_group_id, array( 4 )) ) {
			$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Umum'), array(
				'plugin' => false,
				'controller' => 'settings',
				'action' => 'general_company',
				'admin' => true,
			)));
		}

		if( $isAdmin || empty($isExpired) && in_array($logged_group, array( 3,4,5,11,19,20 )) ) {
			$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Tampilan Website'), array(
				'plugin' => false,
				'controller' => 'settings',
				'action' => 'theme_selection',
				'admin' => true,
			)));
			
			// if( in_array($logged_group, array( 11,19,20 )) ) {
			// 	$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Profil Perusahaan'), array(
			// 		'plugin' => false,
			// 		'controller' => 'users',
			// 		'action' => 'company',
			// 		'admin' => true,
			// 	)));
			// 	$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Media Sosial Perusahaan'), array(
			// 		'plugin' => false,
			// 		'controller' => 'users',
			// 		'action' => 'company_social_media',
			// 		'admin' => true,
			// 	)));
			// }
		}

		if( in_array($logged_group, array( 19, 20 )) ) {
			$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Migrasi Data Rumahku'), array(
				'plugin' => false,
				'controller' => 'settings',
				'action' => 'migrate_company',
				'admin' => true,
			)));
		}

		$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Keamanan'), array(
			'plugin' => false,
			'controller' => 'users',
			'action' => 'security',
			'admin' => true,
		)));
		$contentLiPengaturan .= $this->Html->tag('li', $this->Rumahku->link(__('Edit Profil'), array(
			'plugin' => false,
			'controller' => 'users',
			'action' => 'edit',
			'admin' => true,
		)), array(
			'class' => 'mobile-only',
		));

		switch ($type) {
			case 'header':
				echo $this->Html->tag('li', $this->Html->tag('div', $contentDashboard, array(
					'class' => 'btn-group',
				)));

				// Profile
				// echo $this->Rumahku->_generateMenuTop($labelMenuProfil, 'rv4-user', true, $contentLiProfile);

				// Admin Primesystem
				if( !empty($contentLiAdminRku) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuAdminRku, 'rv4-user', true, $contentLiAdminRku);
				}

				// Group
				if( !empty($contentLiGroup) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuGroup, 'rv4-director', true, $contentLiGroup);
				}

				// User
				if( !empty($contentLiUser) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuUser, 'rv4-user-group', true, $contentLiUser);
				}

				// API Developer
				if( !empty($contentLiDeveloper) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuDeveloper, 'rv4-developer', true, $contentLiDeveloper);
				}

				// Properti
				if( !empty($contentLiProperti) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuProperti, 'rv4-building', true, $contentLiProperti);
				}

                // Co-Broke
                if( !empty($contentLiCoBroke) ) {
                    echo $this->Rumahku->_generateMenuTop($labelMenuCoBroke, 'rv4-cobroke', true, $contentLiCoBroke);
                }

				// echo $this->Html->tag('li', $this->Html->tag('div', $contentReport, array(
				//	 'class' => 'btn-group',
				// )));

				// Ebrosur
				if( !empty($contentLiEbrosur) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuEbrosur, 'rv4-ribbon', true, $contentLiEbrosur);
				}

				// CRM
				if( !empty($contentLiCrm) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuCrm, 'rv4-connect', true, $contentLiCrm);
				}

				// KPR
				if( !empty($contentLiKpr) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuKpr, 'rv4-kpr', true, $contentLiKpr);
				}

				// Finance
				if( !empty($contentLiFinance) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuFinance, 'rv4-cash', true, $contentLiFinance);
				}

				// ContentWeb
				if( !empty($contentLiContentWeb) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuContentWeb, 'rv4-doc-list', true, $contentLiContentWeb);
				}

				// ContentFaq
				if( !empty($contentLiContentFaq) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuContentFaq, 'rv4-doc-list', true, $contentLiContentFaq);
				}

				// Catalog
				if( !empty($contentLiCatalog) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuCatalog, 'rv4-doc', true, $contentLiCatalog);
				}

				// Membership
				if( !empty($contentLiMembership) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuMembership, 'rv4-admin', true, $contentLiMembership);
				}

				// Report
				if( !empty($contentLiLaporanProperti) ) {
					echo $this->Rumahku->_generateMenuTop($labelMenuLaporanProperti, 'rv4-pie-chart', true, $contentLiLaporanProperti);
				}

				// Pengaturan
				echo $this->Rumahku->_generateMenuTop($labelMenuPengaturan, 'rv4-gear', true, $contentLiPengaturan);

				echo $this->Html->tag('li', $this->Html->tag('div', $this->Rumahku->link(__('Log Out'), array(
					'plugin' => false,
					'controller' => 'users',
					'action' => 'logout',
					'admin' => true,
				), array(
					'data-active' => $active_menu,
					'data-icon' => 'rv4-lock',
				)), array(
					'class' => 'btn-group',
				)));
				break;
			
			default:

				echo $this->Html->tag('li', $contentDashboard);

				// Profile
				// echo $this->Rumahku->_generateMenuSide($labelMenuProfil, 'rv4-user', false, $active_menu, 'profile', $contentLiProfile);

				// Admin Primesystem
				if( !empty($contentLiAdminRku) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuAdminRku, 'rv4-user', false, $active_menu, 'adminRumahku', $contentLiAdminRku);
				}

				// Group
				if( !empty($contentLiGroup) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuGroup, 'rv4-director', false, $active_menu, 'group', $contentLiGroup);
				}

				// User
				if( !empty($contentLiUser) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuUser, 'rv4-user-group', false, $active_menu, 'company', $contentLiUser);
				}

				// API Developer
				if( !empty($contentLiDeveloper) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuDeveloper, 'rv4-developer', false, $active_menu, 'developer', $contentLiDeveloper);
				}

				// Properti
				if( !empty($contentLiProperti) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuProperti, 'rv4-building', false, $active_menu, 'property', $contentLiProperti);
				}

                // Co-Broke
                if( !empty($contentLiCoBroke) ) {
                    echo $this->Rumahku->_generateMenuSide($labelMenuCoBroke, 'rv4-cobroke', false, $active_menu, 'co_brokes', $contentLiCoBroke);
                }

				// Ebrosur
				if(!empty($contentLiEbrosur)){
					echo $this->Rumahku->_generateMenuSide($labelMenuEbrosur, 'rv4-ribbon', false, $active_menu, 'ebrosur', $contentLiEbrosur);
				}
				
				// CRM
				if( !empty($contentLiCrm) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuCrm, 'rv4-connect', false, $active_menu, 'crm', $contentLiCrm);
				}
				
				// KPR
				if( !empty($contentLiKpr) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuKpr, 'rv4-kpr', false, $active_menu, 'kpr', $contentLiKpr);
				}

				// Finance
				if( !empty($contentLiFinance) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuFinance, 'rv4-cash', false, $active_menu, 'finance', $contentLiFinance);
				}

				// ContentWeb
				if( !empty($contentLiContentWeb) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuContentWeb, 'rv4-doc-list', false, $active_menu, 'webContent', $contentLiContentWeb);
				}

				// ContentFaq
				if( !empty($contentLiContentFaq) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuContentFaq, 'rv4-doc-list', false, $active_menu, 'faq', $contentLiContentFaq);
				}

				// Catalog
				if( !empty($contentLiCatalog) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuCatalog, 'rv4-doc', false, $active_menu, 'catalog', $contentLiCatalog);
				}

				// Membership
				if( !empty($contentLiMembership) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuMembership, 'rv4-admin', false, $active_menu, 'membership', $contentLiMembership);
				}

				// Report
				// echo $this->Html->tag('li', $contentReport);
				if( !empty($contentLiLaporanProperti) ) {
					echo $this->Rumahku->_generateMenuSide($labelMenuLaporanProperti, 'rv4-pie-chart', false, $active_menu, 'report', $contentLiLaporanProperti);
				}

				// Pengaturan
				echo $this->Rumahku->_generateMenuSide($labelMenuPengaturan, 'rv4-gear', false, $active_menu, 'setting', $contentLiPengaturan);
				break;
		}
?>