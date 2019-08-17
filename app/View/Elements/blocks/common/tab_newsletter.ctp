<?php
		$arr_tab = array(
			'personal' => array(
				'title' => __('Personal'),
				'url' => $this->Html->url(array(
					'controller' => 'newsletters',
					'action' => 'personals',
					'admin' => true
				))
			),
			'campaign' => array(
				'title' => __('Campaign'),
				'url' => $this->Html->url(array(
					'controller' => 'newsletters',
					'action' => 'campaigns',
					'admin' => true
				))
			),
			'template' => array(
				'title' => __('Email Template'),
				'url' => $this->Html->url(array(
					'controller' => 'newsletters',
					'action' => 'templates',
					'admin' => true
				))
			),
		);

		if(!empty($tab_content)){
			$arr_tab = $tab_content;
		}

		$key_val = array();
		$active_url = '';
?>
<div class="detail-project-menu">
	<ul class="desktop-only">
		<?php
				foreach ($arr_tab as $key => $value) {
					$active_class = (!empty($tab_active) && $tab_active == $key) ? 'active' : ''; 
					echo $this->Html->tag( 'li', $this->AclLink->link($value['title'], $value['url'], array(
						'class' => $active_class
					)) );

					if($active_class == 'active'){
						$active_url = $value['url'];
					}

					$key_val[$value['url']] = $value['title'];
				}
		?>
	</ul>
	<div class="mobile-only">
		<div class="input-group">
			<div class="select">
				<?php
						echo $this->Form->input('change_url', array(
							'options' => $key_val,
							'onchange' => 'location = this.options[this.selectedIndex].value;',
							'div' => false,
							'value' => $active_url,
							'label' => false
						));

						echo $this->Html->tag('span', '', array('class' => 'rv4-angle-down'));
				?>
			</div>
		</div>
	</div>
</div>