<?php 
		$data = $this->request->data;
		$_is_advanced_search = isset($_is_advanced_search) ? $_is_advanced_search : 'adv-search';
		$_with_action = isset($_with_action) ? $_with_action : true;
		$_display = isset($_display) ? $_display : 'hide';
?>
<div class="search-box <?php echo $_display; ?>">
	<div class="detail-menu <?php echo $_is_advanced_search ?>">
		<div class="menu">
			<div class="row">
				<div class="col-sm-12">
					<div class="basic">
						<?php 
								echo $this->Rumahku->buildInputForm('Search.full_name',  array(
									'type' => 'text',
					            	'frameClass' => 'col-sm-8 pr7',
									'label' => __('Nama'),
									'labelClass' => 'col-xl-2 col-sm-2 control-label',
					            ));
						?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="space">
						<?php 
								echo $this->Rumahku->buildInputForm('Search.email',  array(
									'type' => 'text',
					            	'frameClass' => 'col-sm-8 pr7',
									'label' => __('Email'),
									'labelClass' => 'col-xl-2 col-sm-2 control-label',
					            ));
						?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="extra">
						<?php 
								echo $this->Rumahku->buildInputForm('Search.phone',  array(
									'type' => 'text',
					            	'frameClass' => 'col-sm-8 pr7',
									'label' => __('Phone'),
									'labelClass' => 'col-xl-2 col-sm-2 control-label',
					            ));
						?>
					</div>
				</div>
			</div>
		</div>

		<?php
				if( !empty($_with_action) ) {
		?>
		<div class="action-btn">
			<?php 
					echo $this->Html->link(__('Reset'), array(
		                'controller' => 'properties',
						'action' => 'index',
		                'admin' => true,
		            ));
					echo $this->Form->button(__('Cari Properti'),  array(
		            	'class' => 'btn blue',
		            ));
			?>
		</div>
		<?php
				}
		?>
	</div>
</div>