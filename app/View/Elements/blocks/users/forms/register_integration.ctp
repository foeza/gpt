<?php
		$data = $this->request->data;
		$is_email_all_addon = $this->Rumahku->filterEmptyField($data, 'UserIntegratedOrder', 'is_email_all_addon');

		echo $this->Rumahku->buildInputForm('name_applicant', array_merge($options, array(
            'label' => __('Nama Lengkap *'),
            'labelClass' => 'col-sm-2 control-label taright',
            'class' => 'col-sm-5 col-xl-7 relative',
        )));

        echo $this->Rumahku->buildInputForm('phone', array_merge($options, array(
            'label' => __('No. Telepon *'),
            'labelClass' => 'col-sm-2 control-label taright',
            'class' => 'col-sm-5 col-xl-7 relative',
            'infoText' => __('Harap gunakan kode area untuk nomor telepon. Contoh: 0215332555'),
        )));

        
?>

	<div class="form-group">
	    <div class="row">
	        <div class="col-sm-12 col-md-12">
	            <div class="row">
	                <div class="col-xl-2 col-sm-2 control-label taright"></div>
	                <div class="col-xl-4 col-sm-5 extra-checkbox relative">
	                    <div class="cb-custom mt10 mb10">
					        <div class="cb-checkmark">
					            <?php   
					                    // echo $this->Form->input('is_email_all_addon',array(
					                    //     'type' => 'checkbox',
					                    //     'label'=> false,
					                    //     'required' => false,
					                    //     'class' => 'trigger-toggle',
					                    //     'required' => false,
					                    //     'div' => false,
					                    //     'data-show' => '#trigger-all-addon',
					                    //     'data-hide' => '.trigger-hide',
					                    // ));
					                    // echo $this->Form->label('is_email_all_addon', __('Gunakan 1 email untuk semua addon?'));
					            ?>
					        </div>
					    </div>
					    <?php
					    		echo $this->Html->tag('div',
					    			$this->Form->input('email_all_addon', array(
					    				'type' => 'email',
						            	'label' => false,
						            	'required' => false,
					                    'div' => false,
						            	'autocomplete' => 'off',
						        	)), array(
					            		'id' => 'trigger-all-addon',
						            	'class' => !empty($is_email_all_addon)?'shows':'hide',
						        ));
					    ?>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="form-group">
	    <div class="row">
	        <div class="col-sm-12 col-md-12">
	        	<div class="form-group">
		            <div class="row">
		                <div class="col-sm-2 control-label taright">
		                	<label class="control-label">Tambah Addon</label>
		                </div>
		                <div class="col-sm-9 relative">
						    <?php
						    		// ambil value dari tabel setting apabila ada pengurangan atau penambahan form
						    		$content = false;
						    		foreach ($get_forms as $key => $value) {
						    			$name_form = Common::hashEmptyField($value, 'Setting.value');
							    		$content.= $this->element(sprintf('blocks/users/forms/register_integration_%s', $name_form), array(
							    			'data' => $data,
							    		));
						    		}

						    		echo $content;
						    ?>
		                </div>
		            </div>
	            </div>
	        </div>
	    </div>
	</div>

	<?php
	        echo $this->Html->tag('h2', __('Alamat'), array(
	        	'class' => 'sub-heading'
	        ));
	        echo $this->element('blocks/users/forms/address', array(
	            'options' => $options,
	            'inputAddress' => $inputAddress,
	            'modelName' => 'UserIntegratedOrder',
	        ));
	?>