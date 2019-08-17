<?php
		$searchUrl = array(
			'controller' => 'settings',
			'action' => 'search',
			'attributes',
			'admin' => true,
		);
		
        echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Filter by attribute name'),
        	'url' => $searchUrl,
        	'sorting' => array(
        		'buttonDelete' => array(
		            'text' => __('Delete').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => array(
		            	'controller' => 'settings',
			            'action' => 'delete_attributes',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete',
	            		'data-alert' => __('Are you sure want to delete this attributes?'),
	        		),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Add New'),
		            'url' => array(
		            	'controller' => 'settings',
			            'action' => 'attribute_add',
			            'admin' => true,
	            	),
		        ),
		        'options' => array(
		        	'options' => array(
		        		'' => __('- Choose Filtering -'),
		        		'Attribute.name-asc' => __('Attribute Name ( A - Z )'),
		        		'Attribute.name-desc' => __('Attribute Name ( Z - A )'),
	        		),
	        		'url' => $searchUrl,
	        	),
    		),
    	));
?>
<div class="table-responsive">
	<?php 
        	echo $this->Form->create('Attribute', array(
        		'class' => 'form-target',
    		));

			if( !empty($values) ) {
				$dataColumns = array(
		            'checkall' => array(
		                'name' => $this->Rumahku->buildCheckOption('Attribute'),
		                'class' => 'tacenter',
		            ),
		            'name' => array(
		                'name' => __('Name'),
		                'field_model' => 'Attribute.name',
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
	      				$id = $this->Rumahku->filterEmptyField($value, 'Attribute', 'id');
	      				$name = $this->Rumahku->filterEmptyField($value, 'Attribute', 'name');
	      				$modified = $this->Rumahku->filterEmptyField($value, 'Attribute', 'modified');

	      				$customModified = $this->Rumahku->formatDate($modified, 'd M Y');
	      				$action = '';

	      				// Set Action
	      				$action = $this->Html->link(__('Manage'), array(
	      					'controller' => 'settings',
	      					'action' => 'attribute_options',
	      					$id,
	      					'admin' => true,
      					));
      					
	      				$action .= $this->Html->link(__('Edit'), array(
	      					'controller' => 'settings',
	      					'action' => 'attribute_edit',
	      					$id,
	      					'admin' => true,
      					));

	      				echo $this->Html->tableCells(array(
			            	array(
			            		array(
		            				$this->Rumahku->buildCheckOption('Attribute', $id, 'default'),
						            array(
						            	'class' => 'tacenter',
					            	),
			         			),
					            $name,
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
		echo $this->element('blocks/common/pagination');
?>