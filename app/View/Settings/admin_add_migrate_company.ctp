<?php 
		$defaultOptions	= array('frameClass' => 'col-sm-8', 'labelClass' => 'col-xl-2 col-sm-4 control-label taright', 'class' => 'relative col-sm-8');
		$options		= empty($options) ? $defaultOptions : array_merge($defaultOptions, $options);

		echo $this->Html->div('form-group', $this->Html->div('info-full alert', $this->Html->tag('p', __('Anda dapat mengatur data migrasi apa saja yang hanya dibutuhkan.'))));

		$data = $this->request->data;

		echo $this->Form->create('MigrateCompany');
?>
<div class="row">
    <div class="col-sm-12">
		<?php
				echo($this->Rumahku->buildInputForm('MigrateCompany.user_id', array_merge($options, array(
					'label' => __('Principle Email *'),
					'empty' => __('Pilih Principle'),
					'options' => $list_principle,
					'frameClass' => 'col-sm-6'
				))));

				$list = '';
				$is_all = false;
				if(!empty($data_migrates)){
					
					$all_count_field = count($data_migrates);
					$total_checked_field = 0;
					
					foreach ($data_migrates as $field => $value) {
						$text = $this->Rumahku->filterEmptyField($value, 'text');
						$checkbox = $this->Rumahku->filterEmptyField($value, 'checkbox');

						$data_field = !empty($data['MigrateAdvanceCompany'][$field]) ? true : false;
						$checked = (empty($checkbox) || !empty($data_field)) ? true : false;

						if($checked == true){
							$total_checked_field++;
						}

						$input = $this->Rumahku->checkbox('MigrateAdvanceCompany.'.$field, array(
							'label' => $text,
							'class' => !empty($checkbox) ? 'check-option' : false,
							'checked' => $checked,
							'disabled' => empty($checkbox) ? true : false,
						));

						$list .= $this->Html->tag('li', $input, array(
							'class' => 'col-sm-3'
						));
					}

					$is_all = ($total_checked_field == $all_count_field) ? true : false;
				}

				$listAll = $this->Html->tag('li', $this->Rumahku->checkbox('MigrateAdvanceCompany.check_all', array(
					'label' => __('Semua'),
					'class' => 'checkAll',
					'checked' => $is_all
				)), array(
					'class' => 'col-sm-12 mb5'
		        ));
				
				$result = $this->Html->tag('ul', $listAll.$list, array(
					'class' => 'row'
				));

				$label = $this->Html->div('col-sm-2', $this->Html->tag('label', __('Pilih Data *'), array(
					'class' => 'control-label taright'
				)));

				echo $this->Html->div('form-group', $this->Html->div('row', $label.$this->Html->div('col-sm-10', $result)), array(
					'id' => 'migrate-principle-box'
				));
		?>
    </div>
</div>
<div class="action-group bottom mt20">
	<div class="btn-group floright">
		<?php
	            echo $this->Html->link(__('Kembali'), array(
					'controller' => 'settings',
					'action' => 'migrate_company',
					'admin' => true
				), array(
                    'escape' => false,
                    'class' => 'btn default',
                ));
				echo $this->Form->button(__('Simpan'), array(
	                'type' => 'submit',
	                'class' => 'btn blue',
	            ));
		?>
	</div>
</div>
<?php
		echo $this->Form->end();
?>