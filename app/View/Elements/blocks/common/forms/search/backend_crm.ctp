<?php
		$url = !empty($url)?$url:false;

		$sorting = !empty($sorting)?$sorting:false;
		$divClass = !empty($divClass)?$divClass:false;
		$sortingCampaign = !empty($sortingCampaign)?$sortingCampaign:false;
		$buttonDelete = !empty($sorting['buttonDelete'])?$sorting['buttonDelete']:false;
		$overflowDelete = !empty($sorting['overflowDelete'])?$sorting['overflowDelete']:false;
		$buttonAdd = !empty($sorting['buttonAdd'])?$sorting['buttonAdd']:false;

		$options = $this->Rumahku->filterEmptyField($sorting, 'options', 'options');
		$labelSort = $this->Rumahku->filterEmptyField($sorting, 'labelSort', false, 'Urut Berdasarkan');
		$labelClass = $this->Rumahku->filterEmptyField($sorting, 'labelClass', false, 'col-sm-4');
		$formClass = $this->Rumahku->filterEmptyField($sorting, 'formClass', false, 'col-sm-8');
		$labelInClass = $this->Rumahku->filterEmptyField($sorting, 'labelClass');

		echo $this->Form->create('Search', array(
    		'url' => $url,
		));
?>
<div class="<?php echo $divClass;?>">
	<div class="form-type detail-project-action">
		<div class="row">
			<div class="col-sm-3">
				<div class="row">
					<div class="col-xs-3">
						<?php
								echo $this->Html->div('cb-custom check-all', $this->Form->input('checkbox_all', array(
			                        'type' => 'checkbox',
			                        'class' => 'checkAll',
			                        'label' => ' ',
			                        'div' => array(
			                            'class' => 'cb-checkmark',
			                        ),
			                    )));			                    
						?>
					</div>
					<?php
							if( !empty($buttonAdd) ) {
								echo $this->Html->div('col-xs-9', $this->AclLink->link($buttonAdd['text'], $buttonAdd['url'], array(
			                    	'class' => 'btn blue'
			                    )));
							}
					?>
				</div>
			</div>
			<?php
					if( !empty($buttonDelete) ) {
						$class = isset( $buttonDelete['class'] ) ? $buttonDelete['class'] : 'btn red';
						$column_class = isset( $buttonDelete['column_class'] ) ? $buttonDelete['column_class'] : 'col-xs-12 col-sm-3';
						echo $this->Rumahku->buildButton($buttonDelete, $column_class.' button-type button-style-1', $class.' hide');

						if( !empty($overflowDelete) ) {
			?>
			<div class="delete-overflow clear">
				<div class="counter floleft">
					<?php 
							echo $this->Html->tag('span', 0);
							echo __(' Data dihapus');
					?>
				</div>
				<div class="action-delete">
					<?php 
							$buttonDelete['text'] = $this->Rumahku->icon('rv4-cross').__('Hapus');
							echo $this->Rumahku->buildButton($buttonDelete);
					?>
				</div>
			</div>
			<?php
						}
					}
			?>
			<?php
					if(!empty($sortingCampaign)){
			?>
			<div class="col-xs-12 col-sm-6 pull-right">
				<ul class="taright">
					<?php
							echo $this->Html->tag('li', $this->Html->link(__('Semua'), array(
								'controller' => 'newsletters',
								'action' => 'campaigns',
								'admin' => true
							), array(
								'class' => empty($add_type) ? 'active' : ''
							)));

							echo $this->Html->tag('li', $this->Html->link(__('Terkirim'), array(
								'controller' => 'newsletters',
								'action' => 'campaigns',
								'add_type' => 'sended',
								'admin' => true
							), array(
								'class' => (!empty($add_type) && $add_type == 'sended') ? 'active' : ''
							)));

							echo $this->Html->tag('li', $this->Html->link(__('Dijadwalkan'), array(
								'controller' => 'newsletters',
								'action' => 'campaigns',
								'add_type' => 'scheduled',
								'admin' => true
							), array(
								'class' => (!empty($add_type) && $add_type == 'scheduled') ? 'active' : ''
							)));
					?>
				</ul>
			</div>
			<?php
					}

					if(!empty($options)){
			?>
			<div class="col-sm-5 floright">
				<div class="row">
				<?php
						echo $this->Html->div($labelClass, $this->Form->label('sort', $labelSort, array(
	                        'class' => $labelInClass,
	                    )));

						echo $this->Html->div($formClass, $this->Html->div('', $this->Form->input('sort', array(
	        				'label' => false,
	        				'class' => 'form-control',
	        				'options' => $options,
	        				'onChange' => 'submit();',
	                        'div' => array(
	                            'class' => 'form-group',
	                        ),
	    				))));
				?>
				</div>
			</div>
			<?php
					}
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>