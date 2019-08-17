<div class="table-responsive">
	<?php
			echo $this->Form->create('Property', array(
        		'class' => 'form-target',
    		));

			if( !empty($values) ) {
				$dataColumns = array(
					'Date' => array(
		                'name' => __('Tanggal'),
		            ),
		            'name' => array(
		                'name' => __('Nama'),
		            ),
		            'email' => array(
		                'name' => __('Email'),
		            ),
		            'no_hp' => array(
		                'name' => __('No. HP'),
		            ),
		            'message' => array(
		                'name' => __('Message'),
		                'style' => 'width:45%',
		            ),
		            'utm' => array(
		                'name' => __('UTM'),
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
      			foreach( $values as $key => $value ) {
      				$date = $this->Rumahku->filterEmptyField($value, 'Message', 'created');
	                $name = $this->Rumahku->filterEmptyField($value, 'Message', 'name', '-');
	                $email = $this->Rumahku->filterEmptyField($value, 'Message', 'email', '-');
	                $no_hp = $this->Rumahku->filterEmptyField($value, 'Message', 'phone', '-');
	                $message = $this->Rumahku->filterEmptyField($value, 'Message', 'message', '-');
	                $utm = $this->Rumahku->filterEmptyField($value, 'Message', 'utm', '-');

  					$customDate = $this->Rumahku->formatDate($date, 'd M Y');
	               
					echo $this->Html->tableCells(array(
						array(
							$customDate,
				        	$name,
				        	$email,
				        	$no_hp,
				        	$message,
				        	$utm,
				        )
				    ));
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

        	echo $this->Form->end(); 
    ?>
</div>
<?php 	
		echo $this->element('blocks/common/pagination');
?>