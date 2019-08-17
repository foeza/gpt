<?php
		$recordID = !empty($recordID) ? $recordID : false;

		$url = array(
			'controller' => 'groups',
        	'action' => 'index',
        	'plugin' => false,
        	'admin' => true,
		);

		if(empty($self)){
			$url[] = $recordID;	
		}

		$urlBack = $this->Html->url($url, true);
?>
<div class="row">
	<div class="col-sm-12">
        <div class="action-group top">
            <div class="btn-group floright">
				<?php
    		            echo $this->Html->link(__('Kembali'), $urlBack, array(
    						'class'=> 'btn default',
    					));
				?>
			</div>
		</div>
	</div>
</div>
<div id="acl-manager" class="tacenter mt30">
	<div class="tacenter mb30">
		<?php
				echo $this->Html->tag('h1', $module_title);
		?>
	</div>
	<?php
			if(!empty($acos)){
	?>
	<div class="row">
		<div class="col-lg-3">
			<div>
				<?php
						echo $this->Html->tag('h2', __('Hak Akses'), array(
							'class' => 'mb15',
						));
						echo $this->Html->tag('p', __('Dipergunakan untuk memberikan akses ke divisi untuk mengelola beberapa modul.'), array(
							'class' => 'margin-top-md-1 margin-bottom-sm-0 margin-bottom-xs-0 cgray2'
						));
				?>
				<div class="mt15">
					<?php
							echo $this->Html->link('Allow All', array(
								'controller' => 'groups',
								'action' => 'checkall',
								$group_id,
								'false',
								'allow',
								'admin' => true,
								'plugin' => false
							), array(
								'class' => 'disinblock mr15 color-green'
							), __('Apakah Anda yakin ingin mengizinkan semua hak akses untuk divisi %s?', $group_name));

							echo $this->Html->link('Deny All', array(
								'controller' => 'groups',
								'action' => 'checkall',
								$group_id,
								'false',
								'deny',
								'admin' => true,
								'plugin' => false
							), array(
								'class' => 'disinblock color-red'
							), __('Apakah Anda yakin ingin menolak semua hak akses untuk divisi %s?', $group_name));
					?>
				</div>
			</div>
		</div>
		<div class="col-lg-9">
			<div>
					<?php
							$content_arr = array();
							$idx = 0;
							foreach ($acos as $key => $aco) {
								$aco_id = $this->Rumahku->filterEmptyField($aco, 'Aco', 'id');

								if(!empty($childs[$aco_id])){
									if($idx % 2 == 0){
										echo '<div class="row">';
									}
									
									$Child = $this->Rumahku->filterEmptyField($aco, 'Child');
									$action = $this->Rumahku->filterEmptyField($aco, 'Action');
									$alias = $this->Rumahku->filterEmptyField($aco, 'Aco', 'alias');

									echo $this->Html->div('col-md-6 mb20', $this->element('blocks/common/forms/access_acl', array(
										'aco' => $aco
									)));

									if($idx % 2 == 1){
										echo '</div>';
									}

									$idx++;
								}
							}

							if($idx % 2 > 0){
								echo '</div>';
							}
					?>
			</div>
		</div>
	</div>
	<?php
			}
	?>
</div>
<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
    		            echo $this->Html->link(__('Kembali'), $urlBack, array(
    						'class'=> 'btn default',
    					));
				?>
			</div>
		</div>
	</div>
</div>