<?php
		$searchUrl = array(
			'controller' => 'settings',
			'action' => 'search',
			'attribute_sets',
			'admin' => true,
		);
		
        echo $this->element('blocks/common/forms/search/backend', array(
        	'placeholder' => __('Filter by set name'),
        	'url' => $searchUrl,
        	'sorting' => array(
        		'buttonDelete' => array(
		            'text' => __('Delete').$this->Html->tag('span', '', array(
		            	'class' => 'check-count-target',
	            	)),
		            'url' => array(
		            	'controller' => 'settings',
			            'action' => 'delete_attribute_sets',
			            'admin' => true,
	            	),
	            	'options' => array(
	            		'class' => 'check-multiple-delete',
	            		'data-alert' => __('Are you sure want to delete this attribute set?'),
	        		),
		        ),
		        'buttonAdd' => array(
		            'text' => __('Add New'),
		            'url' => array(
		            	'controller' => 'settings',
			            'action' => 'attribute_set_add',
			            'admin' => true,
	            	),
		        ),
		        'options' => array(
		        	'options' => array(
		        		'' => __('- Choose Filtering -'),
		        		'AttributeSet.name-asc' => __('Set Name ( A - Z )'),
		        		'AttributeSet.name-desc' => __('Set Name ( Z - A )'),
	        		),
	        		'url' => $searchUrl,
	        	),
    		),
    	));
?>
<div class="table-responsive">
	<?php 
        	echo $this->Form->create('AttributeSet', array(
        		'class' => 'form-target',
    		));

			if( !empty($values) ) {
				$dataColumns = array(
		            'checkall' => array(
		                'name' => $this->Rumahku->buildCheckOption('AttributeSet'),
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
	      				$id = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'id');
	      				$name = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'name');
	      				$modified = $this->Rumahku->filterEmptyField($value, 'AttributeSet', 'modified');

	      				$customModified = $this->Rumahku->formatDate($modified, 'd M Y');

	      				// Set Action
	      				$action = $this->Html->link(__('Edit'), array(
	      					'controller' => 'settings',
	      					'action' => 'attribute_set_edit',
	      					$id,
	      					'admin' => true,
      					));

	      				echo $this->Html->tableCells(array(
			            	array(
			            		array(
		            				$this->Rumahku->buildCheckOption('AttributeSet', $id, 'default'),
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
		echo $this->element('blocks/common/admin/pagination');
?>