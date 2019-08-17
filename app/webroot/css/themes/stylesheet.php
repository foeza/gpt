<?php
	    function getColorCode ( $data, $fieldName, $default, $divider = '#', $position = 'left', $addText = '' ) {
	        $value = !empty($data[$fieldName])?urldecode($data[$fieldName]):$default;
	        $findAplpha = strstr($value, 'rgba(');
	        $find = strstr($value, 'rgb(');

	        if( !empty($value) && empty($findAplpha) && empty($find) && substr($value, 0, 1) != '#' ) {
	        	if( $position == 'right' ) {
	            	$value = sprintf('%s%s', $value, $divider);
	            } else {
	            	$value = sprintf('%s%s', $divider, $value);
	            }
	        }

	        return $value.$addText;
	    }

		if(!empty($_GET['theme'])) {
			$base_url = !empty($base_url)?$base_url:$_SERVER['SERVER_NAME'];
			$theme_name					= strtolower($_GET['theme']);
			$theme						= sprintf('_%s', $theme_name);
			$bg_footer					= 'background-color: #4a4763;background-image: url(\'/img/subheader-page-bg.jpg\');';
			$main_content_color			= NULL;
			$sub_content_color			= NULL;
			$bg_header					= NULL;
			$button_color				= NULL;
			$bg_color					= NULL;
			$bg_color_top_header 		= NULL; 
			$font_type					= NULL;
			$font_size					= NULL;
			$font_color					= NULL;
			$font_menu_color			= NULL;
			$font_heading_footer_color	= NULL;
			$font_heading_color			= NULL;
			$font_link_color			= NULL;
			$font_menu					= NULL;
			$tab_button					= NULL;
			$tab_button_active			= NULL;
			$header_image				= NULL;
			$bg_image					= NULL;
		//	$footer						= NULL;

			/*default theme conditions*/
			switch ($theme_name) {
				case 'easyliving':
					$main_content_color	= '#545098';
					$bg_footer = '#373550';
					$sub_content_color = '#FFFFFF';
					$button_color = '#81be32';
					$bg_color_top_header = '#222';
					$bg_color = '#fff';
					$font_type = "'Proxima Nova Light, Helvetica, Arial'";
					$font_size = '11';
					$font_color = '#464646';
					$font_menu_color = 'rgba(70,70,70,1)';
					$font_heading_footer_color = '#4d4f52';
					$font_heading_color = '';
					$font_link_color = '#81be32';
					$tab_button = '#545098';
					$tab_button_active = 'rgba(74,71,134,1)';
					// $footer = '#4e4c6c';
					break;
				case 'realsitematerial':
					$main_content_color = '#000';
					$bg_footer = '#000';
					$bg_color_top_header = '#222';
					$sub_content_color = '#000';
					$button_color = '#E91E63';
					$bg_color = '#FAFAFA';
					$font_type = '"Roboto", "Arial", sans-serif';
					$font_size = '14';
					$font_color = '#757575';
					$font_menu_color = 'rgba(117, 117, 117, 1)';
					$font_heading_color = '';
					$font_link_color = '#EC407A';
					$font_menu = 'color: #FFFFFF;';
					break;
				case 'cozy':
					$main_content_color = '#df4a43';
					$bg_footer = '#f1f3f6';
					$bg_color_top_header = '#222';
					$sub_content_color = '#f0f0f0';
					$button_color = '#adb2b6';
					$bg_color = '#fff';
					$font_type = "''Open Sans', sans-serif'";
					$font_size = '14';
					$font_color = '#74777c';
					$font_menu_color = 'rgba(70,70,70,1)';
					$font_heading_footer_color = '#4d4f52';
					$font_heading_color = '#4d4f52';
					$font_link_color = '#df4a43';
					break;

				case'estato':
					$main_content_color			= 'rgb(96,167,212)';
					$font_type					= '"Open Sans", sans-serif';
					$font_size					= '14';
					$bg_color_top_header 		= '#222';
					$font_color					= 'rgb(51,51,51)';
					$font_menu_color			= 'rgba(70,70,70,1)';
					$font_link_color			= 'rgb(51,51,51)';
					$bg_header					= '';
					$bg_color					= 'rgba(255,255,255,1)';
					$sub_content_color			= '';
					$font_heading_color			= 'rgba(70,70,70,1)';
					$button_color				= 'rgb(96,167,212)';
					$bg_footer					= 'rgb(51,51,51)';
					$font_heading_footer_color	= 'rgba(255,255,255,1)';
					$header_image				= '../../img/themes/head-detail.jpg';
				break;

				case'realspaces':
					$main_content_color			= '#F55A4E';
					$font_type					= '"Open Sans", sans-serif';
					$font_size					= '14';
					$bg_color_top_header 		= '#222';
					$font_color					= '#666666';
					$font_menu_color			= '#666666';
					$font_link_color			= '#5e5e5e';
					$bg_header					= '#FFFFFF';
					$bg_color					= '#FFFFFF';
					$font_heading_color			= '#666666';
					$button_color				= '#ffffff';
					$bg_footer					= 'rgb(248,248,248)';
					$font_heading_footer_color	= '#666666';
					$header_image				= '../../img/themes/page-header.jpg';
				break;

				case'suburb':
					$main_content_color			= '#FFFFFF';
					$font_type					= '"Open Sans", sans-serif';
					$font_size					= '14';
					$bg_color_top_header 		= '#222';
					$font_color					= '#1f2126';
					$font_menu_color			= '#35393B';
					$font_link_color			= '#3a7de3';
					$bg_color					= '#f5f5f5';
					$font_heading_color			= '#35393b';
					$button_color				= '#3a7de3';
					$bg_footer					= '#1f2126';
					$font_heading_footer_color	= '#FFFFFF';
				break;

				case'realtyspace':
					$bg_color_top_header 		= '#222';
					$bg_color					= 'rgb(0,187,170)';
					$bg_footer					= 'rgb(34,34,34)';
					$button_color				= 'rgb(243,188,101)';
					$font_color					= 'rgb(44,62,80)';
					$font_heading_color			= 'rgba(70,70,70,1)';
					$font_heading_footer_color	= 'rgba(255,255,255,1)';
					$font_link_color			= 'rgb(190,190,190)';
					$font_menu_color			= 'rgb(44,62,80)';
					$font_size					= '14';
					$font_type					= '"Open Sans", sans-serif';
					$main_content_color			= 'rgb(0,187,170)';
				break;

				case'villareal':
					$bg_color_top_header 		= '#222';
					$bg_color					= 'rgb(249, 249, 248)';
					$bg_footer					= 'rgb(73,69,69)';
					$button_color				= 'rgb(11,183,165)';
					$font_color					= 'rgb(50,50,50)';
					$font_heading_color			= 'rgba(70,70,70,1)';
					$font_heading_footer_color	= 'rgba(255,255,255,1)';
					$font_link_color			= 'rgb(11,183,165)';
					$font_menu_color			= 'rgb(50,50,50)';
					$font_size					= '14';
					$font_type					= '"Open Sans", sans-serif';
					$main_content_color			= 'rgb(11,183,165)';
				break;

				case'apartement':
					$bg_color_top_header 		= '#222';
					$bg_color					= 'rgb(255,255,255)';
					$bg_footer					= 'rgb(21,31,43)';
					$button_color				= 'rgb(55,151,221)';
					$font_color					= 'rgb(137,137,137)';
					$font_heading_color			= 'rgb(93,93,93)';
					$font_heading_footer_color	= 'rgba(255,255,255,1)';
					$font_link_color			= 'rgb(11,183,165)';
					$font_menu_color			= 'rgb(93,93,93)';
					$font_size					= '14';
					$font_type					= 'Roboto, Arial, sans-serif';
					$main_content_color			= 'rgb(55,151,221)';
					$bg_image					= '../../img/themes/default-bg-pattern.jpg';
				break;

				case'bigcity':
					$bg_image					= '../../img/themes/default-bg-pattern.jpg';
				break;

				case'thenest':
				case'bigcity':
					$bg_footer					= 'background-color: #4a4763;';
				break;
			}
			/*end default theme conditions*/

			$main_content_color = getColorCode($_GET, 'main_content_color', $main_content_color);
			$bg_footer = getColorCode($_GET, 'bg_footer', $bg_footer);
			$bg_header = getColorCode($_GET, 'bg_header', $bg_header);
			$button_color = getColorCode($_GET, 'button_color', $button_color);
			$bg_color = getColorCode($_GET, 'bg_color', $bg_color);
			$bg_color_top_header = getColorCode($_GET, 'bg_color_top_header', $bg_color_top_header);
			$bg_color_border_header = getColorCode($_GET, 'bg_color_border_header', '#3b3b3b');
			$font_type = $_GET['font_type'];
			$font_size = getColorCode($_GET, 'font_size', $font_size, 'px', 'right');
			$font_color = getColorCode($_GET, 'font_color', $font_color);
			$font_menu_color = getColorCode($_GET, 'font_menu_color', $font_menu_color);
			$font_heading_footer_color = getColorCode($_GET, 'font_heading_footer_color', $font_heading_footer_color);
			$font_link_color = getColorCode($_GET, 'font_link_color', $font_link_color);
			$tab_button = getColorCode($_GET, 'main_content_color', $tab_button);
			$tab_button_active = getColorCode($_GET, 'button_color', $tab_button_active);
			// $footer = getColorCode($_GET, 'main_content_color', $footer);

			$font_type_import = '';

			if( $theme_name == 'easyliving' ) {
				$sub_content_color = getColorCode($_GET, 'main_content_color', $sub_content_color);
			} else {
				$sub_content_color = getColorCode($_GET, 'bg_color', $sub_content_color);
			}

			if( !empty($_GET['bg_footer']) ) {
				$bg_footer = getColorCode($_GET, 'bg_footer', $bg_footer);
			}

			if( !empty($_GET['bg_header']) ) {
				$bg_header = sprintf('background-color: %s;', getColorCode($_GET, 'bg_header', $bg_header, '#', 'left', ' !important'));
				$bg_header .= 'background-image: none;';
			}

			if( !empty($_GET['font_heading_color']) ) {
				$font_heading_color = sprintf('color: %s;', getColorCode($_GET, 'font_heading_color', $font_heading_color, '#', 'left', ' !important'));
			} else if( !empty($font_heading_color) ) {
				$font_heading_color = sprintf('color: %s;', getColorCode($_GET, 'font_heading_color', $font_heading_color, '#', 'left', ' !important'));
			}

			if( !empty($_GET['font_color']) ) {
				$font_menu = sprintf('color: %s;', getColorCode($_GET, 'font_color', $font_menu, '#', 'left', ' !important'));
			}
			
			if( !empty($_GET['font_menu_color']) ) {
				$font_menu_color = sprintf('color: %s;', getColorCode($_GET, 'font_menu_color', $font_menu_color, '#', 'left'));
			} else {
				$font_menu_color = sprintf('color: %s;', getColorCode($_GET, 'font_menu_color', $font_menu_color, '#', 'left'));
			}
			
			if( !empty($_GET['font_heading_footer_color']) ) {
				$font_heading_footer_color = sprintf('color: %s;', getColorCode($_GET, 'font_heading_footer_color', $font_heading_footer_color, '#', 'left', ' !important'));
			} else if( !empty($font_heading_footer_color) ) {
				$font_heading_footer_color = sprintf('color: %s;', getColorCode($_GET, 'font_heading_footer_color', $font_heading_footer_color, '#', 'left', ' !important'));
			}

			if(!empty($_GET['font_type'])){
				$font_type_import = "@import url('http://fonts.googleapis.com/css?family=".str_replace("'", '', str_replace(' ', '+', $_GET['font_type']))."');";
			}

			if( !empty($_GET['header_image']) ) {
				$header_image = $_GET['header_image'];
			}

			if( !empty($header_image) ) {
				$header_image = sprintf('url("%s")', $header_image);
			}

			if( !empty($_GET['bg_image']) ) {
				$bg_image = $_GET['bg_image'];
			}

			if( !empty($bg_image) ) {
				$bg_image = sprintf('url("%s")', $bg_image);
			}

			$default = array(	
				'main_content_color' => $main_content_color,
				'bg_footer' => $bg_footer,
				'bg_header' => $bg_header,
				'button_color' => $button_color,
				'bg_color' => $bg_color,
				'bg_color_top_header' => $bg_color_top_header,
				'bg_color_border_header' => $bg_color_border_header,
				'sub_content_color' => $sub_content_color,
				'font_type' => $font_type,
				'font_size' => $font_size,
				'font_color' => $font_color,
				'font_menu_color' => $font_menu_color,
				'font_heading_footer_color' => $font_heading_footer_color,
				'font_type_import' => $font_type_import,
				'font_heading_color' => $font_heading_color,
				'font_menu' => $font_menu,
				'font_link_color' => $font_link_color,
				'tab_button' => $tab_button,
				'tab_button_active' => $tab_button_active,
				'header_image' => $header_image,
				'bg_image'	=> $bg_image,	
				// 'footer' => $footer,
			);

			extract($default);
		}

		$stylesheet = 'default'.$theme.'.css';
		header('Content-type: text/css');

		$content = preg_replace('/\$([\w]+)/e','$0',@file_get_contents($stylesheet));
		echo $content;
?>