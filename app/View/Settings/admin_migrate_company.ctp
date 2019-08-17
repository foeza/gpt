<?php
		$searchUrl = array(
			'controller' => 'settings', 
			'action' => 'search',
			'migrate_company', 
			'admin' => TRUE,
		);

		echo($this->element('blocks/common/forms/search/backend', array(
			'placeholder' => __('Cari berdasarkan Nama atau Email'),
			'url' => $searchUrl,
        	'_with_action' => array(
				'controller' => 'settings', 
				'action' => 'admins',
				'admin' => TRUE,
			),
			'sorting' => array(
				'options' => array(
					'options' => array(
						'User.full_name-asc'	=> __('Nama ( A - Z )'),
						'User.full_name-desc'	=> __('Nama ( Z - A )'),
						'User.email-asc'		=> __('Email ( A - Z )'),
						'User.email-desc'		=> __('Email ( Z - A )'),
					),
					'url' => $searchUrl
				), 
				'buttonAdd'	 => array(
					'text' => __('Tambah'),
					'url' => array(
						'controller'=>'settings',
						'action'=> 'add_migrate_company', 
						'admin'=> true
					),
				)
			),
		)));

?>

<div class="table-responsive">
	<?php
			if(!empty($values)){
				$dataColumns = array(
					'principle_name' => array('name' => __('Nama Principle')),
					'progress_name' => array('name' => __('Progress')),
					'status' => array(
						'name' => __('Dibatalkan'), 
						'class' => 'tacenter',
						'style' => 'width: 10%;',
					),
					'created' => array(
						'name' => __('Dibuat'), 
						'class' => 'tacenter',
						'style' => 'width: 10%;',
					),
					'action' => array(
						'name' => __('Action'), 
						'class' => 'tacenter', 
						'style' => 'width: 10%;',
					),
				);

				$fieldColumn = $this->Rumahku->_generateShowHideColumn($dataColumns, 'field-table');
	?>
	<table class="table grey table-migrate">
		<?php
				if(!empty($fieldColumn)){
					echo($this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn)));
				}

				$list = '';

				foreach ($values as $key => $migrate) {
					$id = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'id');
					$name = $this->Rumahku->filterEmptyField($migrate, 'User', 'full_name');
					$company_name = $this->Rumahku->filterEmptyField($migrate, 'UserCompany', 'name');
					$email = $this->Rumahku->filterEmptyField($migrate, 'User', 'email');
					$created = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'created');
					$in_proccess = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'in_proccess');
					$agent_complete = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'agent_complete');
					
					$is_complete_sync = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'is_complete_sync');
					$canceled = $this->Rumahku->filterEmptyField($migrate, 'MigrateCompany', 'canceled');

					$result = $this->Rumahku->getTotalProgresMigrate($migrate);

					$total = $this->Rumahku->filterEmptyField($result, 'total', false, 0);
					$status = $this->Rumahku->filterEmptyField($result, 'status');
					$fields = $this->Rumahku->filterEmptyField($result, 'fields');

					$detail_principle = sprintf(__('
						Nama Perusahaan : <br><b>%s</b><br>
						Nama User : <br><b>%s</b><br>
						Email User : <br><b>%s</b><br>
					'), $company_name, $name, $email);

					if(!empty($fields)){
						$text = '';
						foreach ($fields as $key => $field) {
							if(!empty($field['status']) && $field['status'] == 'completed'){
								$icon = 'rv4-bold-check';
							}else{
								$icon = 'rv4-bold-cross';
							}

							$icon = $this->Rumahku->icon($icon);

							$text .= $this->Html->tag('li', sprintf('%s%s', $icon, $field['field']), array(
								'class' => 'col-sm-4'
							));
						}

						$text = $this->Html->tag('ul', $text, array(
							'class' => 'row list-progress-field'
						));

						$fields = $this->Html->tag('p', __('<b>Field</b> : <br />')).$text;
					}

					$fields = __('Progress : <b>').$total.'%</b>'.$fields;

					if($canceled == 1) {
						$icon = 'rv4-bold-check';
					}else{
						$icon = 'rv4-bold-cross';
					}

					$canceled_label = $this->Rumahku->icon($icon);

					$action = '';
					if(empty($in_proccess) && empty($canceled) && empty($is_complete_sync)){
						$action = $this->Html->link('<i class="rv4-bold-cross"></i> '.__('Batalkan'), array(
							'action' => 'cancel_migrate_company', 
							$id,
							'admin' => true
						), array(
							'escape' => false,
							'class' => 'btn btn-danger btn-xs'
						), __('Yakin ingin membatalkan?'));

						$action .= $this->Html->link('<i class="rv4-compose"></i> '.__('Edit'), array(
							'action' => 'edit_migrate_company', 
							$id,
							'admin' => true
						), array(
							'escape' => false,
							'class' => 'btn btn-danger btn-xs'
						));
					}

					$content = array(
						$detail_principle, 
						$fields,
						array(
							$canceled_label,
							array('class' => 'tacenter')
						),
						$this->Rumahku->customDate($created),
						$action
					);

					$list .= $this->Html->tableCells(array($content));
				}

				if(!empty($list)){
					echo $this->Html->tag('tbody', $list);
				}
		?>
	</table>
	<?php
			}else{
				echo($this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border')));
			}
	?>
</div>