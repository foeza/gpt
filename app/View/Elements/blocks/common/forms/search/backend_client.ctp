<?php 
		$url = !empty($url)?$url:false;
		$placeholder = !empty($placeholder)?$placeholder:false;
		$_advanced = !empty($_advanced)?$_advanced:false;
		$btnSearchClass = !empty($btnSearchClass)?$btnSearchClass:'btn-search';
		$addClass = '';

		$with_search = isset($with_search) ? $with_search : true;

		$sorting = !empty($sorting)?$sorting:false;
		$buttonDelete = !empty($sorting['buttonDelete'])?$sorting['buttonDelete']:false;
		$overflowDelete = !empty($sorting['overflowDelete'])?$sorting['overflowDelete']:false;
		$buttonAdd = !empty($sorting['buttonAdd'])?$sorting['buttonAdd']:false;
		$options = !empty($sorting['options'])?$sorting['options']:false;

		$datePicker = !empty($datePicker)?$datePicker:false;

		if( empty($_advanced) ) {
			$addClass = 'no-side-left';
		}

		echo $this->Form->create('Search', array(
    		'url' => $url,
		));

		if($with_search){
?>
<div class="search-style-1">
	<div class="row">
		<div class="col-sm-12">
		    <div class="input-group <?php echo $addClass; ?>">
				<?php 
						echo $this->Html->link(__('Pencarian'), 'javascript:void(0);', array(
							'escape' => false,
                            'class'=> 'input-group-addon at-left',
                            'role' => 'button',
                        ));
						echo $this->Form->input('keyword', array(
                            'type' => 'text', 
							'label' => false,
                            'div' => false,
                            'class'=> 'form-control has-side-control at-left refine-keyword',
                            'placeholder' => $placeholder,
                        ));
						echo $this->Form->button($this->Rumahku->icon('rv4-magnify'), array(
                            'type' => 'submit', 
                            'class'=> $btnSearchClass,
                        ));

	                    if( !empty($advanced_content) ) {
							echo $this->Html->link(__('Detail Pencarian').$this->Rumahku->icon('caret'), 'javascript:void(0);', array(
								'escape' => false,
	                            'class'=> 'input-group-addon at-right toggle-display advanced-search',
	                            'role' => 'button',
	                            'data-display' => '.search-box',
	                        ));
						}
				?>
			</div>
			<?php 
                    if( !empty($advanced_content) ) {
                		echo $this->element($advanced_content);
					}
			?>
		</div>
	</div>
</div>
<?php
		}
?>
<div class="form-type">
	<div class="row">
		<?php 
				if( !empty($sorting) ) {
			        echo $this->element('blocks/common/forms/sorting/backend', array(
				        'sorting' => $options,
				        'with_checkall' => true,
			    	));
				}

				if( !empty($buttonDelete) ) {
					$class = isset( $buttonDelete['class'] ) ? $buttonDelete['class'] : 'btn red';
					$column_class = isset( $buttonDelete['column_class'] ) ? $buttonDelete['column_class'] : 'col-sm-2';
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
			<div class="action-delete floright">
				<?php 
						$buttonDelete['text'] = $this->Rumahku->icon('rv4-cross').__('Hapus');
						echo $this->Rumahku->buildButton($buttonDelete);
				?>
			</div>
		</div>
		<?php
					}
				}

				if( !empty($buttonAdd) ) {
					echo $this->Rumahku->buildButton($buttonAdd, 'col-sm-2 pull-right btn-add-full', 'btn blue');
				}

				if( !empty($datePicker) ) {
			        echo $this->element('blocks/common/forms/datepicker/backend');
				}

				echo $this->Form->end(); 
		?>
	</div>
</div>