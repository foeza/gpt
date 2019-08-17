<?php
		$tag = !empty($tag) ? $tag : 'li';
		$divClass = !empty($divClass) ? $divClass : false;
		$divStyle = !empty($divStyle) ? $divStyle : false;

		$class = !empty($class) ? $class : false;
		$style = !empty($style) ? $style : false;
		$iconInside = isset($iconInside) ? $iconInside : true;
		$tagLabel = !empty($tagLabel) ? $tagLabel : false;
		$tagIcon = !empty($tagIcon) ? $tagIcon : false;
		$tagLabelClass = !empty($tagLabelClass) ? $tagLabelClass : false;
		$tagIconClass = !empty($tagIconClass) ? $tagIconClass : false;

		$contentIcon = !empty($contentIcon) ? $contentIcon : false;

		if(isset($User) && $User){
			$icon 			= $this->Rumahku->icon('fa fa-bar-chart', $contentIcon); 
			$linkLabel		= __(' Halaman Admin');
			$dashboardURL	= Configure::read('User.dashboard_url');
		} else {
			$icon 			= $this->Rumahku->icon('fa fa-lock', $contentIcon);
			$linkLabel		= __(' Staff Login');
			$dashboardURL	= array(
				'controller' => 'users',
				'action' => 'login',
				'admin' => true,
			);
		}

		if($iconInside){
			$linkLabel = $icon.$linkLabel;
			$icon = null;	
		}

		$link = $this->Html->link($linkLabel, $dashboardURL, array(
			'escape' => FALSE,
			'class' => $class,
			'style' => $style,
			'target' => '_blank',
		));

		if($tagLabel){
			$link = $this->Html->tag($tagLabel, $link, array(
				'class' => $tagLabelClass,
			));
		}

		if($tagIcon){
			$icon = $this->Html->tag($tagIcon, $icon, array(
				'class' => $tagIconClass,
			));
		}

		$link = $icon.$link;

		if($tag <> 'not'){
			echo($this->Html->tag( $tag , $link, array(
				'class' => sprintf('border %s', $divClass),
				'style' => $divStyle,
			)));
		} else {
			echo $link;
		}
?>