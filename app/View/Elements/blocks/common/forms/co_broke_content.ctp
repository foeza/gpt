<?php
		$data = $this->request->data;

		$group_id = Configure::read('User.group_id');

		$cobroke_types = Configure::read('__Site.cobroke_type');

		$isOpenCoBroke 	= Hash::get($data, 'UserCompanyConfig.is_open_cobroke');
		$is_co_broke  	= Hash::get($data, 'UserCompanyConfig.is_co_broke');

        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );

		$cobroke_requirement_default = "1. Nilai komisi [%KOMISI%] untuk CO.BROKING, termasuk teamnya\n";
		$cobroke_requirement_default.= "2. Perjanjian ini hanya berlaku khusus untuk proyek tersebut diatas\n";
		$cobroke_requirement_default.= "3. Pembagian komisi dilakukan saat komisi telah dibayar lunas";

        $cobroke_requirement = Common::hashEmptyField($data, 'UserCompanyConfig.cobroke_requirement', __($cobroke_requirement_default));
        $default_type_co_broke = Common::hashEmptyField($data, 'UserCompanyConfig.default_type_co_broke', 'both');

        if($group_id > 10){
	        echo($this->Rumahku->buildInputToggle('is_co_broke', array_merge($options, array(
	            'label'         => __('Co Broke'),
	            'attributes'    => array(
	                'class'         => 'handle-toggle-content',
	                'data-target'   => '.co-broke-open-box', 
	            ), 
	        ))));
        }
?>
<div class="co-broke-open-box" style="display: <?php echo (!empty($is_co_broke) ? 'block' : 'none');?>">
	<?php

		$isAdmin		= Configure::read('User.admin');
		$isCompanyAdmin	= Configure::read('User.companyAdmin');
		$dataMatch		= str_replace('"', "'", json_encode(array(
			array('.cobroke-approval-placeholder', array('in_corp'), 'slide'), 
		)));

		if($group_id > 10){
			echo($this->Rumahku->buildInputToggle('is_admin_approval_cobroke', array_merge($options, array(
				'label' => __('Approval jadikan Co Broke?'),
			))));
		}

		echo($this->Rumahku->buildInputToggle('is_signature_principle', array_merge($options, array(
			'label' => __('Tampilkan Signature principle saat cetak aplikasi?'),
		))));

		echo($this->Rumahku->buildInputToggle('is_open_cobroke', array_merge($options, array(
			'label'			=> __('Co Broke otomatis?'),
			'attributes'	=> array(
				'class'			=> 'handle-toggle-content',
				'data-target'	=> '.co-broke-box', 
			), 
		))));

		echo($this->Rumahku->buildInputForm('default_type_co_broke', array(
			'label' => __('Tipe Co Broke *'),
			'class' => 'relative col-sm-4 col-xl-4',
			'options' => $cobroke_types,
			'default' => $default_type_co_broke,
			'frameClass' => 'col-sm-12',
			'labelClass' => 'col-xl-2 col-sm-3 control-label taright', 
			'inputClass' => 'handle-toggle', 
			'attributes' => array(
				'data-match' => $dataMatch, 
			), 
		)));

		if($isAdmin){
			echo($this->Html->tag('div', $this->Rumahku->buildInputToggle('is_auto_approve_cobroke', array_merge($options, array(
				'label' => __('Otomatis approve join Co Broke?'),
			))), array(
				'class' => 'cobroke-approval-placeholder', 
			)));
		}

	?>
	<div class="co-broke-box" style="<?php printf('display:%s;', $isOpenCoBroke ? 'block' : 'none');?>">
		<div class="form-group">
	        <div class="row">
	            <div class="col-sm-12">
	            	<div class="row">
	            		<?php
		                       echo $this->Html->div('col-xl-1 col-sm-4 col-md-3 control-label taright', $this->Form->label('default_agent_commission', sprintf(__('Komisi Agen*')), array(
		                            'class' => 'control-label',
		                        )));
		                ?>
						<div class="relative col-sm-4 col-xl-4">
							<div class="relative input-group mb0">
								<?php
										echo $this->Form->input('default_agent_commission', array(
											'type' => 'text',
											'label' => false,
											'div' => false,
											'class' => 'input_number has-side-control at-right form-control',
											'required' => false,
											'error' => false
										));
								?>
								<div class="input-group-addon at-right ">%</div>
							</div>
							<?php
			                  		if( $this->Form->isFieldError('default_agent_commission') ) {
				                        echo $this->Form->error('default_agent_commission', null, array(
				                            'class' => 'error-message'
				                        ));
				                    }
			              	?>
						</div>
					</div>
	            </div>
	        </div>
		</div>
		<div class="form-group">
		    <div class="row">
		        <div class="col-sm-12">
		            <div class="row">
		                <?php
		                       echo $this->Html->div('col-xl-1 col-sm-4 col-md-3 control-label taright', $this->Form->label('default_co_broke_commision', sprintf(__('Komisi Broker *')), array(
		                            'class' => 'control-label',
		                        )));
		                ?>
		                <div class="relative col-sm-4 col-xl-4">
		                	<div class="relative input-group mb0">
			                    <div>
			                        <?php
			                                echo $this->Form->input('default_type_price_co_broke_commision', array(
			                                    'id' => 'currency',
			                                    'class' => 'input-group-addon change-type-price-commission',
			                                    'label' => false,
			                                    'div' => false,
			                                    'required' => false,
			                                    'options' => array(
			                                        'percentage' => 'Persentase',
			                                        'nominal' => 'Nominal'
			                                    )
			                                ));

			                                echo $this->Form->input('default_co_broke_commision', array(
			                                    'type' => 'text',
			                                    'id' => 'price',
			                                    'class' => 'form-control has-side-control at-left input_price padding-custom-input-group',
			                                    'label' => false,
			                                    'div' => false,
			                                    'required' => false,
			                                    'error' => false,
			                                ));
			                        ?>
			                    </div>
			                </div>
			                <?php
			                  		if( $this->Form->isFieldError('default_co_broke_commision') ) {
				                        echo $this->Form->error('default_co_broke_commision', null, array(
				                            'class' => 'error-message'
				                        ));
				                    }
			              	?>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<?php
				echo  $this->Rumahku->buildInputForm('default_type_co_broke_commission', array(
		            'label' => __('Asal Komisi *'),
		            'class' => 'relative col-sm-4 col-xl-4',
		            'options' => $type_commission,
		            'frameClass' => 'col-sm-12',
		            'labelClass' => 'col-xl-2 col-sm-3 control-label taright'
		        ));
		?>
		
	</div>
	<?php
	        echo $this->Rumahku->buildInputForm('cobroke_requirement', array_merge($options, array(
	            'type' => 'textarea',
	            'label' => __('Ketentuan Co Broke'),
	            'infoText' => __('Sisipkan "<b>[%KOMISI%]</b>" untuk melakukan generate terhadap persentase komisi yang akan di dapat oleh broker'),
	            'infoClass' => 'extra-text',
	            'attributes' => array(
	                'value' => $cobroke_requirement
	            )
	        )));
	?>
</div>