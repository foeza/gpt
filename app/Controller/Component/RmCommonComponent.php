<?php
class RmCommonComponent extends Component {
	var $components = array(
		'Email', 'Session', 'RequestHandler',
		'RmUser', 'Auth', 'Rest.Rest', 'Cookie', 'RmSetting',
		'Hybridauth', 
	);

	var $authPageDefaultPages = array(
		'pages'		=> array('blog' => 'is_blog', 'faq' => 'is_faq', 'developers' => 'is_developer_page', 'career' => 'is_career'), 
		'ebrosurs'	=> array('index' => 'is_brochure')
	);

	/**
	*	@param object $controller - inisialisasi class controller
	*/
	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	/**
	*
	*	mendapatkan nilai tanggal sekarang
	*
	*	@return string tanggal
	*/
	function currentDate( $formatDate = 'Y-m-d H:i:s' ){
		return date($formatDate);
	}
	
	function formatDate($dateString, $format = false, $empty = '-') {
		if( empty($dateString) || $dateString == '0000-00-00' || $dateString == '0000-00-00 00:00:00') {
			return $empty;
		} else {
			if( !empty($format) ) {
				return date($format, strtotime($dateString));
			} else {
				return $this->Time->niceShort(strtotime($dateString));
			}
		}
	}

	/**
	*
	*	set nilai pesan setelah melakukan suatu action pada controller
	*
	*	@param string $message - info dari pesan
	*	@param string $type 
	*		- string success - menyatakan pesan berhasil
	*		- string error - menyatakan pesan gagal
	*		- string info - menyatakan info dari suatu action
	*	@param array $params - parameter pendukung untuk pesan
	*	@param boolean $ajaxMsg - true jika di jalankan ketika event ajax, false jika tidak
	*	@param boolean $flash 
	*/
	function setCustomFlash($message, $type = 'success', $params=array(), $flash = true) {
		$flashType = 'flash_'.$type;
		if( $flash ){
			$this->Session->setFlash($message, $flashType, $params, $type);
		}

		if( $type == 'success' ) {
			$status = 1;
		} else if( $type == 'error' ) {
			$status = 0;
		} else {
			$status = $type;
		}

		$this->controller->set('msg', $message);
		$this->controller->set('status', $status);
	}

	function validateEmail($options = false) {
		if( !empty($options['SendEmail']) ) {
			$dataEmail = $this->filterEmptyField($options, 'SendEmail');
			$flagSendMail = true;

			if( empty($dataEmail[0]) ) {
				$dataEmails[0] = $dataEmail;
			} else {
				$dataEmails = $dataEmail;
			}

			foreach( $dataEmails as $value ) {
				$to_name = $this->filterEmptyField($value, 'to_name');
				$to_email = $this->filterEmptyField($value, 'to_email');
				$subject = $this->filterEmptyField($value, 'subject');
				$temp = $this->filterEmptyField($value, 'template');
				$data = $this->filterEmptyField($value, 'data');
				$debug = $this->filterEmptyField($value, 'debug');

				$include_role = $this->filterEmptyField($value, 'include_role');

				if(!empty($include_role)){
					unset($value['include_role']);
				}

				if( !empty($debug) ) {
					$data['debug'] = $debug;
				}

				if( !$this->sendEmail(
					$to_name,
					$to_email,
					$temp,
					$subject,
					$data
				) ) {
					$flagSendMail = false;
				}

				if(!empty($include_role)){
					$role = $this->filterEmptyField($include_role, 'role');
					$from_parent_id = $this->filterEmptyField($include_role, 'from_parent_id');

					$email_bcc = $this->getFieldRoleUser($role, 'email', $from_parent_id);

					if(in_array($to_email, $email_bcc) && isset($email_bcc[$to_email])){
						unset($email_bcc[$to_email]);
					}

					$data['bcc'] = $email_bcc;

					$this->sendEmail(
						'',
						$email_bcc,
						$temp,
						$subject,
						$data
					);
				}
			}

			return $flagSendMail;
		} else {
			return true;
		}
	}

	function sendEmail( $name = null, $email = null, $template='general', $subject=null, $params=null, $debug=false) {

		if( !empty($email) ) {
			$email_tipe = 'outside';
			$_layout = isset($params['layout']) ? $params['layout']:'default';
			$_attachments = !empty($params['_attachments']) ? $params['_attachments']:false;

			if(!$_layout){
				$_layout = 'plain';
			}

			$bcc = !empty($params['bcc'])?$params['bcc']:false;
			$from = !empty($params['from'])?$params['from']:false;
			
			if( empty($from) ) {
				$dataCompany = $this->filterEmptyField($params, 'dataCompany');
			
				if( !empty($dataCompany) ) {
					$fromCompany = $this->filterEmptyField($dataCompany, 'User', 'email');
					$from = $this->filterEmptyField($dataCompany, 'UserCompany', 'contact_email', $fromCompany);
				} else {
					$from = Configure::read('__Site.send_email_from');
				}
			}

			$params['_email'] = $email;
			$params['name'] = $name;
			$params['subject'] = $subject;

			if(isset($params['MailChimp']) && $params['MailChimp']){
				return $this->renderViewToVariable($template, $params);
			}else{
				$send_error = false;

				$this->Email->reset();
				
				/*
				* NOTE : "bcc" dengan "to" di pisah karena sistem yang berjalan sekarang tidak di perlukan email terusan dari mana, "to" untuk email akan kosong jika konfigurasi di jadikan "bcc" dan email satuan akan di limpahkan ke array "bcc"
				*/
				$data_email = array(
					'bcc' => $bcc,
					'email' => $email,
					'data' => $params
				);

				if(!empty($bcc)){
					$temp_bcc = array_map('trim', $bcc);
					$temp_bcc = array_filter($temp_bcc);
					
					$bcc = !empty($temp_bcc) ? $temp_bcc : array();

					if(!empty($email)){
						if(is_array($email)){
							/*email dan email bcc di gabung*/
							$bcc = array_merge($bcc, $email);

							/*untuk menghilangkan value dengan space*/
							$bcc = array_map('trim', $bcc);

							/*untuk menghapus value kosong*/
							$bcc = array_filter($bcc);
						}else if(is_string($email)){
							$email = trim($email);

							array_push($bcc, $email);
						}

						/*untuk menghilangkan array dengan value yang sama*/
						$bcc = array_unique($bcc);
					}
					
					if(!empty($bcc)){
						$this->Email->bcc = $bcc;
					}else{
						$send_error = true;
					}
				}else{
					if(is_array($email)){
						$email = array_map('trim', $email);
						$email = array_filter($email);
					}

					$this->Email->to = $email;
				}

				if($send_error == true){
					$this->Email->to = 'developer@rumahku.com';	

					$from = 'support@primesystem.id';
					$subject = '[PRIMEAGENT] ada kejanggalan LIST pengiriman email';
					$template = 'netral';

					$content_data = serialize($data_email);

					$params['content'] = $content_data;
				}
				
				$this->Email->from = $from;
				$this->Email->subject = $subject;
				$this->Email->template = $template;
				$this->Email->sendAs = 'both';

				if(!empty($_attachments)){
					if(!is_array($_attachments)){
						$_attachments = array($_attachments);
					}

					$this->Email->attachments = $_attachments;
				}

				if(isset($params['replyTo']) && $params['replyTo']) {
					$this->Email->replyTo = $params['replyTo'];
				}

				if($debug) {
					$this->Email->delivery = 'debug';
				} else {
					$this->Email->delivery = 'mail';
				}

				$this->Email->layout = $_layout;

				$this->controller->set(compact(
					'name', 'params', 'template', 
					'email_tipe'
				));

				if ($this->Email->send()) {
					return true;
				} else {
					return false;
				}
				exit();
			}
		} else {
			return false;
		}
	}

	function _set_global_variable ($data) {

		// S: init meta setting backend
		$UserCompany 	= $this->filterEmptyField($data, 'UserCompany');
		$company_name 	= $this->filterEmptyField($UserCompany, 'name', false, '');
		$subarea_name 	= $this->filterEmptyField($UserCompany, 'Subarea', 'name', '');
		$city_name 		= $this->filterEmptyField($UserCompany, 'City', 'name', '');
		$region_name 	= $this->filterEmptyField($UserCompany, 'Region', 'name', '');

		$url_without_http = Configure::read('__Site.domain');
		$principle_id 	= Common::hashEmptyField($data, 'User.id');

		// set tag title
		$meta_title = $company_name;
		$meta_title = $this->filterEmptyField($data, 'UserCompanyConfig', 'meta_title', $meta_title);

		// set meta description
		$meta_desc = sprintf(__('%s adalah situs jual beli berbagai busana dan atau keperluan fashion. Pusat Grosir Pasar Tasik. Toko Online Termurah dan Terpercaya.'), $url_without_http);

		$meta_desc = $this->filterEmptyField($data, 'UserCompanyConfig', 'meta_description', $meta_desc);
		// E: init meta setting backend

		// Mobile Belum di implementasi
		$isTablet = $this->controller->MobileDetect->detect('isTablet');
		$this->controller->mobile = $isMobile = $this->controller->MobileDetect->detect('isMobile');


	//	fonts
		$modelName		= 'GoogleFont';
		$googleFonts	= Cache::read(sprintf('%s.Data', $modelName));

		if(empty($googleFonts)){
			$this->$modelName = ClassRegistry::init($modelName);

			$googleFonts = $this->$modelName->getData('all', array(
				'cache' => sprintf('%s.Data', $modelName),
			));

			Cache::write(sprintf('%s.Data', $modelName), $googleFonts);
		}

		$googleFonts = Hash::combine($googleFonts, '{n}.{s}.name', '{n}.{s}.name');

		switch ($principle_id) {
			case '71404':
			case '135314':
			case '74126':
			case '135314':
				$blog_text_id = __('Training');
				$blog_text_en = __('Training');
				$blog_text_latest_id = __('Training');
				$blog_text_latest_en = __('Training');
				break;
			
			default:
				$blog_text_id = __('Berita');
				$blog_text_en = __('Blog');
				$blog_text_latest_id = __('Berita Terkini');
				$blog_text_latest_en = __('Latest Blog');
				break;
		}

		return array(
			'office_address' => array(
				'Wisma Slipi, Jl. S. Parman Kav. 12, Suite #318', 
				'Slipi, Jakarta 11480'
			),
			'office_phone' => array(
				'(021) 5332555', 
			),
			'office_fax' => array(
				'(021) 5332515'
			),
			'meta' => array(
				'title_for_layout' => $meta_title,
				'description_for_layout' => $meta_desc,
			),
			'facebook' => 'https://www.facebook.com/Primesystem.id/',
			'twitter' => 'https://twitter.com/primesystemid',
			'googleplus' => 'https://plus.google.com/u/1/111493357278233019320',
			'instagram' => 'https://www.instagram.com/primesystem.id/',
			'youtube' => 'https://www.youtube.com/channel/UC0hM7fkAMkdLakvv0BLVeDw',
			'yahoo_messenger'	=> 'rumahkucom@yahoo.com', 
			'blackberry_pin'	=> '23130777', 
			'whatsapp_number'	=> '082250577777', 
			'finance_email'		=> 'finance@primesystem.id', // apa ini benar email live 
			'finance_email_dev' => 'financeprimesystem@yopmail.com', // email buat development
			'membership_email'  => 'member@primesystem.id',
			'furnished' => array(
				'1' => __('Kosong'),
				'2' => __('Semi furnished'),
				'3' => __('Full furnished'),
			),
			'gender_options' => array(
				'1' => __('Laki-laki'),
				'2' => __('Perempuan'),
			),
			'status_marital' => array(
				'single' => __('Belum Menikah'),
				'marital' => __('Menikah'),
			),
			'sent_app_day' => array(
				'1' => __('1 Hari'),
	            '3' => __('3 Hari'),
	            '7' => __('7 Hari'),
			),
			'room_options' => array(
				'1' => __('≥ 1'),
				'2' => __('≥ 2'),
				'3' => __('≥ 3'),
				'4' => __('≥ 4'),
				'5' => __('≥ 5'),
				'6' => __('≥ 6'),
			),
			'lot_options' => array(
				'<100' => __('Dibawah 100'),
				'100-300' => __('100 - 300'),
				'300-500' => __('300 - 500'),
				'500-1000' => __('500 - 1,000'),
				'1000-3000' => __('1,000 - 3,000'),
				'3000-5000' => __('3,000 - 5,000'),
				'5000-10000' => __('5,000 - 10,000'),
				'10000-30000' => __('10,000 - 30,000'),
				'30000-50000' => __('30,000 - 50,000'),
				'>50000' => __('50,000 keatas'),
			),
			'price_options' => array(
				'0-50000000' => __('Dibawah 50 juta'),
				'50000000-100000000' => __('50 - 100 juta'),
				'100000000-200000000' => __('100 - 200 juta'),
				'200000000-500000000' => __('200 - 500 juta'),
				'500000000-800000000' => __('500 - 800 juta'),
				'800000000-1500000000' => __('800 juta - 1.5 miliar'),
				'1500000000-5000000000' => __('1.5 - 5 miliar'),
				'5000000000-10000000000' => __('5 - 10 miliar'),
				'10000000000-50000000000' => __('10 - 50 miliar'),
				'>50000000000' => __('50 miliar keatas'),
			),
			'operator_options' => array(
				'1' => '=',
				'2' => '<',
				'3' => '>',
				'4' => 'LIKE %s%',
				'5' => 'LIKE %%s',
				'6' => 'LIKE %%s%',
			),
			'google_font' => $googleFonts, 
			'font_type' => array(
				'Chelsea Market'=>'Chelsea Market',
				'Droid Serif'=>'Droid Serif',
				'Droid Sans'=>'Droid Sans', 
				'Ruluko'=>'Ruluko', 
				'Magra'=>'Magra', 
				'Esteban'=>'Esteban', 
				'Lora'=>'Lora', 
				'Jura'=>'Jura',
				'Hammersmith One'=>'Hammersmith One',
				'Lato'=>'Lato',
				'Vollkorn'=>'Vollkorn',
				'Ubuntu'=>'Ubuntu',
				'Dancing Script'=>'Dancing Script',
				'Open Sans'=>'Open Sans'
			),
			'web_colors' => array(
				'bg_color' => '',
				'main_content_color' => '',
				'font_color' => '',
				'button_color' => '',
				'font_heading_color' => '',
				'font_link_color' => '',
				'bg_footer' => '',
			),
			'ebrosur_colors' => array(
				'potrait' => array(
					'content_color' => 'rgba(0, 0, 0, 1)',
					'footer_color' => 'rgba(0, 0, 0, 1)',
				),
				'landscape' => array(
					'content_color' => 'rgba(0, 0, 0, 1)',
					'footer_color' => 'rgba(255, 255, 255, 1)',
				)
			),
			'launcher_colors' => array(
				'header_bg' => 'rgba(0, 0, 0, 1)',
				'body_bg_color' => 'rgba(255, 255, 255, 1)', 
				'footer_bg' => 'rgba(0, 0, 0, 1)',
				'footer_color' => 'rgba(255, 255, 255, 1)',
				'button_active_bg' => 'rgba(35, 35, 35, 1)',
				'button_active_color' => 'rgba(255, 255, 255, 1)',
				'button_bg' => 'rgba(225, 225, 225, 1)',
				'button_color' => 'rgba(35, 35, 35, 1)',
			),
			'theme_colors' => array(
				'easyliving' => array(
					'main_content_color' => 'rgba(84,80,152,1)',
					'sub_content_color' => 'rgba(255,255,255,1)',
					'button_color' => 'rgba(129,190,50,1)',
					'bg_color' => 'rgba(255,255,255,1)',
					'bg_color_top_header' => 'rgba(34, 34, 34, 1)',
					'font_type' => 'Proxima Nova Light, Helvetica, Arial',
					'font_size' => '11',
					'font_color' => 'rgba(70,70,70,1)',
					'font_menu_color' => 'rgba(70,70,70,1)',
					'font_heading_color' => '',
					'font_link_color' => 'rgba(129,190,50,1)',
					'tab_button' => 'rgba(84,80,152,1)',
					'tab_button_active' => 'rgba(74,71,134,1)',
					'footer' => 'rgba(78,76,108,1)',
					'font_menu' => '',
					'font_heading_footer_color' => '',
					'bg_footer' => '',
					'bg_header' => '',
				),
				'realsitematerial' => array(
					'main_content_color' => 'rgba(0,0,0,1)',
					'sub_content_color' => 'rgba(0,0,0,1)',
					'button_color' => 'rgba(233,30,99,1)',
					'bg_color' => 'rgba(250,250,250,1)',
					'bg_color_top_header' => 'rgba(34, 34, 34, 1)',
					'font_type' => 'Roboto, Arial, sans-serif',
					'font_size' => '14',
					'font_color' => 'rgba(117,117,117,1)',
					'font_menu_color' => 'rgba(117, 117, 117, 1)',
					'font_heading_color' => 'rgba(117, 117, 117,1)',
					'font_link_color' => 'rgba(236,64,122,1)',
					'tab_button' => '',
					'tab_button_active' => '',
					'footer' => '',
					'font_menu' => 'rgba(255,255,255,1)',
					'font_heading_footer_color' => '',
					'bg_footer' => '',
				),
				'cozy' => array(
					'main_content_color' => 'rgba(223,74,67,1)',
					'sub_content_color' => 'rgba(240,240,240,1)',
					'button_color' => 'rgba(173,178,182,1)',
					'bg_color' => 'rgba(255,255,255,1)',
					'bg_color_top_header' => 'rgba(34, 34, 34, 1)',
					'font_type' => 'Open Sans, sans-serif',
					'font_size' => '14',
					'font_color' => 'rgba(116,119,124,1)',
					'font_menu_color' => 'rgba(70,70,70,1)',
					'font_heading_color' => 'rgb(77,79,82)',
					'font_link_color' => 'rgba(223,74,67,1)',
					'tab_button' => '',
					'tab_button_active' => '',
					'footer' => '',
					'font_menu' => '',
					'font_heading_footer_color' => '',
					'bg_footer' => '',
				),
				'estato' => array(
					'main_content_color'		=> 'rgb(96,167,212)',
					'sub_content_color'			=> '',
					'button_color'				=> 'rgb(96,167,212)',
					'bg_color'					=> 'rgba(255,255,255,1)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'font_type'					=> '"Open Sans", sans-serif',
					'font_size'					=> '14',
					'font_color'				=> 'rgb(51,51,51)',
					'font_menu_color'			=> 'rgba(70,70,70,1)',
					'font_heading_color'		=> 'rgba(70,70,70,1)',
					'font_link_color'			=> 'rgb(51,51,51)',
					'font_heading_footer_color'	=> 'rgba(255,255,255,1)',
					'bg_footer'					=> 'rgb(51,51,51)',
				),
				'realspaces' => array(
					'main_content_color'		=> 'rgba(223,74,67,1)',
					'sub_content_color'			=> 'rgba(240,240,240,1)',
					'button_color'				=> 'rgba(173,178,182,1)',
					'bg_color'					=> 'rgba(255,255,255,1)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'font_type'					=> '"Open Sans", sans-serif',
					'font_size'					=> '14',
					'font_color'				=> 'rgb(102,102,102)',
					'font_menu_color'			=> 'rgb(102,102,102)',
					'font_heading_color'		=> 'rgb(102,102,102)',
					'font_link_color'			=> 'rgb(94,94,94)',
					'tab_button'				=> '',
					'tab_button_active'			=> '',
					'footer'					=> '',
					'font_menu'					=> '',
					'font_heading_footer_color'	=> 'rgb(102,102,102)',
					'bg_footer'					=> 'rgb(248,248,248)',
				),
				'suburb' => array(
					'main_content_color'		=> 'rgb(255,255,255)',
					'button_color'				=> 'rgb(58,125,227)',
					'bg_color'					=> 'rgb(245,245,245)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'font_type'					=> '"Open Sans", sans-serif',
					'font_size'					=> '14',
					'font_color'				=> 'rgb(31,33,38)',
					'font_menu_color'			=> 'rgb(53,57,59)',
					'font_heading_color'		=> 'rgb(53,57,59)',
					'font_link_color'			=> 'rgb(58,125,227)',
					'font_menu'					=> 'rgb(53,57,59)',
					'font_heading_footer_color'	=> 'rgb(255,255,255)',
					'bg_footer'					=> 'rgb(31,33,38)',
				),
				'realtyspace' => array(
					'bg_color'					=> 'rgba(255, 255, 255, 1)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'bg_footer'					=> 'rgb(34,34,34)',
					'button_color'				=> 'rgb(243,188,101)',
					'font_color'				=> 'rgb(44,62,80)',
					'font_heading_color'		=> 'rgba(70,70,70,1)',
					'font_heading_footer_color'	=> 'rgba(255,255,255,1)',
					'font_link_color'			=> 'rgb(190,190,190)',
					'font_menu_color'			=> 'rgb(44,62,80)',
					'font_size'					=> '14',
					'font_type'					=> '"Open Sans", sans-serif',
					'main_content_color'		=> 'rgb(0,187,170)',
				),
				'villareal' => array(
					'bg_color'					=> 'rgb(249, 249, 248)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'bg_footer'					=> 'rgb(73,69,69)',
					'button_color'				=> 'rgb(11,183,165)',
					'font_color'				=> 'rgb(50,50,50)',
					'font_heading_color'		=> 'rgba(70,70,70,1)',
					'font_heading_footer_color'	=> 'rgba(255,255,255,1)',
					'font_link_color'			=> 'rgb(11,183,165)',
					'font_menu_color'			=> 'rgb(50,50,50)',
					'font_size'					=> '14',
					'font_type'					=> '"Open Sans", sans-serif',
					'main_content_color'		=> 'rgb(11,183,165)',
				),
				'apartement' => array(
					'bg_color'					=> 'rgb(255,255,255)',
					'bg_color_top_header' 		=> 'rgba(34, 34, 34, 1)',
					'bg_footer'					=> 'rgb(21,31,43)',
					'button_color'				=> 'rgb(55,151,221)',
					'font_color'				=> 'rgb(137,137,137)',
					'font_heading_color'		=> 'rgb(93,93,93)',
					'font_heading_footer_color'	=> 'rgba(255,255,255,1)',
					'font_link_color'			=> 'rgb(11,183,165)',
					'font_menu_color'			=> 'rgb(93,93,93)',
					'font_size'					=> '14',
					'font_type'					=> 'Roboto, Arial, sans-serif',
					'main_content_color'		=> 'rgb(55,151,221)',
				),
				'bigcity' => array(
					'bg_color'					=> 'rgb(255,255,255)',
					'bg_color_top_header' 		=> 'rgb(34,34,34)',
					'bg_header' 				=> 'rgb(255,255,255)',
					'bg_footer'					=> 'rgb(21,31,43)',
					'button_color'				=> 'rgb(26,81,118)',
					'font_color'				=> 'rgb(137,137,137)',
					'font_link_color'			=> 'rgb(28,134,206)',
					'font_menu_color'			=> 'rgb(137,137,137)',
					'main_content_color'		=> 'rgb(26,81,118)',
					'font_heading_color'		=> 'rgb(93,93,93)',
					'font_heading_footer_color'	=> 'rgb(255,255,255)',
					'font_size'					=> '13',
					'font_type'					=> 'Lato, sans-serif',
				),
				'sunhouse' => array(
					'bg_header'					=> '',
					'bg_color'					=> '',
					'bg_color_top_header' 		=> '',
					'bg_footer'					=> '',
					'button_color'				=> '',
					'font_color'				=> '',
					'font_heading_color'		=> '',
					'font_heading_footer_color'	=> '',
					'font_link_color'			=> '',
					'font_menu_color'			=> '',
					'font_size'					=> '',
					'font_type'					=> '',
					'main_content_color'		=> '',
				),
				'thenest' => array(
					'bg_header'					=> '',
					'bg_color'					=> '',
					'bg_color_top_header' 		=> '',
					'bg_footer'					=> '',
					'button_color'				=> '',
					'font_color'				=> '',
					'font_heading_color'		=> '',
					'font_heading_footer_color'	=> '',
					'font_link_color'			=> '',
					'font_menu_color'			=> '',
					'font_size'					=> '',
					'font_type'					=> '',
					'main_content_color'		=> '',
				),
			),
			'theme_custom_badge' => array(
				'badge_color' => NULL,
			),
			'subjects' => array(
				'Feedback' => __('Feedback'),
				'Question and suggestion' => __('Question and suggestion'),
				'Account Support' => __('Account Support'),
				'Billing Support' => __('Billing Support'),
				'Technical Support' => __('Technical Support'),
				'Bug report' => __('Bug report'),
			),
			'translates' => array(
            	'en' => array(
	                'home' => __('Home'),
	                'property' => __('Property'),
	                'sell' => __('Sell'),
	                'rent' => __('Rent'),
	                'developers' => __('Developers'),
	                'agent' => __('Agents'),
	                'ebrosur' => __('E Brochure'),
	                'blog' => $blog_text_en,
	                'latest_blog' => $blog_text_latest_en,
	                'about' => __('About Us'),
	                'contact' => __('Contact Us'),
	                'faq' => __('FAQ'),
	                'career' => __('Career'),
	                'company' => __('Company'),

	                // S: RQ WIDIA REALTY INI PENAMBAHAN SEMENTARA minta penamaannya beda
	                'find-property' 	 => __('Find Property'),      // ini menu find property
	                'current-project' 	 => __('Current Project'),      // ini menu find property
	                'teams' 			 => __('Team'),               // ini menu agent
	                'eflyer' 			 => __('E-Flyer'),            // ini menu ebrosur
	                'properti-highlight' => __('Property Highlight'), // ini menu property highlight
	                'insight' 			 => __('Insight'),            // ini menu Berita
	                'join' 			     => __('Join A Team'),

	                // E: RQ WIDIA REALTY INI PENAMBAHAN SEMENTARA

            	),
            	'id' => array(
	                'home' => __('Beranda'),
	                'property' => __('Properti'),
	                'sell' => __('Dijual'),
	                'rent' => __('Disewakan'),
	                'developers' => __('Developers'),
	                'agent' => __('Agen'),
	                'ebrosur' => __('E-Brosur'),
	                'blog' => $blog_text_id,
	                'latest_blog' => $blog_text_latest_id,
	                'about' => __('Tentang Kami'),
	                'contact' => __('Hubungi Kami'),
	                'faq' => __('FAQ'),
	                'career' => __('Karir'),
	                'company' => __('Perusahaan'),
            	),
        	),
			'dateFilter' => array(
				'date',
				'modified',
				'last_login',
				'log_view',
				'date_range',
				'document_date',
				'sold_date',
			),
			// 'landing_page' => array( // local
				// 'main' => 'http://www.primesystem.id',
			// 	'agent' => 'http://ww.agent.primesystem.id/',
			// 	'developer' => 'http://ww.developer.primesystem.id/',
			// ),
			'landing_page' => array( // pasiris
				'main' => 'https://www.primesystem.id',
				'agent' => 'https://agentmembership.pasiris.com/',
				'developer' => 'https://devmembership.pasiris.com/',
			),
			'limit_dashboard' => array(
				'limit_top_agent' => __('Top Agen'),
		    	'limit_property_list' => __('Listing Properti'),
		    	'limit_property_popular' => __('Properti Terpopuler'),
		    	'limit_latest_news' => __('Berita Terkini'),
			),
			// 'landing_page' => array( // live
			// 	'agent' => 'http://agent.primesystem.id/',
			// 	'developer' => 'http://developer.primesystem.com/',
			// ),
			'MobileDetect' => array(
				'mobile' => $isMobile,
				'tablet' => $isTablet,
			),
			'periodePicker' => array(
				'1' => __('1 Bulan'),
				'3' => __('3 Bulan'),
				'6' => __('6 Bulan'),
				'12' => __('1 Tahun'),
			),
			'price_property' => array(
				'under_500jt' => '< 500 jt',
				'under_1m' => '500 jt - 1 m',
				'under_5m' => '1 m - 5 m',
				'under_10m' => '5 m - 10 m',
				'up_10m' => '> 10 m',
			),
			'source_client' => array(
				'child' => '< 26',
				'teens' => '26 - 35',
				'adult' => '36 - 50',
				'elderly' => '> 50',
			),
			'budget_client' => array(
				'under50jt' => __('< 50 Jt'),
                '50-100' => __('50 - 100 Jt'),
                '100-200' => __('100 - 200 Jt'),
                '200-500' => __('200 - 500 Jt'),
                '500-800' => __('500 - 800 Jt'),
                '800-1,5m' => __('800 Jt - 1,5 m'),
                '1,5m-5m' => __('1,5 m - 5 m'),
                '5m-10m' => __('5 m - 10 m'),
                '10m-50m' => __('10 m - 50 m'),
                'uper50m' => __('> 50 m'),
			),
			'property_log' => array(
				'add' => __('properti telah ditambah'),
				'edit' => __('Properti telah diedit'),
				'deleted' => __('Properti telah dihapus'),
				'active' => __('Properti telah diaktifkan'),
				'inactive' => __('Properti telah dinonaktifkan'),
				// 'feature' => __('Properti dalam tahap pratinjau'),
				'update' => __('Pratinjau telah diapproved'),
				'reject_update' => __('Pratinjau telah ditolak'),
				'sold' => __('Properti terjual'),
				'unsold' => __('Properti batal terjual'),
			),
			'history_options'	=> array(
				'promotion'	=> __('Promosi'), 
				'resign'	=> __('Resign'), 
				'recruit'	=> __('Rekruit'), 
				'update'	=> __('Update'), 
			), 
		);
	}

	function _callDefaultSessionName () {
		return 'Session.Property.%s';
	}

	function _callSessionName ( $draft_id = false ) {
		$strSession = $this->_callDefaultSessionName();

		if( empty($draft_id) ) {
			$draft_id = Configure::read('__Site.PropertyDraft.id');	 
		}

		if( !empty($draft_id) ) {
			$strSession = 'Session.PropertyDraft.%s.'.$draft_id;
		}

		return $strSession;
	}

	function _setConfigVariable ($data = array()) {
		// kebutuhan log properti untuk ambil slug action karena di afterSave(model) tidak bisa get params
		$params = $this->controller->params->params;
		Configure::write('__Site.params', $params);

	//	google plus client id, liat di http://console.developers.google.com/
		Configure::write('__Site.gauth', array(
			'client_id'			=> '194083791417-pj7h2b06094pl4fg6k27oqdv69k94pc3.apps.googleusercontent.com', 
			'client_secret'		=> 'y7kA-ResfpjOkO19awGcg3Jr', 
			'client_redirect'	=> 'http://agentmembership.pasiris.com/users/gauth', 
			'client_key'		=> false, 
			'client_version'	=> false, 
			'client_scopes'		=> array(
			//	scope (apa aja yang mau di akses dari user)
				'https://www.googleapis.com/auth/userinfo.profile', 
				'https://www.googleapis.com/auth/userinfo.email', 
				'https://www.googleapis.com/auth/plus.me', 
			), 
		));

	//	https://developers.facebook.com/
		Configure::write('__Site.facebook', array(
			'client_id'			=> '268939743710959', 
			'client_secret'		=> '62b952af4453c1a4f2ba57f7b037db31', 
			'client_redirect'	=> 'http://agentmembership.pasiris.com/users/facebook_connect', 
			'client_key'		=> false, 
			'client_version'	=> false, 
			'client_scopes'		=> array(
				'public_profile', 'email', 
			//	'publish_actions', 'user_birthday',
			//	'user_location', 'user_work_history',
			//	'user_hometown', 'user_photos','user_likes', 
			), 
		));

	//	youtube client id, liat di http://console.developers.google.com/
		Configure::write('__Site.youtube_client_id', '477037212613-ehk1rger43mm6ehelc5c0ic1ejf35am7.apps.googleusercontent.com');
		Configure::write('__Site.youtube_api_key', 'AIzaSyB7M5hGwCFfnM80dvU1nTKYI8KqxKvp0H0');

	//	recaptcha
		Configure::write('__Site.recaptcha_site_key', '6Le68jgUAAAAAABuczUbcu-8cRNvWzRp9wjEd6aL');
		Configure::write('__Site.recaptcha_secret_key', '6Le68jgUAAAAABrhFLYVTgGdVxv60WCj3bcE_VAV');

		Configure::write('__Site.company_profile', array(
			// 'name_premiere' => 'Rumahku.com',
			// 'name' => 'PT. NAGA LANGIT',
			// 'app_name' => 'Prime System',
			// 'ceo' => 'Robert Adrian',
			// 'address' => 'Wisma Slipi, Jl. S. Parman Kav. 12, Suite #318',
			// 'phone' => '(021) 5332555',
			// 'phone2' => '0822 505 77777',
			// 'email_name' => 'support Primesystem',
			// 'email' => 'support@primesystem.id',
			// 'link' => 'http://www.rumahku.com',
			// 'bank_account' => array(
			// 	'no_account' => '482-3000-777',
			// 	'bank_name' => 'BCA',
			// 	'npwp' => '03.093.658.7-031.000',
			// ),
		));

		// Image Path
		Configure::write('__Site.upload_path', APP.'Uploads');
		Configure::write('__Site.error_path', APP.'Error');
		Configure::write('__Site.recycle_bin_path', APP.'webroot'.DS.'img'.DS.'view'.DS.'recycle_bin'.DS); // harus pake trailing slash!
		Configure::write('__Site.thumbnail_view_path', APP.'webroot'.DS.'img'.DS.'view');
		Configure::write('__Site.webroot_files_path', APP.'webroot'.DS.'files');
		Configure::write('__Site.cache_view_path', '/img/view');

		// Options Image
		Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif'));
		Configure::write('__Site.allowed_all_ext', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx'));
		Configure::write('__Site.noticed_allowed_ext_document', __('* Hanya File berekstensi jpg, gif, png dan pdf'));
		Configure::write('__Site.max_image_size', 10242880);
		Configure::write('__Site.max_image_width', 2500);
		Configure::write('__Site.max_image_height', 2000);

		// Path Folder
		Configure::write('__Site.profile_photo_folder', 'users');
		Configure::write('__Site.file_folder', 'files');
		Configure::write('__Site.property_photo_folder', 'properties');
		Configure::write('__Site.logo_photo_folder', 'logos');
		Configure::write('__Site.advice_photo_folder', 'advices');
		Configure::write('__Site.badge_photo_folder', 'badges');
		Configure::write('__Site.general_folder', 'general');
		Configure::write('__Site.ebrosurs_photo', 'ebrosur');
		Configure::write('__Site.ebrosurs_template', 'ebrosur_template');
		Configure::write('__Site.document_folder', 'documents');
		Configure::write('__Site.report_folder', 'reports');
		Configure::write('__Site.fullsize', 'fullsize');

		$currencies = $this->controller->User->Property->Currency->getData('list', array(
			'fields' => array(
				'Currency.id', 'Currency.symbol',
			),
			'cache' => __('Currency.symbol'),
		));
		Configure::write('__Site.config_currency_code', 'IDR ');
		Configure::write('__Site.config_currency_symbol', 'Rp. ');
		Configure::write('__Site.Config.Currencies', $currencies);

		Configure::write('__Site.typeCommission', array(
			'agent',
			'rumahku'
		));

		$dimensionProfile = array(
			'ps'  => '50x50',
			'pm'  => '100x100',
			'pl'  => '150x150',
			'pxl' => '300x300',
		);
		Configure::write('__Site.dimension_profile', $dimensionProfile);

		$dimensionArr = array(
			's' => '150x84',
   			'm' => '300x169',
   			'l' => '855x481',
		);
		Configure::write('__Site.dimension', $dimensionArr);

		Configure::write('__Site.config_pagination', 10);
		Configure::write('__Site.config_new_table_pagination', 12);
		Configure::write('__Site.config_admin_pagination', 20);
		Configure::write('__Site.config_expired_listing', 720);
		Configure::write('__Site.config_expired_listing_in_year', 2);
		Configure::write('__Site.config_expired_rent', 120);

		// LIMIT CRONTAB
		Configure::write('__Site.config_limit_crontab', 15);
		Configure::write('__Site.config_limit_listing_home', 12);
		
		Configure::write('Facebook.appId', '113858472015943');
		Configure::write('__Site.Admin.List.id', array( 11,19,20 ));
		Configure::write('__Site.Admin.Company.id', array( 3,4,5 ));
		Configure::write('__Site.Role', array(
			'agent'				=> array(1, 2), 
			'independent_agent'	=> array(1), 
			'company_agent'		=> array(2), 
			'company_admin'		=> array(3, 4, 5), 
			'principal'			=> array(3), 
			'director'			=> array(4), 
			'head_liner'		=> array(3,4), 
			'admin'				=> array(11, 19, 20), 
		));

		$draft_id = $this->filterEmptyField($this->controller->params, 'named', 'draft');
		Configure::write('__Site.PropertyDraft.id', $draft_id);

		$sessionName = $this->_callSessionName();
		Configure::write('__Site.Property.SessionName', $sessionName);

		if(!empty($data)){
			$approval = $this->filterEmptyField($data, 'UserCompanyConfig', 'is_approval_property');

			Configure::write('Config.Approval.Property', $approval);
			Configure::write('Config.Company.data', $data);

		}

		Configure::write('__Site.Attribute.Type', array(
			'checkbox' 		=> __('Checkbox'),
			'email' 		=> __('Email'),
			'number' 		=> __('Number'),
			'payment' 		=> __('Pembayaran'),
			'phone_number' 	=> __('Phone Number'),
			'price' 		=> __('Price'),
			'sold' 			=> __('Proses Terjual/Tersewa'),
			'radio' 		=> __('Radio Button'),
			'select' 		=> __('Selectbox'),
			'option' 		=> __('Select Option'),
			'textarea' 		=> __('Textarea'),
			'text' 			=> __('Textbox'),
			'file' 			=> __('Upload File'),
			'image' 		=> __('Upload Image'),
		));

		Configure::write('__Site.site_wa', '');
		Configure::write('__Site.domain', $_SERVER['HTTP_HOST']);

		// config Rest
		Configure::write('__Site.is_rest', $this->Rest->isActive());

		Configure::write('__Site.Property.Status', array(
    		'active-pending' => __('Tayang/Aktif'),
    		'pending' => __('Pratinjau'),
    		'update' => __('Updated'),
    		'sold' => __('Terjual/Tersewa'),
    		'inactive' => __('Tidak Aktif/Rejected'),
		));
		Configure::write('__Site.UserCompany.Status', array(
        	'expired' => __('Expired'),
        	'active' => __('Active'),
    	));

		Configure::write('__Site.language', array(
			'id' => __('Indonesia'),
            'en' => __('Inggris'),
		));
		Configure::write('__Site.monthly', array(
			'named' => array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'),
			'options' => array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'),
			'getName' => array('Januari' => '1', 'Februari' => '2', 'Maret' => '3', 'April' => '4', 'mei' => '5', 'Juni' => '6', 'Juli' => '7', 'Agustus' => '8', 'September' => '9', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'),
		));

		Configure::write('__Site.days', array(
			'0' => 'Min',
			'1' => 'Sen',
			'2' => 'Sel',
			'3' => 'Rab',
			'4' => 'Kam',
			'5' => 'Jum',
			'6' => 'Sab',
		));

		Configure::write('__Site.textDays', array(
			1 => __('Senin'),
        	2 => __('Selasa'),
        	3 => __('Rabu'),
        	4 => __('Kamis'),
        	5 => __('Jumat'),
        	6 => __('Sabtu'),
        	7 => __('Minggu'),
		));

		if($this->Rest->isActive()){
			$credential_data = $this->Rest->credentials();

			$passkey = $this->filterEmptyField($credential_data, 'passkey');
			
			$this->Setting = ClassRegistry::init('Setting');
			
			$setting = $this->Setting->find('first', array(
				'conditions' => array(
					'token' => $passkey,
					'slug' => array('android', 'ios'),
				),
			));

			Configure::write('__Site.settings', $setting);

			Configure::write('__Site.Device.field', array(
				'android' => 'device_id_android',
				'iod' => 'device_id_ios',
			));
		}

		$certificates = $this->controller->User->Property->Certificate->getData('list', array(
            'group' => array(
                'Certificate.slug'
            ),
            'fields' => array(
                'Certificate.slug', 'Certificate.name',
            ),
            'cache' => 'Certificate.GroupSlug.List',
        ));
		Configure::write('__Site.Certificates', $certificates);

        $this->controller->set('certificates', $certificates);

		Configure::write('__Site.Report.GroupTypes', array(
        	'group' => __('Group Company'),
        	'company' => __('Company'),
    	));
    	Configure::write('__Site.Report.GroupProperty', array(
        	'properties' => __('Properti'),
        	'cities' => __('Kota'),
    	));
    	Configure::write('__Site.dataAgent', array(
			'Property' => array(
				'field' => 'user_id',
				'field_count' => 'property_count',
			),
			'UserClient' => array(
				'field' => 'agent_id',
				'field_count' => 'client_count',
			),
			'UserClientRelation' => array(
				'field' => 'agent_id',
				'field_count' => 'client_relation_count',
			),
		));

    //	market trend
		Configure::write('Config.MarketTrend', array(
			'max_type_displayed'	=> 4, 
			'property_cap'			=> 100, 
			'chart_color'			=> array('#DE1850', '#009CFF', '#FFAF48', '#955433'),	// sesuai max_type_displayed
			'default_property_type'	=> array('rumah', 'apartemen', 'ruko', 'tanah'), 
			'filter_tag'			=> array(
				'affordable', 
				'transport', 
				'education', 
				'healthcare', 
				'entertainment', 
			), 
		));

		Configure::write('Config.PaymentConfig', array(
			'payment_cash' => 98,
			'payment_channels' => array(
				//	payment method doku
				'04' => 'DOKU Wallet',
				'15' => 'Credit Card',

				//	payment method internal
				'98' => 'Cash',
				'99' => 'Bank Transfer', //'Saldo Deposit RumahKu',

				//	yang belom sempet di test (karna error dari sisi server test / hal lain)
				//	'02' => 'Mandiri Clickpay',
				//	'06' => 'e-Pay BRI',
				//	'03' => 'Klik BCA',
				//	'05' => 'ATM Transfer',
			),
			'payment_tenors' => array(
				'03' => __('3 Bulan'),
				'06' => __('6 Bulan'),
				'09' => __('9 Bulan'),
				'12' => __('12 Bulan'),
			),
			'transfer_status' => array(
	    		1 => __('Sudah Transfer'),
	    		'none' => __('Belum Transfer'),
			),
			'payment_type_status' => array(
	    		'paid' => __('Lunas'),
	    		'waiting' => __('Menunggu pembayaran'),
	    		'completed' => __('Completed'),
	    		'cancelled' => __('Dibatalkan'),
    			'expired' => __('Kadaluarsa')
			),
		));

		Configure::write('__Site.exlude_acos', array(
			'AclManager',
			'Ajax',
			'Api',
			'ApiKprs',
			'ApiProperties',
			'ApiUsers',
			'Crontab',
			'FileUpload',
			'Minify',
			'Vouchers',
			'Settings',
			'Profiles',
			'Memberships',
			'MembershipOrders',
			'MembershipFeatures',
			'Mailchimp',
			'Bank',
			'Groups',
			'Payments',
			'TargetRevenue',
			'Transactions',
		));

	}

	public function configureMembership(){
	//	doku dev
		$baseURL				= FULL_BASE_URL;
		$dokuIP					= '103.10.129.';
		$dokuMallID				= 2762;
		$dokuSharedKey			= '4W1JgkN98eDf';
		$dokuPaymentURL			= 'https://staging.doku.com/Suite/Receive';
		$dokuPaymentMIPURL		= 'https://staging.doku.com/Suite/ReceiveMIP';
		$dokuCheckPaymentURL	= 'https://staging.doku.com/Suite/CheckStatus';
		$membershipRequestURL	= $baseURL.'/memberships';
		$expiredHourDuration	= 3;	// 3 jam, liat settingan merchant di doku, default 3 jam, kalo di ganti disana ini juga ganti
		$invoicePrefix			= 'CW';	// prefix untuk switch antara dokumen transaksi V2 atau V4 (unique 2 digit)
		// $invoicePrefix			= 'CM';	// Mirror

		$siblingApp = array(
			'PR' => array(
				'name'		=> 'V4', 
				'url'		=> 'http://v4.pasiris.com/', 
				'notify'	=> 'http://v4.pasiris.com/invoices/notify', 
				'identify'	=> 'http://v4.pasiris.com/invoices/identify', 
				'redirect'	=> 'http://v4.pasiris.com/invoices/finalize', 
			), 
			'CW' => array(
				'name'		=> 'V2', 
				'url'		=> sprintf('%s/', $baseURL), 
				'notify'	=> sprintf('%s/admin/payments/notify', $baseURL), 
				'identify'	=> sprintf('%s/admin/payments/identify', $baseURL), 
				'redirect'	=> sprintf('%s/admin/payments/finalize', $baseURL), 
			), 
			'CM' => array(
				'name'		=> 'MirrorV2', 
				'url'		=> sprintf('%s/', $baseURL), 
				'notify'	=> sprintf('%s/admin/payments/notify', $baseURL), 
				'identify'	=> sprintf('%s/admin/payments/identify', $baseURL), 
				'redirect'	=> sprintf('%s/admin/payments/finalize', $baseURL), 
			), 
			'PMI' => array(
				'name'		=> 'V2', 
				'url'		=> sprintf('%s/', $baseURL), 
				'notify'	=> sprintf('%s/admin/PartnerMedias/notify/', $baseURL), 
				'identify'	=> sprintf('%s/admin/PartnerMedias/identify/', $baseURL), 
				'redirect'	=> sprintf('%s/admin/PartnerMedias/finalize/', $baseURL), 
			), 
		);

	//	doku live
	/*
		$baseURL				= $this->manage_base_url();
		$dokuIP					= '103.10.129.';
		$dokuMallID				= 1246;
		$dokuSharedKey			= '4W1JgkN98eDf';
		$dokuPaymentURL			= 'https://pay.doku.com/Suite/Receive';
		$dokuPaymentMIPURL		= 'https://pay.doku.com/Suite/ReceiveMIP';
		$dokuCheckPaymentURL	= 'https://pay.doku.com/Suite/CheckStatus';
		$membershipRequestURL	= $baseURL.'/memberships';
		$expiredHourDuration	= 3;	// 3 jam, liat settingan merchant di doku, default 3 jam, kalo di ganti disana ini juga ganti
		$invoicePrefix			= 'CW';	// prefix untuk switch antara dokumen transaksi V2 atau V4 (unique 2 digit)

	//	config aplikasi lain yang bisa di switch
		$siblingApp = array(
			'PR' => array(
				'name'		=> 'V4', 
				'url'		=> 'http://www.rumahku.com/', 
				'notify'	=> 'http://www.rumahku.com/invoices/notify/', 
				'identify'	=> 'http://www.rumahku.com/invoices/identify/', 
				'redirect'	=> 'http://www.rumahku.com/invoices/finalize/', 
			), 
			'CW' => array(
				'name'		=> 'V2', 
				'url'		=> sprintf('%s/', $baseURL), 
				'notify'	=> sprintf('%s/admin/payments/notify/', $baseURL), 
				'identify'	=> sprintf('%s/admin/payments/identify/', $baseURL), 
				'redirect'	=> sprintf('%s/admin/payments/finalize/', $baseURL), 
			), 
		);
	*/

		Configure::write('__Site.doku_ip', $dokuIP);
		Configure::write('__Site.doku_mall_id', $dokuMallID);
		Configure::write('__Site.doku_shared_key', $dokuSharedKey);
		Configure::write('__Site.doku_payment_url', $dokuPaymentURL);
		Configure::write('__Site.doku_payment_mip_url', $dokuPaymentMIPURL);
		Configure::write('__Site.doku_check_payment_url', $dokuCheckPaymentURL);
		Configure::write('__Site.membership_request_url', $membershipRequestURL);
		Configure::write('__Site.expired_hour_duration', $expiredHourDuration);
		Configure::write('__Site.invoice_prefix', $invoicePrefix);
		Configure::write('__Site.sibling_application', $siblingApp);

		Configure::write('__Site.payment_channels', array(
		//	'01' => 'Credit Card Visa/Master IDR',
		//	'02' => 'Mandiri ClickPay',
		//	'03' => 'KlikBCA',
			'05' => 'ATM Transfer',
		//	'06' => 'BRI e-Pay',
		//	'07' => 'ATM Permata VA',
		//	'08' => 'Mandiri Multipayment LITE',
		//	'09' => 'Mandiri Multipayment',
		//	'10' => 'ATM Mandiri VA LITE',
		//	'11' => 'ATM Mandiri VA',
		//	'12' => 'PayPal',
		//	'13' => 'BNI Debit Online (VCN)',
		//	'14' => 'Alfamart',
			'15' => 'Kartu Kredit',
			'04' => 'DOKU Wallet',
		//	'16' => 'Tokenization',
		//	'17' => 'Recur',
		//	'18' => 'KlikPayBCA',
		//	'19' => 'CIMB Clicks',
		//	'20' => 'PTPOS',
		//	'21' => 'Sinarmas VA Full',
		//	'22' => 'Sinarmas VA Lite',
		//	'23' => 'MOTO', 
		));

		Configure::write('__Site.payment_tenors', array(
			'03' => '3 Bulan', 
			'06' => '6 Bulan', 
			'09' => '9 Bulan', 
			'12' => '12 Bulan', 
		));

		Configure::write('__Site.invoice_statuses', array(
			'pending'	=> __('Pending'), 
			'process'	=> __('Process'), 
			'cancelled' => __('Cancelled'), 
			'expired'	=> __('Expired'), 
			'paid'		=> __('Paid'), 
			'failed'	=> __('Failed'), 
			'waiting'	=> __('Waiting'), 
		));

		Configure::write('__Site.Global', array(
			'Share' => array(
				'Sosmed' => array(
					'facebook' => __('Facebook'),
					'twitter' => __('Twitter'),
					'googleplus' => __('Google Plus'),
					'pinterest' => __('Pinterest'),
					'linkedin' => __('LinkedIn'),
					'whatsapp' => __('Whatsapp'),
				),
				'Module' => array(
					'property' => __('Property'),
					'berita' => __('Berita'),
					'ebrosur' => __('EBrosur'),
					'developer' => __('Developer'),
					'project_list_product' => __('Project List Product'),
					'project_list_unit' => __('Project List Unit'),
					'project_detail_unit' => __('Project Detail Unit'),
				),
			),
			'Expert' => array(
				'PointConditions' => array(
                	'equal' => __('Sama Dengan'),
                	'less_than' => __('Kurang Dari'),
                	'between' => __('Diantara'),
                	'more_than' => __('Lebih Dari'),
                ),
				'ActionConditions' => array(
                	'equal' => __('='),
                	'less_than' => __('<'),
                	'more_than' => __('>'),
                ),
				'EqualLessConditions' => array(
                	'equal',
                	'less_than',
                ),
			),
			'Variable' => array(
				'CRM' => array(
					'Cancel' => 6,
					'Closing' => 3,
					'Completed' => array( 5 ),
					'Finalisasi' => array( 4 ),
					'Prospect' => 1,
					'HotProspect' => 2,
					'DataClosing' => array( 8,4,5 ),
					'DevicePlatform' => array(
						'BB' => __('BB'),
						'Line' => __('Line'),
						'Whatsapp' => __('Whatsapp'),
						'Lainnya' => __('Lainnya'),
					),
					'AttributeOpton' => array(
						'limit_id' => '6',
					),
				),
				'ListContentLabel' => array(
					'sosmed' => 4,
				),
				'Company' => array(
					'agent' => 2,
					'client' => 10,
				),
				'KPR' => array(
					'status_progress' => array(
						'approved_bi_checking',
						'approved_verification',
						'approved_bank',
						'approved_credit',
					),
					'status_color' => array(
						'hold' => '#111E20',
						'pending' => '#666',
						'process' => '#F0B865',
						'approved_proposal' => '#F0B865',
						'approved_bi_checking' => '#F0B865',
						'approved_verification' => '#F0B865',
						'process_appraisal' => '#F0B865',
						'rejected_verification' => '#FF0000',
						'rejected_bi_checking' => '#FF0000',
						'rejected' => '#FF0000',
						'cancel' => '#FF0000',
						'rejected_proposal' => '#FF0000',
						'rejected_bank' => '#FF0000',
						'rejected_credit' => '#FF0000',
						'approved_bank' => '#4B3FA9',
						'approved' => '#4B3FA9',
						'credit_process' => '#4B3FA9',
						'approved_credit' => '#C057D8',
						'completed' => '#069D54',
					),
					'list_status_progress' => array(
						'approved_bi_checking' => array(
							'num' => 1,
							'text' => __('Lulus BI Checking'),
						),
						'approved_verification' => array(
							'num' => 2,
							'text' => __('Lulus Dokumen'),
						),
						'approved_bank' => array(
							'num' => 3,
							'text' => __('Appraisal'),
						),
						'approved_credit' => array(
							'num' => 4,
							'text' => __('Akad Disetujui'),
						),
						'completed' => array(
							'num' => 5,
							'text' => __('Complete'),
						),
					),
					'document_client' => array( 2,6 ),
					'document_client_spouse' => array( 19,20 ),
					'terms_and_conditions' => array(
						'note' => array(
							__('Cicilan per bulan dapat berubah ketika memasuki masa suku bunga floating'),
							__('Semakin lama jangka waktu pembayaran, maka semakin besar bunga yang akan dibayarkan'),
						),
						'notice' => __('* Rincian pinjaman dan Provisi hanya berupa ilustrasi, data dapat berubah sewaktu-waktu'),
						'without_provision' => __('Promo ini tidak menyediakan Provisi'),
					),
					'deny_edit_document' => array('rejected', 'pending', 'completed'),
					'action' => array(
						'credit_process' => array(
		                    'controller' => 'kpr',
		                    'action' => 'update_kpr',
		                    'status_confirm' => TRUE,
		                    'admin' => true,
		                ),
						'cancel' => array(
			                'controller' => 'kpr',
			                'action' => 'delete_kpr',
			                'admin' => true,
			            ),
					),
					'PropertyTypes' => array( 1,3 ),
				),
			),
		));
	}

	function configureKPR () {
		$kprURL = 'http://ww.kpr.dev.com';
		// $kprURL = 'http://www.kpr.pasiris.com'; //Live
		// $kprURL = 'http://ww.mandiri.com';

		Configure::write('__Site.kpr_url', $kprURL);
		Configure::write('__Site.kpr_credit_fix', 20);
		Configure::write('__Site.bunga_kpr', 20);
		Configure::write('__Site.interest_rate', 10);
		Configure::write('__Site.KPR.provision', 1);
		Configure::write('__Site.KPR.administration', 1000000);
		Configure::write('__Site.KPR.appraisal', 500000);
		Configure::write('__Site.KPR.insurance', 2);
		Configure::write('__Site.KPR.min_dp', 20);
		Configure::write('__Site.KPR.tenor', 20);
	}

	function checkAllowFunction () {
		$params = $this->controller->params;
		$controller = $this->filterEmptyField( $params, 'controller' );
		$prefix = $this->filterEmptyField( $params, 'prefix' );
		$action = $this->controller->action;

		if( $action == 'admin_login' ) {
			return false;
		} else if( $controller == 'memberships' && empty($prefix) ) {
			return false;
		} else if( in_array($prefix, array( 'api' )) || in_array($controller, array( 'ApiKprs', 'api_kprs' )) ) {
			return false;
		} else {
			return true;
		}
	}

	function checkAllowLandingPage(){
		$params = $this->controller->params;
		$controller = $this->filterEmptyField( $params, 'controller' );
		$prefix = $this->filterEmptyField( $params, 'prefix' );
		$action = $this->controller->action;

		$allowedControllers = array('memberships');

		if( in_array($controller, $allowedControllers) && empty($prefix) ) {
			if($controller == 'memberships' && $action == 'dashboard' ){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function checkAllowMembership () {
		$params = $this->controller->params;
		$controller = $this->filterEmptyField( $params, 'controller' );
		$prefix = $this->filterEmptyField( $params, 'prefix' );
		$action = $this->controller->action;
		
	//	if( $controller == 'memberships' && empty($prefix) ) {
		$allowedControllers = array('memberships', 'payments', 'vouchers');
		if( in_array($controller, $allowedControllers) && empty($prefix) ) {
			if($controller == 'memberships' && $action != 'dashboard'){
				return true;
			}
			else{
				$allowedActions = array('checkout', 'admin_post_payment', 'complete', 'validateVoucher');
				return in_array($action, $allowedActions);
			}
		} else {
			return false;
		}
	}

	function _layout ( $params = false, $theme = false, $layout = 'default' ) {
		$is_rest = Configure::read('__Site.is_rest');

		if( $this->checkAllowMembership() ) {
			$layout = 'memberships';
		} else if($this->checkAllowLandingPage() ){
			$layout = 'landing_page';
		} else if ( !empty($params['prefix']) && $params['prefix'] == 'admin' ) {
			$layout = 'admin';
		} else if ( !empty($params['prefix']) && $params['prefix'] == 'client' ) {
			$layout = 'client';
		} else if( !empty($theme) ) {
			$this->controller->theme = $theme;
		} else if(empty($theme) && $params['controller'] == 'ajax' && Configure::read('User.Admin.Rumahku')){
			$layout = 'ajax';
		} else if(empty($theme) && in_array($params['controller'], array( 'api', 'api_properties', 'api_users', 'api_kprs', 'ApiKprs', 'crontab' ))){
			$layout = false;

		} else if( $this->checkAllowFunction() && empty($is_rest) ) {
			$_base_url = FULL_BASE_URL;
			$checkDomain = strstr($_base_url, 'properti.me');

			if( !empty($checkDomain) ) {
				$this->controller->redirect(array(
					'controller' => 'memberships',
					'action' => 'index',
					'admin' => false,
				));
			}
		//	else {
		//	ini buat apaan?
		//		$this->controller->redirect(array(
		//			'controller' => 'users',
		//			'action' => 'login',
		//			'admin' => true,
		//		));
		//	}
		} else {
			$layout = 'admin';
		}

		$this->controller->layout = $layout;
	}

	function getMessageToAdmin ( $msg ) {
		$msg = $this->removeLastCharacter($msg, '.');

		return sprintf(__('%s. Silahkan hubungi Admin Kami untuk informasi lebih lanjut.'), $msg);
	}

	/**
	*
	*	set nilai validasi dari suatu form
	*
	*	@param boolean $errors
	*/
	function setValidationError( $errors = false ) {
		$tempErrors = array();
		if( !empty($errors) ) {
			$validationErrors = array();
			foreach ($errors as $key => $error) {
				$validationErrors[$key] = $error;

				foreach ($error as $key => $value) {
					if( !empty($value) && is_array($value) ) {
						foreach ($value as $key2 => $value2) {
							$tempErrors[] = $value2;
						}
					}
				}
			}
			$this->controller->set('validationErrors', $validationErrors);
		}

		return $tempErrors;
	}

	function getGlobalVariable ( $action ) {
		switch ($action) {
			case 'agent_certificates_option':
				return array(
					'AREBI'
				);
				break;
			case 'color_banner_option':
				return array(
					'1' => array(
						'background_class' => 'lightgreen',
						'background_color' => '#39B54A',
						'font_color' 		=> '#000000',
					),
					'2' => array(
						'background_class' => 'lightyellow',
						'background_color' => '#FFF200',
						'font_color' => '#000000',
					),
					'3' => array(
						'background_class' => 'lightblue',
						'background_color' 	=> '#27AAE1',
						'font_color' 		=> '#000000',
					),
					'4' => array(
						'background_class' => 'lightbrown',
						'background_color' 	=> '#C2B59B',
						'font_color' 		=> '#000000',
					),
					'5' => array(
						'background_class' => 'lightwhite',
						'background_color' => '#FFFFFF',
						'font_color' 		=> '#000000',
					),
					'6' => array(
						'background_class' => 'darkgreen',
						'background_color' => '#006838',
						'font_color' 		=> '#FFFFFF',
					),
					'7' => array(
						'background_class' => 'darkred',
						'background_color' => '#ED1C24',
						'font_color' 		=> '#FFFFFF',
					),
					'8' => array(
						'background_class' => 'darkblue',
						'background_color' => '#2B3990',
						'font_color' 		=> '#FFFFFF',
					),
					'9' => array(
						'background_class' => 'darkbrown',
						'background_color' 	=> '#754C29',
						'font_color' 		=> '#FFFFFF',
					),
					'10' => array(
						'background_class' => 'darkblack',
						'background_color' 	=> '#000000',
						'font_color' 		=> '#FFFFFF',
					)
				);
				break;
			case 'type_commision_cobroke':
				return array(
					'in_corp' => __('Penjualan Properti'),
				    'out_corp' => __('Komisi Pemilik Listing'),
				);
				break;
			case 'category_status':
				$isPersonalPage	= Configure::read('Config.Company.is_personal_page');
				$userID			= array();

				if($isPersonalPage){
				//	frontend
					$userID = Configure::read('Config.Company.data.User.id');
				}
				else{
				//	by login
					$principleID	= Configure::read('Principle.id');
					$authGroupID	= Configure::read('User.data.group_id');
					$isAgent		= Common::validateRole('agent', $authGroupID);

					$userID	= array($principleID);

					if($isAgent){
						$userID[] = Configure::read('User.data.id');
					}
				}	

				$data_category = $this->controller->User->Property->PropertyStatusListing->getData('list', array(
					'cache' => __('PropertyStatusListing.List.%s', $userID),
					'cacheConfig' => 'default',
				), array(
					'status' => 'active',
					'company' => true,
				));
				return $data_category;
				break;
			default:
				return false;
				break;
		}
	}

	function getUrlReferer( $params, $is_admin ) {
		
		$referer = array(
			'controller' => $params->controller,
			'action' => $params->action, 
			'admin' => $is_admin
		);
		return $referer;
	}

	function refineUserProfession($user, $type = 'view') {

		if( $type == 'view' ) {

			if( !empty($user['UserConfig']['client_types']) ) {
				$user['UserConfig']['client_types'] = explode(', ', $user['UserConfig']['client_types']);
			}
			if( !empty($user['UserConfig']['user_property_types']) ) {
				$user['UserConfig']['user_property_types'] = explode(', ', $user['UserConfig']['user_property_types']);
			}
			if( !empty($user['UserConfig']['specialists']) ) {
				$user['UserConfig']['specialists'] = explode(', ', $user['UserConfig']['specialists']);
			}
			if( !empty($user['UserConfig']['certifications']) ) {
				$user['UserConfig']['certifications'] = explode(', ', $user['UserConfig']['certifications']);
			}
			if( !empty($user['UserConfig']['languages']) ) {
				$user['UserConfig']['languages'] = explode(', ', $user['UserConfig']['languages']);
			}
			if( empty($user['UserConfig']['other_certifications']) ) {
				$user['UserConfig']['other_certifications'] = '';
			}
			if( empty($user['UserConfig']['other_languages']) ) {
				$user['UserConfig']['other_languages'] = '';
			}

		} else if ( $type == 'beforeSave' ) {

			if( !empty($user['UserConfig']['client_types']) ) {
				$user['UserConfig']['client_types'] = implode(', ', array_filter($user['UserConfig']['client_types']));
				if( empty($user['UserConfig']['client_types']) ) {
					$user['UserConfig']['client_types'] = '';
				}
			}
			if( !empty($user['UserConfig']['user_property_types']) ) {
				$user['UserConfig']['user_property_types'] = implode(', ', array_filter($user['UserConfig']['user_property_types']));
				if( empty($user['UserConfig']['user_property_types']) ) {
					$user['UserConfig']['user_property_types'] = '';
				}
			}
			if( !empty($user['UserConfig']['specialists']) ) {
				$user['UserConfig']['specialists'] = implode(', ', array_filter($user['UserConfig']['specialists']));
				if( empty($user['UserConfig']['specialists']) ) {
					$user['UserConfig']['specialists'] = '';
				}
			}
			if( !empty($user['UserConfig']['certifications']) ) {
				$user['UserConfig']['certifications'] = implode(', ', array_filter($user['UserConfig']['certifications']));
				if( empty($user['UserConfig']['certifications']) ) {
					$user['UserConfig']['certifications'] = '';
				}
			}
			if( !empty($user['UserConfig']['languages']) ) {
				$user['UserConfig']['languages'] = implode(', ', array_filter($user['UserConfig']['languages']));
				if( empty($user['UserConfig']['languages']) ) {
					$user['UserConfig']['languages'] = '';
				}
			}
			if( empty($user['UserConfig']['other_certifications_flag']) ){
				$user['UserConfig']['other_certifications'] = '';
			}

			if( empty($user['UserConfig']['other_languages_flag']) ){
				$user['UserConfig']['other_languages'] = '';
			}
		}
		
		return $user;
	}

	function redirectReferer ( $msg = false, $status = 'error', $urlRedirect = false, $options = array() ) {
		$flash = $this->filterEmptyField($options, 'flash', false, true, array(
			'type' => 'isset',
		));
	 	$paramFlash = $this->filterEmptyField($options, 'paramFlash', false, array());
	 	$ajaxRedirect = $this->filterEmptyField($options, 'ajaxRedirect');
	 	$modal = $this->filterEmptyField($options, 'modal');
	 	$isRest = $this->filterIssetField($options, 'rest', false, $this->Rest->isActive());
	 	$ext = $this->filterIssetField($this->controller->params, 'ext');

		if( !empty($msg) ) {
			$this->setCustomFlash($msg, $status, $paramFlash, $flash);
		}

		$isAjax = $this->RequestHandler->isAjax();

		if( !$isRest ) {
			if( !$isAjax || !empty($ajaxRedirect) ) {
				if( !empty($ext) && $ext == 'json' ) {
					$urlRedirect['ext'] = $ext;
				}

				if( !empty($urlRedirect) ) {
					$this->controller->redirect($urlRedirect);
				} else {
					$this->controller->redirect($this->controller->referer());
				}
			} else if( $modal == $status ) {
				$this->controller->render('/Elements/blocks/common/modals/thanks');
			}
		}
	}

	/*
		code_error : code error sangat penting untuk mengetahui jenis error yang di dapat
		berikut code yang umum :
			- 200 : sukses melakukan proses
			- 500 : error coding
			- 301 : data tidak ditemukan, yang menyebabkan proses tidak bisa berlanjut
			- 302 : data tidak ditemukan, tapi terkait API
			- 304 : API tidak bisa di akses
			- 305 : gagal melakukan save data
			- 306 : API berhasil di jalankan, tetapi dengan hasil error
			- 307 : proses berjalan normal, tapi tidak berjalan dengan semestinya
		*note : 
		jika ingin menambahkan code error sendiri, harap mencatat di atas (harus dengan deskripsi), 
		tapi harus code errornya di bawah 400
	*/

	function getModelName($controller_arr = array()){
		$result = false;

		if($controller_arr){
			foreach ($controller_arr as $key => $value) {
				$result .= ucfirst($value);
			}
		}
		return $result;
	}

	function _saveLog( $activity = NULL, $old_data = false, $document_id = false, $error = 0, $code_error = false, $options = array() ){
		$this->Log = ClassRegistry::init('Log');

		$log = array();
		$user_id = $this->filterEmptyField($options, 'user_id', false, Configure::read('User.id'));
		$parent_id = $this->filterEmptyField($options, 'parent_id', false, Configure::read('Principle.id'));
		$group_id = $this->filterEmptyField($options, 'group_id', false, Configure::read('User.group_id'));
		$_admin = Configure::read('User.Admin.Rumahku');

		$controllerName = $this->controller->params['controller'];
		$actionName = $this->controller->params['action'];
		$data = serialize($this->controller->params['data']);
		$named = serialize( $this->controller->params['named'] );

		$controller_arr = explode('_', $controllerName);
		$controllerName = $this->getModelName($controller_arr);

		$is_admin = !empty($_admin)?true:false;
		$old_data = serialize( $old_data );
		$validation_data = $this->filterEmptyField($options, 'validation_data', false);

		$url = $this->RequestHandler->getReferer();
		$ip_address = $this->RequestHandler->getClientIP();
		$browser_arr = Common::hashEmptyField($_SERVER, 'HTTP_USER_AGENT');
		$user_agents = @get_browser(null, true);

		$browser = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
		$os = !empty($user_agents['platform'])?$user_agents['platform']:'';

		$allowAccess = Configure::read('Rest.validate');
		$mobile = Configure::read('Global.Data.MobileDetect.mobile');
		$tablet = Configure::read('Global.Data.MobileDetect.tablet');
		$document_id = !empty($document_id)?$document_id:0;

		if( !empty($mobile) || !empty($tablet) ){
			$device = 'mobile';
		
		} else {
			$device = 'browser';
		}
		
		$log['Log']['api_id'] = Common::hashEmptyField($allowAccess, 'id', 0);
		$log['Log']['parent_id'] = $parent_id;
		$log['Log']['user_id'] = $user_id;
		$log['Log']['group_id'] = $group_id;
		$log['Log']['document_id'] = $document_id;
		$log['Log']['name'] = $activity;
		$log['Log']['model'] = $controllerName;
		$log['Log']['action'] = $actionName;
		$log['Log']['old_data'] = $old_data;
		$log['Log']['device'] = $device;
		$log['Log']['data'] = $data;
		$log['Log']['ip'] = $ip_address;
		$log['Log']['user_agent'] = env('HTTP_USER_AGENT');
		$log['Log']['from'] = $url;
		$log['Log']['named'] = $named;
		$log['Log']['error'] = !empty($error)?$error:0;
		$log['Log']['admin'] = $is_admin;
		$log['Log']['validation_data'] = !empty($validation_data) ? serialize($validation_data) : false;

		if(!empty($error)){
			if(!empty($code_error)){
				$log['Log']['code_error'] = $code_error;
			}
		}else{
			$log['Log']['code_error'] = 200;
		}

		if( $this->Log->doSave($log) ) {

			if($code_error >= 300 && $code_error < 400){
				$options = array(
					'data' 			=> !empty($data) ? $data : false,
					'old_data' 		=> !empty($old_data) ? $old_data : false,
					'validation_data' => !empty($validation_data) ? serialize($validation_data) : false,
					'document_id' 	=> !empty($document_id) ? $document_id : false,
					'user_id' 		=> !empty($user_id) ? $user_id : false,
				);

				if(in_array($code_error, array(305))){
					$path_folder = 'save_log_database_error';
				} else if (in_array($code_error, array(302, 304, 306))){
					$path_folder = 'log_api_error';
				} else {
					$path_folder = 'log';
				}

				$this->_writeErrorFile($path_folder, $data, 0, $named, $activity);
			}

			return true;	
		} else {
			return false;
		}
	}

	function getFieldRoleUser($role = array(), $field = 'id', $from_parent_id = false){
		$temp = array();

		if(!empty($role)){
			foreach ($role as $key => $value) {
				$conditions = array(
					'User.email LIKE'	=> '%@%',
				);

				if(!empty($from_parent_id)){
					$conditions = array(
						'OR' => array(
							array('User.id' => $from_parent_id),
							array('User.parent_id' => $from_parent_id,)
						)
					);
				}

				$result = $this->controller->User->getData('list', array(
					'conditions' => $conditions,
					'fields' => array(
						'User.'.$field, 'User.'.$field
					)
				), array(
					'role' => $value,
					'status' => 'active',
					'admin' => false
				));

				if(!empty($result)){
					
					$temp = array_merge($temp, $result);
				}
			}
		}

		return $temp;
	}

	function _saveNotifRole($dataNotif = false){
		if($dataNotif){
			$dataNotif = Common::hashEmptyField($dataNotif, 'Notification', $dataNotif);

			$include_role 	= Common::hashEmptyField($dataNotif, 'include_role.role');
			$from_parent_id = Common::hashEmptyField($dataNotif, 'include_role.from_parent_id');

			if(!empty($include_role)){
				unset($dataNotif['include_role']);

				$user_id_target	= Common::hashEmptyField($dataNotif, 'user_id');
				$link			= Common::hashEmptyField($dataNotif, 'link');

				if(!empty($link) && is_array($link)){
					$dataNotif['link'] = serialize($link);
				}

			/*
				$temp_array = array();
				if (!empty($user_id_target)) {
					$temp_array[] = array(
						'Notification' => $dataNotif 
					);
				}

				$id_user = $this->getFieldRoleUser($include_role, 'id', $from_parent_id);

				if(!empty($id_user)){
					if(!empty($user_id_target) && in_array($user_id_target, $id_user)){
						// unset($id_user[$user_id_target]);
						$key = array_search($user_id_target, $id_user);
						if($key !== false) {
						    unset($id_user[$key]);
						}
					}

					foreach ($id_user as $key => $value) {
						$dataNotif['user_id'] = $value;

						$temp_array[] = array(
							'Notification' => $dataNotif 
						);
					}
				}

				if( !$this->controller->User->Notification->doSaveMany($temp_array) ) {
					$flag = false;
				}
			*/

				$notifications = array();

			//	main recipient (pake all biar format array sama)
				$targetUser = $this->controller->User->getData('all', array(
					'contain'		=> array('UserProfile'), 
					'conditions'	=> array('User.id' => $user_id_target), 
				), array(
					'status'	=> 'active', 
					'company'	=> false, 
				));

			//	included recipient
				$includedUsers = $this->controller->User->getData('all', array(
					'contain'		=> array('UserProfile'), 
					'conditions'	=> array(
						'OR' => array(
							array('User.id'			=> $from_parent_id),
							array('User.parent_id'	=> $from_parent_id)
						), 
					),
				), array(
					'role'		=> $include_role,
					'status'	=> 'active',
					'admin'		=> false, 
				));

				$recipients = array_merge($targetUser, $includedUsers);

				if($recipients){
					$message = Common::hashEmptyField($dataNotif, 'name');

					foreach($recipients as $recipient){
						$userID	= Common::hashEmptyField($recipient, 'User.id');
						$noHP	= Common::hashEmptyField($recipient, 'UserProfile.no_hp');
						$noHP2	= Common::hashEmptyField($recipient, 'UserProfile.no_hp_2');

						$notifications[] = array(
							'Notification' => Hash::insert($dataNotif, 'user_id', $userID), 
						);

					/*	send wa notif
						if($noHP || $noHP2){
						//	format whatsapp
							$clientData	= array(
								'type'				=> 'text',
								'recipient_type'	=> 'individual', 
								'to'				=> $noHP ?: $noHP2, 
								'text'				=> array(
									'body' => $message,
								), 
							);

						//	send request
							$result = Common::httpRequest($requestURL, $clientData, array(
							//	'debug'		=> true, 
								'data_type'	=> 'json', 
								'method'	=> 'post',
								'header'	=> array(
									'authorization'	=> $accessToken, 
									'accept'		=> 'application/json', 
									'content_type'	=> 'application/json', 
								), 
							));
						}
					*/
					}
				}

			//	ditampung tapi ga di return ??
				$flag = $this->controller->User->Notification->doSaveMany($notifications);
			}else{
				if( !empty($dataNotif['name']) ) {
					$notifs = array(
						array(
							'Notification' => $dataNotif,
						),
					);
				} else {
					$notifs = $dataNotif;
				}

				if( !empty($notifs) ) {
					foreach ($notifs as $key => $notif) {
						$data_submit['Notification'] = Common::hashEmptyField($notif, 'Notification');
						
						if( !$this->controller->User->Notification->doSave($data_submit) ) {
							$flag = false;
						}else{
							$message 		= Common::hashEmptyField($notif, 'Notification.name');
							$user_id_target = Common::hashEmptyField($notif, 'Notification.user_id');

							$this->mobileNotif($message, $user_id_target);
						}
					}
				}
			}
		} else {
			return false;
		}
	}

	function _saveNotification( $data = array() ){
		$flag = true;
		$data = (array) $data;

		$notifications = Common::hashEmptyField($data, 'Notification', array());

		if($notifications){
			$isMultiple		= Hash::numeric($notifications, array_keys($notifications));
			$notifications	= $isMultiple ? $notifications : array($notifications);

		//	ini flag ga bakal valid soalnya masuk loop
		//	contoh
		//	loop 1 true
		//	loop 2 false
		//	loop 3 true
		//	result pasti true

			foreach($notifications as $key => $notification){
				$flag = $this->_saveNotifRole($notification);
			}
		}

		return $flag;
	}

	function setProcessParams ( $data, $urlRedirect = false, $options = array() ) {
		$redirectError = $this->filterEmptyField($options, 'redirectError');
		$noRedirect = $this->filterEmptyField($options, 'noRedirect');
	 	$ajaxFlash = $this->filterEmptyField($options, 'ajaxFlash');
	 	$flash = $this->filterIssetField($options, 'flash', false, true);
	 	$ajaxRedirect = $this->filterEmptyField($options, 'ajaxRedirect');
	 	$modal = $this->filterEmptyField($options, 'modal');
	 	$paramFlash = $this->filterEmptyField($options, 'paramFlash', false, array());
	 	$rest = $this->filterIssetField($options, 'rest', false, $this->Rest->isActive());
	 	$restData = $this->filterIssetField($options, 'restData', false);

		$this->validateEmail($data);
		$this->_saveNotification($data);

		if( $this->RequestHandler->isAjax() && !$ajaxFlash ) {
			$flash = false;
		}

		if ( !empty($data['status']) ) {
			$msg = $this->filterEmptyField($data, 'msg');
			
			if ( !empty( $data['Log'] ) ) {
				$activity = $this->filterEmptyField($data, 'Log', 'activity');
				$old_data = $this->filterEmptyField($data, 'Log', 'old_data');
				$document_id = $this->filterEmptyField($data, 'Log', 'document_id');
				$error = $this->filterEmptyField($data, 'Log', 'error');
				$code_error = $this->filterEmptyField($data, 'Log', 'code_error');
				$validation_data = $this->filterEmptyField($data, 'Log', 'validation_data');

				$options_log = array(
					'validation_data' => $validation_data
				);

				$this->_saveLog( $activity, $old_data, $document_id, $error, $code_error, $options_log );
			}

			if ( !empty( $data['RefreshAuth'] ) ) {
				$user_id = $this->filterEmptyField($data, 'RefreshAuth', 'id');
				$this->RmUser->refreshAuth($user_id);
			}

			if ( ( $data['status'] == 'success' || !empty($redirectError) ) && !$noRedirect && !$rest ) {
				$this->redirectReferer($msg, $data['status'], $urlRedirect, array(
					'flash' => $flash,
					'ajaxRedirect' => $ajaxRedirect,
					'modal' => $modal, 
					'rest' => $rest, 
				));
			} else if( !empty($msg) ) {
				$this->setCustomFlash($msg, $data['status'], $paramFlash, $flash);
			}

			if(!empty($data['validationErrors'])){
				$this->controller->set('validationErrors', $data['validationErrors']);
			}
		}

		if ( !empty( $data['data'] ) ) {
			$this->controller->request->data = $data['data'];

			if( !empty($restData) ) {
				$this->controller->set('data', $data['data']);
			}
		}
	}

	function filterEmptyField($value, $modelName, $fieldName = false, $empty = null, $options = false){
		$types = !empty($options['type'])?$options['type']:'empty';
		$date = !empty($options['date'])?$options['date']:false;
		$urldecode = isset($options['urldecode'])?$options['urldecode']:true;
		$result = $empty;

		if( !empty($types) ) {
			if( !is_array($types) ) {
				$types = array(
					$types,
				);
			}

			foreach ($types as $key => $type) {
				switch($type){
					case 'isset':
						if(empty($fieldName) && isset($value[$modelName])){
							$result = $value[$modelName];
						} else {
							$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
						}
						break;
					
					default:
						if(empty($fieldName) && !empty($value[$modelName])){
							$result = $value[$modelName];
						} else {
							if(!empty($type) && $type == 'trim' && isset($value[$modelName][$fieldName])){
								$value[$modelName][$fieldName] = trim($value[$modelName][$fieldName]);
								
								$result = !empty($value[$modelName][$fieldName]) ? $value[$modelName][$fieldName]:$empty;
							}else{
								$result = !empty($value[$modelName][$fieldName]) ? $value[$modelName][$fieldName]:$empty;
							}
						}
						break;
				}

				switch($type){
					case 'slug':
						$result = $this->toSlug($result);
						break;
					case 'strip_tags':
						$result = $this->safeTagPrint($result);
						break;
					case 'unserialize':
						$result = unserialize($result);
						break;
					case 'htmlentities':
						$result = htmlentities($result);
						break;
					case 'EOL':
						$result = str_replace(PHP_EOL, '<br>', $result);
						break;
					case 'trailing_slash':
						$last_char = substr($result, -1);
						if( $last_char === '/' ) {
							$result = rtrim($result, $last_char);
						}
						break;
					case 'currency':
						$result = $this->getFormatPrice($result);
						break;
				}
			}
		}

        if( !empty($date) ) {
            $format = $date;
            $result = $this->formatDate($result, $format);
        }
        if( is_string($result) && $urldecode ) {
			$result = trim(urldecode($result));
        }

		return $result;
	}

	function filterIssetField($value, $modelName, $fieldName = false, $empty = false, $options = false){
		$result = '';
		$type = !empty($options['type'])?$options['type']:'empty';
		
		if( empty($modelName) && !is_numeric($modelName) ) {
			$result = isset($value)?$value:$empty;
		} else if( empty($fieldName) && !is_numeric($fieldName) ) {
			$result = isset($value[$modelName])?$value[$modelName]:$empty;
		} else {
			$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
		}

		if( !empty($result) ) {
			switch($type){
				case 'slug':
					$result = $this->toSlug($result);
					break;
				case 'strip_tags':
					$result = $this->safeTagPrint($result);
					break;
				case 'unserialize':
					$result = unserialize($result);
					break;
				case 'htmlentities':
					$result = htmlentities($result);
					break;
				case 'EOL':
					$result = str_replace(PHP_EOL, '<br>', $result);
					break;
				case 'trailing_slash':
					$last_char = substr($result, -1);;

					if( $last_char === '/' ) {
						$result = rtrim($result, $last_char);
					}
					break;
			}
		}

		return $result;
	}

	function _layout_file ( $type ) {
		$layout_js = array();
		$layout_css = array();
		$contents = array();

		if( !is_array($type) ) {
			$contents[] = $type;
		} else {
			$contents = $type;
		}

		if( !empty($contents) ) {
			foreach ($contents as $key => $type) {
				switch ($type) {
					case 'map':
						$layout_js = array_merge($layout_js, array(
							// '//maps.google.com/maps/api/js?key=false',
							// 'jquery/gmap.js',
						));
						break;
					case 'fileupload':
						$layout_js = array_merge($layout_js, array(
							'/file_upload/js/vendor/jquery.ui.widget.js', 
							'/file_upload/js/tmpl.min.js',
							'/file_upload/js/load-image.min.js',
							'/file_upload/js/canvas-to-blob.min.js',
							'/file_upload/js/jquery.iframe-transport.js',
							'/file_upload/js/jquery.fileupload.js',
							'/file_upload/js/jquery.fileupload-fp.js',
							'/file_upload/js/jquery.fileupload-ui.js',
							'/file_upload/js/locale.js',
							'/file_upload/js/main.js',

						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/vendor/jquery.ui.widget.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/tmpl.min.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/load-image.min.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/canvas-to-blob.min.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.iframe-transport.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload-fp.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/jquery.fileupload-ui.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/locale.js',
						//	'https://s3-ap-southeast-1.amazonaws.com/rmcompany/v2/js/file_upload/main.js',
						));
						$layout_css = array_merge($layout_css, array(
							// 'file_upload/file_upload_style.css',
							'file_upload/jquery.fileupload-ui.css',
						));
						break;
					case 'ckeditor':
						$layout_js = array_merge($layout_js, array(
							'ckeditor/ckeditor',
						));
						break;
					case 'gchart':
						$layout_js = array_merge($layout_js, array(
							'jquery.gchart.js',
						));
						break;
					case 'map-cozy':
						$layout_js = array_merge($layout_js, array(
							'markerclusterer.min.js',
							// SEMENTARA
							// 'infobox.min.js',
						));
						break;
					case 'map-easyliving':
						$layout_js = array_merge($layout_js, array(
							'map-one-pin.js',
						));
						break;
					case 'ebrosur':
						$layout_js = array_merge($layout_js, array(
							'fancybox.js',
						));
						break;
					case 'color-picker':
						$layout_css = array_merge($layout_css, array(
							'admin/jquery.minicolors',
						));
						break;
					case 'scroll_paginate':
						$layout_js = array_merge($layout_js, array(
							'jscroll/jquery.jscroll.min',
						));
						break;
					case 'google_api' : 
						$layout_js['bottom'] = array(
							'admin/cors_upload', 
						//	'https://apis.google.com/js/client.js', 
						//	'https://apis.google.com/js/platform.js', 
							'admin/youtube_upload',
						);
						break;
					case 'launcher':
						$layout_css = array_merge($layout_css, array(
							'launchers/style',
						));
						break;
                    case 'freeze':
      					$mobile = Configure::read('Global.Data.MobileDetect.mobile');

      					if( empty($mobile) ) {
	                        $layout_js = array_merge($layout_js, array(
	                            'admin/freeze',
	                        ));
	                        $layout_css = array_merge($layout_css, array(
	                            'admin/freeze',
	                        ));
	                    }
                        break;
                    case 'bank':
                    	$layout_css = array_merge($layout_css, array(
							'client/bank',
						));
					case 'tour' :
						$layout_js = array_merge($layout_js, array(
							'tour/bootstrap-tour.min',
						));

						$layout_css = array_merge($layout_css, array(
							'tour/bootstrap-tour.min',
						));
						break;
					case 'market_trend' : 
						$layout_js = array_merge($layout_js, array(
						//	'https://code.jquery.com/jquery-3.2.1.min.js', 
							'https://www.gstatic.com/charts/loader.js', 
						//	'/js/market_trend/bootstrap.bundle.js', 
							'market_trend/custom.js', 
						));

						$layout_css = array_merge($layout_css, array(
						//	'https://getbootstrap.com/dist/css/bootstrap.min.css', 
						//	'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', 
							'market_trend/style.css',
							'market_trend/extrastyle.css',
						));
						break;
					case 'admin_market_trend' : 
						$layout_js = array_merge($layout_js, array(
							'https://www.gstatic.com/charts/loader.js', 
							'market_trend/custom.js', 
							'admin/dashboard.js', 
						));

						$layout_css = array_merge($layout_css, array(
							'font-awesome.min.css', 
							'market_trend/admin.style.css',
							'market_trend/extrastyle.css',
						));
						break;
					case 'membership_video':
						$layout_js = array_merge($layout_js, array(
							'membershipV2/video', 
						));
						break;
					case 'animate-counter':
						$layout_js = array_merge($layout_js, array(
							'membershipV2/counter/jquery.waypoints.min',
							'membershipV2/counter/jquery.counterup.min',
						));
						break;
					case 'report':
						$layout_css = array_merge($layout_css, array(
							'admin/report',
							'admin/chart',
						));
						$layout_js = array(
							'date/tether.min',
                			'date/datePicker',
                			'https://www.gstatic.com/charts/loader.js',
                			// 'admin/report',
                			'html2canvas', 
						);
						break;
					case 'dashboard':
						$layout_js['bottom'] = array(
                			'admin/dashboard',
						);
						break;
					case 'acl':
						$layout_js['bottom'] = array('admin/acl.function.js');
					break;
				}
			}
		}
		
		$this->controller->set(compact(
			'layout_js', 'layout_css'
		));
	}

	function get_result_upload($options = false){
  		$file = new stdClass();

  		if( !empty($options['error']) ){
  			$file->message = $options['message'];
			$file->error = 1;
  		}else{
  			if(!empty($options['imagePath'])){
	  			$file->thumbnail_url = $options['imagePath'];
  			}

  			if(!empty($options['name'])){
	  			$file->name = $options['name'];
  			}
  		}

  		return $file;
  	}

  	function array_random($arr, $num = 1) {
		shuffle($arr);
		
		$r = array();
		for ($i = 0; $i < $num; $i++) {
			$r[] = $arr[$i];
		}
		return $num == 1 ? $r[0] : $r;
	}

  	function createRandomNumber( $default= 4, $variable = 'bcdfghjklmnprstvwxyz', $modRndm = 20 ) {
		$chars = $variable;
		srand((double)microtime()*1000000);
		$pass = array() ;

		$i = 1;
		while ($i != $default) {
			$num = rand() % $modRndm;
			$tmp = substr($chars, $num, 1);
			$pass[] = $tmp;
			$i++;
		}
		$pass[] = rand(1,9);
		$rand_code = $this->array_random($pass, count($variable));

		return $pass;
	}

	function toSlug($data, $fields = false, $glue = '-') {
		if( !empty($data) ) {
			if( !is_array($data) ) {
				$data = strtolower(Inflector::slug($data, $glue));
			} else {
				foreach ($fields as $key => $value) {
					if( is_array($value) ) {
						foreach ($value as $idx => $fieldName) {
							if( !empty($data[$key][$fieldName]) ) {
								$data[$key][$fieldName] = strtolower(Inflector::slug($data[$key][$fieldName], $glue));
							}
						}
					} else {
						$data[$value] = strtolower(Inflector::slug($data[$value], $glue));
					}
				}
			}
		}

		return $data;
	}

	function beforeSave( $data, $modelName, $options) {
		if( !empty($options) ) {
			foreach ($options as $type => $values) {
				$type = strval($type);

				if( $values == 'order' ) {
					if ( isset($data[$modelName][$values]) ) {
						$data[$modelName][$values] = $this->filterEmptyField($data, $modelName, $values, 0);
					}
				} else {
					switch ($type) {
						case 'slug':
							if( !empty($values) ) {
								foreach ($values as $key => $fieldName) {
									$value = $this->filterEmptyField($data, $modelName, $fieldName);
									$value = $this->toSlug($value);

									if( isset($data[$modelName][$fieldName]) ) {
										$data[$modelName][$type] = $value;
									}
								}
							}
							break;
						
						case 'order':
							if ( isset($data[$modelName][$values]) ) {
								$data[$modelName][$values] = $this->filterEmptyField($data, $modelName, $values, 0);
							}
							break;
					}
				}
			}
		}

		return $data;
	}

	/**
	*
	*	filterisasi content tag
	*
	*	@param string string : string
	*	@return string
	*/
	function safeTagPrint($string){
		if( is_string($string) ) {
			return strip_tags($string);
		} else {
			return $string;
		}
	}

	function processRefine( $refine = false ) {
		$params = array();

		if(!empty($refine)) {
			if( !empty($refine['Search']['keyword']) ) {
				$params['keyword'] = urlencode($refine['Search']['keyword']);
			}
		}

		return $params;
	}

	function getRefine( $refine = false, $fieldName = false, $empty = false ) {
		$keyword = $this->filterEmptyField($refine, 'named', $fieldName, $empty);
		$this->controller->request->data['Search'][$fieldName] = $keyword;

		return $keyword;
	}

	function convertDataAutocomplete( $data ) {
		$result = array();

		if( !empty($data) ) {
			foreach ($data as $id => $value) {
				array_push($result, $value);
			}
		}

		return $result;
	}

	function _callGetYoutubeID ( $url ) {
		$result = false;

		if( !empty($url) ) {
			parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
			$result = !empty($my_array_of_vars['v'])?$my_array_of_vars['v']:false;
		}

		return $result;
	}

	function _callTitleYoutubeID ( $youtube_id ) {
		$result = false;

		if( !empty($youtube_id) ) {
			$contextOptions = array(
				'ssl' => array(
					'verify_peer'		=> false,
					'verify_peer_name'	=> false,
				),
			);

			$content = file_get_contents("http://youtube.com/get_video_info?video_id=".$youtube_id, false, stream_context_create($contextOptions));
			parse_str($content, $ytarr);
			$result = !empty($ytarr['title'])?$ytarr['title']:false;
		}

		return $result;
	}

	public function getYoutubeDetail($youtubeID){
		$result = array();

		if($youtubeID){
			$contextOptions = array(
				'ssl' => array(
					'verify_peer'		=> false,
					'verify_peer_name'	=> false,
				),
			);

			$content = file_get_contents('http://youtube.com/get_video_info?video_id=' . $youtubeID, false, stream_context_create($contextOptions));
			parse_str($content, $result);
		}

		return $result;
	}


	function getDate ( $date, $reverse = false ) {
		$dtString = false;
		$date = trim($date);

		if( !empty($date) && $date != '0000-00-00' ) {
			if($reverse){
				$dtString = date('d/m/Y', strtotime($date));
			}else{
				$dtArr = explode('/', $date);

				if( count($dtArr) == 3 ) {
					$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
				} else {
					$dtArr = explode('-', $date);

					if( count($dtArr) == 3 ) {
						$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
					}
				}
			}
		}
		
		return $dtString;
	}

	function _callPriceConverter ($price) {
		$price = $this->safeTagPrint($price);

		/*edit by iksan, harga jadi ga bisa di buat "."*/
		// return trim(str_replace(array( ',', '.', 'Rp ' ), array( '', '', '' ), $price));
		return trim(str_replace(array( ',', 'Rp ' ), array( '', '' ), $price));
	}

	function dataConverter( $data, $fields, $reverse = false, $round = 0 ) {
		if( !empty($data) && !empty($fields) ) {
			foreach ($fields as $type => $models) {
				$data = $this->_converterLists($type, $data, $models, $reverse, $round);
			}
		}
		return $data;
	}

	function _converterLists($type, $data, $models, $reverse = false, $round = 0){
    	if(!empty($type) && !empty($data) && !empty($models)){
    		if(is_array($models)){
    			foreach($models AS $loop => $model){
 	   				if(!empty($model) || $model === 0){
	 	   				if( is_array($model) && !empty($data[$loop]) ){
	 	   					if(is_numeric($loop)){
	 	   						foreach($data AS $key => $dat){
	 	   							if(is_array($model) && !empty($dat)){
	 	   								$data[$key] = $this->_converterLists($type, $data[$key], $model, $reverse, $round);
	 	   							}
	 	   						}
	 	   					}else{
	 	   						$data[$loop] = $this->_converterLists($type, $data[$loop], $models[$loop], $reverse, $round);
	 	   					}
	 	   				} else if( !is_array($model) ) {
	 	   					if(in_array($type, array('unset', 'array_filter'))){
	 	   						if($type == 'array_filter'){
	 	   							$data[$model] = array_filter($data[$model]);
	 	   							if(empty($data[$model])){
	 	   								unset($data[$model]);
	 	   							}
	 	   						}else{
	 	   							unset($data[$model]);
	 	   						}

	 	   					} else if( !empty($data[$model]) ) {
	 	   						$data[$model] = $this->_generateType($type, $data[$model], $reverse, $round);
	 	   					}
	 	   				}
	 	   			}
	    		}
    		}else{
    			if(in_array($type, array('unset', 'array_filter'))){
    				if($type == 'array_filter'){
						$data[$models] = array_filter($data[$models]);
						if(empty($data[$models])){
							unset($data[$models]);
						}
					}else{
						unset($data[$models]);
					}
    			}else{
    				$data[$models] = $this->_generateType($type, $data[$models], $reverse, $round);
    			}
    		}
    	}
    	return $data;
    }

    function _generateType($type, $data, $reverse, $round){
    	switch($type){
			case 'date' : 
			$data = $this->getDate($data, $reverse);
			break;
		case 'price' : 
			$data = $this->_callPriceConverter($data, $reverse);
			break;
		case 'round' : 
			$data = $this->_callRoundPrice($data, $round);
			break;
		case 'url' : 
			$data = $this->wrapWithHttpLink($data, $reverse);
			break;
		case 'auth_password' : 
			$data = $this->Auth->password($data);
			break;
        case 'daterange':
			$data = $this->_callDateRangeConverter($data);
            break;
        case 'year':
            $data = intval($data);
            $data = !empty($data)?$data:false;
            break;
		case 'currency' : 
			$data = $this->getFormatPrice($data);
			break;
		case 'ucfirst' : 
			$data = ucfirst($data);
			break;
		## ADA CASE BARU TAMBAHKAN DISINI, ANDA HANYA MEMBUAT $this->FUNCTION yang anda inginkan tanpa merubah flow dari
		## function dataConverter dan _converterLists
		}
		return $data;
    }

    function _callRoundPrice($price, $round = 0, $empty = '', $options = array()){
    	$format = Common::hashEmptyField($options, 'format');

    	if (!empty($format)) {
    		switch($format){
				case 'percent' : 
					$data = round($price, $round);
					$data = __('%s%', $data);
					break;
			}

			return $data;

    	} elseif(isset($price)) {
    		return round($price, $round);
    	} else {
    		return $empty;
    	}
    }

    function _callDateRangeConverter ( $daterange, $fieldName = 'date', $fromName = 'start_date', $toName = 'end_date' ) {
    	$result = array();

        if( !empty($daterange) ) {
            $dateStr = urldecode($daterange);
            $daterange = explode('-', $dateStr);

            if( !empty($daterange) ) {
                $daterange[0] = urldecode($daterange[0]);
                $daterange[1] = urldecode($daterange[1]);
                $dateFrom = $this->getDate($daterange[0]);
                $dateTo = $this->getDate($daterange[1]);
                $result[$fromName] = $dateFrom;
                $result[$toName] = $dateTo;
            }
        }

        return $result;
    }

	function _callDataRegister ( $data ) {
		if( !empty($data['User']['password']) ) {
			$data['User']['auth_password'] = $this->Auth->password($data['User']['password']);
			$data['User']['code'] = $this->RmUser->_generateCode('user_code');

			$data['UserConfig']['activation_code'] = $this->RmUser->_generateCode();
		}

		return $data;
	}

	function unList ( $data, $separator = ',' ) {
		return explode($separator, $data);
	}

	function _callUnset( $fieldArr, $data , $removeField = false) {
		if( !empty($fieldArr) ) {
			foreach ($fieldArr as $key => $value) {
				if( is_array($value) ) {
					foreach ($value as $idx => $fieldName) {
						if( isset($data[$key][$fieldName]) ) {
							unset($data[$key][$fieldName]);
						}else{
							if($removeField){
								unset($data[$key][$fieldName]);
							}
						}
					}
				} else {
					unset($data[$value]);
				}
			}
		}
		return $data;
	}

	function _callSet( $fieldArr, $data ) {
		if( !empty($fieldArr) && !empty($data) ) {
			$data = array_intersect_key($data, array_flip($fieldArr));
		}
		return $data;
	}

	function _callRequestSubarea ( $values, $customField = false ) {
		if( empty($values) ) {
			$values = array(
				'Subarea' => 'UserProfile',
			);
		} else if( !is_array($values) ) {
			$explode = explode('.', $values);

			if(count($explode) > 1){
				$parent_model = !empty($explode[0])?$explode[0]:false;
				$modelName = !empty($explode[1])?$explode[1]:false;
				$values = array(
					'Subarea' => $modelName,
				);
			}else{
				$values = array(
					'Subarea' => $values,
				);	
			}
			
		}

		if( !empty($values) ) {
			foreach ($values as $variableName => $modelName) {
				if(!empty($this->controller->request->data[$modelName][0])){
					foreach($this->controller->request->data[$modelName] AS $key => $model){
						if( !empty($model['city_id']) ) {
							$city_id = $model['city_id'];
						} else if( !empty($model['city']) ) {
							$city_id = $model['city'];
						}

						if( !empty($city_id) ) {
							$subareas = $this->controller->User->UserProfile->Subarea->getSubareas('list', false, $city_id);
							if($customField){
								$this->optionsLocation($modelName, $subareas, $key);
							}else{
								$this->controller->set(compact(
									'subareas'
								));	
							}
						}
					}
				}else{
					if(empty($parent_model)){
						if( !empty($this->controller->request->data[$modelName]['city_id']) ) {
							$city_id = $this->controller->request->data[$modelName]['city_id'];
						} else if( !empty($this->controller->request->data[$modelName]['city']) ) {
							$city_id = $this->controller->request->data[$modelName]['city'];
						}
					}else{
						if( !empty($this->controller->request->data[$parent_model][$modelName]['city_id']) ) {
							$city_id = $this->controller->request->data[$parent_model][$modelName]['city_id'];
						} else if( !empty($this->controller->request->data[$parent_model][$modelName]['city']) ) {
							$city_id = $this->controller->request->data[$parent_model][$modelName]['city'];
						}
					}
					

					if( !empty($city_id) ) {
						$subareas = $this->controller->User->UserProfile->Subarea->getSubareas('list', false, $city_id);
						$this->controller->set(compact(
							'subareas'
						));	
					}
				}
				
			}
		}
	}

	function optionsLocation( $modelName, $values, $key = 0){
		if(!empty($modelName) && !empty($values)){
			$subarea_list = sprintf('subareas_%s', $key);
			$this->controller->set($subarea_list, $values);	
		}
	}	

	function processSorting ( $params, $data, $with_param_id = true, $param_id_only = false, $redirect = true ) {
		$filter = $this->filterEmptyField($data, 'Search', 'filter');
		$sort = $this->filterEmptyField($data, 'Search', 'sort');
		$excel = $this->filterEmptyField($data, 'Search', 'excel');
		$min_price = $this->filterEmptyField($data, 'Search', 'min_price', 0);
		$max_price = $this->filterEmptyField($data, 'Search', 'max_price', 0);
		
		$min_building_size = $this->filterEmptyField($data, 'Search', 'min_building_size', 0);
		$max_building_size = $this->filterEmptyField($data, 'Search', 'max_building_size', 0);

		$min_lot_size = $this->filterEmptyField($data, 'Search', 'min_lot_size', 0);
		$max_lot_size = $this->filterEmptyField($data, 'Search', 'max_lot_size', 0);

		$user = $this->filterEmptyField($data, 'Search', 'user');
		$month_range = Common::hashEmptyField($data, 'Search.month_range');

		$named = $this->filterEmptyField($this->controller->params, 'named');

		if( !empty($with_param_id) ) {
			$param_id = $this->filterEmptyField($named, 'param_id');
			if( is_array($param_id) ) {
				$params = array_merge($params, $param_id);
			} else {
				$params[] = $param_id;
			}
		}

		if( !empty($param_id_only) ) {
			return $params;
		}

		if(!empty($data['Search']['change_url'])){
			unset($data['Search']['change_url']);
		}

		$dateFilter = Configure::read('Global.Data.dateFilter');
		$data = $this->_callUnset(array(
			'Search' => array(
				'sort',
				'direction',
				'excel',
				'action',
				'min_price',
				'max_price',
				'min_building_size',
				'max_building_size',
				'min_lot_size',
				'max_lot_size',
				'colview',
				'month_range',
				'pushstate_url',
			),
		), $data);

		if( !empty($dateFilter) ) {
			foreach ($dateFilter as $key => $fieldFilter) {
				$date = $this->filterEmptyField($data, 'Search', $fieldFilter);
				$fieldFrom = __('%s_from', $fieldFilter);
				$fieldTo = __('%s_to', $fieldFilter);

				$data = $this->_callUnset(array(
					'Search' => array(
						$fieldFilter,
					),
				), $data);

				if( empty($date) ) {
					$date_from = $this->filterEmptyField($data, 'Search', $fieldFrom);
					$date_to = $this->filterEmptyField($data, 'Search', $fieldTo);

					if( !empty($date_from) && !empty($date_to) ) {
						$date = sprintf('%s - %s', $date_from, $date_to);
					}
				}

				if( !empty($date) ) {
					$params = $this->_callConvertDateRange($params, $date, array(
						'date_from' => $fieldFrom,
						'date_to' => $fieldTo,
					));
				}
			}
		}

		$dataSearch = $this->filterEmptyField($data, 'Search');
		// if( isset($dataSearch['keyword']) ) {
		// 	$dataSearch['keyword'] = urlencode(trim($dataSearch['keyword']));
		// }
		
		if( !empty($dataSearch) ) {
			foreach ($dataSearch as $fieldName => $value) {
				if( is_array($value) ) {
					$value = array_filter($value);

					if( !empty($value) ) {
						$result = array();

						foreach ($value as $id => $boolean) {
							if( !empty($id) ) {
								$result[] = $id;
							}
						}

						$value = implode(',', $result);
					}
				}

				if( !empty($value) ) {
					if( !is_array($value) ) {
						$isSlash = strpos($value, '/');

						if( is_numeric($isSlash) ) {
							$value = str_replace('/', urlencode('/'), $value);
						}

						$params[$fieldName] = urlencode($value);
					} else {
						$params[$fieldName] = $value;
					}
				}
			}
		}
		if( !empty($filter) ) {
			$filterArr = strpos($filter, '.');

			if( !empty($filterArr) ) {
				$sort = $filter;
			}
		}

		if( !empty($sort) ) {
			$dataArr = explode('-', $sort);

			if( !empty($dataArr) && count($dataArr) == 2 ) {
				$sort = !empty($dataArr[0])?$dataArr[0]:false;
				$direction = !empty($dataArr[1])?$dataArr[1]:false;

				$sortLower = strtolower($sort);
				$directionLower = strtolower($direction);

				if( !in_array($direction, array( 'asc', 'desc' )) ) {
					$params[$sort] = $direction;
				} else {
					$params['sort'] = $sort;
					$params['direction'] = $direction;
				}
			}
		}

		if( !empty($excel) ) {
			$params['export'] = 'excel';
		}
		if( !empty($min_price) || !empty($max_price) ) {
			$min_price = $this->_callPriceConverter($min_price);
			$max_price = $this->_callPriceConverter($max_price);

			if( empty($max_price) ) {
				$price = $min_price;
			} else {
				$price = sprintf('%s-%s', $min_price, $max_price);
			}

			$params['price'] = $price;
		}
		if( !empty($min_building_size) || !empty($max_building_size) ) {
			$min_building_size = $this->_callPriceConverter($min_building_size);
			$max_building_size = $this->_callPriceConverter($max_building_size);

			if(!empty($min_building_size) && !empty($max_building_size)){
				$building_size = sprintf('%s-%s', $min_building_size, $max_building_size);
			}else if(!empty($max_building_size)){
				$building_size = sprintf('<%s', $max_building_size);
			}else if(!empty($min_building_size)){
				$building_size = sprintf('>%s', $min_building_size);
			}

			$params['building_size'] = $building_size;
		}
		if( !empty($min_lot_size) || !empty($max_lot_size) ) {
			$min_lot_size = $this->_callPriceConverter($min_lot_size);
			$max_lot_size = $this->_callPriceConverter($max_lot_size);

			if( !empty($min_lot_size) && !empty($max_lot_size) ){
				$lot_size = sprintf('%s-%s', $min_lot_size, $max_lot_size);
			}else if(!empty($max_lot_size)){
				$lot_size = sprintf('<%s', $max_lot_size);
			}else if(!empty($min_lot_size)){
				$lot_size = sprintf('>%s', $min_lot_size);
			}

			$params['lot_size'] = $lot_size;
		}

		if(!empty($user)){
			$params['user'] = $user;
		}

	//	B:REQUEST ROUTE IPA =====================================================

		$controllerName	= $this->controller->name;
		$redirectAction = Common::hashEmptyField($params, 'action');

		if(in_array(strtolower($controllerName), array('properties', 'profiles')) && in_array($redirectAction, array('find', 'property_find'))){
		//	cuma beberapa yang diambil
			$propertyModel	= $this->controller->User->Property;
			$propertyAction	= Common::hashEmptyField($dataSearch, 'property_action');
			$propertyType	= Common::hashEmptyField($dataSearch, 'typeid');

			if($propertyAction && is_numeric($propertyAction)){
				$actionName	= $propertyAction == 1 ? 'dijual' : 'disewakan';
				$params		= Hash::insert($params, 'property_action', $actionName);
			}

			if($propertyType){
				$propertyType	= explode(',', urldecode($propertyType));
				$propertyType__	= array_filter(array_map('intval', $propertyType));
				$isNumericArray	= empty($propertyType) == false;

				if($isNumericArray){
					$propertyType = $propertyType__;
					$propertyType = $propertyModel->PropertyType->getData('list', array(
						'fields'		=> array('PropertyType.id', 'PropertyType.slug'), 
						'conditions'	=> array(
							'PropertyType.id' => $propertyType, 
						), 
					));
				}

				$params = Hash::insert($params, 'type', implode(',', $propertyType));
				$params = Hash::remove($params, 'typeid');
			}

			$locationFields = array(
				'region'	=> 'Region', 
				'city'		=> 'City', 
				'subarea'	=> 'Subarea', 
			);

			foreach($locationFields as $fieldName => $modelName){
				$fieldValue = Common::hashEmptyField($dataSearch, $fieldName);

				if($fieldValue && is_numeric($fieldValue)){
					$modelData = $propertyModel->PropertyAddress->$modelName->getData('first', array(
						'conditions' => array(
							$modelName.'.id' => $fieldValue, 
						), 
					));

					$fieldValue	= Common::hashEmptyField($modelData, $modelName.'.slug');
					$params		= Hash::insert($params, $fieldName, $fieldValue);
				}	
			}

			$params = $this->_callUnset(array(
				'current_region_id',
				'current_city_id',
				'current_subarea_id',
			), $params);
		}

		if( !empty($month_range) ) {
			$date = $month_range;
			$params = Common::_callConvertMonthRange($params, $date);
		}

	//	E:REQUEST ROUTE IPA =====================================================
		if( !empty($redirect) ) {
			$this->controller->redirect($params);
		} else {
			return $params;
		}
	}

	function _callRefineParams ( $data, $default_options = false ) {
		$propertyModel = $this->controller->User->Property;
		$default_status = $this->filterEmptyField($default_options, 'status');

		$dateFilter = Configure::read('Global.Data.dateFilter');
		$dataSearch = $this->filterEmptyField($data, 'named');
		$sort = $this->filterEmptyField($data, 'named', 'sort');
		$filter = $this->filterEmptyField($data, 'named', 'filter');
		$status = $this->filterEmptyField($data, 'named', 'status', $default_status);
		$keyword = $this->filterEmptyField($data, 'named', 'keyword');
		$type = $this->filterEmptyField($data, 'named', 'type');
		$add_type = $this->filterEmptyField($data, 'named', 'add_type');
		$subareas = $this->filterEmptyField($data, 'named', 'subareas');
		$user = $this->filterEmptyField($data, 'named', 'user');
		$typeid = $this->filterEmptyField($data, 'named', 'typeid');
		$property_status_id = $this->filterEmptyField($data, 'named', 'property_status_id');
		$price = $this->filterEmptyField($data, 'named', 'price');

		$options = array();

		$dataString = $this->_callUnset(array(
			'sort',
			'direction',
			'status',
			'subareas',
			'type',
			'colview',
		), $dataSearch);

		if( !empty($dateFilter) ) {
			foreach ($dateFilter as $key => $fieldFilter) {
				$fieldFrom = __('%s_from', $fieldFilter);
				$fieldTo = __('%s_to', $fieldFilter);

				$dataString = $this->_callUnset(array(
					$fieldFrom,
					$fieldTo,
				), $dataSearch);

				$date_from = $this->filterEmptyField($data, 'named', $fieldFrom);
				$date_to = $this->filterEmptyField($data, 'named', $fieldTo);

				if( !empty($date_from) ) {
					$date = $this->_callReverseDateRange($date_from, $date_to);
					$this->controller->request->data['Search'][$fieldFilter] = $date;
					
					$this->controller->request->data['Search'][$fieldFrom] = date('d-m-Y', strtotime($this->filterEmptyField($data, 'named', $fieldFrom)));
					$this->controller->request->data['Search'][$fieldTo] = date('d-m-Y', strtotime($this->filterEmptyField($data, 'named', $fieldTo)));
				}
			}
		}

		if( !empty($dataString) ) {
			foreach ($dataString as $fieldName => $value) {
				$value = urldecode($value);
				$this->controller->request->data['Search'][$fieldName] = urldecode($value);
			}
		}

		$dataArr = $this->_callSet(array(
			'subareas',
			'type',
			'colview',
		), $dataSearch);

		if( !empty($dataArr) ) {
			foreach ($dataArr as $fieldName => $value) {
				$value = urldecode($value);
				$valueArr = explode(',', $value);

				if( !empty($valueArr) ) {
					$result = array();

					foreach ($valueArr as $key => $id) {
						$result[$id] = true;
					}

					$this->controller->request->data['Search'][$fieldName] = $result;
				}
			}
		}

		if( !empty($sort) ) {
			$direction = $this->filterEmptyField($data, 'named', 'direction', 'asc');
			$direction = strtolower($direction);
			$sortName = $sort;

			if( !empty($direction) ) {
				$sortName = sprintf('%s-%s', $sort, $direction);
			}

			$this->controller->request->data['Search']['sort'] = $sortName;
			$this->controller->request->data['Search']['filter'] = $sortName;
		}
		if( !empty($filter) ) {
			$this->controller->request->data['Search']['filter'] = $filter;
		}
		if( !empty($status) ) {
			$options['status'] = $status;
			$this->controller->request->data['Search']['status'] = $status;
		}

		if(!empty($user)){
			$this->controller->request->data['Search']['user'] = $user;
		}

		if(!empty($typeid)){
			$typeid = explode(',', urldecode($typeid));
			$datas = array();

			if( count($typeid) > 1 ) {
				foreach ($typeid as $key => $id) {
					$datas[$id] = true;	
				}
			} else {
				$datas = implode('', $typeid);
			}

			$this->controller->request->data['Search']['typeid'] = $datas;
		}
		else if(!empty($type)){
			$type = explode(',', urldecode($type));

			if(Hash::numeric($type) === false){
				$typeid = $propertyModel->PropertyType->getData('list', array(
					'fields'		=> array('PropertyType.id', 'PropertyType.id'), 
					'conditions'	=> array(
						'PropertyType.slug' => $type, 
					), 
				));

				if( count($typeid) > 1 ) {
					foreach($typeid as $key => $id){
						$typeid[$id] = true;
					}
				}
				else{
					$typeid = array_shift($typeid);
				}

				$this->controller->request->data['Search']['typeid'] = $typeid;
			}
		}

		$locationFields	= array(
			'region'	=> 'Region', 
			'city'		=> 'City', 
			'subarea'	=> 'Subarea', 
		);

		foreach($locationFields as $fieldName => $modelName){
			$fieldValue = Common::hashEmptyField($dataSearch, $fieldName);

			if($fieldValue && is_numeric($fieldValue) === false){
				
				$conditions = array(
					$modelName.'.slug' => $fieldValue, 
				);

				switch ($fieldName) {
					case 'subarea':
						if( !empty($this->controller->request->data['Search']['region']) ) {
							$conditions['Subarea.region_id'] = $this->controller->request->data['Search']['region'];
						}

						if( !empty($this->controller->request->data['Search']['city']) ) {
							$conditions['Subarea.city_id'] = $this->controller->request->data['Search']['city'];
						}
						break;
				}

				$modelData = $propertyModel->PropertyAddress->$modelName->getData('first', array(
					'conditions' => $conditions, 
				));

				$fieldValue	= Common::hashEmptyField($modelData, $modelName.'.id');
				$this->controller->request->data['Search'][$fieldName] = $fieldValue;
			}	
		}

		if(!empty($property_status_id)){
			$this->controller->request->data['Search']['property_status_id'] = $property_status_id;
		}
		
		if(!empty($add_type)){
			$this->controller->set('add_type', $add_type);
		}

		if( empty($dataArr['colview']) ) {
			$modelName = ucwords($this->controller->params['controller']);
			$type = strtolower($this->controller->action);

			$colview = $this->controller->Session->read(__('Sort.%s.%s', $modelName, $type));

			if( !empty($colview) ) {
				$colviews = explode(',', $colview);

				if( !empty($colviews) ) {
					foreach ($colviews as $key => $colname) {
						$this->controller->request->data['Search']['colview'][$colname] = true;
					}
				}
			}
		}

		if($price){
			$firstChar	= substr($price, 0, 1);
			$minPrice	= null;
			$maxPrice	= null;

			if(in_array($firstChar, array('>', '<'))){
				$price = substr($price, 1);

				if($firstChar == '<'){
					$maxPrice = $price;
				}
				else{
					$minPrice = $price;
				}
			}
			else{
				$price		= explode('-', $price);
				$minPrice	= Common::hashEmptyField($price, 0);
				$maxPrice	= Common::hashEmptyField($price, 1);
			}

			$this->controller->request->data['Search']['min_price'] = $minPrice;
			$this->controller->request->data['Search']['max_price'] = $maxPrice;
		}

		return $options;
	}

	function _isAdmin ( $group_id = false ) {
		$admin_id = Configure::read('__Site.Admin.List.id');

		if( empty($group_id) ) {
			$group_id = Configure::read('User.group_id');
		}

		if( in_array($group_id, $admin_id) ) {
			return true;
		} else {
			return false;
		}
	}

	function _isCompanyAdmin ( $group_id = false ) {
		$admin_id = Configure::read('__Site.Admin.Company.id');

		if( empty($group_id) ) {
			$group_id = Configure::read('User.group_id');
		}

		if( in_array($group_id, $admin_id) ) {
			return true;
		} else {
			return false;
		}
	}

	function _callMergeRecursive ( $data1, $data2, $options ) {
		if( !empty($options) && !empty($data2) ) {
			foreach ($options as $key => $fieldName) {
				if( !empty($data2[$fieldName]) ) {
					$dateTmp = array_filter($data2[$fieldName]);

					if( !empty($dateTmp) ) {
						if( !empty($data1[$fieldName]) ) {
							$data1[$fieldName] = array_merge($data1[$fieldName], $data2[$fieldName]);
						} else {
							$data1[$fieldName] = $data2[$fieldName];
						}
					}
				}
			}
		}

		return $data1;
	}

	function removeLastCharacter ( $str, $char ) {
		return rtrim($str, $char);
	}
	
	function findIp() { 
		if(getenv("HTTP_CLIENT_IP")) 
			return getenv("HTTP_CLIENT_IP");  
		elseif(getenv("HTTP_X_FORWARDED_FOR")) 
			return getenv("HTTP_X_FORWARDED_FOR");  
		else  
			return getenv("REMOTE_ADDR");  
	}

	function _callViewCounter( $id = false, $user_id = false, $type_view = 'normal', $fieldName = 'property_id', $modelName = 'PropertyView', $options = array() ) {
		$user_ip = $this->findIp();
		$dataBrowser = $this->getBrowser();

		$is_mobile = Common::hashEmptyField($options, 'is_mobile', Configure::read('Global.Data.MobileDetect.mobile'));
		$device = Common::hashEmptyField($options, 'device');

		if($is_mobile && empty($device)){			
			$device = 'mobile';
		} else {
			$device = 'dekstop';
		}

		$principle_id = Configure::read('Principle.id');

		if( !empty($id) && in_array($modelName, array('PropertyView', 'PropertyLead')) ){
			$customModel = ClassRegistry::init($modelName);
			
			$Property = $customModel->Property;
			$property = $Property->findById($id);

			$agent_id = Common::hashEmptyField($property, 'Property.user_id');

			$data[$modelName]['agent_id'] = $agent_id;
		}

		$data[$modelName][$fieldName]= $id;
		$data[$modelName]['user_id'] = !empty($user_id)?$user_id:0;
		$data[$modelName]['ip']	= $user_ip;
		$data[$modelName]['user_agent'] = !empty($dataBrowser['userAgent'])?$dataBrowser['userAgent']:'';
		$data[$modelName]['is_mobile'] = $is_mobile;
		$data[$modelName]['browser'] = !empty($dataBrowser['browser'])?$dataBrowser['browser']:'';
		$data[$modelName]['os'] = !empty($dataBrowser['os'])?$dataBrowser['os']:'';
		$data[$modelName]['utm'] = $type_view;
		$data[$modelName]['device'] = $device;
		$data[$modelName]['principle_id'] = !empty($principle_id)?$principle_id:0;

		return $data;
	}

	function _callSaveVisitor ( $id, $modelName = 'Property', $fieldName = 'property_id', $options = array() ) {
		$cacheConfig = Common::hashEmptyField($options, 'cache_config', 'log_daily');
		$is_mobile = Common::hashEmptyField($options, 'is_mobile');
		$is_cookie = Common::hashEmptyField($options, 'is_cookie', true, array(
			'isset' => true,
		));
		$cookie_time = Common::hashEmptyField($options, 'cookie_time', '1 hour');
		$slug = Common::hashEmptyField($options, 'slug');
		$name_cookie = sprintf('%s_%s_%s', $modelName, $this->controller->parent_id, $id);

		if($slug){
			$name_cookie = sprintf('%s_%s', $slug, $name_cookie);			
		}

		if($is_cookie || $is_mobile){
			$getCookie = $this->controller->Cookie->read($name_cookie);
			if($is_mobile){
				$getCache = Cache::read($name_cookie, $cacheConfig);
			}
		}

		$data = false;
		if( empty($getCookie) && empty($getCache) ){
			$data = $this->_callViewCounter($id, $this->controller->user_id, FULL_BASE_URL, $fieldName, $modelName, $options);

			if($is_mobile){
				Cache::write($name_cookie, true, $cacheConfig);
				$is_cookie = false;
			}

			if($is_cookie){
				$this->controller->Cookie->write($name_cookie, true, false, $cookie_time);
			}
		}
		return $data;
	}

	function wrapWithHttpLink( $url ){
		$result		= $url;
		$textUrl	= 'http://';
		$textUrls	= 'https://';

		if( !empty($url) ) {
			$flag = array();

			if( strpos($url, $textUrl) === false && substr($url, 0, 7) != $textUrl ) {
				$flag[] = true;
			}
			if( strpos($url, $textUrls) === false && substr($url, 0, 8) != $textUrls ) {
				$flag[] = true;
			}

			if( count($flag) == 2 ) {
				$result = sprintf("%s%s", $textUrl, $url);
			}
		}

		return $result;
	}

	function _callBeforeSaveBanner( $data = false, $modelName = false ) {
		if( !empty($data) && !empty($modelName) ) {
			$order = $this->filterEmptyField($data, $modelName, 'order');
			$url = $this->filterEmptyField($data, $modelName, 'url');
			$url = $this->wrapWithHttpLink($url);

			if( !empty($url) ) {
				$data[$modelName]['url'] = $url;
			}

			if( !empty($data[$modelName]['start_date']) && empty($data[$modelName]['end_date']) ) {
				$data[$modelName]['end_date'] = NULL;
			}

			if( !empty($data[$modelName]['start_date']) ) {
				$data[$modelName]['start_date'] = $this->getDate($data[$modelName]['start_date']);
			} else {
				$data[$modelName]['start_date'] = NULL;
			}

			if( !empty($data[$modelName]['end_date']) ) {
				$data[$modelName]['end_date'] = $this->getDate($data[$modelName]['end_date']);
			} else {
				$data[$modelName]['end_date'] = NULL;
			}

			if( empty($order) ) {
				$data[$modelName]['order'] = 0;
			}
		}
		
		return $data;
	}

	function _callBeforeRenderBanner( $data = false, $modelName = false ) {
		if( !empty($data) && !empty($modelName) ) {
			if( !empty($data[$modelName]['start_date']) ) {
				$data[$modelName]['start_date'] = $this->getDate($data[$modelName]['start_date'], true);
			}

			if( !empty($data[$modelName]['end_date']) ) {
				$data[$modelName]['end_date'] = $this->getDate($data[$modelName]['end_date'], true);
			}
		}
		return $data;
	}

	function getAccessPage($config, $field_target, $number_access){
		if( (!empty($config['UserCompanyConfig'][$field_target]) && $number_access < $config['UserCompanyConfig'][$field_target]) || empty($config['UserCompanyConfig'][$field_target]) ){
			return true;
		}else{
			return false;
		}
	}

	function converterRate ( $price, $currency ) {
		$rate = $this->filterEmptyField($currency, 'Currency', 'rate');

		if( !empty($rate)  ) {
			$price = $price * $rate;
		}

		return $price;
	}

	function convertPriceToString ( $price, $result = '' ) {
		if( !empty($price) ) {
			$resultTmp = str_replace(array(',', ' '), array('', ''), trim($price));

			if( !empty($resultTmp) ) {
				$result = $resultTmp;
			}
		}

		return $result;
	}

	function getBrowser() {
		$u_agent = Common::hashEmptyField($_SERVER, 'HTTP_USER_AGENT');
		$bname = 'Unknown';
		$os = 'Unknown';
		$version= "";

		// get operating system
		if (preg_match('/linux/i', $u_agent)) {
			$os = 'linux';
		}
		else if (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$os = 'mac';
		}
		else if (preg_match('/windows|win32/i', $u_agent)) {
			$os = 'windows';
		}

		// Next get the name of the useragent yes separately and for good reason.
		if (preg_match('/MSIE/i',$u_agent) && !preg_match('/OPR/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		else if (preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		else if (preg_match('/OPR/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		else if (preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		else if (preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		else if (preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}
		else
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}

		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// See how many we have.
		$i = count($matches['browser']);
		if ($i != 1) {
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub) && !empty($matches['version'][0])){
				$version = $matches['version'][0];
			}
			else if(!empty($matches['version'][1])) {
				$version = $matches['version'][1];
			}
		}
		else {
			$version = $matches['version'][0];
		}

		if ( $version == null || $version == "" ) {
			$version = "?";
		}

		return array(
			'userAgent' => $u_agent,
			'browser'	  => $bname,
			'version'   => $version,
			'os'  => $os,
		);
	}

	function registerUser( $data ){
		$id = $this->controller->user_id;

		if( !empty( $data ) ) {
			$modelUser = $this->controller->User;
			$email = $this->filterEmptyField($data, 'User', 'email', '-');
			$random_password = false;

			if( !empty($id) ) {
				$conditions = array(
					'User.id' => $id,
				);
			} else {
				$conditions = array(
					'User.email' => $email,
				);
			}

			$user = $modelUser->getData('first', array(
				'conditions' => $conditions,
			), false);

			if( empty($id) && empty( $user ) ) {
				$modelUser->create();
				$modelUser->UserProfile->create();
				$username = $this->filterEmptyField($data, 'User', 'name', '-');
				$random_password = implode('', $this->createRandomNumber(6));
				if( empty($data['User']['last_name']) ) {
					unset($data['User']['last_name']);
				}

				$data['User']['code'] = $this->RmUser->_generateCode('user_code');
				$data['User']['password'] = $this->controller->Auth->password( $random_password );
				$data['User']['activation_code'] = md5(date('mdY').String::uuid());

			} else if( !empty( $user ) ) {
				$modelUser->id = $id;

				$user = $modelUser->UserProfile->getMerge($user, $id);
				$user_profile_id = $this->filterEmptyField($user, 'UserProfile', 'id');

				if( !empty($id) ) {
					$modelUser->UserProfile->id = $user_profile_id;
					$data = $this->_callUnset(array(
						'User' => array(
							'email',
						)
					), $data);
				}
			}

			$modelUser->set($data);

			if( $modelUser->save( $data ) ) {
				$data['User']['id'] = $modelUser->id;
				$data['UserProfile']['user_id'] = $modelUser->id;

				$activation_code = $this->filterEmptyField($data, 'User', 'activation_code');
				$modelUser->UserProfile->save( $data );

				if( !empty($random_password) ) {
					$this->sendActivationEmailAdvance($data, $activation_code, $random_password);
				}
			}
		}
	}

	function sendActivationEmailAdvance($user, $activation_code, $password) {
		$mail_params = array(
			'name' => ( trim($user['User']['full_name']) ) ? $user['User']['full_name'] : 'New User',
			'email' => $user['User']['email'],
			'username' => !empty($user['User']['username']) ? $user['User']['username'] : '',
			'activation_code' => $user['User']['activation_code'],
			'password' => $password,
			'id' => $user['User']['id'],
			'browser' => false
		);
		
		if($this->sendEmail($user['User']['full_name'], $user['User']['email'], 'registration_advance', sprintf(__('Selamat datang di %s'), Configure::read('__Site.site_name')), $mail_params)) {
			return true;
		} else {
			return false;
		}
	}

	function _callProjectSlug () {
		$params = $this->controller->params;
		return $this->filterEmptyField($params, 'project_slug');
	}

	function _callConvertDateRange ( $params, $date, $options = array() ) {
		$startField = $this->filterEmptyField($options, 'date_from', false, 'date_from');
		$endField = $this->filterEmptyField($options, 'date_to', false, 'date_to');

		$date = urldecode($date);
		$dateArr = explode(' - ', $date);

		if( !empty($dateArr) && count($dateArr) == 2 ) {
			$fromDate = !empty($dateArr[0])?$this->getDate($dateArr[0]):false;
			$toDate = !empty($dateArr[1])?$this->getDate($dateArr[1]):false;

			$params[$startField] = $fromDate;
			$params[$endField] = $toDate;
		}

		return $params;
	}

	function _callReverseDateRange ( $fromDate, $toDate ) {
		$fromDate = urldecode($fromDate);
		$toDate = urldecode($toDate);
		$date = false;

		if( !empty($fromDate) ) {
			$fromDate = $this->getDate($fromDate, true);
			$date[] = $fromDate;
		}
		if( !empty($toDate) ) {
			$toDate = $this->getDate($toDate, true);
			$date[] = $toDate;
		}

		if( !empty($date) && is_array($date) ) {
			$date = array_filter($date);
			$date = implode(' - ', $date);
		}

		return $date;
	}

	function truncate( $str, $len, $ending = '...', $stripTag = true ) {
		App::import('Helper', 'Text'); 
		$Text = new TextHelper(new View(null));

		$str = trim($str);

		if( !empty($stripTag) ) {
			$str = $this->safeTagPrint($str);
		}

		if($len > 0){
			return $Text->truncate($str, $len, array(
				'ending' => $ending,
				'exact' => false
			));
		}else{
			return '';
		}
	}

	function truncateByStr($str, $len, $ending = '...', $stripTag = true){
		if(!empty($str)){

			if( !empty($stripTag) ) {
				$str = $this->safeTagPrint($str);
			}

			if(strlen($str) <= $len){
				$ending = '';
			}

			$str = substr($str, 0, $len).$ending;
		}
		return $str;
	}

	function getDataRefineProperty () {
		$categoryStatus = $this->getGlobalVariable('category_status');
		$propertyDirections = $this->controller->User->Property->PropertyAsset->PropertyDirection->getData('list', array(
            'cache' => __('PropertyDirection.List'),
        ));
		$propertyActions = $this->controller->User->Property->PropertyAction->getData('list', array(
			'cache' => __('PropertyAction.List'),
		));
		$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
			'cache' => __('PropertyType.List'),
		));

		$this->controller->set(compact(
			'propertyTypes', 'propertyActions',
			'propertyDirections', 'categoryStatus'
		));
	}

	/**
	*
	*	function mengambil data view email
	*	@param string $template : nama template email
	*	@param array $param : parameter untuk render email
	*	@return string $dataArr
	*/
	function renderViewToVariable( $template, $default_layout, $params = false ){
		$this->controller->set('params', $params);
		$this->controller->layout = 'Emails/html/default';
		$ViewTemplate = $this->controller->render('/Emails/html/'.$template);

		$_view = $ViewTemplate->body();

		$this->controller->layout = $default_layout;

		return $_view;
	}

	function _callCheckAccessMsg ( $from_id = false, $to_id = false ) {
		if($this->Rest->isActive()){
			$agent_id = $this->controller->User->getAgents( $this->controller->parent_id, true );
		}else{
			$authGroupID	= Configure::read('User.group_id');
			$isIndependent	= Common::validateRole('independent_agent', $authGroupID);

			$agent_id = empty($isIndependent) ? $this->controller->agent_id : Configure::read('User.id');
			$agent_id = (array) $agent_id;
		}

		if( !empty($agent_id) && (in_array($from_id, $agent_id) || in_array($to_id, $agent_id)) ) {
			return true;
		} else {
            $group_id = Configure::read('User.group_id');

            if( $group_id == 4 ) {
                $parent_id = Configure::read('Principle.id');
                $principle_id = $this->controller->User->getAgents($parent_id, true, 'list', false, array('role' => 'principle'));
				
				$fromUser = $this->controller->User->getMerge(array(), $from_id);
				$from_parent_id = $this->filterEmptyField($fromUser, 'User', 'parent_id');

				if( in_array($from_parent_id, $principle_id) ) {
					return true;
				} else {
					$toUser = $this->controller->User->getMerge(array(), $to_id);
					$to_parent_id = $this->filterEmptyField($toUser, 'User', 'parent_id');
					
					if( in_array($to_parent_id, $principle_id) ) {
						return true;
					} else {
						return false;
					}
				}
            } else {
				$adminId = $this->controller->User->getListAdmin();

				if( in_array($from_id, $adminId) || in_array($to_id, $adminId) ) {
					return true;
				} else if( $from_id == Configure::read('Principle.id') || $to_id == Configure::read('Principle.id') ) {
					return true;
				} else {
					return false;
				}
			}
		}
	}

	function defaultSearch ( $params, $data ) {
		if( !empty($data) ) {
			foreach ($data as $fieldName => $value) {
				if (!empty($value)) {
					$this->controller->request->data['named'][$fieldName] = $value;

					if( is_array($params['named']) ) {
						if( !array_key_exists($fieldName, $params['named']) ) {
							$params['named'][$fieldName] = $value;
						}
					} else {
						$params['named'][$fieldName] = $value;
					}
					
				}
				
			}
		}

		return $params;
	}

	function getEmailConverter ( $email ) {
		$emailArr = explode('|', $email);

		if( !empty($emailArr) ) {
			$email = reset($emailArr);
		}

		return trim($email);
	}

	function getCombineDate ( $startDate, $endDate, $empty = false, $emptyEndDate = ' - ..', $options = array() ) {
		$customDate	= false;
		$startDate	= $startDate == '0000-00-00' ? NULL : $startDate;
		$endDate	= $endDate == '0000-00-00' ? NULL : $endDate;

		$divider = $this->filterEmptyField($options, 'divider', false, '-');

		if( !empty($startDate) && !empty($endDate) ) {
			$startDate = strtotime($startDate);
			$endDate = strtotime($endDate);

			if( $startDate == $endDate ) {
				$customDate = date('d M Y', $startDate);
			} else if( date('M Y', $startDate) == date('M Y', $endDate) ) {
				$customDate = sprintf('%s - %s', date('d', $startDate), date('d M Y', $endDate));
			} else if( date('Y', $startDate) == date('Y', $endDate) ) {
				$customDate = sprintf('%s - %s', date('d M', $startDate), date('d M Y', $endDate));
			} else {
				$customDate = sprintf('%s - %s', date('d M Y', $startDate), date('d M Y', $endDate));
			}
		} else if( !empty($startDate) ) {
			$startDate = strtotime($startDate);
			$customDate = sprintf('%s%s', date('d M Y', $startDate), $emptyEndDate);
		} else if( !empty($empty) ) {
			$customDate = $empty;
		}

		return $customDate;
	}

	/*
	* manage session url
	*
	* domain = digunakan untuk set domain ketika domain tidak terdaftar
	* off_domain = digunakan untuk mengakhiri sesi dengan nilai 1
	*/
	function manage_base_url(){
		$_base_url = FULL_BASE_URL;

		$params = $this->controller->params;

		$session_name = 'Url_Cookie';
		$session_url = $this->controller->Cookie->read($session_name);

		if(!empty($params->query['domain'])){
			$this->controller->Cookie->write($session_name, $params->query['domain'], false, '24 hour');
		}else if(!empty($params->query['off_domain'])){
			$this->controller->Cookie->delete($session_name);
		}

		if(!empty($params->query['domain'])){
			$_base_url = $params->query['domain'];
		}else if(!empty($session_url)){
			$_base_url = $session_url;
		}

		return $_base_url;
	}

	/*
	* validate whether requested page can be accessed or not
	*/
	/**
	*	function authPage
	*	@param array $pages : list page yang akan divalidasi
	*	@param array $rules : list permission dari page 
	*	@return bool

		struktur $pages = array(
			'nama_controller_1' => array('nama_action_1' => 'field_permission_1', 'nama_action_2' => 'field_permission_2'), 
			'nama_controller_2' => array('nama_action_1' => 'field_permission_1', 'nama_action_2' => 'field_permission_2')
		)

		struktur $permission = array('field_permission_1' => 1, 'field_permission_2' => 0,'field_permission_3' => 1)
	*/
	public function authPage($pages = array(), $rules = array()){
		if(empty($pages) || empty($rules)){
			return;
		}

		$controller	= strtolower($this->controller->params['controller']);
		$action		= strtolower($this->controller->params['action']);
		$pages		= $pages ? $pages : $this->authPageDefaultPages;
		$rules		= $rules ? $rules : Configure::read('Config.Company.data');

	//	set fixed "pages group" to be validated
		$pages		= isset($pages[$controller]) && is_array($pages[$controller]) ? $pages[$controller] : $pages;
		$fieldName	= isset($pages[$action]) ? $pages[$action] : NULL;

		if(isset($rules[$fieldName]) && $rules[$fieldName] != 1){
			return FALSE;
		}

		return TRUE;
	}

	/*
	* clean any specialchars found with another char / or none if nothing is set
	*/
	/**
	*	function cleanSpecialChar
	*	@param	string		$string			: string to be cleaned
	*	@param	char/string	$replaceChar	: replacer
	*	@return	string						: cleaned string
	*/
	public function cleanSpecialChar($string = '', $replaceChar = ''){
		return preg_replace('/[^A-Za-z0-9\-]/', $replaceChar, $string);
	}

	function getColorCode ( $value, $divider = '#', $position = 'left', $addText = '', $to_allocate = false ) {
        $result = '';
        if(!empty($value)){
        	$find = strstr($value, 'rgba(');

	        if( !empty($value) && empty($find) && substr($value, 0, 1) != '#' ) {
	        	if( $position == 'right' ) {
	            	$value = sprintf('%s%s', $value, $divider);
	            } else {
	            	$value = sprintf('%s%s', $divider, $value);
	            }

	            $result = $value.$addText;
	        }else if(!empty($value) && !empty($find) && $to_allocate){
	        	$str = str_replace(array('rgba(', ')'), '', $value);
	        	$result = explode(',', $str);
	        	
	        	unset($result[count($result) - 1]);
	        }
        }
        
        return $result;
    }

    /**
	*
	*	menghapus foto yan ada
	*
	*	@param string $pathfolder - path sampai ke folder tujuan
	*	@param string $filename - nama file yang akan di hapus
	*	@param string $dimensions - memilih jenis foto yang akan di hapus 
	*	@param boolean $deleteUploadFile - perintah untuk hapus file
	*	@param string $project_path - project path
	*	@param string $project_path - project path
	*/
	function deletePathPhoto( $pathfolder = false, $filename = false, $dimensions = false, $deleteUploadFile = true, $project_path = false ) {
		if( !empty($filename) ) {
			if( !empty($project_path) ) {
				$project_path = DS.$project_path;
			} else {
				$project_path = '';
			}

			$path = Configure::read('__Site.thumbnail_view_path').DS.$pathfolder;
			$pathUpload = Configure::read('__Site.upload_path').DS.$pathfolder.$project_path.$filename;
			$pathUpload = str_replace('/', DS, $pathUpload);

			if( $deleteUploadFile && file_exists($pathUpload) ) {
				unlink($pathUpload);
			}

			if( !$dimensions ) {
				$dimensions = array();
				if( $pathfolder == 'users' ) {
					$dimensions = Configure::read('__Site.dimension_profile');
				}
				$dimensions = array_merge($dimensions, Configure::read('__Site.dimension'));
			}

			foreach ($dimensions as $key => $dimension) {
				$urlPhoto = $path.DS.$key.$project_path.$filename;
				$this->deletePhoto($urlPhoto);
			}
		}
	}

	/**
	*
	*	menghapus foto yang ada
	*
	*	@param string $urlPhoto - path sampai ke file tujuan
	*/
	function deletePhoto ( $urlPhoto ) {
		$urlPhoto = str_replace('/', DS, $urlPhoto);

		if(file_exists($urlPhoto)) {
			unlink($urlPhoto);
		}
	}

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	function replaceCodeDate($content){
		$cek_custom_date = strstr($content, '*|DATE:');
		$cek_date = strstr($content, '*|DATE|*');

		if(!empty($cek_date)){
			$content = str_replace('*|DATE|*', date('d M Y'), $content);
		}
		
		if(!empty($cek_custom_date)){
			$content_explode = explode('*|', $content);
			
			$temp_date = array();
			foreach ($content_explode as $key => $value) {
				if( strpos($value, '|*') ){
					$date_format = $this->get_string_between($value, 'DATE:', '|*');
					
					if(!empty($date_format)){
						$temp_date[] = array(
							'format' => sprintf('*|DATE:%s|*', $date_format),
							'date' => date($date_format)
						);
					}
				}
			}

			if(!empty($temp_date)){
				foreach ($temp_date as $key => $value) {
					$content = str_replace($value['format'], $value['date'], $content);
				}
			}
		}
		
		return $content;
	}

	function checkRootDomain ( $_base_url ) {
        $debug = Configure::read('debug');
        $checkDomain = strstr($_base_url, 'pasiris.com');

        if( !empty($checkDomain) ) {
            $site_url_default = 'http://v4.pasiris.com';
        } else {
            $site_url_default = 'http://ww.v4.rumahku.com';
        }

        if($debug == 0){
            $site_url_default = 'http://www.rumahku.com';
        }

        return $site_url_default;
    }

	function filterArray( $list, $allowed, $explode_allowed = true, $implode_result = true ) {
		$result = array();
		if( !empty($list) && !empty($allowed) ) {
			if( !empty($explode_allowed) && !is_array($allowed) ) {
				$allowed = explode(',', $allowed);
			}

			$result = array_intersect_key($list, array_flip($allowed));
			if( !empty($implode_result) ) {
				$result = implode(',', $result);
			}
		}

		return $result;
	}

	function getFormatPrice ($price, $empty = 0) {
		App::uses('CakeNumber', 'Utility'); 
		if( !empty($price) ) {
			return CakeNumber::currency($price, '', array('places' => 0));
		} else {
			return $empty;
		}
	}

	function getCurrencyPrice ($price, $empty = false, $currency = false) {
		App::uses('CakeNumber', 'Utility'); 
		
		if( !empty($empty) && empty($price) ) {
			return $empty;
		} else {
			if( empty($currency) ) {
				$currency = Configure::read('__Site.config_currency_symbol');
			}

			return CakeNumber::currency($price, $currency, array('places' => 0));
		}
	}

	function errorReport($msg, $code, $message_exception, $line_error = 0, $path_error_file = '', $option_errors = array()){
		$params = $this->controller->params;
		
		$controller = $params->params['controller'];
		$action = $params->params['action'];
		$HTTP_HOST = $_SERVER['HTTP_HOST'];
		
		$date = date('Y-m-d');

		$name_cookie = sprintf('Error-Handling-%s-%s-%s-line%s-%s', $HTTP_HOST, $controller, $action, $line_error, $date);
			
		$getCookie = $this->controller->Cookie->read($name_cookie);

		if(empty($getCookie)){
			$options = array(
				'message_exception' => $message_exception,
				'line_error' => $line_error,
				'path_error_file' => $path_error_file
			);

			if(!empty($option_errors)){
				$options = array_merge($options, $option_errors);
			}

			$this->_writeErrorFile('log_error', $message_exception, $line_error, $path_error_file, $msg);
			
			if($code >= 500){
				// $this->sendEmail('Developer', 'rnd@rumahku.com', 'error_code', $msg, $options);
				$this->sendEmail('Developer', array('ichsan@rumahku.com', 'randy@rumahku.com', 'donirumahku@gmail.com', 'anggarumahku@gmail.com'), 'error_code', $msg, $options);
			}

			$this->controller->Cookie->write($name_cookie, $date, false, '24 hour');
		}
	}

	private function _writeErrorFile($path_error_folder, $message_exception, $line_error, $path_error_file, $message_from_developer, $options = array()){
		$data 			= $this->filterEmptyField($options, 'data', false, '');
		$old_data 		= $this->filterEmptyField($options, 'old_data', false, '');
		$document_id 	= $this->filterEmptyField($options, 'document_id', false, '');
		$user_id 		= $this->filterEmptyField($options, 'user_id', false, '');
		$validation_data 		= $this->filterEmptyField($options, 'validation_data', false, '');

		$params 	= $this->controller->params;
		$controller = $params->params['controller'];
		$action 	= $params->params['action'];
		$HTTP_HOST 	= $_SERVER['HTTP_HOST'];

		$path_file = sprintf('error-%s', date('Y-m-d'));

		$path = Configure::read('__Site.error_path');

		$path_folder = $path.DS.$path_error_folder;
		$path_file_full = $path.DS.$path_error_folder.DS.$path_file.'.txt';

		if( !file_exists($path_folder) ) {
    		mkdir($path_folder, 0777, true);
    	}

    	$txt = sprintf('[time : %s] [line : %s]', date('H:i:s'), $line_error);
    	$txt .= "\r\n".sprintf('[HOST : %s] [Controller : %s] [action : %s]', $HTTP_HOST, $controller, $action);
    	
    	if(!empty($document_id)){
    		$txt .= sprintf(' [document_id : %s]', $document_id);
    	}

    	if(!empty($user_id)){
    		$txt .= sprintf(' [user_id : %s]', $user_id);
    	}
    	
    	$txt .= "\r\n".sprintf('[Error : %s] [Path : %s] [message-developer : %s]', $message_exception, $path_error_file, $message_from_developer);

    	if(!empty($data)){
    		if(is_array($data)){
    			$data = var_dump($data);	
    		}
			
			$txt .= "\r\n".sprintf('[data : %s]', $data);
		}

		if(!empty($old_data)){
    		if(is_array($old_data)){
    			$old_data = var_dump($old_data);	
    		}
			
			$txt .= "\r\n".sprintf('[old_data : %s]', $old_data);
		}

		if(!empty($validation_data)){
    		if(is_array($validation_data)){
    			$validation_data = var_dump($validation_data);
    		}
			
			$txt .= "\r\n".sprintf('[validation_data : %s]', $validation_data);
		}

    	if(file_exists($path_file_full)){
    		$file = fopen($path_file_full, "r+") or die("Unable to open file!");

			if(filesize($path_file_full) > 0){
				$txt_temp = fread($file, filesize($path_file_full));
				
				$txt = "\r\r\n\n".$txt;
			}
    	}else{
    		$file = fopen($path_file_full, "w") or die("Unable to open file!");
    	}
		
		fwrite($file, $txt);

		fclose($file);
		
		chmod($path_file_full, 0777);
	}

	function CurrentUrlWithoutTLD(){
		$url = $_SERVER['HTTP_HOST'];
		preg_match("/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/", parse_url(FULL_BASE_URL, PHP_URL_HOST), $_domain_tld);
		
		return str_replace(array('.'.$_domain_tld[0], 'www.', 'ww.'), '', $url);
	}

	function _callAllowAccess ( $field ) {
		$isAdmin		= Configure::read('User.admin');
		$authGroupID	= Configure::read('User.group_id');

		if( $isAdmin || $authGroupID == 1 ) {
			return true;
		} else {
			$_config	= Configure::read('Config.Company.data');
			$is_allow	= Common::hashEmptyField($_config, sprintf('UserCompanyConfig.%s', $field), 0);

			return $is_allow;
		}
	}

    function _callDashboardUrl () {
    	$group_id = Configure::read('User.group_id');

		if($group_id == 10){
			$dashboardUrl = array(
				'controller' => 'ebrosurs', 
				'action' => 'index', 
				'client' => TRUE
			);
		} else{
			$dashboardUrl = $this->controller->Auth->redirect();
		}

		Configure::write('User.dashboard_url', $dashboardUrl);
		return $dashboardUrl;
    }

    function _callValidateIllegalAccess () {
		$rest_api = Configure::read('Rest.token');
		$_base_url = $this->controller->_base_url;
		$basic_url	= str_replace(array('http://', 'https://', '/'), null, $_base_url);
		$main_prime = Configure::read('Global.Data.landing_page.main');

		if( in_array($basic_url, array( 'www.agentrealestate.id', 'www.agentproperty.id' )) ) {
			$root_url = Configure::read('Global.Data.landing_page.agent');
        	$this->controller->redirect($root_url);
        } else if( empty($rest_api) && $_base_url != $main_prime ) {
			$controller	= strtolower($this->controller->params->controller);
			$action		= strtolower($this->controller->params->action);

			$primeURL		= Configure::read('Global.Data.landing_page.agent');
			$dataCompany	= Configure::read('Config.Company.data');

		//	PERSONAL PAGE ======================================================================================
		//	karena url nya http://personalweb/profiles <- wajib pake controller profiles
		//	kalo $isHomePage langsung di-redirect (khusus saat visit home)

			$isPrimeDomain	= Common::isPrimeDomain();
			$isHomePage		= (empty($controller) || $controller == 'pages') && $action == 'home';

			if(empty($isPrimeDomain)){
			//	kalo personal page isinya agent id, kalo yang biasa principle id
				$configUserID			= Common::hashEmptyField($dataCompany, 'User.id');
				$configUserDomain		= Common::hashEmptyField($dataCompany, 'UserConfig.personal_web_url');
				$configCompanyDomain	= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.domain');
				$isPersonalPage			= Configure::read('Config.Company.is_personal_page');

				if(empty($configUserID) || $configUserID && empty($configUserDomain) && empty($configCompanyDomain)){
				//	ini kasus domain udah di daftarin tapi user_config belum ada
					// $this->redirectReferer(__('Domain tidak valid'), 'error', $primeURL);
				}

			//	if($isPersonalPage && $isHomePage){
			//	//	homepage punya company, bukan personal page
			//		$this->controller->redirect(array(
			//			'plugin'		=> false, 
			//			'admin'			=> false, 
			//			'controller'	=> 'profiles', 
			//		));
			//	}
			//	else if(empty($configUserID) || empty($configDomain)){
			//	//	ini kasus domain udah di daftarin tapi user_config belum ada
			//		$this->redirectReferer(__('Domain tidak valid'), 'error', $primeURL);
			//	}
			}
			else if($isPrimeDomain && $isHomePage){
				$this->controller->redirect(array(
					'admin'			=> true, 
					'controller'	=> 'users', 
					'action'		=> 'login', 
				));
			}

		//	====================================================================================================

			$authUserID		= $this->Auth->user('id');
			$authGroupID	= $this->Auth->user('group_id');
			$isAdmin		= $this->_isAdmin($authGroupID);
			$isCompanyAdmin	= $this->_isCompanyAdmin($authGroupID);

			if( $isAdmin === false && !in_array($controller, array( 'profiles', 'crontab', 'api', 'api_properties', 'api_kprs', 'api_users', 'projects' )) ){
				$currentDate = date('Y-m-d');

				if($isPrimeDomain && in_array($action, array('admin_login', 'admin_logout', 'get_projects_from_primedev', 'get_result_request_primedev')) === false){
					if($authUserID){
					//	user login di prime padahal bukan admin prime (data company pasti kosong)
						$userConfig = $this->controller->User->UserConfig->getData('first', array(
							'conditions' => array(
								'UserConfig.user_id' => $authUserID, 
							), 
						));

						$redirectURL = Router::url(array(
							'controller'	=> 'users', 
							'action'		=> 'logout', 
							'admin'			=> true, 
						), true);

					//	membership package id personal page
						$userPackageID	= Common::hashEmptyField($userConfig, 'UserConfig.membership_package_id');
						$userEndDate	= Common::hashEmptyField($userConfig, 'UserConfig.end_date');

						if($authGroupID == 1){
						//	INDEPENDENT AGENT
						//	- independent agent boleh masuk selama punya membership personal page

							if($userPackageID){
								$isExpired = strtotime($currentDate) > strtotime($userEndDate);
							}
							else{
							//	belum pernah beli membership, redirect ke landing page primesystem
								$message = __('Maaf, Anda tidak berhak mengakses halaman tersebut.');
								$message = __('%s Anda bisa berlangganan paket Membership Personal untuk dapat mengakses halaman tersebut.', $message);

								$redirectURL = $this->Auth->logout();
								$this->redirectReferer($message, 'error', $redirectURL);
							}
						}
						else{
							$parentID		= $this->Auth->user('parent_id') ?: $authUserID;
							$dataCompany	= $this->controller->User->UserCompanyConfig->getData('first', array(
								'conditions' => array(
									'UserCompanyConfig.user_id' => $parentID, 
								), 
							));

							$companyConfigID	= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.id');
							$companyPackageID	= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.membership_package_id');
							$companyDomain		= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.domain');
							$companyEndDate		= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.end_date');
							$isCompanyExpired	= strtotime($currentDate) > strtotime($companyEndDate);

							if($authGroupID == 2 && $isCompanyExpired){
							//	COMPANY AGENT
							//	- company agent boleh masuk selama punya membership personal page DAN masa tayang perusahaan HABIS

								if($userPackageID){
									$isExpired = strtotime($currentDate) > strtotime($userEndDate);
								}
								else{
								//	belum pernah beli membership, redirect ke landing page primesystem
									$message = __('Maaf, Anda tidak berhak mengakses halaman tersebut.');
									$message = __('%s Anda bisa berlangganan paket Membership Personal untuk dapat mengakses halaman tersebut.', $message);

									$redirectURL = $this->Auth->logout();
									$this->redirectReferer($message, 'error', $redirectURL);
								}
							}
							else{
							//	OTHER USERS BESIDE COMPANY AGENT AND NON PRIME ADMIN

								$message	= __('Maaf, Anda tidak berhak mengakses halaman tersebut.');
								$isExpired	= $isCompanyExpired;

								if($companyConfigID){
									if($companyDomain){
										$message = __('%s Anda bisa langsung mengakses halaman website perusahaan Anda di %s.', $message, $companyDomain);
									}
									else{
										$message = __('%s Mohon tunggu sampai website perusahaan Anda aktif', $message);
									}
								}
								else{
									$message = __('%s Sepertinya Anda belum terdaftar di perusahaan yang bekerja sama dengan Kami.', $message);
								}

							//	$this->redirectReferer($message, 'error', $companyDomain ?: $primeURL);
								$redirectURL = $this->Auth->logout();
								$this->Hybridauth->logout();

								$this->redirectReferer($message, 'error', $redirectURL);
							}
						}
					}
					else{
					//	domain prime ga ada expirednya
						$isExpired = false;
					}
				}
				else{
					$isPersonalPage = Configure::read('Config.Company.is_personal_page');
					$userPackageID	= Common::hashEmptyField($dataCompany, 'UserConfig.membership_package_id');

					if($isPersonalPage){
					//	akses frontend personal page
						$userEndDate	= Common::hashEmptyField($dataCompany, 'UserConfig.end_date');	
						$isExpired		= strtotime($currentDate) > strtotime($userEndDate);
					}
					else{
					//	user login di web masing-masing
						$companyEndDate	= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.end_date');
						$isExpired		= strtotime($currentDate) > strtotime($companyEndDate);
					}
				}

				$dashboardUrl	= $this->_callDashboardUrl();
				$isAjax			= $this->RequestHandler->isAjax();

				Configure::write('__Site.is_expired', $isExpired);

				if(empty($isAjax) && $isExpired){
					$siteName	= Configure::read('__Site.site_name');
					$siteEmail	= Configure::read('__Site.company_profile.email');
					$message	= __('Maaf, Website ini sudah kadaluarsa, mohon hubungi Customer Support %s (%s)', $siteName, $siteEmail);

				//	menu yang masih allowed (meskipun masa aktif expired)
					$continue		= true;
					$allowedPages	= array(
						'users' => array(
							'admin_info', 
							'admin_account', 
							'admin_security', 
							'admin_edit', 
							'admin_login', 
							'admin_logout', 
							'admin_notifications', 
						), 
						'messages' => array(
							'admin_index', 
							'admin_read', 
						), 
						'memberships' => array(
							'index', 
							'order', 
							'terms_and_conditions', 
						), 
						'membership_orders' => array(
							'admin_search', 
							'admin_index', 
							'admin_add', 
							'admin_view', 
							'admin_process', 
						), 
						'payments' => array(
							'checkout', 
							'complete', 
							'admin_search', 
							'admin_index', 
							'admin_view', 
							'admin_checkout', 
							'admin_notify', 	// jangan di apus, buat doku request ke kita
							'admin_finalize', 	// jangan di apus, buat doku request ke kita 
							'admin_identify', 	// jangan di apus, buat doku request ke kita
						), 
					);

				//	personal page ==============================================================

				//	kalo web biasa isi nya principal, kalo personal web isinya agent
					$ownerGroupID	= Configure::read('Config.Company.data.User.group_id');
					$isAgent		= Common::validateRole('agent', $ownerGroupID);

					if($isAgent){
						$allowedPages = array_merge($allowedPages, array(
							'kpr' => array(
								'detail_banks', 
								'select_product', 
								'application_product', 
								'detail_installment', 
							),
						));
					}

				//	============================================================================

					$allowedAccess = array_key_exists($controller, $allowedPages) && array_key_exists($action, array_flip($allowedPages[$controller]));

					if(in_array($action, array('admin_login', 'admin_logout')) === false && empty($allowedAccess)){
						if(empty($isCompanyAdmin)){
						//	force logout, jangan redirect ke logout, flash-nya jadi ilang
							$redirectURL = $this->Auth->logout();
							$this->redirectReferer($message, 'error', $redirectURL);
						}
						else if($isCompanyAdmin){
							$message = __d('cake', 'You are not authorized to access that location.');
							$this->redirectReferer($message, 'error', $dashboardUrl);
						}
					}

					if($action == 'admin_account'){
						$this->setCustomFlash($message, 'info');
					}
				}
				else if( !empty($isExpired) && $authGroupID == 10){
					$message = __d('cake', 'You are not authorized to access that location.');
					$this->redirectReferer($message, 'error', $dashboardUrl);
				}
			}
		}
    }

	function getMergePost($posts) {
		$data = array();
		
		if( !empty($posts) ) {
			foreach ($posts as $key => $post) {
				$postArr = ucwords(str_replace('_', ' ', $key));
				$model = str_replace(' ', '', $postArr);
				
				if( is_array($post) ) {
					foreach ($post as $key => $field) {
						$data[$model][strtolower($key)] = $field;
					}
				} else {
					$data[strtolower($model)] = $post;
				}
			}
		}

		if( !empty($_FILES) ) {
			foreach ($_FILES as $key => $img) {
				$postArr = ucwords(str_replace('_', ' ', $key));
				$model = str_replace(' ', '', $postArr);

				if( is_array($img) ) {
					foreach ($img as $key => $field) {
						if( is_array($field) ) {
							foreach ($field as $index => $value) {
								if( is_array($value) ) {
									$data[$model][strtolower($index)][$key] = array_shift($value);
								} else {
									$data[$model][strtolower($index)][$key] = $value;
								}
							}
						}
					}
				} else {
					$data[strtolower($model)] = $img;
				}
			}
		}

		return $data;
	}

	function buildDocument( $type, $options = array()){
		$owner_id = $this->filterEmptyField($options, 'owner_id', false, 0);
		$name_file = $this->filterEmptyField($options, 'name_file');
		$baseName = $this->filterEmptyField($options, 'baseName');
		$typeName = $this->filterEmptyField($options, 'typeName');
		$save_path = $this->filterEmptyField($options, 'save_path');
		$kpr_application_id = $this->filterEmptyField($options, 'kpr_application_id', false, null);

		$document_category = $result = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('first', array(
			'conditions' => array(
				'DocumentCategory.slug' => $type,
			),
		));
		$document_category_id = $this->filterEmptyField( $document_category, 'DocumentCategory', 'id', null);
		$document_category_name = $this->filterEmptyField( $document_category, 'DocumentCategory', 'name');
		$documentType = $this->filterEmptyField( $document_category, 'DocumentCategory', 'type');
		$typeName = !empty($typeName)?$typeName:$documentType;

		return array(
			'CrmProjectDocument' => array(
				'document_category_id' => $document_category_id,
				'document_type' => $typeName,
				'owner_id' => $owner_id,
				'save_path' => $save_path,
				'kpr_application_id'=> $kpr_application_id,
				'file' => $name_file,
				'name' => $baseName,
				'title' => $document_category_name,
			),
		);
	}

	function _callDataForAPI ( $data, $type = false ) {
		$rest_api = Configure::read('Rest.token');

		if( !empty($rest_api) ) {
			switch ($type) {
				case 'manual':
					$this->controller->set('data', $data);
					break;
				
				default:
					if( !empty($this->controller->request->data) ) {
						$data = null;
					}
					
					$this->controller->set('data', $data);
					break;
			}
		}
	}

	function _setConfigData ( $data ) {
		$user = Configure::read('User.data');
		$prefix = $this->filterEmptyField($this->params, 'prefix');
		$global_advices = $this->filterEmptyField($data, 'Theme', 'is_advice_global');
		$slide_tour = $this->filterEmptyField($user, 'UserConfig', 'slide_tour');
		
		if( $prefix != 'admin' ) {
			if( !empty($global_advices) ) {
				$glb_advices = $this->controller->User->Advice->getData('all', array(
					'limit' => 2,
				));
			}
		}

		if( empty($slide_tour) ) {
			$this->controller->set('class_body', 'body-slide-tour');
		}

		$this->controller->set(compact(
			'glb_advices'
		));
	}

	function _callCounterListing () {
		$dataCompany = $this->controller->data_company;
		$is_about_counter = $this->filterEmptyField($dataCompany, 'Theme', 'is_about_counter');

		if( !empty($is_about_counter) ) {
			$total_sell = $this->controller->User->Property->getData('count', false, array(
				'status' => 'active-pending',
				'action_type' => 'sell',
			));
			$total_rent = $this->controller->User->Property->getData('count', false, array(
				'status' => 'active-pending',
				'action_type' => 'rent',
			));
			$total_sold = $this->controller->User->Property->getData('count', false, array(
				'status' => 'sold',
			));
			$total_listings = array(
				array(
					'title' => __('Properti Dijual'),
					'value' => $total_sell,
				),
				array(
					'title' => __('Properti Disewa'),
					'value' => $total_rent,
				),
				array(
					'title' => __('Properti Terjual'),
					'value' => $total_sold,
				),
			);
			
			$this->controller->set(compact(
				'total_listings'
			));
		}
	}

	function apiResult($result){
		if($this->Rest->isActive()){
			$this->setProcessParams($result, false, array(
				'noRedirect' => true,
				'redirectError' => false
			));
		}
	}

	function renderRest($options = array()){
		$is_paging = $this->filterEmptyField($options, 'is_paging');
		$params = $this->filterEmptyField($options, 'params');
		$render = $this->filterEmptyField($options, 'render');
		
		if($this->Rest->isActive()){
			if($is_paging){
				App::import('Helper', 'Paginator');					
				$Paginator 	= new PaginatorHelper(new View(null));

				$next_url = '';
				$prev_url = '';
				$last_url = '';
				$first_url = '';

				if($Paginator->hasNext()){
					$url = $Paginator->next('&nbsp;', array('only_url' => true));
					$last_url = $Paginator->last('&nbsp;', array('only_url' => true));
					
					$next_url = $this->mergeLinkUrlJson($params, $url);
					$last_url = $this->mergeLinkUrlJson($params, $last_url);
				}

				if($Paginator->hasPrev()){
					$url = $Paginator->prev('&nbsp;', array('only_url' => true));
					$first_url = $Paginator->first('&nbsp;', array('only_url' => true));

					$prev_url = $this->mergeLinkUrlJson($params, $url);
					$first_url = $this->mergeLinkUrlJson($params, $first_url);
				}

				$count_data = $Paginator->counter(array('format' => '%count%'));
				$count_page = $Paginator->counter(array('format' => '%pages%'));

				$paging_list = $Paginator->numbers(array(
					'modulus' => 4,
					'only_url' => true
				));
				
				if(!empty($paging_list)){
					foreach ($paging_list as $key => $value) {
						$paging_list[$key]['url'] = $this->mergeLinkUrlJson($params, $value['url']);
					}
				}

				$paging = array(
					'page_first' => $first_url,
					'page_prev' => $prev_url,
					'page_next' => $next_url,
					'page_last' => $last_url,
					'paging_list' => $paging_list,
					'count_data' => $count_data,
					'count_page' => $count_page,
				);
				
				$this->controller->set(compact('paging'));
			}

			$this->controller->render(false);
		} else if( !empty($render) ) {
			$this->controller->render($render);
		}
	}

	function link($val, $url, $options = array()){
		if($url){
			App::import('Helper', 'Html');
			$Html = new HtmlHelper(new View(null));

			return $Html->link($val, $url, $options);
		}
		return false;
	}

	function mergeLinkUrlJson($params, $url){
		$next_url = '';

		if(!empty($url)){
			App::import('Helper', 'Html');
			$Html 		= new HtmlHelper(new View(null));

			if(!empty($params)){
				$url = array_merge($params, $url);
			}

			$next_url = $Html->url($url);
			$next_url = rtrim($next_url, '/');
			$next_url .= '.json';
		}

		return $next_url;
	}

	function _callGenerateDataModel ( $data, $modelName ) {
		$result = array();

		if( !empty($data) && is_array($data) ) {
			foreach ($data as $id => $name) {
				$result[] = array(
					$modelName => array(
						'id' => $id,
						'name' => $name,
					),
				);
			}
		}

		return $result;
	}

	function _callIsDirector () {
		$dataCompany = Configure::read('Config.Company.data');
        $group_id = $this->filterEmptyField($dataCompany, 'User', 'group_id');

        if( $group_id == 4 ) {
        	return true;
        } else {
        	return false;
        }
	}

	function printPDF($flag = true){
		if(!empty($flag)){
			$this->controller->layout = '/pdf/default';
		}
	}

	// function setStatusMarital($data, $options = array(), $reserve = false){
	// 	if(!empty($options)){
	// 		foreach($options AS $modelName =>  $field){
	// 			foreach($field AS $key => $val){
	// 				$status_marital = $this->filterEmptyField($data, $modelName, $val);
	// 				if($reserve){
	// 					if(!empty($status_marital)){
	// 						if($status_marital == "single"){
	// 							$status = 1;
	// 						}else if($status_marital == "marital"){
	// 							$status = 2;
	// 						}
	// 						$data[$modelName][$val] = $status;
	// 					}
	// 				}else{
	// 					if(!empty($status_marital)){
	// 						if($status_marital == 1){
	// 							$status = "single";
	// 						}else if($status_marital == 2){
	// 							$status = "marital";
	// 						}
	// 						$data[$modelName][$val] = $status;
	// 					}
	// 				}
					
					
	// 			}
	// 		}
	// 	}
	// 	return $data;	
	// }

	function _callRestValidateAccess () {
		$version = Configure::read('Rest.validate');
		$status = $this->filterEmptyField($version, 'status');
		$msg = $this->filterEmptyField($version, 'msg');

		if( !empty($version) ) {
			$this->setCustomFlash($msg, $status, false, false);
		} else {
			$this->setCustomFlash(__('Unaccepted User'), 'error', false, false);
		}
	}

	function _callCheckAPI($data){
		$this->controller->loadModel('Setting');
		$token = $this->filterEmptyField($data, 'token');
		$slug = $this->filterEmptyField($data, 'slug');

		if( !empty($token) ) {
			$this->controller->loadModel('Setting');

			$setting = $this->controller->Setting->find('first', array(
				'conditions' => array(
					'token' => $token,
					'slug' => $slug,
				),
			));

			$access_id = $this->filterEmptyField($setting, 'Setting', 'id');
			$version['id'] = $access_id;
			$version['passkey'] = $token;
			$version['slug'] = $slug;

			if( empty($setting) ) {
				$version['status'] = 0;
				$version['msg'] = __('Anda tidak memiliki hak untuk mengakses halaman ini.');
			} else {
				Configure::write('Rest.token', $token);

				$version['status'] = 1;
				$version['msg'] = __('User accepted');
			}
		} else {
			$version['status'] = 0;
			$version['msg'] = __('Anda tidak memiliki hak untuk mengakses halaman ini.');
		}

		$data = array_merge($data, $version);

		Configure::write('Rest.validate', $version);
		$this->_callRestValidateAccess();
		$this->controller->request->data = $data;
	}

	function _callGetDataAPI ( $url ) {
		App::uses('HttpSocket','Network/Http');
        App::uses('Xml','Utility');
        $this->layout = null;
        $this->autorender = false;
        $HttpSocket = new HttpSocket();
        $response = $HttpSocket->get($url);
        $request = $HttpSocket->request; 
        $xmlString = $response['body']; 
		return json_decode($xmlString, TRUE);
	}

	function getSearchParamsApi(){
        $data = $this->controller->request->data;
        
        if(!empty($data)){
            $params = $this->filterEmptyField($this->controller->params->params, 'named');

            $params = $this->processSorting($params, $data, true, false, false);
        }else{
            $params = $this->filterEmptyField($this->controller->params->params, 'named');
        }

        return $params;
    }

    function FcmNotifier($message, $target, $title = 'Prime System'){
    	$api_key = 'AIzaSyDOzVUAT7lkvb8xZCiXSURwEIt_H8p5Ckk'; // untuk live

    	$result = array();
    	if(!empty($message) && !empty($target)){
    		// Get cURL resource
			$ch = curl_init();

			// Set url
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

			// Set method
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

			// Set options
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// Set headers
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization: key='.$api_key,
				'Content-Type: application/json',
			));

			// Create body
			$json_array = array(
				'notification' => array(
					'title' => $title,
					'sound' => 'default',
					'body' => $message
				),
				'to' => $target,
				'priority' => 'high'
			);

			$body = json_encode($json_array);

			// Set body
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

			// Send the request & save response to $resp
			$resp = curl_exec($ch);

			if(!$resp) {
				$result = array(
					'msg' => 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch),
					'status' => 'error'
				);
			} else {
				$result = array(
					'msg' => "Response HTTP Status Code : " . curl_getinfo($ch, CURLINFO_HTTP_CODE)."\nResponse HTTP Body : " . $resp,
					'status' => 'success'
				);
			}

			curl_close($ch);
    	}else{
    		$result = array();
    	}

		return $result;
    }

    function mobileNotif($message, $user_id){
    	$this->User = $this->controller->User;

    	$user_config = $this->User->UserConfig->getData('first', array(
			'conditions' => array(
				'UserConfig.user_id' => $user_id
			)
		));

		$device_id = array(
			'device_id_android',
			'device_id_ios'
		);

		if(!empty($user_config)){
    		foreach ($device_id as $key => $value) {
    			if(!empty($user_config['UserConfig'][$value])){
    				$device_id = $user_config['UserConfig'][$value];

    				$this->FcmNotifier($message, $device_id);
    			}
    		}
    	}
    }

	function _callCompanies ( $role = 'principle', $options = null ) {
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( empty($options) ) {
			$options =  $this->controller->User->UserCompany->_callRefineParams($this->controller->params);
			$options = $this->controller->User->getData('paginate', $options, array(
				'status' => 'semi-active',
				'company' => true,
				'admin' => true,
				'role' => $role,
			));
		}

		if( !empty($admin_rumahku) ) {
			$conditions = $this->filterEmptyField($options, 'conditions');
			$companies =  $this->controller->User->UserCompanyConfig->getData('list', array(
				'conditions' => array_merge($conditions, array(
					'UserCompany.name <>' => '',
				)),
				'fields' => array(
					'UserCompany.user_id', 'UserCompany.name',
				),
				'order' => array(
					'UserCompany.created' => 'DESC',
					'UserCompany.name' => 'ASC',
				),
				'contain' => array(
					'User',
					'UserCompany',
				),
			), array(
				'mine' => true,
			));
		} else {
			$conditions = $this->filterEmptyField($options, 'conditions');
			$companies =  $this->controller->User->UserCompany->getData('list', array(
				'conditions' => $conditions,
				'fields' => array(
					'UserCompany.user_id', 'UserCompany.name',
				),
				'contain' => array(
					'User',
				),
				'order' => array(
					'UserCompany.created' => 'DESC',
					'UserCompany.name' => 'ASC',
				),
			), array(
				'company' => true,
			));
		}

		return $companies;
	}

	function _callPIC () {
		$options =  $this->controller->User->_callRefineParams($this->controller->params, array(
			'fields' => array(
				'User.id', 'User.full_name',
			),
			'order' => array(
				'User.full_name' => 'ASC',
				'User.created' => 'DESC',
			),
		));
		return $this->controller->User->getData('list', $options, array(
			'role' => 'pic',
		));
	}

	function AllowOriginRequest(){
		$parent_access = $this->controller;
		$params = $parent_access->params->query;

		$is_allow_origin = $this->filterEmptyField($params, 'is_allow_origin');
		$passname = $this->filterEmptyField($params, 'passname');
		$passkey = $this->filterEmptyField($params, 'passkey');

		$this->controller->allow_origin = false;

		if(!empty($is_allow_origin) && !empty($passname) && !empty($passkey)){
			$this->Setting = ClassRegistry::init('Setting');
			
			$setting = $this->Setting->find('first', array(
				'conditions' => array(
					'token' => $passkey,
					'slug' => $passname,
				),
			));

			if(!empty($setting)){
				$parent_access->response->header('Access-Control-Allow-Origin','*');
		        $parent_access->response->header('Access-Control-Allow-Methods','*');
		        $parent_access->response->header('Access-Control-Allow-Headers','Content-Type, Authorization');

		        $this->controller->allow_origin = true;
			}
		}

		$this->Auth->allow(array(
			'admin_search', 'search',
		));
	}

	// S: Formatan type listing dari rumah123
	function formatCategoryTyper123($id, $cat_type = 'group'){
		$result = '';

		switch($cat_type){
			case 'group':
				$residential = array('1', '2', '3', '4');
				$commercial  = array('5', '6', '7', '8', '9', '11', '12', '15', '18', '20', '21');
				if (in_array($id, $residential)) {
					$result = "Residential";
				} elseif (in_array($id, $commercial)) {
					$result = "Commercial";
				}
				break;
			case 'type':
				$la = array('2');
				$fa = array('9');
				$ho = array('1', '4', '8', '20');
				$ap = array('3', '11', '18');
				$sh = array('6', '7', '12', '21');
				$of = array('5', '15');

				if (in_array($id, $ho)) {
					$result = "ho";
				} elseif (in_array($id, $la)) {
					$result = "la";
				} elseif (in_array($id, $ap)) {
					$result = "ap";
				} elseif (in_array($id, $fa)) {
					$result = "fa";
				} elseif (in_array($id, $sh)) {
					$result = "sh";
				} elseif (in_array($id, $of)) {
					$result = "of";
				}
				break;
		}

		return $result;
	}

	// S: formatan status dari rumah123
	function getStatusListingfor123 ( $data ) {
		$id = $this->filterEmptyField($data, 'Property', 'id');
		$active = $this->filterEmptyField($data, 'Property', 'active', 0);
		$status = $this->filterEmptyField($data, 'Property', 'status', 0);
		$sold = $this->filterEmptyField($data, 'Property', 'sold', 0);
		$published = $this->filterEmptyField($data, 'Property', 'published', 0);
		$deleted = $this->filterEmptyField($data, 'Property', 'deleted', 0);
		$in_update = $this->filterEmptyField($data, 'Property', 'in_update', 0);

		if( $active && $status && !$sold && $published && !$deleted ) {
			$labelStatus = __('Online');
		} else if( !$active && $status && !$sold && $published && !$deleted ) {
			$labelStatus = __('Pending');
		} else if( !$status && $published && !$deleted ) {
			$labelStatus = __('Offline');
		} else if( $deleted ) {
			$labelStatus = __('Deleted');
		} else {
			$labelStatus = false;
		}

		return $labelStatus;
	}
	
	function listToCakeArray($data){
		if(!empty($data)){
			$temp = array();
			foreach ($data as $key => $value) {
				$temp[] = array(
					'id' => $key,
					'name' => $value
				);
			}

			$data = $temp;
		}

		return $data;
	}

	function doLogView($user_id, $value, $options = array()){
    	$dataView = $this->_callSaveVisitor($user_id, 'LogView', 'user_id', $options);
		$this->controller->User->LogView->doSave($dataView, $value, $options);
    }

    function _callUserLogin( $redirect = true ) {
		$is_user_login = Configure::read('User.id');

		if( !empty($is_user_login) ) {
			return true;
		} else {
			$this->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman ini'));
		}
    }

    function _setTour () {
		$slide_tour = Configure::read('User.data.UserConfig.slide_tour');

		if( $slide_tour == 1 ) {
    		$this->controller->set('tour_guide', true);
    	} else if( $slide_tour == 2 ) {
    		$this->controller->set('group_tour_guide', true);
    	}
    }

    // ACL
    function manageAcl($group_id, $aco, $perm){
		$advance_role_acl = Configure::read('AclManager.grant_rule');
		$grant_multiple = Configure::read('AclManager.grant_multiple');

		App::uses('Acl', 'Controller/Component');
		$this->Acl = new AclComponent(new ComponentCollection());

		$node = array(
			'model' => 'Group',
			'foreign_key' => $group_id,
		);

		$action = str_replace("-", "/", $aco);

		if ($perm == 'allow') {
			$result = $this->Acl->allow($node, $action);
		}else if ($perm == 'deny') {
			$result = $this->Acl->deny($node, $action);
		}else{
			$result = false;
		}

		$advance = Common::hashEmptyField($advance_role_acl, $action);

		if(!empty($advance)){
			$allow = array();
			foreach ($advance as $key => $value) {
				if ($perm == 'allow') {
					$result = $this->Acl->allow($node, $value);
				}else if ($perm == 'deny') {  
					$result = $this->Acl->deny($node, $value);
				}
			}
			$advance_grant_multiple  = Common::hashEmptyField($grant_multiple, $action);
		}

		if($result){
		//	delete acl cache
			$cacheConfig	= 'permission';
			$cacheName		= sprintf('Permission.%s', $group_id);

			Cache::delete($cacheName, $cacheConfig);
		}

		return $result;
	}

	function nodeAclTree($data){
		if(!empty($data['Aco'])){
			$temp = array();
			foreach ($data['Aco'] as $key => $value) {
				$alias = $this->filterEmptyField($value, 'alias');
				$id = $this->filterEmptyField($value, 'id');

				if($alias == 'controllers'){
					unset($data['Aco'][$key]);
				}else{
					$temp[$id] = $value;
				}
			}

			$result = array();
			if(!empty($temp)){
				foreach ($temp as $key => $value) {
					$parent_id = $this->filterEmptyField($value, 'parent_id');
					$id = $this->filterEmptyField($value, 'id');
					$alias = $this->filterEmptyField($value, 'alias');

					if(!isset($temp[$parent_id])){
						$result[$key] = $value;
						$result[$key]['Child'] = array();
					}else if(isset($result[$parent_id])){
						$result[$parent_id]['Child'][] = $value;
					}
				}

				$data = $result;
			}
		}
		return $data;
	}

	function _callCheckAcl( $url, $redirect = true ) {
		App::import('Helper', 'AclLink.AclLink');
		$this->AclLink = new AclLinkHelper(new View(null));
		if( !$this->AclLink->aclCheck($url) ) {
			if( !empty($redirect) ) {
				$this->redirectReferer(__('Data tidak ditemukan'));
			} else {
				return false;
			}
		} else {
			return true;
		}
    }
    // 

    function replaceCode($content, $param = null, $slugs = array('LOGO')){
		if(!empty($slugs) && is_array($slugs)){
			foreach ($slugs as $key => $slug) {
				switch ($slug) {
					case 'LOGO':
						$content = $this->codeLogo($content);
						break;
				}
			}
		}
		return $content;
	}

	function codeLogo($content){
		App::import('Helper', 'Rumahku');
		$Rumahku = new RumahkuHelper(new View(null));
		$project = Configure::read('Global.Data.Project');
		$logo = Common::hashEmptyField($project, 'logo');

		$photoView = $Rumahku->photo_thumbnail(array(
			'save_path' => Configure::read('Global.Data.PathFolder.general_folder'), 
			'src' => $logo, 
            'size' => 's',
            'fullbase' => true,
		));

		$content = str_replace('*|LOGO|*', $photoView, $content);
		return $content;
	}

	function setCookieUser($value = false){
		if(!empty($value)){
			$user_id = Common::hashEmptyField($value, 'User.id');
			$group_id = Common::hashEmptyField($value, 'User.group_id');

			if(!in_array($group_id, array(3, 4))){
				$this->Cookie->write('setUser', $user_id);
			} else {
				$this->Cookie->delete('setUser');
			}
		}
	}

	function getCookieUser(){
		return $this->Cookie->read('setUser');
	}

    function _callCheckAclMultiple( $allows ) {
    	if( !empty($allows) ) {
    		foreach ($allows as $key => $url) {
    			$flag = $this->_callCheckAcl($url, false, false);

    			if( !empty($flag) ) {
    				break;
    			}
    		}

    		return $flag;
    	} else {
    		return false;
    	}
    }

    function rowsChart($rows){
		$result = array();
		foreach ($rows as $key => $value) {
			$temp = array();

			foreach ($value as $key_val => $val) {
				$temp_val = $val;
				if(is_array($val)){
					$alias 	= Common::hashEmptyField($val, 'alias', ' ');
					$val 	= Common::hashEmptyField($val, 'value');
				}else{
					$alias = '';
				}

				$temp_inside = array(
					'v' => $val
				);

				if(is_array($temp_val)){
					if(!empty($alias)){
						$temp_inside['f'] = $alias;
					}else{
						$temp_inside['f'] = '';
					}
				}

				$temp[] = $temp_inside;
			}

			$result[] = array(
				'c' => $temp
			);
		}

		return $result;
	}

	function fieldsChart($fields){
		if(!empty($fields)){
			$temp_fields = array();
			foreach ($fields as $key => $value) {
				$temp_fields[] = array(
					'label' => $value, 
					'type' => ($key == 0) ? 'string' : 'number', 
				);
			}

			$fields = $temp_fields;
		}

		return $fields;
	}

	function getDateRangeCompare($date = array()){
		$date_from = Common::hashEmptyField($date, 'date_from');
		$date_to = Common::hashEmptyField($date, 'date_to');

		$convert_from = strtotime($date_from);
		$convert_to = strtotime($date_to);

		$countMonth = date("m",$convert_to)-date("m",$convert_from);

		$date_prev_to = date('Y-m-d', strtotime('-1 days', strtotime($date_from)));
		$date_prev_from = date('Y-m-01', strtotime(sprintf('-%s months', $countMonth), strtotime($date_prev_to)));

		return array(
			'date_from' => $date_prev_from,
			'date_to' => $date_prev_to,
		);
	}

	function getDateRangeReport(){
		$params = $this->controller->params->params;
		$data 	= $this->controller->request->data;

		$date_from 	= Common::hashEmptyField($params, 'named.date_from');
		$date_to 	= Common::hashEmptyField($params, 'named.date_to');

		$month_range = Common::hashEmptyField($data, 'Search.month_range');
		$date = Common::hashEmptyField($data, 'Search.date');

		$allow = true;
		if(!empty($month_range)){
			if(!empty($date_from) && !empty($date_to)){
				$allow = false;
			}else{
				$date = $month_range;	
			}
		}

		if(!empty($date) && $allow){
			$temp_params = Common::_callConvertMonthRange(array(), $date, array(
				'date_from' => 'date_from',
				'date_to' => 'date_to',
			));
			
			$params['named']['date_from'] = $temp_params['date_from'];
			$params['named']['date_to'] = $temp_params['date_to'];
		}
		
		$date_from 	= Common::hashEmptyField($params, 'named.date_from');
		$date_to 	= Common::hashEmptyField($params, 'named.date_to');

		return array(
			'date_from' => $date_from,
			'date_to' => $date_to,
		);
	}

	function splitWindow(){
		$params = $this->controller->params->params;

		$current_year = date('Y');
		$period_id 	= Common::hashEmptyField($params, 'named.periode_id', 3);
		$period_id 	= Common::hashEmptyField($params, 'named.period_id', $period_id);
		$period_id--;

		$current_month_year 		= date('Y-m-d');
		$current_from_month_year 	= date('Y-m-d', strtotime($current_month_year.'-'.$period_id.' month'));

		$current_month_year 		= date('Y-m-t', strtotime($current_month_year));
		$current_from_month_year 	= date('Y-m-01', strtotime($current_from_month_year));

		$date_from 	= Common::hashEmptyField($params, 'named.date_from', $current_from_month_year);
		$date_to 	= Common::hashEmptyField($params, 'named.date_to', $current_month_year);

		if(!empty($date_from) && !empty($date_to)){
			$split_window = Common::splitYear($date_from, $date_to);
		}else{
			$split_window[$current_year] = array();
		}

		return $split_window;
	}

/*	on progress
	public function requestPrimeAPI($targetURL, $options = array(), $debug = false){
		$params	= $this->controller->params->params;
		$page	= Common::hashEmptyField($params, 'named.page');

		$post	= Common::hashEmptyField($options, 'post', array());
		$get	= Common::hashEmptyField($options, 'get', array());
		$header	= Common::hashEmptyField($options, 'header', array());

		$headerName		= Common::hashEmptyField($header, 'name', 'PrimeDeveloper');
		$headerData		= Common::hashEmptyField($header, 'data', array());
		$headerSlug		= Common::hashEmptyField($header, 'slug');
		$includePasskey	= Common::hashEmptyField($header, 'include_passkey');

		$principle = Configure::read('Config.Company.data.User.id');
		$principle = Common::hashEmptyField($header, 'principle', $principle);

		if($page){
			if(is_array($api_action)){
				$api_action['page'] = $page;
			}
			else{
				$api_action.= sprintf('/page:%s', $page);
			}
		}

		$this->Setting = ClassRegistry::init('Setting');

		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
			),
		));

		$token	= Common::hashEmptyField($setting, 'Setting.token', '571dea10-c1d8-4d35-b81f-0c3465ca98e3');
		$domain	= Common::hashEmptyField($setting, 'Setting.link');
		$domain	= rtrim($domain);

		if(is_array($api_action)){
			App::import('Helper', 'Html');

			$this->Html = new HtmlHelper(new View(null));

			$link_api = $domain.$this->Html->url($api_action);
		} else {
			$link_api = $domain.'/'.$api_action.'.json';
		}

		$header_data = (array) $header_data;
		$header_data = array_merge(array(
			'principle'		=> $principle, 
			'passkey'		=> $token, 
			'slug'			=> $slug, 
			'username'		=> 'primeagentweb', 
			'device'		=> 'website', 
			'integration'	=> 1, 
		), $header_data);

		if($include_passkey){
			$get['passkey'] = $token;
		}

		if($get){
			$get = http_build_query($get);
			$connector = strpos($link_api, '?') === false ? '?' : '&';
			$link_api = $link_api.$connector.$get;
		}

		$authHeader = sprintf('%s %s', $header_name, http_build_query($header_data));
		$method		= $post ? 'post' : 'get';

		$apiRequest	= Common::httpRequest($link_api, $post, array(
			'method'		=> $method, 
			'ssl_version'	=> 1, 
			'debug'			=> true,
			'header'		=> array(
				'authorization' => $authHeader, 
			), 
		));
	}
*/

	// Sistem API Menggunakan JSON, ini akan diganti jangan di pake untuk Contoh
	function getAPI($api_action, $options = array(), $debug_header = false){
		$params = $this->controller->params->params;

    	$post_url = Common::hashEmptyField($options, 'post_url');
    	$page = Common::hashEmptyField($params, 'named.page');
    	$header = Common::hashEmptyField($options, 'header');
    	$post = Common::hashEmptyField($options, 'post');
    	$get = Common::hashEmptyField($options, 'get');
    	
    	$header_name = Common::hashEmptyField($header, 'name', 'PrimeDeveloper');
    	$header_data = Common::hashEmptyField($header, 'data');
    	$slug = Common::hashEmptyField($header, 'slug');
    	$include_passkey = Common::hashEmptyField($header, 'include_passkey');

		$principle = Configure::read('Config.Company.data.User.id');
    	$principle = Common::hashEmptyField($header, 'principle', $principle);
		$ch = curl_init();

		if(!empty($page)){
    		$api_action .= '/page:'.$page;
    	}

    	$this->Setting = ClassRegistry::init('Setting');
    	$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
			),
		));

		$domain = Common::hashEmptyField($setting, 'Setting.link');
		$token 	= Common::hashEmptyField($setting, 'Setting.token', '571dea10-c1d8-4d35-b81f-0c3465ca98e3');
		$domain = rtrim($domain);

		if( is_array($api_action) || !empty($post_url)) {
			App::import('Helper', 'Html');
			$this->Html = new HtmlHelper(new View(null));

			if (!empty($post_url)) {
				$link_api = $api_action;
			} else {
				$link_api = $domain.$this->Html->url($api_action);
			}

		} else {
			$link_api = $domain.'/'.$api_action.'.json';
		}

		$opGet = '?';

		if(!empty($get)){
			$idx = 0;

			foreach ($get as $field => $val) {
				if( empty($idx) ) {
					$opGet = '?';
				} else {
					$opGet = '&';
				}

				$link_api .= $opGet.$field.'='.$val;
				
				$opGet = '&';
				$idx++;
			}
		}

		if( !empty($include_passkey) ) {
			$link_api .= $opGet.'passkey='.$token;
		}

		// Set url
		curl_setopt($ch, CURLOPT_URL, $link_api);

		// Set method
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		// Set options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$text_author = 'Authorization: '.$header_name.'  passkey='.$token.'&slug='.$slug.'&username=primeagentweb&device=website&integration=1&principle='.$principle;

		if(!empty($header_data)){
			foreach ($header_data as $field => $val) {
				$text_author .= '&'.$field.'='.$val;
			}
		}

		// Set headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			$text_author
		));

		// Set body
		if(!empty($post)){
			$body = http_build_query($post);

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}

		// Send the request & save response to $resp
		$resp = curl_exec($ch);

		if(!empty($debug_header)){
			debug($link_api);
			debug($text_author);
			debug($resp);die();
		}

		$resp = json_decode($resp, true);

		$resp = Common::hashEmptyField($resp, 'data');

		curl_close($ch);

		$paging = Common::hashEmptyField($resp, 'paging');

		if(!empty($paging)){
			Configure::write('Config.PaginateApi', array(
				'page' 	 => $page,
				'paging' => $paging,
			));
		}
		
		return $resp;
	}

	// curl olx
	function curlOLX($api_action = array(), $options = array()){
    	$post     = Common::hashEmptyField($options, 'post');
    	$headers  = Common::hashEmptyField($options, 'headers');
    	$method	  = Common::hashEmptyField($options, 'method');
    	$debug    = Common::hashEmptyField($options, 'debug', false);

    	if (!empty($api_action)) {
	    	$target_url = rtrim($api_action);
    	}

		$post_header = array();
		if(!empty($headers)){
			$post_header = $headers;
		}

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");

		// Set body
		if(!empty($post)){
			$postData = http_build_query($post);
			$postData = urldecode($postData);

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}

		if (!empty($debug)) {
			echo('<div class="debug-content">');
			echo('<div class="content-header">CURL_GETINFO</div>');
			debug(curl_getinfo($ch));
			echo('</div>');
		}

		// Send the request & save response to $resp
		$resp = curl_exec($ch);

		if(!empty($debug)){
			echo('<link rel="stylesheet" type="text/css" href="/css/request-debugger.css">');
			echo('<div class="debug-content">');
			echo('<div class="content-header">Target URL ['.$method.']</div>');
			debug($target_url);
			echo('</div>');

			echo('<div class="debug-content">');
			echo('<div class="content-header">Header</div>');
			debug($post_header);
			echo('</div>');

			echo('<div class="debug-content">');
			echo('<div class="content-header">Data to POST</div>');
			debug($postData);
			echo('</div>');

			$requestContentType = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
			echo('<div class="debug-content">');
			echo('<div class="content-header">request Content Type was:</div>');
			debug($requestContentType);
			echo('</div>');

			echo('<div class="debug-content">');
			echo('<div class="content-header">Response</div>');
			debug(json_decode(utf8_encode(trim($resp)), true));
			echo('</div>');
			die;
		}

		$resp = json_decode($resp, true);

		$resp = Common::hashEmptyField($resp, 'data');

		curl_close($ch);

		if($resp){
			$resp	= json_decode(utf8_encode(trim($resp)), true);
			$error	= null;

			if($resp !== null){
			//	switch and check possible JSON errors
				switch (json_last_error()){
					case JSON_ERROR_NONE:
						$error = ''; // JSON is valid // No error has occurred
						break;
					case JSON_ERROR_DEPTH:
						$error = 'The maximum stack depth has been exceeded.';
					break;
					case JSON_ERROR_STATE_MISMATCH:
						$error = 'Invalid or malformed JSON.';
					break;
					case JSON_ERROR_CTRL_CHAR:
						$error = 'Control character error, possibly incorrectly encoded.';
					break;
					case JSON_ERROR_SYNTAX:
						$error = 'Syntax error, malformed JSON.';
					break;

				//	PHP >= 5.3.3
					case JSON_ERROR_UTF8:
						$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
					break;

				//	PHP >= 5.5.0
					case JSON_ERROR_RECURSION:
						$error = 'One or more recursive references in the value to be encoded.';
					break;

				//	PHP >= 5.5.0
					case JSON_ERROR_INF_OR_NAN:
						$error = 'One or more NAN or INF values in the value to be encoded.';
					break;
					case JSON_ERROR_UNSUPPORTED_TYPE:
						$error = 'A value of a type that cannot be encoded was given.';
					break;
					default:
						$error = 'Unknown JSON error occured.';
					break;
				}
			}

		//	append to result
		    $status		= $error ? 'error' : 'success';
		    $message	= $error ? $error : 'Valid Request';
		}
		else{
			$status		= 'success';
			$message	= 'Valid Request';
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> __($message), 
			'response'	=> $resp, 
		);
		
		return $result;
	}

	function generateParamsApi($action){
		$params = $this->controller->params->params;

		$named_param = Common::hashEmptyField($params, 'named');
		
		if(!empty($named_param)){
			$temp = '';
			foreach ($named_param as $key => $value) {
				if($key != 'page'){
					$temp .= __('/%s:%s', $key, $value);
				}
			}

			$action .= $temp;
		}

		return $action;
	}

	function url($url, $full = false){
		if(!empty($url)){
			App::import('Helper', 'Html');
			$Html = new HtmlHelper(new View(null));

			$url = $Html->url($url);

			if( !empty($full) ) {
				if( is_bool($full) ) {
					$url = FULL_BASE_URL.$url;
				} else {
					$url = $full.$url;
				}
			}
		}

		return $url;
	}

	public function getThemePath($dataCompany = array()){
		$dataCompany	= (array) ($dataCompany ?: $this->controller->data_company);
		$themePath		= false;

		if($dataCompany){
		//	company web
			$isAdmin		= Configure::read('User.admin');
			$themePath		= Common::hashEmptyField($dataCompany, 'Theme.slug');
			$flagThemePath	= Common::hashEmptyField($dataCompany, 'FlagSettingTheme.slug');

			if($isAdmin && $flagThemePath){
				$themePath = $flagThemePath; 
			}
		}
		else{
		//	note kemungkinan besar udah ga bakal masuk kesini lagi, data udah di handle pas getCompanyData, 
		//	jadi pasti masuk IF atas aja

		//	personal web
			$isPrimeDomain	= Common::isPrimeDomain();
			$authUser		= Configure::read('User.data');

			if(empty($isPrimeDomain)){
				$currentURL	= Router::url('/', true);
				$currentURL	= str_replace(array('http://', 'https://', '/', 'www.', 'ww.'), null, $currentURL);

				$userConfig	= $this->controller->User->UserConfig->getData('first', array(
					'contain'		=> array('Theme'), 
					'conditions'	=> array(
						// 'UserConfig.personal_web_url LIKE' => '%' . $currentURL . '%', 

						// Klo domainnya mengandung nama yg sama maka jadi double Contoh:
						// property.thepremiere.co.id & Jualbeliproperty.thepremiere.co.id
						// Mengandung kata property.thepremiere.co.id
						'UserConfig.personal_web_url' => $currentURL, 
					), 
				));

				$themePath = Common::hashEmptyField($userConfig, 'Theme.slug');
			}
		}

		return $themePath;
	}

	public function getCacheName($options = array()){
		$options	= (array) $options;
		$cacheName	= Common::hashEmptyField($options, 'name');
		$append		= Common::hashEmptyField($options, 'append');
		$delimiter	= Common::hashEmptyField($options, 'delimiter', '.');

		if(empty($cacheName)){
			$controller	= $this->controller->name;
			$action		= Inflector::camelize($this->controller->action);

			$companyData	= Configure::read('Config.Company.data');
			$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', 0);
			$cacheName		= implode($delimiter, array($controller, $action, $companyID));
		}

		$cacheName = array_filter(array($cacheName, $append));
		$cacheName = implode($delimiter, $cacheName);

		return $cacheName;
	}

	public function setQueryCache($cacheConfig, $record, $options = array()){
		$options	= (array) $options;
		$cacheName	= $this->getCacheName($options);

		$pagingParams	= Common::hashEmptyField($this->controller->request->params, 'paging', array());
		$cacheData		= array(
			'paging'	=> $pagingParams,
			'named'		=> $this->controller->request->params['named'],
			'pass'		=> $this->controller->request->params['pass'],
			'query'		=> $this->controller->request->query,
			'record'	=> $record, 
		);

		Cache::write($cacheName, $cacheData, $cacheConfig);
	}

	public function getQueryCache($cacheConfig, $options = array()){
		$options	= (array) $options;
		$autoSet	= Common::hashEmptyField($options, 'auto_set', true, array('isset' => true));
		$cacheName	= $this->getCacheName($options);

		$pagingParams	= Common::hashEmptyField($this->controller->request->params, 'paging', array());
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		$cacheData		= array_replace_recursive(array(
			'paging'	=> $pagingParams,
			'named'		=> $this->controller->request->params['named'], 
			'pass'		=> $this->controller->request->params['pass'], 
			'query'		=> $this->controller->request->query, 
			'record'	=> array(), 
			'cache'		=> array(
				'config'	=> $cacheConfig, 
				'name'		=> $cacheName, 
			), 
		), (array) $cacheData);

		if($cacheData['record'] && $autoSet){
			$this->controller->request->params['named']	= $cacheData['named'];
			$this->controller->request->params['pass']	= $cacheData['pass'];
			$this->controller->request->query			= $cacheData['query'];
		}

		return Hash::remove($cacheData, 0);
	}

	function _saveShare( $data = array() ){
		$this->ShareLog = ClassRegistry::init('ShareLog');

		$utm = $this->RequestHandler->getReferer();
		$ip_address = $this->RequestHandler->getClientIP();
		$browser_arr = Common::hashEmptyField($_SERVER, 'HTTP_USER_AGENT');
		$user_agents = @get_browser(null, true);

		$browser = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
		$os = !empty($user_agents['platform'])?$user_agents['platform']:'';

		$mobile = Configure::read('Global.Data.MobileDetect.mobile');
		$tablet = Configure::read('Global.Data.MobileDetect.tablet');

		if( !empty($mobile) || !empty($tablet) ){
			$device = 'mobile';
		
		} else {
			$device = 'browser';
		}

		$user_login_id = Configure::read('User.id');
		$user_login_id = !empty($user_login_id)?$user_login_id:0;

		$group_login_id = Configure::read('User.group_id');
		$group_login_id = !empty($group_login_id)?$group_login_id:0;

		$principle_id = Configure::read('Principle.id');
		$principle_id = !empty($principle_id)?$principle_id:0;

		$company_id = Configure::read('Config.Company.data.UserCompany.id');
		$company_id = !empty($company_id)?$company_id:0;

		$data['ShareLog']['user_id'] = Common::hashEmptyField($data, 'ShareLog.user_id', $user_login_id);
		$data['ShareLog']['group_id'] = Common::hashEmptyField($data, 'ShareLog.group_id', $group_login_id);
		$data['ShareLog']['parent_id'] = Common::hashEmptyField($data, 'ShareLog.parent_id', $principle_id);
		$data['ShareLog']['company_id'] = Common::hashEmptyField($data, 'ShareLog.company_id', $company_id);
		$data['ShareLog']['device'] = $device;
		$data['ShareLog']['ip'] = $ip_address;
		$data['ShareLog']['user_agent'] = env('HTTP_USER_AGENT');
		$data['ShareLog']['utm'] = $utm;

		if( $this->ShareLog->doSave($data) ) {
			return true;	
		} else {
			return false;
		}
	}

	function getCredential($slug = false){
		switch ($slug) {
			case 'prime-agent':
				$apiUrl = 'ww.apiprimesystem.com/Api';
				Configure::write('Site.UrlApi', 'ww.apiprimesystem.com');

				$token = $this->controller->User->ApiSettingUser->getData('first', false, array(
					'company' => true,
					'type' => 'master',
				));

				if($token){
					$username = Common::hashEmptyField($token, 'ApiSettingUser.user_key');
					$password = Common::hashEmptyField($token, 'ApiSettingUser.secret_key');

					$postData = array(
						'App' => array(
							'username' => $username, 
							'password' => $password, 
						)
					);
					$requestURL	= sprintf('%s/token.json', $apiUrl);

					$options = array(
						'method'			=> 'post', 
						'debug'			=> true,
						'ssl_version'	=> 1, 
						'data_type'		=> 'file', 
						'method'		=> 'POST', 
						'header'		=> array(
							'content_type' => 'multipart/form-data', 
					));
					$apiRequest	= Common::httpRequest($requestURL, $postData, $options);
				}



				break;
			
			default:
				# code...
				break;
		}
	}

	public function getViewLocation($subareaID){
		$location = array();

		if($subareaID){
			$this->controller->loadModel('ViewLocation');

			$location = $this->controller->ViewLocation->getData('first', array(
				'conditions' => array(
					'ViewLocation.subarea_id' => $subareaID, 
				), 
			));

			if($location){
				$regionName		= Common::hashEmptyField($location, 'ViewLocation.region_name');
				$cityName		= Common::hashEmptyField($location, 'ViewLocation.city_name');
				$subareaName	= Common::hashEmptyField($location, 'ViewLocation.subarea_name');
				$locationName	= array_filter(array($subareaName, $cityName, $regionName));
				$locationName	= implode(', ', $locationName);

				$location = Hash::insert($location, 'ViewLocation.location_name', $locationName);
			}
		}

		return $location;
	}

	function modalMessage ( $msg ) {
		$this->controller->set(array(
			'message' => $msg,
		));
		$this->controller->render('/Elements/blocks/common/modals/message');
	}

}
?>