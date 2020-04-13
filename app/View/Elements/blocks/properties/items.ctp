<?php
		$_config = empty($_config) ? array() : $_config;
		$value 	 = empty($value) ? array() : (array)$value;

		if($value){
			$f_display 	 = isset($f_display)?$f_display:true;
			$_action 	 = isset($_action)?$_action:true;
			$_target 	 = !empty($_target)?$_target:false;
			$_draft 	 = !empty($_draft)?$_draft:false;
			$_report 	 = !empty($_report)?$_report:false;
			$_sold_stat  = !empty($_sold_stat)?$_sold_stat:false;
			$_item_sold  = isset($_item_sold)?$_item_sold:false;

			$isAdmin 	 = Configure::read('User.admin');
			$read_uID    = Configure::read('User.id');
			$read_gID    = Configure::read('User.group_id');

			$user_name 	 = Common::hashEmptyField($value, 'User.full_name');

			$domain 	 = Common::hashEmptyField($value, 'UserCompanyConfig.domain');

			$id 		 = Common::hashEmptyField($value, 'Property.id');
			$sold 		 = Common::hashEmptyField($value, 'Property.sold');
			$title 		 = Common::hashEmptyField($value, 'Property.title');
			$photo 		 = Common::hashEmptyField($value, 'Property.photo');
			$mls_id 	 = Common::hashEmptyField($value, 'Property.mls_id');
			$created 	 = Common::hashEmptyField($value, 'Property.created');
			$in_update 	 = Common::hashEmptyField($value, 'Property.in_update');
			$change_date = Common::hashEmptyField($value, 'Property.change_date');

			$prop_act 	 = Common::hashEmptyField($value, 'PropertyAction.name');

			$prop_cnt 	 = Common::hashEmptyField($value, 'PropertyMedias.cnt');

			$prop_cnt 	 = Common::hashEmptyField($value, 'PropertyView', '-');

			$status 	 = $this->Property->getStatus($value, 'span', $_action);
			$price 		 = $this->Property->getPrice($value, __('(Harga belum ditentukan)'));
			$label 		 = $this->Property->getNameCustom($value);
			$cat_badge 	 = $this->Property->getPropertyCatBadge($value);
			$dataExtra 	 = $this->Property->_callGetCustom($value, false, false, false);
			$specs 		 = $this->Property->getSpec($value, $dataExtra);

			$cst_created = $this->Rumahku->formatDate($created, 'd/m/Y H:i:s');
			$cst_c_date  = $this->Rumahku->formatDate($change_date, 'd/m/Y H:i:s');

			$createdBy 		  = sprintf(__('Dipasarkan oleh: %s'), $this->Html->tag('strong', $user_name));
			$customCreated 	  = sprintf(__('Tgl dibuat: %s'), $this->Html->tag('strong', $cst_created));
			$customChangeDate = sprintf(__('Terakhir update: %s'), $this->Html->tag('strong', $cst_c_date));

			// $exDate = date ("d/m/Y", strtotime("+".Configure::read('__Site.config_expired_listing_in_year')." Year", strtotime($change_date)));
			// $customExDate = sprintf(__('Berakhir pada: %s'), $this->Html->tag('strong', $exDate));

			if($domain) {
				$slug	= $this->Rumahku->toSlug($label);
				$url	= array(
					'admin'	=> false,
					'mlsid'	=> $mls_id,
					'slug'	=> $slug, 
				);

				if($read_gID == 1) {
					$url = array_merge($url, array('controller' => 'profiles', 'action' => 'property_detail'));
				} else {
					$url = array_merge($url, array('controller' => 'properties', 'action' => 'detail'));
				}
				$url = $domain . $this->Html->url($url);

			} else {
				$url = 'javascript:void(0);';

			}

			$photoProperty = $this->Rumahku->photo_thumbnail(array(
				'save_path' => Configure::read('__Site.property_photo_folder'), 
				'size' 		=> 'm',
				'src'		=> $photo, 
			), array(
				'alt' 	=> $title,
				'title' => $title,
				'class' => 'default-thumbnail',
			));

			// $_commission = !empty($_commission)?$_commission:false;
			// $msgRejected = $this->Property->getNotifRejected($value, 'error alert');
			// if( !empty($_commission) ) {
			// 	$sharing_to_company = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'sharingtocompany_percentage', 0);
			// 	$royalty = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'royalty_percentage', 0);
			// 	$pph = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'pph_percentage', 0);
			// 	$agent_commission_net = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'agent_commission_net', 0);
			// }

?>
			<div class="item row">
				<div class="col-sm-4">
					<div class="relative">
						<?php 
								echo $this->Html->link($photoProperty, $url, array(
									'escape' => false,
									'target' => $domain ? '_blank' : '',
								));
								echo $cat_badge;

								if( !empty($photo_cnt) ) {
									echo $this->Html->tag('div', $this->Rumahku->icon('rv4-image-2').$photo_cnt, array(
										'class' => 'property-media',
									));
								}
						?>
					</div>
				</div>
				<div class="col-sm-8 no-pleft">
					<div class="row">
						<div class="col-sm-6">
							<?php 
									echo $this->Html->tag('div', $prop_act, array(
										'class' => 'label',
									));
									echo $this->Html->tag('div', $this->Html->link($title, $url, array(
										'escape' => false,
										'target' => $domain ? '_blank' : '',
									)), array(
										'class' => 'title',
									));

									echo $this->Html->tag('div', $price, array(
										'class' => 'price',
									));

									if( empty($_draft) && !empty($mls_id) ) {
										$customMlsId = sprintf(__('ID Properti: %s'), $this->Html->tag('strong', $mls_id));
										echo $this->Html->tag('div', $customMlsId);
									}

									echo $this->Html->tag('div', $specs, array(
										'class' => 'specs',
									));

									// if( !empty($_commission) ) {
									// 	$customSharing = sprintf(__('Sharing to Company: %s%%'), $this->Html->tag('strong', $sharing_to_company));
									// 	$customRoyalty = sprintf(__('Royalty: %s%%'), $this->Html->tag('strong', $royalty));
									// 	$customPph = sprintf(__('PPH: %s%%'), $this->Html->tag('strong', $pph));

									// 	$customAgentCommissionNet = $this->Rumahku->getFormatPrice($agent_commission_net);
									// 	$customAgentCommissionNet = sprintf(__('Total Komisi: %s'), $this->Html->tag('strong', __('Rp. ').$customAgentCommissionNet));

									// 	echo $this->Html->tag('div', $customSharing);
									// 	echo $this->Html->tag('div', $customRoyalty);
									// 	echo $this->Html->tag('div', $customPph);

									// 	if( !empty($sold) ) {
									// 		echo $this->Html->tag('div', $customAgentCommissionNet);
									// 	}
									// }
							?>
						</div>
						<div class="col-sm-6">
							<?php
									if( !empty($_item_sold) && !empty($propertySold) ) {
										echo $this->element('blocks/properties/item_sold', array(
											'propertySold' => $propertySold,
										));
									} else if( empty($_draft) ) {
										echo $this->Html->tag('div', $customCreated, array(
											'class' => 'created-date mt30',
										));
										echo $this->Html->tag('div', $customChangeDate, array(
											'class' => 'created-date',
										));
										// echo $this->Html->tag('div', $customExDate, array(
										// 	'class' => 'created-date',
										// ));
										// echo $this->Html->tag('div',
										// 	sprintf(__('Jumlah pengunjung: %s'), $this->Html->tag('strong', $prop_cnt))
										// );
										echo $this->Html->tag('div', $createdBy, array(
											'class' => 'created-by',
										));

										echo $this->Html->tag('div', sprintf(__('Status Produk: %s'), $status));

										if( !empty($_sold_stat) && !empty($sold) ) {
											$action_name = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');
											$sold_date = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'sold_date');
											$end_date = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'end_date');
											$customSoldDate = $this->Rumahku->getCombineDate($sold_date, $end_date, false, false);

											echo $this->Html->tag('div', sprintf(__('Tgl %s : %s'), $action_name, $this->Html->tag('strong', $customSoldDate)));
										}
									}

							?>
						</div>
					</div>
				</div>
				<?php
						// condition disp btn
						if( !empty($f_display) && !empty($_action) ) {

				?>
				<div class="col-sm-12">
					<ul class="action-btn list-btn hidden-print taright">
						<?php
								echo $this->Property->previewButton($value);
								
								// echo $this->Property->soldButton($value);
								echo $this->Property->ActivateButton($value);
								echo $this->Property->deActivateButton($value);

								$check = $this->AclLink->aclCheck(array(
									'admin' 	 => true,
									'controller' => 'properties',
									'action' 	 => 'edit',
									$id,
								));

								if( !$sold && $check && $this->Rumahku->_callAllowAccess('is_edit_property') ){
									echo $this->Html->tag('li', $this->AclLink->link(__('Edit'), array(
										'admin' 	 => true,
										'controller' => 'properties',
										'action' 	 => 'edit',
										$id,
									), array(
										'class'  => 'btn default',
										'target' => $_target,
									)));
								}

								$urlDelete = array(
									'admin' 	 => true,
									'controller' => 'properties',
									'action' 	 => 'delete',
									$id,
								);
								$checkDelete = $this->AclLink->aclCheck($urlDelete);
								
								if( $this->Rumahku->_callAllowAccess('is_delete_property') && $checkDelete ) {
									echo $this->Html->tag('li', $this->AclLink->link(__('Hapus'), $urlDelete, array(
										'class' => 'btn default'
									), __('Anda yakin ingin menghapus produk ini?')));
								}
						?>
					</ul>
				</div>
				<?php 
						} else if( !empty($_draft) ) {
							echo $this->Html->tag('div', $this->element('blocks/properties/draft_action', array(
								'value' => $value,
							)), array(
								'class' => 'col-sm-12',
							));

						} else if( !empty($_report) ) {

				?>
							<div class="col-sm-12">
								<ul class="action-btn list-btn hidden-print">
									<?php 
											echo $this->Property->reportButton($value);
									?>
								</ul>
							</div>
				<?php 
						}
				?>
			</div>
			<?php

		}
?>