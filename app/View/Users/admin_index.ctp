<?php 	
		$this->Html->addCrumb(__('Daftar User')); 

		echo $this->element('blocks/layouts/admin_search', array(
			'path_content' => 'blocks/users/admin_search_index'
		));
?>
<div class="box box-success">
    <div class="box-header">
    	<?php
	            echo $this->Html->tag('h3', '<i class="fa fa-table"></i> '.$module_title, array(
	                'class' => 'box-title'
	            ));
	    ?>
    </div>
    <div class="box-body">
        <div class="table-responsive">
			<table class="table table-striped table-advance table-hover">
				<thead>
					<tr>
						<th width="15%">
							<?php
									echo $this->Paginator->sort('User.code', '<i class="fa fa-star"></i> '.__('Kode User'), array(
										'escape' => false
									));
							?>
						</th>
						<th width="15%">
							<?php
									echo $this->Paginator->sort('User.full_name', '<i class="fa fa-user"></i> '.__('Nama User'), array(
										'escape' => false
									));
							?>
						</th>
						<th width="15%">
							<?php
									echo $this->Paginator->sort('User.email', '<i class="fa fa-envelope"></i> '.__('Email'), array(
										'escape' => false
									));
							?>
						</th>
						<th width="10%">
							<?php
									echo $this->Paginator->sort('Group.name', '<i class="fa fa-bookmark"></i> '.__('Group'), array(
										'escape' => false
									));
							?>
						</th>
						<th width="10%" class="text-center">
							<?php
									echo $this->Paginator->sort('User.status', '<i class="fa fa-bookmark"></i> '.__('Status'), array(
										'escape' => false
									));
							?>
						</th>
						<th width="10%">
							<?php
									echo $this->Paginator->sort('User.modified', '<i class="fa fa-calendar"></i> '.__('Dirubah'), array(
										'escape' => false
									));
							?>
						</th>
						<th><?php echo '<i class="fa fa-wrench"></i> '.__('Actions');?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
					if(!empty($users)) {
						foreach ($users as $user) {
							if(!empty($user['User'])){
				?>
					<tr>
						<td><?php echo $user['User']['code']; ?></td>
						<td><?php echo $this->Rumahku->safeTagPrint($user['User']['full_name']); ?></td>
						<td>
							<?php 
								echo $this->Rumahku->safeTagPrint($user['User']['email']); 
							?>
						</td>
						<td>
							<?php
									if( !empty($groups[$user['User']['group_id']]) ) {
										echo $this->Rumahku->safeTagPrint($groups[$user['User']['group_id']]);
									} else {
										echo '-';
									}
							?>
						</td>
						<td class="text-center">
							<?php 
									if($user['User']['status']) {
										echo $this->Html->link('<i class="fa fa-check"></i>', array(
											'action' => 'toggle',
											$user['User']['id'],
											'status',
											'admin' => true
										), array(
											'escape' => false,
											'class' => 'btn btn-success btn-xs'
										));
									} else {
										echo $this->Html->link('<i class="fa fa-times"></i>', array(
											'action' => 'toggle',
											$user['User']['id'],
											'status',
											'admin' => true
										), array(
											'escape' => false,
											'class' => 'btn btn-danger btn-xs'
										));
									}
							?>
						</td>
						<td><?php echo $this->Time->timeAgoInWords($user['User']['modified'], array('end'=> '+1 months')); ?></td>
						<td class="actions">
							<?php 
									if($user['User']['active'] && $user['User']['status']) {
										echo $this->Html->link('<i class="fa fa-search"></i> '.__('Lihat profil'), array(
											'action' => 'profile', 
											$user['User']['id'], 
											$user['User']['username'], 
											'admin'=> false
										), array(
											'target'=> '_blank', 
											'rel'=> 'external',
											'escape' => false,
											'class' => 'btn btn-primary btn-xs'
										));
										echo '&nbsp;';
									}

									if(in_array($user['User']['group_id'], array(1, 2))) {
										echo $this->Html->link('<i class="fa fa-user"></i> '.__('Daftar properti'), array(
											'controller' => 'properties', 
											'action'=> 'index', 
											'email'=> $user['User']['email'], 
											'admin'=> true
										), array(
											'target'=> '_blank', 
											'escape' => false,
											'class' => 'btn btn-success btn-xs'
										));
										echo '&nbsp;';
									}

									$parentTitle = '';
									switch ($user['User']['group_id']) {
										case 3:
											$parentTitle = __('Daftar Agen');
											break;
										
										case 4:
											$parentTitle = __('Daftar Principal');
											break;

										case 7:
											$parentTitle = __('Daftar Agen Developer');
											break;
									}

									if( !empty($parentTitle) ) {
										echo $this->Html->link('<i class="fa fa-user"></i> '.$parentTitle, array(
											'controller' => 'users', 
											'action'=> 'index', 
											'parentid' => $user['User']['id'], 
											'admin'=> true
										), array(
											'target'=> '_blank', 
											'escape' => false,
											'class' => 'btn btn-success btn-xs'
										));
										echo '&nbsp;';
									}

									$edit_link = array(
										'action' => 'edit', 
										$user['User']['id'], 
										$user['User']['username'], 
										'admin'=> true
									);

									echo $this->Html->link('<i class="fa fa-pencil"></i> '.__('Edit'), 
										$edit_link, 
										array(
											'escape' => false,
											'class' => 'btn btn-info btn-xs'
										)
									);
									echo '&nbsp;';

									echo $this->Html->link('<i class="fa fa-lock"></i> '.__('Password'), array(
										'action' => 'password', 
										$user['User']['id'], 
										'admin'=> true
									), array(
										'escape' => false,
										'class' => 'btn btn-warning btn-xs'
									));
									echo '&nbsp;';

									if(in_array($logged_group, array(20))){
										echo $this->Html->link('<i class="fa fa-times"></i> '.__('Hapus'), array(
											'action' => 'delete', 
											$user['User']['id'] 
										), array(
											'escape' => false,
											'class' => 'btn btn-danger btn-xs'
										), sprintf(__('Yakin ingin menghapus user %s?'), $user['User']['full_name']));
										echo '&nbsp;';
									}

									if( !$user['User']['status'] ) {
										echo $this->Html->link('<i class="fa fa-envelope"></i> '.__('Send Activation'), array(
											'action' => 'resend', 
											'email' => $user['User']['email'], 
											'admin'=> false
										), array(
											'target' => 'blank',
											'class' => 'btn btn-info btn-xs',
											'escape' => false,
										));
										echo '&nbsp;';
									}
									
									if( $user['User']['status']==1 && $user['User']['active']==1 && $user['User']['deleted']==0) {
										echo $this->Html->link('<i class="fa fa-user"></i> '.('Login Sebagai Saya'), array(
											'action' => 'login_as', 
											$user['User']['id'], 
											'admin'=> true
										), array(
											'target' => 'blank',
											'class' => 'btn btn-primary btn-xs',
											'escape' => false,
										));
										echo '&nbsp;';
									}

									if( !empty($user['Property']) ) {
										echo $this->Html->link('<i class="fa fa-envelope"></i> '.__('Kirim Verifikasi'), array(
											'controller' => 'crontab',
											'action' => 'verify_listing_property', 
											$user['User']['id'], 
											'admin'=> false
										), array(
											'class' => 'btn btn-warning btn-xs',
											'escape' => false,
										), __('Anda yakin ingin mengirimkan verifikasi email listing properti ?'));
									}

									echo $this->Html->link('<i class="fa fa-line-chart"></i> '.__('Histori Point'), array(
										'controller' => 'users',
										'action' => 'points', 
										$user['User']['id'], 
										'admin'=> true
									), array(
										'class' => 'btn btn-success btn-xs',
										'escape' => false,
									));
							?>
						</td>
					</tr>
				<?php 
							}
						} 
					}else{
						echo $this->Html->tag('tr', $this->Html->tag('td', __('Data Tidak Ditemukan.'), array(
							'class' => 'alert alert-danger text-center',
							'colspan' => 7
						)));
					}
				?>
				</tbody>
			</table>
		</div>
    </div>
</div>