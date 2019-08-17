<?php
		function getColorCode ( $data, $fieldName, $default, $divider = '#', $position = 'left', $addText = '' ) {
	        $value = !empty($data[$fieldName])?urldecode($data[$fieldName]):$default;
	        $find = strstr($value, 'rgba(');

	        if( !empty($value) && empty($find) && substr($value, 0, 1) != '#' ) {
	        	if( $position == 'right' ) {
	            	$value = sprintf('%s%s', $value, $divider);
	            } else {
	            	$value = sprintf('%s%s', $divider, $value);
	            }
	        }

	        return $value.$addText;
	    }
	    
	    $default = array(	
			'url_sell' => getColorCode($_GET, 'url_sell', '', ''),
			'url_rent' => getColorCode($_GET, 'url_rent', '', ''),
			'color_content_font' => getColorCode($_GET, 'color', '', '')
		);

		extract($default);

		$stylesheet = 'default_admin.css';
		header('Content-type: text/css');
		
		$content = preg_replace('/\$([\w]+)/e','$0',@file_get_contents($stylesheet));
		echo $content;
?>