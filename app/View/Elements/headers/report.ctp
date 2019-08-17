<?php 
		if( !empty($module_title) || !empty($period_title) ) {
			if( !empty($period_title) ) {
				$module_title .= '<br>'.$this->Html->tag('span', $period_title, array(
					'style' => 'font-size: 14px;'
				));
			}

			echo $this->Html->tag('h1', $module_title, array(
                'style' => 'text-align: center;font-size: 24px;',
            ));
		}
?>