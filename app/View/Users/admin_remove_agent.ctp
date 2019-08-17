<?php
		$flag_agent = Common::hashEmptyField($values, 'agent');
		$recordID = !empty($recordID) ? $recordID : false;
?>
<div id="modal-remove-agent" class="modal-subheader transparent">
	<div id="wrapper-modal-write">
		<?php
				echo $this->Form->create('UserRemoveAgent', array(
		            'class' => 'ajax-form',
		            'data-type' => 'content',
		            'data-wrapper-write' => '#wrapper-modal-write',
		            'data-reload' => 'true',
		        ));

		        // Set Build Input Form
		        $options = array(
		            'frameClass' => 'col-sm-12',
		            'labelClass' => 'col-xl-2 col-sm-4',
		            'class' => 'relative col-sm-8 col-xl-7',
		        );
		?>
			<div class="table-responsive">
				<?php
						if( !empty($values) ) {

							$dataColumns = array(
								'hiddenCheckall' => array(
					                'name' => __('Checkbox'),
					                'display' => false,
					            ),
					            'name' => array(
					                'name' => __('Nama'),
					            ),
					            'email' => array(
					                'name' => __('Email'),
					            ),
					            'merge' => array(
					                'name' => __('Alihkan Data'),
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
				      				$id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
				      				$name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
				      				$email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
				      				$group_id = $this->Rumahku->filterEmptyField($value, 'User', 'group_id');

				      				if($group_id == 2){
				      					$icon = $this->Rumahku->icon('rv4-check', false, 'i', 'no-margin color-green');
				      				} else {
				      					$icon = $this->Rumahku->icon('rv4-cross', false, 'i', 'no-margin color-red');
				      				}

				      				$icon = $this->Html->tag('span', $icon, array(
							            'class' => 'status-label-checked',
							        ));

				      				$email = $email ? $this->Html->link($email, sprintf('mailto:%s', $email)) : '-';

				      				if( !empty($id) ) {
					      				echo $this->Html->tableCells(array(
						            		array(
						            			array(
								         			$this->Rumahku->buildCheckOption('UserRemoveAgent', $id, 'default', true, 'custom-check'),
										            array(
										            	'class' => 'hide',
										            ),
							         			),
						            			$name,
									            $email,
									            $icon,
					            			),
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
			<div class="content">
				<?php 

				//	buat handle proses di backend
					echo($this->Form->hidden('process', array(
						'value' => 1, 
					)));

					if($flag_agent){
						echo $this->Rumahku->buildInputForm('agent_email', array_merge($options, array(
							'type' => 'text',
			                'label' => __('Alihkan data ke agen *'),
			                'id' => 'autocomplete',
			                'attributes' => array(
	                    		'autocomplete' => 'off',
	                    		'data-ajax-url' => $this->Html->url(array(
				                    'controller' => 'ajax',
				                    'action' => 'list_users',
				                    'admin' => false,
				                    2,
				                    'parent_id' => $recordID,
				                )),
	                		),
			            )));
					}

					echo $this->Rumahku->buildInputForm('reason_principle', array_merge($options, array(
						'type' => 'textarea',
		                'label' => __('Alasan Menghapus *'),
		            )));

				?>
			</div>
			<div class="modal-footer">
				<?php 
						echo $this->Html->link(__('Batal'), '#', array(
		    	            'class' => 'close btn default',
		    	            'data-dismiss' => 'modal',
		    	            'aria-label' => 'close',
		    	        ));
						echo $this->Form->button(__('Simpan'), array(
		    	            'class' => 'btn blue',
		    	        ));
				?>
			</div>
		<?php 
		        echo $this->Form->end();
		?>
	</div>
</div>