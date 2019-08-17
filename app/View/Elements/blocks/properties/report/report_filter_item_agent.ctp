<?php
		$class = !empty($class) ? $class : ''; 

		$filter_options = !empty($filter_options) ? $filter_options : false;
		$filter_param_options = $this->Rumahku->filterEmptyField($filter_options, 'filter_param_options');
		$filter_text_options = $this->Rumahku->filterEmptyField($filter_options, 'filter_text_options');
?>

<div class="form-group <?php echo $class; ?>">
	<div class="row">
		<?php
				echo $this->Form->input('AdvSearch.filter_type', array(
	                'label' => __('Tipe Filter'),
	                'class' => 'ddlFilterType',
	                'div' => array(
	                	'class' => 'col-sm-2'
	                ),
	                'empty' => false,
	                'options' => array(
	                	'Include' => 'Include',
	                	'Exclude' => 'Exclude',
	                ),
	            ));

	            echo $this->Form->input('AdvSearch.filter_param', array(
	                'label' => __('Parameter'),
	                'div' => array(
	                	'class' => 'col-sm-2'
	                ),
	                'class' => 'ddlFilterParamReport',
	                'empty' => false,
	                'options' => $filter_param_options,
	            ));

	            echo $this->Form->input('AdvSearch.filter_condition', array(
	                'label' => __('Condition'),
	                'class' => 'ddlFilterCondition',
	                'div' => array(
	                	'class' => 'col-sm-3 customFilter directFilterText'
	                ),								                
	                'empty' => false,
	                'options' => $filter_text_options,
	            ));

	            echo $this->Form->input('FilterParam.inc|full_name|match.', array(
	                'label' => __('Value'),
	                'class' => 'valueField',
	                'div' => array(
	                	'class' => 'col-sm-4 customFilter directFilterText directFilterNumeric'
	                ),
	            ));
		?>
		<div class="col-sm-1 mt30">
			<?php
					echo $this->Html->link($this->Rumahku->icon('rv4-trash'), '#', array(
                        'escape' => false,
                        'class' => 'btnDeleteCurrentRow',
                        'target-parent-selector' => '.form-group, .valueField',
                    ));
			?>
		</div>
	</div>
</div>