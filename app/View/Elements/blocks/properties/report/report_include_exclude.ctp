<?php
		$title = !empty($title) ? $title : false;
		$filter_values  = !empty($filter_values) ? $filter_values : false;

		echo $this->Html->tag('h2', $title, array(
			'class' => 'mb10',
		));
?>
<div class="table-responsive">
	<?php
			if( !empty($filter_values) ) {
				$dataColumns = array(
					'parameter_type' => array(
		                'name' => __('Parameter'),
		            ),
		            'input' => array(
		                'name' => __('Input'),
		            ),
		        );
	
		        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
	?>
	<table class="table grey">
		<?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
      	<tbody>
      		<?php
	      			foreach( $filter_values as $key => $value ) {
	      				
	      				$name = $this->Rumahku->filterEmptyField($value, 'name');
	      				$value = $this->Rumahku->filterEmptyField($value, 'value');

	      				if( !empty($value) ) {
	      					echo $this->Html->tableCells(array(
								array(
						        	$name,
						        	$value,
						        )
						    ));
	      				}
					}
      		?>
      	</tbody>
    </table>
    <?php 
    		} else {
    			echo $this->Html->tag('p', __('Data belum tersedia'), array(
    				'class' => 'alert alert-warning'
				));
    		}
    ?>
</div>