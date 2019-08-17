<?php 
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=laporan-lead-properti-klien.xls');
?>
<div class="table-responsive" style="margin-bottom: 30px;overflow-x: auto;min-height: 0.01%;border: 1px solid #f2f2f2;-moz-border-radius: 3px;-webkit-border-radius: 3px;border-radius: 3px;">
	<?php 
			echo $this->element('headers/report');

			if( !empty($values) ) {
				
				$dataColumns = array(
					'no' => array(
		                'name' => __('No'),
		                'style' => 'text-align: center; background-color: #069E55; color: #FFFFFF; width:40px;',
		            ),
		            'Date' => array(
		                'name' => __('Tanggal'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:100px;',
		            ),
					'name' => array(
		                'name' => __('Nama'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:260px;',
		            ),
		            'email' => array(
		                'name' => __('Email'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:260px;',
		            ),
		            'no_hp' => array(
		                'name' => __('No. HP'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:140px;',
		            ),
		            'browser' => array(
		                'name' => __('Browser'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:200px;',
		            ),
		            'utm' => array(
		                'name' => __('UTM'),
		                'style' => 'background-color: #069E55; color: #FFFFFF; width:220px;',
		            ),
		        );
				
		        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
	?>
	<table class="table grey" style="width: 100%;max-width: 100%;margin-bottom: 30px;border-top-left-radius: 10px;border-top-right-radius: 10px; border-collapse: collapse; border-spacing: 0; border-color: grey;">
    	<?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
      	<tbody style="display: table-row-group;vertical-align: middle; border-color: inherit;">
      		<?php
      				$no = 1;

	      			foreach( $values as $key => $value ) {
	      				$date = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'created');
	      				$name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name', '-');
		                $email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '-');
		                $no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
		                $browser = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'browser', '-');
		                $utm = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'utm', '-');

	  					$customDate = $this->Rumahku->formatDate($date, 'd M Y');

						echo $this->Html->tableCells(array(
							array(
					        	array(
					        		$no,
					            	array(
			                			'style' => 'text-align: center;',
					            	),
				        		),
				        		array(
					        		$customDate,
					            	array(
			                			'style' => 'text-align: center;',
					            	),
				        		),
					        	$name,
					        	$email,
					        	($no_hp!='-')?sprintf('# %s', $no_hp):'-',
					        	$browser,
					        	$utm,
					        )
					    ));

      					$no++;
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