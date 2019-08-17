<?php
    	$data = $this->request->data;
        $check_term = $this->Rumahku->filterIssetField( $data, 'KprBankTransfer', 'check_term', null);
    	$bank_name = Common::hashEmptyField( $value, 'Bank.name');

    	$user = $this->Rumahku->filterEmptyField($value, 'User');
    	$user_profile = $this->Rumahku->filterEmptyField($value, 'UserProfile');

    	$full_name = $this->Rumahku->filterEmptyField($user, 'full_name');
    	$full_name_akun = $this->Rumahku->filterEmptyField($user_profile, 'rekening_nama_akun');

    	$full_name = !empty($full_name_akun)?$full_name_akun:$full_name;
    	$mandatory = $this->Html->tag('span',__('*'), array(
    		'class' => 'color-red',
    	));
?>
<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('KprBankTransfer', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-3 col-sm-3',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>
	<div class="content kpr-commission-payment" id="property-sold">
        <?php
                echo $this->Html->tag('div', 
                    $this->Html->tag('div', 
                        $this->Html->tag('label', __('PENTING')).
                        $this->Html->tag('ul', 
                            $this->Html->tag('li', __('Informasi rekening Agen untuk klaim pembayaran provisi kepada Pihak Bank, mohon diisi dengan benar & tepat. Segala kesalahan informasi diluar tanggung jawab %s.', Configure::read('__Site.site_name'))).
                            $this->Html->tag('li', __('Provisi yang diterima, telah dipotong pajak oleh Bank.'))
                        ), array(
                        'class' => 'wrapper-alert',
                    )), array(
                    'class' => 'crm-tips kpr-alert',
                ));

				echo $this->Rumahku->buildInputForm('bank_name', array_merge($options, array(
                    'label' => sprintf(__('Nama Bank %s'), $mandatory),
                    'type' => 'text',
                    'class' => 'relative col-sm-7 col-xl-7',
                )));

				echo $this->Rumahku->buildInputForm('name_account', array_merge($options, array(
                    'label' => sprintf(__('Nama Pemilik Rekening %s'), $mandatory),
                    'type' => 'text',
                    'class' => 'relative col-sm-7 col-xl-7',
                )));

                echo $this->Rumahku->buildInputForm('no_account', array_merge($options, array(
                    'label' => sprintf(__('No Rekening %s'), $mandatory),
                    'type' => 'text',
                    'class' => 'relative col-sm-7 col-xl-7',
                )));

                echo $this->Rumahku->buildInputForm('no_npwp', array_merge($options, array(
                    'label' => sprintf(__('NPWP %s'), $mandatory),
                    'type' => 'text',
                    'class' => 'relative col-sm-7 col-xl-7',
                )));

                $check_box = $this->Form->checkbox('check_term', array(
                	'class' => 'nowidth',
                    'id' => 'chk-terms',
                ));
                $label = $this->Html->tag('label', sprintf(__('Informasi di atas adalah benar dan kesalahan dalam input data diluar tanggung jawab pihak %s'), $this->Html->tag('strong', Configure::read('__Site.site_name'))), array(
                    'for' => 'chk-terms',
                ));

                $div_space = $this->Html->tag('div', false, array(
                	'class' => 'col-xl-3 col-sm-3',
                ));
                $div_label = $this->Html->tag('div', $check_box.$label, array(
                	'class' => 'relative col-sm-7 col-xl-7',
                ));
                echo $this->Html->div('form-group mb0', $this->Html->tag('div', sprintf('%s %s', $div_space, $div_label), array(
                	'class' => 'row',
                )));

                if(isset($check_term) && $check_term == 0){
                	$div_error = $div_label = $this->Html->tag('div', __('Keterangan di atas harus dicentang'), array(
                    	'class' => 'relative col-sm-7 col-xl-7 color-red',
                    ));
                    echo $this->Html->div('form-group', $this->Html->div('row', $div_space.$div_error));
                }
                ## SET KETERANGAN 
              	// $keterangan = array(
              	// 	__('No. rekening  merupakan rekening tujuan pembayaran provisi oleh Bank kepada Agen'),
              	// 	__('Provisi yang diterima, telah dipotong pajak oleh Bank'),
              	// );
                /*
        ?>
        <div class="form-group mt15">
        	<div class="row">
        		<div class="col-sm-10 col-sm-offset-2 terms-commission">
        			<?php
            				echo $this->Html->tag('h3', __('Keterangan :'));

            				if(!empty($keterangan)){
                                $contentLi = false;

	        					foreach($keterangan AS $key => $ket){
	        						$contentLi .= $this->Html->tag('li', $ket);
	        					}

                                echo $this->Html->tag('ul', $contentLi);
	                  	   }
        			?>
        		</div>
        	</div>
        </div>
        <?php
                */
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