<?php
		$globalData = Configure::read('Global.Data');
		$urlBack = isset($urlBack) ? $urlBack : '#';
		$value = isset($value) ? $value : NULL;

		$appversion 	= Common::hashEmptyField($value, 'MobileAppVersion.appversion', 'N/A');
        $device 		= Common::hashEmptyField($value, 'MobileAppVersion.device', 'N/A');
        $type 			= Common::hashEmptyField($value, 'MobileAppVersion.type', 'N/A');
        $version_code 	= Common::hashEmptyField($value, 'MobileAppVersion.version_code', 'N/A');
        $message 		= Common::hashEmptyField($value, 'MobileAppVersion.message', 'N/A');
        $detail_message = Common::hashEmptyField($value, 'MobileAppVersion.detail_message', 'N/A', array(
        	'EOL' => true
        ));
        $link 			= Common::hashEmptyField($value, 'MobileAppVersion.link');

        if(!empty($link)){
        	$link = $this->Html->link(__('Klik Disini'), $link);
        }else{
        	$link = 'N/A';
        }
?>
<div class="tabs-box">
	<div class="row">
		<div class="<?php echo !empty($dataCompany)?'col-xs-12 col-md-6':'col-md-12'; ?>">
			<div class="box box-success">
				<div class="box-header with-border">
					<?php echo($this->Html->tag('h3', __('Informasi Mobile App Version'))); ?>
				</div>
				<div class="box-body">
					<?php
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('App Version')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $appversion, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Code Version')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $version_code, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Device')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $device, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Tipe Update')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $type, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Pesan')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $message, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Detail Pesan')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $detail_message, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Link')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $link, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="action-group bottom">
	<div class="tacenter">
		<?php
				echo $this->Html->link(__('Kembali'), $urlBack, array(
					'class' => 'btn default inline',
				));
		?>
	</div>
</div>