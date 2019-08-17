<?php 
		$sorting = !empty($sorting)?$sorting:false;

        $with_checkall = isset($with_checkall) ? $with_checkall : false;

		if( !empty($sorting) ) {
            $ctmLblFilter  = $this->Rumahku->filterEmptyField($sorting, 'ctmLblFilter', false, 'Filter berdasarkan');
            $default_col   = $this->Rumahku->filterEmptyField($sorting, 'customOptions', false, 'col-sm-4');
            $optionsStatus = $this->Rumahku->filterEmptyField($sorting, 'optionsStatus');
            $optionsFilter = $this->Rumahku->filterEmptyField($sorting, 'optionsFilter');
			$options = $this->Rumahku->filterEmptyField($sorting, 'options');
            $url = $this->Rumahku->filterEmptyField($sorting, 'url');

            $divClassFilter = $this->Rumahku->filterIssetField($sorting, 'divClassFilter', false, 'col-sm-4 no-pright');
            
            if( !empty($optionsStatus) ) {
                $addClass = 'col-sm-7';
                $addClassFilter = 'col-sm-3';
                $addClassSort = 'col-sm-5';
            } else {
                if(!$with_checkall){
                    $addClass = $default_col;
                    $addClassFilter = 'col-sm-5';
                    $addClassSort = 'col-sm-7';
                }else{
                    $addClass = 'col-sm-3';
                    $addClassFilter = 'col-xs-2 col-sm-2';
                    $addClassSort = 'col-xs-10 col-sm-10';
                }
            }
?>
<div class="<?php echo $addClass; ?>">
	<div class="sorting-type sorting-style-1">
        <div class="form-group">
            <div class="row">
            	<?php 
                        if(!$with_checkall){
                            echo $this->Html->tag('div', $this->Form->label('sorting', $ctmLblFilter), array(
                                'class' => $addClassFilter,
                            ));
                        }else{
                            echo $this->Html->tag('div', $this->Html->tag('div', $this->Form->input('checkbox_all', array(
                                'type' => 'checkbox',
                                'class' => 'checkAll',
                                'label' => ' ',
                                'div' => array(
                                    'class' => 'cb-checkmark',
                                ),
                            )), array(
                                'class' => 'cb-custom',
                            )), array(
                                'class' => $addClassFilter,
                            ));
                        }

                        if( !empty($optionsStatus) ) {
                            $defaultOptionsStatus  = array(
                                'all' => __('- Semua -'),
                            );

                            $defaultOptionsStatus = array_merge($defaultOptionsStatus, $optionsStatus);

                            echo $this->Form->input('status', array(
                                'label' => false,
                                'class' => 'form-control',
                                'options' => $defaultOptionsStatus,
                                'id' => 'status-changed',
                                // 'empty' => __('- Semua -'),
                                'div' => array(
                                    'class' => $divClassFilter,
                                ),
                                'onChange' => 'submit();',
                            ));
                        }

                        if( !empty($optionsFilter) ) {
                            echo $this->Form->input('filter', array(
                                'label' => false,
                                'class' => 'form-control',
                                'options' => $optionsFilter,
                                'id' => 'status-changed',
                                'empty' => __('- Filter -'),
                                'div' => array(
                                    'class' => $divClassFilter,
                                ),
                                'onChange' => 'submit();',
                            ));
                        }

                        if( !empty($options) ) {
                			echo $this->Form->input('sort', array(
                				'label' => false,
                				'class' => 'form-control',
                				'options' => $options,
                				'onChange' => 'submit();',
                                // 'empty' => __('- Pilih Urutan -'),
                                'div' => array(
                                    'class' => $addClassSort,
                                ),
            				));
                        }
            	?>
            </div>
		</div>
	</div>
</div>
<?php 
		}
?>