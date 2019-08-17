<?php 
		$active_menu = !empty($active_menu)?$active_menu:false;
        $_global_variable = !empty($_global_variable)?$_global_variable:false;

        $language = $this->Rumahku->filterEmptyField($_global_variable, 'translates');
		$developer_page = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_developer_page');
		$brochure = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_ebrosur_frontend');
		$blog = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_blog');
		$faq = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_faq');
		$career = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_career');
    	$lang = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'language', 'id');
    	$isMarketTrend = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'mt_is_show_trend');
		$isExpertSystem = Common::hashEmptyField($_config, 'UserCompanyConfig.is_expert_system');

        $home = $this->Rumahku->filterEmptyField($language, $lang, 'home');
        $property = $this->Rumahku->filterEmptyField($language, $lang, 'property');
        $sell = $this->Rumahku->filterEmptyField($language, $lang, 'sell');
        $rent = $this->Rumahku->filterEmptyField($language, $lang, 'rent');
        $developers = $this->Rumahku->filterEmptyField($language, $lang, 'developers');
        $agent = $this->Rumahku->filterEmptyField($language, $lang, 'agent');
        $ebrosur = $this->Rumahku->filterEmptyField($language, $lang, 'ebrosur');
        $blog_text = $this->Rumahku->filterEmptyField($language, $lang, 'blog');
        $about = $this->Rumahku->filterEmptyField($language, $lang, 'about');
        $contact = $this->Rumahku->filterEmptyField($language, $lang, 'contact');
        $faq_text = $this->Rumahku->filterEmptyField($language, $lang, 'faq');
        $career_text = $this->Rumahku->filterEmptyField($language, $lang, 'career');
		$company_text	= $this->Rumahku->filterEmptyField($language, $lang, 'company');
        $isDirector = $this->Rumahku->_callIsDirector();
?>
<nav class="navbar">
	<button id="nav-mobile-btn"><i class="fa fa-bars"></i></button>
	<ul class="nav navbar-nav">
		<?php 
				if( !empty($isDirector) ) {
					echo $this->Html->tag('li', $this->Html->link($company_text, array(
						'controller' => 'users',
						'action' => 'companies'
					),array(
						'class' => ($active_menu == 'company') ? 'active' : '',
					)));
				} else {
					echo $this->Html->tag('li', $this->Html->link($home, '/', array(
						'class' => ( $active_menu == 'home' ) ? 'active': '',
					)));
				}
		?>
		<li class="dropdown">
			<?php 
					echo $this->Html->link($property, '#', array(
						'class' => 'dropdown-toggle '.(( $active_menu == 'list_properties' ) ? 'active': ''),
						'data-toggle' => 'dropdown',
						'role' => 'button',
						'aria-haspopup' => 'true',
						'aria-expanded' => 'false',
						'data-toggle' => 'dropdown',
					));
			?>
			<ul class="dropdown-menu">
				<?php 
						echo $this->Html->tag('li', $this->Html->link($sell, array(
							'controller' => 'properties',
							'action' => 'find',
						//	'property_action' => 1,
							'property_action' => 'dijual',
						)));
						echo $this->Html->tag('li', $this->Html->link($rent, array(
							'controller' => 'properties',
							'action' => 'find',
						//	'property_action' => 2,
							'property_action' => 'disewakan',
						)));

						if($isMarketTrend){
							echo($this->Html->tag('li', $this->Html->link(__('Market Trend'), array(
								'admin'			=> false,
								'controller'	=> 'properties',
								'action'		=> 'market_trend', 
							), array(
								'target' => 'blank', 
							))));
						}
				?>
			</ul>
		</li>
		<?php
				if( !empty($developer_page) ){
					echo $this->Html->tag('li', $this->Html->link($developers, array(
						'controller' => 'pages',
						'action' => 'developers'
					), array(
						'class' => ( $active_menu == 'developers' ) ? 'active': '',
					)));
				}

				if( empty($isDirector) ) {
					$agentLi = null;

					if( !empty($isExpertSystem) ) {
						$agentLi .= $this->Html->tag('li',
							$this->Html->link(__('List %s', $agent), array(
								'controller'	=> 'users',
								'action'		=> 'agents'
						)));
						$agentLi .= $this->Html->tag('li',
							$this->Html->link(__('Rank %s', $agent), array(
								'controller'		=> 'activities',
								'action'			=> 'ranks',
						)));
						$agentLi .= $this->Html->tag('li',
							$this->Html->link(__('Rate %s', $property), array(
								'controller'		=> 'properties',
								'action'			=> 'price_movement',
						)));

						echo $this->Html->tag('li', 
							$this->Html->link($agent, '#', array(
								'escape' => FALSE,
								'class' => sprintf('dropdown-toggle %s', ($active_menu == 'agents' ? 'active' : NULL)),
								'data-toggle' => 'dropdown',
								'role' => 'button',
								'aria-haspopup' => 'true',
								'aria-expanded' => 'false'
							)).$this->Html->tag('ul', $agentLi, array(
								'class' => 'dropdown-menu', 
							)), array(
								'class' => 'dropdown', 
							) 
						);
					} else {
						echo $this->Html->tag('li', $this->Html->link($agent, array(
							'controller' => 'users',
							'action' => 'agents'
						), array(
							'class' => ( $active_menu == 'agents' ) ? 'active': '',
						)));
					}
				}

				if( !empty($brochure) ) {
					echo $this->Html->tag('li', $this->Html->link($ebrosur, array(
						'controller' => 'ebrosurs',
						'action' => 'index',
						'admin' => false
					), array(
						'class' => ( $active_menu == 'ebrosurs' ) ? 'active': '',
					)));
				}

				echo $this->Html->tag('li', $this->Html->link($about, array(
					'controller' => 'pages',
					'action' => 'about'
				), array(
					'class' => ( $active_menu == 'about' ) ? 'active': '',
				)));
				echo $this->Html->tag('li', $this->Html->link($contact, array(
					'controller' => 'pages',
					'action' => 'contact'
				), array(
					'class' => ( $active_menu == 'contact' ) ? 'active': '',
				)));

				if( !empty($blog) ) {
					echo $this->Html->tag('li', $this->Html->link($blog_text, array(
						'controller' => 'advices',
						'action' => 'index'
					), array(
						'class' => ( $active_menu == 'advices' ) ? 'active': '',
					)));
				}

				if( !empty($faq) ) {
					echo $this->Html->tag('li', $this->Html->link($faq_text, array(
						'controller' => 'pages',
						'action' => 'faq'
					), array(
						'class' => ( $active_menu == 'faq' ) ? 'active': '',
					)));
				}

				if( !empty($career) ) {
					echo $this->Html->tag('li', $this->Html->link($career_text, array(
						'controller' => 'pages',
						'action' => 'career'
					), array(
						'class' => ( $active_menu == 'career' ) ? 'active': '',
					)));
				}
		?>
	</ul>
</nav>