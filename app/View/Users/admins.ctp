<?php
	$dataColumns = array(
        'name' => array(
            'name' => __('Nama'),
            'field_model' => 'User.full_name',
            'display' => true,
        ),
        'email' => array(
            'name' => __('Email'),
            'field_model' => 'User.Email',
            'display' => true,
        ),
        'action' => array(
            'name' => __('Action'),
            'field_model' => false,
            'display' => true,
        ),
    );
    $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );
?>

<div class="account">
	<div class="row">
		<div class="col-sm-3">
			<?php
				echo $this->element('sidebars/left_sidebar_menu');
			?>
		</div>

		<div class="col-sm-9">
			<?php
				echo $this->Html->tag('div', $this->Html->tag('h3', __('Daftar Admin')), array(
					'class' => 'page-header'
				))
			?>

			<div class="wrapper-table">
				<?php
						if( !empty($admins) ) {
				?>
				<table class="table table-hover">
					<thead>
		                <tr>
		                    <?php
	                            if( !empty($fieldColumn) ) {
	                                echo $fieldColumn;
	                            }
		                    ?>
		                </tr>
		            </thead>
			      	<tbody>
			      		<?php
			      			foreach( $admins as $key ) {
			      				$current_admin_user = $key['User'];
			      				$current_admin_profile = $key['UserProfile'];
			      				$btnEditAdmin = $this->Html->link(__('Edit'), array(
			      					'controller' => 'users',
			      					'action' => 'edit_admin',
			      					$current_admin_user['id'],
			      					'admin' => false,
		      					), array(
		      						'class' => 'btn btn-primary',
		      					));
		      					$btnDeleteAdmin = $this->Html->link(__('Hapus'), array(
			      					'action' => 'delete_admin',
			      					$current_admin_user['id'],
			      					'admin' => false,
		      					), array(
		      						'class' => 'btn btn-danger',
		      					),__('Yakin ingin menghapus ?'));

								echo $this->Html->tableCells(
							        array(
							            $current_admin_user['full_name'],
							            $current_admin_user['email'],
							         	$btnEditAdmin.'&nbsp;'.$btnDeleteAdmin 
							        )
							    );
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
		</div>
	</div>
</div>