<div class="table-responsive">
	<?php
			echo $this->Form->create('Property', array(
        		'class' => 'form-target',
    		));

			if( !empty($values) ) {
				$dataColumns = array(
					'VisitDate' => array(
		                'name' => __('Tgl Kunjung'),
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
		            'browser' => array(
		                'name' => __('Browser'),
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
      				$visit_date = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'created');
	                $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name', '-');
	                $email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '-');
	                $no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
	                $browser = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'browser', '-');
	                $utm = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'utm', '-');

  					$customVisitDate = $this->Rumahku->formatDate($visit_date, 'd M Y');
	               
					echo $this->Html->tableCells(array(
						array(
							$customVisitDate,
				        	$name,
				        	$email,
				        	$no_hp,
				        	$browser,
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