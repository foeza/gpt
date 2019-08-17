<?php 
		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$sold = $this->Rumahku->filterEmptyField($property, 'Property', 'sold');

		$url = array(
			'controller' => 'properties',
			'action' => 'preview',
			$id,
			'admin' => true,
		);
		$check = $this->AclLink->aclCheck($url);
?>
<div class="form-type header-crumb action-group" id="header-preview">
	<div class="row">
		<div class="col-sm-2">
			<?php
					echo $this->AclLink->link(sprintf(__('%s kembali'), $this->Rumahku->icon('rv4-angle-left')), array(
						'controller' => 'properties',
						'action' => 'index',
						'admin' => true
					), array(
						'escape' => false
					));
			?>
		</div>
		<div class="col-sm-10 btn-group floright taright">
			<?php
					echo $this->AclLink->link(__('Edit'), array(
						'admin'			=> true,
						'controller'	=> 'properties',
					//	'action'		=> 'edit',
						'action'		=> 'easy_preview',
						$id,
					), array(
						'class' => 'btn default floright'
					));

					if($is_revision){
						echo $this->AclLink->link(__('Tolak'), array(
							'controller' => 'properties',
							'action' => 'rejected',
							$id,
							'admin' => true
						), array(
							'class' => 'btn red ajaxModal floright',
							'title' => __('Menolak revisi Properti'),
						));

						$check = $this->AclLink->aclCheck(array(
							'controller' => 'properties',
							'action' => 'approval',
							'admin' => true,
						));

						echo $this->Form->button(__('Setujui'), array(
							'type' => 'submit',
							'class' => 'btn blue pull-right btn-save-confirm floright',
							'data-msg' => __('Apakah Anda yakin ingin menyetujui revisi properti ini?')
						));
					} else if(in_array($logged_group, Configure::read('__Site.Admin.Company.id')) || $check ){
						if( !empty($sold) ) {
							echo $this->Property->soldButton($property, array(
								'btnClass' => 'btn red',
								'frame' => 'div',
								'frameClass' => 'floright',
							));
						}
						
						echo $this->Property->approveButton($property, array(
							'btnClass' => 'btn green',
							'frame' => 'div',
							'frameClass' => 'floright',
						));
					}
			?>
		</div>
	</div>
</div>