<?php
		$id = !empty($id)?$id:false;
		$searchUrl = array(
			'controller' => 'settings',
			'action' => 'search',
			'attribute_options',
			$id,
			'admin' => true,
		);
		
		echo $this->element('blocks/settings/attributes/tab_action');
?>
<div class="mt30">
	<?php
        	echo $this->element('blocks/common/forms/search/backend', array(
	        	'placeholder' => __('Filter by option name'),
	        	'url' => $searchUrl,
	        	'sorting' => array(
	        		'buttonDelete' => array(
			            'text' => __('Delete').$this->Html->tag('span', '', array(
			            	'class' => 'check-count-target',
		            	)),
			            'url' => array(
			            	'controller' => 'settings',
				            'action' => 'delete_attribute_options',
				            'admin' => true,
		            	),
		            	'options' => array(
		            		'class' => 'check-multiple-delete',
		            		'data-alert' => __('Are you sure want to delete this option?'),
		        		),
			        ),
			        'buttonAdd' => array(
			            'text' => __('Add New'),
			            'url' => array(
			            	'controller' => 'settings',
				            'action' => 'attribute_option_add',
				            $id,
				            'admin' => true,
		            	),
			        ),
			        'options' => array(
			        	'options' => array(
			        		'' => __('- Choose Filtering -'),
			        		'AttributeOption.name-asc' => __('Option Name ( A - Z )'),
			        		'AttributeOption.name-desc' => __('Option Name ( Z - A )'),
		        		),
		        		'url' => $searchUrl,
		        	),
	    		),
	    	));
	?>
	<div class="table-responsive">
		<?php 
	        	echo $this->Form->create('AttributeOption', array(
	        		'class' => 'form-target',
	    		));

				if( !empty($values) ) {
					$dataColumns = array(
			            'checkall' => array(
			                'name' => $this->Form->checkbox('checkbox_all', array(
		                        'class' => 'checkAll',
		                        'div' => array(
		                        	'class' => 'cb-checkmark',
		                    	),
		                    )),
			                'class' => 'tacenter',
			            ),
			            'name' => array(
			                'name' => __('Name'),
			                'field_model' => 'AttributeOption.name',
			                'display' => true,
			            ),
			            'order' => array(
			                'name' => __('Order'),
			                'class' => 'tacenter',
			            ),
			            'modified' => array(
			                'name' => __('Modified'),
			                'class' => 'tacenter',
			            ),
			            'action' => array(
			                'name' => __('Action'),
			                'class' => 'tacenter',
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
		      				$option_id = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'id');
		      				$name = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'name');
		      				$modified = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'modified');
		      				$order = $this->Rumahku->filterEmptyField($value, 'AttributeOption', 'order');

		      				$customModified = $this->Rumahku->formatDate($modified, 'd M Y');

		      				// Set Action
		      				$action = $this->Html->link(__('Edit'), array(
		      					'controller' => 'settings',
		      					'action' => 'attribute_option_edit',
		      					$id,
		      					$option_id,
		      					'admin' => true,
	      					));
	      					$action .= $this->Html->link(__('Manage'), array(
		      					'controller' => 'settings',
		      					'action' => 'attribute_option_childs',
		      					$option_id,
		      					'admin' => true,
	      					));

		      				echo $this->Html->tableCells(array(
				            	array(
				            		array(
					         			$this->Form->checkbox('id.', array(
				                            'class' => 'check-option',
				                            'value' => $option_id,
				                        )),
							            array(
							            	'class' => 'tacenter',
						            	),
				         			),
						            $name,
			      					array(
						         		$order,
							            array(
							            	'class' => 'tacenter',
						            	),
							        ),
			      					array(
						         		$customModified,
							            array(
							            	'class' => 'tacenter',
						            	),
							        ),
							        array(
						         		$action,
							            array(
							            	'class' => 'tacenter actions',
						            	),
							        ),
			            		),
					        ));
						}
	      		?>
	      	</tbody>
	    </table>
	    <?php 
	    		} else {
	    			echo $this->Html->tag('p', __('Data not available'), array(
	    				'class' => 'alert alert-warning'
					));
	    		}

	        	echo $this->Form->end(); 
	    ?>
	</div>
	<?php 	
			echo $this->element('blocks/common/admin/pagination');
	?>
</div>