<?php 
		if( !empty($url_list) ) {
?>
<div class="form-type header-crumb hidden-print">
	<div class="row">
		<?php 

			$actions = array();

			if(!empty($prev['url'])){
				$label		= Common::hashEmptyField($prev, 'text', 'Prev');
				$actions[]	= $this->Html->link($label, $prev['url'], array(
					'escape' => false, 
				));
			}

			$actions[] = $this->Html->link(__('Kembali ke daftar'), $url_list);

			if(!empty($next['url'])){
				$label		= Common::hashEmptyField($next, 'text', 'Next');
				$actions[]	= $this->Html->link($label, $next['url'], array(
					'escape' => false, 
				));
			}

			$buttons		= array();
			$buttonOptions	= array(
				'options' => array(
					'use_frame' => false, 
				), 
			);

			if( !empty($buttonRegenerate) ) {
				$class		= Common::hashEmptyField($buttonRegenerate, 'class', 'btn darkblue disinblock');
				$buttons[]	= $this->Rumahku->buildButton(array_merge($buttonOptions, $buttonRegenerate), false, $class);
			}

			if( !empty($buttonEdit) ) {
				$class		= Common::hashEmptyField($buttonEdit, 'class', 'btn green disinblock');
				$buttons[]	= $this->Rumahku->buildButton(array_merge($buttonOptions, $buttonEdit), false, $class);
			}

			if( !empty($buttonAdd) ) {
				$class		= Common::hashEmptyField($buttonAdd, 'class', 'btn blue disinblock');
				$buttons[]	= $this->Rumahku->buildButton(array_merge($buttonOptions, $buttonAdd), false, $class);
			}

			$actions = implode(' | ', array_filter($actions));
			$buttons = implode(' ', array_filter($buttons));

			echo($this->Html->div('col-sm-6', $actions));
			echo($this->Html->div('col-sm-6', $buttons, array(
				'align' => 'right', 
			)));

		?>
	</div>
</div>
<?php 
		}
?>