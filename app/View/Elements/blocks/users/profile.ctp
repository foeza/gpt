<?php 
		$fileupload = isset($fileupload)?$fileupload:false;
		$security = isset($security)?$security:true;
		$is_rule = isset($is_rule)?$is_rule:true;
		$info_block_membership = isset($info_block_membership)?$info_block_membership:false;
		
		$custom_wrapper = !empty($custom_wrapper)?$custom_wrapper:false;
		$column1  = $this->Rumahku->filterEmptyField($custom_wrapper, 'column1', false, '');
		$column2  = $this->Rumahku->filterEmptyField($custom_wrapper, 'column2', false, '');
		$wrap_row = $this->Rumahku->filterEmptyField($custom_wrapper, 'wrap_row', false, '');

		if( !empty($User['User']) ) {
			$User = array_merge($User, $User['User']);
			unset($User['User']);
		}

		$userPhoto = $this->Rumahku->filterEmptyField($User, 'photo');
		$userFullName = $this->Rumahku->filterEmptyField($User, 'full_name');
		$userEmail = $this->Rumahku->filterEmptyField($User, 'email');
		$group_id = $this->Rumahku->filterEmptyField($User, 'group_id');

		$userSharing = $this->Rumahku->filterEmptyField($User, 'UserConfig', 'sharingtocompany', '-');
		$userRoyalty = $this->Rumahku->filterEmptyField($User, 'UserConfig', 'royalty', '-');

		if( $userSharing != '-' ) {
			$userSharing = $userSharing.'%';
		}
		if( $userRoyalty != '-' ) {
			$userRoyalty = $userRoyalty.'%';
		}

		$userGroup = $this->Rumahku->filterEmptyField($User, 'Group', 'name');
		$userLastLogin = $this->Rumahku->filterEmptyField($User, 'UserConfig', 'last_login');

		$loginDate = $this->Rumahku->formatDate($userLastLogin, 'd M Y', false);

		if( !empty($security) ) {
			$urlSecurity = array(
	        	'controller' => 'users',
	        	'action' => 'security',
	        	'admin' => true,
	    	);

	    	if( Configure::read('User.group_id') == 10 ) {
	    		$urlSecurity = array(
			    	'controller' => 'users',
			    	'action' => 'security',
			    	'client' => true,
			    	'admin' => false,
				);
	    	}
	    }
?>

<div class="<?php echo $wrap_row; ?>">
	
	<div class="user-information <?php echo $column1; ?>">
		<div class="user-photo">
			<div class="user-thumb relative">
				<?php 
						echo $this->Rumahku->photo_thumbnail(array(
			                'save_path' => Configure::read('__Site.profile_photo_folder'), 
			                'src'=> $userPhoto, 
			                'size' => 'pl',
			            ), array(
			            	'title' => $userFullName,
			            	'alt' => $userFullName,
			            ));
		            	
		            	if( !empty($fileupload) ) {
				            echo $this->Html->tag('div', '&nbsp;', array(
				            	'class' => 'change-photo pick-file',
				            ));
				        }
				?>
			</div>
			<?php 
		            if( !empty($fileupload) ) {
			            echo $this->UploadForm->loadUser($this->Html->url(array(
			                'controller' => 'ajax',
			                'action' => 'profile_photo',
			                'admin' => false,
			            )), false, false, array(
			            	'label' => $this->Rumahku->icon('rv4-cam-2'),
			            ));
			        }
			?>
		</div>
		<div class="user-info">
			<?php 
		            echo $this->Html->tag('div', $userFullName, array(
		            	'class' => 'user-name',
		            ));
		            echo $this->Html->tag('div', $userEmail, array(
		            	'class' => 'user-email fs085',
		            ));
		            echo $this->Html->tag('div', sprintf(__('Status Anggota: %s'), $this->Html->tag('span', $userGroup, array(
		            	'class' => 'color-red fbold',
		            ))), array(
		            	'class' => 'user-status fs085',
		            ));

		            if( !empty($loginDate) ) {
			            echo $this->Html->tag('div', sprintf(__('Terakhir Login: %s'), $this->Html->tag('span', $loginDate, array(
			            	'class' => 'color-green fbold',
			            ))), array(
			            	'class' => 'user-last-login fs085',
			            ));
			        }

			        if( in_array($logged_group, array( 2 )) ) {
			        	if( !empty($userSharing) ) {
				            echo $this->Html->tag('div', sprintf(__('Sharing to Company : %s'), $this->Html->tag('span', $userSharing, array(
				            	'class' => 'color-green fbold',
				            ))), array(
				            	'class' => 'user-last-login fs085',
				            ));
				        }

				        if( !empty($userRoyalty) ) {
				            echo $this->Html->tag('div', sprintf(__('Royalty : %s'), $this->Html->tag('span', $userRoyalty, array(
				            	'class' => 'color-green fbold',
				            ))), array(
				            	'class' => 'user-last-login fs085',
				            ));
				        }
			        }

			        if( !empty($urlSecurity) ) {
			            echo $this->Html->tag('div', $this->Html->link($this->Html->tag('span', __('Keamanan'), array(
			            	'class' => 'fbold',
		            	)), $urlSecurity, array(
		            		'escape' => false,
		            	)), array(
			            	'class' => 'user-password fs085',
			            ));
			        }

			  //       echo $this->AclLink->link($this->Html->tag('span', __('Daftarkan Integrasi'), array(
			  //           'class' => 'fbold',
		   //          )), array(
			  //       	'controller' => 'users',
			  //       	'action' => 'register_integration',
			  //       	'admin' => true
					// ), array(
					// 	'class' => 'link-user-integrated user-password fs085',
					// 	'target' => '_blank',
					// 	'escape' => false,
					// ));

			        if( !empty($is_rule) ) {
						$link_rule = $this->AclLink->link($this->Html->tag('span', __('Read Rule'), array(
				            'class' => 'fbold',
			            )), array(
				        	'controller' => 'rules',
				        	'action' => 'read_rules',
				        	'admin' => true
						), array(
							'target' => '_blank',
							'escape' => false,
						));

						echo $this->Html->tag('div',$link_rule, array(
							'class' => 'fs085',
						));
					}
			?>
		</div>
	</div>

	<?php
			// block status premium
			if ($group_id == 2 && $info_block_membership) {

				$info_package = $this->element('blocks/users/membership_package_rku/info_package');
				echo $this->Html->tag('div', $info_package, array(
					'class' => $column2,
				));

			}
	?>

</div>