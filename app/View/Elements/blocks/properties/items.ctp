<?php

		$value = empty($value) ? array() : (array) $value;

		if($value){
			$isAdmin		= Configure::read('User.admin');
			$_config		= empty($_config) ? array() : $_config;
			$is_easy_mode	= Common::hashEmptyField($_config, 'UserCompanyConfig.is_easy_mode');
			$isBuilder		= Common::hashEmptyField($_config, 'UserCompanyConfig.is_ebrochure_builder');

			$premium_prop   = Common::hashEmptyField($value, 'User.PremiumProperty');
			$packages = !empty($packages)?$packages:false;
			// $premium_property_mine = !empty($premium_property_mine)?$premium_property_mine:false;

			$opsi_packages = array(
				'packages' => $packages,
				'premium_property_mine' => $premium_prop,
			);

			$_action = isset($_action)?$_action:true;
			$_target = !empty($_target)?$_target:false;
			$_item_sold = isset($_item_sold)?$_item_sold:false;
			$fullDisplay = isset($fullDisplay)?$fullDisplay:true;
			$_draft = !empty($_draft)?$_draft:false;
			$_report = !empty($_report)?$_report:false;
			$_soldStatus = !empty($_soldStatus)?$_soldStatus:false;
			$_commission = !empty($_commission)?$_commission:false;
			$address_display = $this->Rumahku->_callLblConfigValue('is_display_address');

			$agent = $this->Rumahku->filterEmptyField($value, 'Agent');
			$user = $this->Rumahku->filterEmptyField($value, 'User', false, $agent);

			$id = $this->Rumahku->filterEmptyField($value, 'Property', 'id');
			$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
			$photo = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');
			$title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');

			$price = $this->Property->getPrice($value, __('(Harga belum ditentukan)'));
			$change_date = $this->Rumahku->filterEmptyField($value, 'Property', 'change_date');
			$created = $this->Rumahku->filterEmptyField($value, 'Property', 'created');
			$sold = $this->Rumahku->filterEmptyField($value, 'Property', 'sold');
			$in_update = $this->Rumahku->filterEmptyField($value, 'Property', 'in_update');

			$user_name = $this->Rumahku->filterEmptyField($user, 'full_name');
			$customChangeDate = $this->Rumahku->formatDate($change_date, 'd/m/Y H:i:s');
			$customCreated = $this->Rumahku->formatDate($created, 'd/m/Y H:i:s');
			$status = $this->Property->getStatus($value, 'span', $_action);
			$msgRejected = $this->Property->getNotifRejected($value, 'error alert');

			$client_owner_name = $this->Rumahku->filterEmptyField($value, 'ClientOwner', 'full_name');
			$client_buyer_name = $this->Rumahku->filterEmptyField($value, 'ClientBuyer', 'full_name');

			$approved = $this->Rumahku->filterEmptyField($value, 'Approved', 'full_name');

			$property_created = Common::hashEmptyField($value, 'UserActivedAgentDetail.User.full_name');

			$createdBy = sprintf(__('Dipasarkan oleh: %s'), $this->Html->tag('strong', $user_name));
			$createdOriginalBy = sprintf(__('Pembuat iklan: %s'), $this->Html->tag('strong', $property_created));
			$customCreated = sprintf(__('Tgl dibuat: %s'), $this->Html->tag('strong', $customCreated));
			$customChangeDate = sprintf(__('Terakhir update: %s'), $this->Html->tag('strong', $customChangeDate));

			$exDate = date ("d/m/Y", strtotime("+".Configure::read('__Site.config_expired_listing_in_year')." Year", strtotime($change_date)));
			$customExDate = sprintf(__('Berakhir pada: %s'), $this->Html->tag('strong', $exDate));

			$dataExtra = $this->Property->_callGetCustom($value, false, false, false);

			if( !empty($_commission) ) {
				$sharing_to_company = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'sharingtocompany_percentage', 0);
				$royalty = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'royalty_percentage', 0);
				$pph = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'pph_percentage', 0);
				$agent_commission_net = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'agent_commission_net', 0);
			}

			$specs = $this->Property->getSpec($value, $dataExtra);

			$photoCnt = $this->Rumahku->filterEmptyField($value, 'PropertyMedias', 'cnt');

			$property_count = $this->Rumahku->filterEmptyField($value, 'PropertyView', false, '-');
			
			$property_status_listing = $this->Property->getPropertyStatusListing($value);
			$label = $this->Property->getNameCustom($value);

			$authUserID		= Configure::read('User.id');
			$authGroupID	= Configure::read('User.group_id');
			$personalWebURL	= Configure::read('User.data.UserConfig.personal_web_url');
			$isIndependent	= Common::validateRole('independent_agent', $authGroupID);

			$domain = Common::hashEmptyField($value, 'UserCompanyConfig.domain');
			$domain = $domain ?: $personalWebURL;

			if($domain){
				$slug	= $this->Rumahku->toSlug($label);
				$url	= array(
					'admin'	=> false,
					'mlsid'	=> $mls_id,
					'slug'	=> $slug, 
				);

				if($authGroupID == 1){
					$url = array_merge($url, array('controller' => 'profiles', 'action' => 'property_detail'));
				}
				else{
					$url = array_merge($url, array('controller' => 'properties', 'action' => 'detail'));
				}

				$url = $domain . $this->Html->url($url);
			}
			else{
				$url = 'javascript:void(0);';
			}

			$photoProperty = $this->Rumahku->photo_thumbnail(array(
				'save_path' => Configure::read('__Site.property_photo_folder'), 
				'src'=> $photo, 
				'size' => 'm',
			), array(
				'alt' => $title,
				'title' => $title,
				'class' => 'default-thumbnail',
			));

			if( !empty($address_display) ) {
				$dataAddress = $this->Rumahku->filterEmptyField($value, 'PropertyAddress');
				$title = $this->Property->getAddress($dataAddress, ',', 'address');
			}

			$propertyUserID	= Common::hashEmptyField($value, 'Property.user_id');
			$isOpenListing	= $this->Rumahku->_callAllowAccess('is_open_listing');
			$isBrochure 	= $this->Rumahku->_callAllowAccess('is_brochure');

			?>
			<div class="item row">
				<div class="col-sm-4">
					<div class="relative">
						<?php 
								echo $this->Html->link($photoProperty, $url, array(
									'escape' => false,
									'target' => $domain ? '_blank' : '',
								));
								echo $property_status_listing;

								if( !empty($photoCnt) ) {
									echo $this->Html->tag('div', $this->Rumahku->icon('rv4-image-2').$photoCnt, array(
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
									echo $this->Html->tag('div', $this->Html->link($label, $url, array(
										'escape' => false,
										'target' => $domain ? '_blank' : '',
									)), array(
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

									if( !empty($_commission) ) {
										$customSharing = sprintf(__('Sharing to Company: %s%%'), $this->Html->tag('strong', $sharing_to_company));
										$customRoyalty = sprintf(__('Royalty: %s%%'), $this->Html->tag('strong', $royalty));
										$customPph = sprintf(__('PPH: %s%%'), $this->Html->tag('strong', $pph));

										$customAgentCommissionNet = $this->Rumahku->getFormatPrice($agent_commission_net);
										$customAgentCommissionNet = sprintf(__('Total Komisi: %s'), $this->Html->tag('strong', __('Rp. ').$customAgentCommissionNet));

										echo $this->Html->tag('div', $customSharing);
										echo $this->Html->tag('div', $customRoyalty);
										echo $this->Html->tag('div', $customPph);

										if( !empty($sold) ) {
											echo $this->Html->tag('div', $customAgentCommissionNet);
										}
									}
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
										echo $this->Html->tag('div', $customExDate, array(
											'class' => 'created-date',
										));
										// echo $this->Html->tag('div',
										// 	sprintf(__('Jumlah pengunjung: %s'), $this->Html->tag('strong', $property_count))
										// );
										echo $this->Html->tag('div', $createdBy, array(
											'class' => 'created-by',
										));

										if($property_created){
											echo $this->Html->tag('div', $createdOriginalBy, array(
											'class' => 'created-by',
										));
										}

										if( !empty($approved) ) {
											$approvedBy = sprintf(__('Disetujui oleh: %s'), $this->Html->tag('strong', $approved));
											echo $this->Html->tag('div', $approvedBy, array(
												'class' => 'approved-by',
											));
										}

										echo $this->Html->tag('div', sprintf(__('Status Listing: %s'), $status));

										if( !empty($_soldStatus) && !empty($sold) ) {
											$action_name = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'inactive_name');
											$sold_date = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'sold_date');
											$end_date = $this->Rumahku->filterEmptyField($value, 'PropertySold', 'end_date');
											$customSoldDate = $this->Rumahku->getCombineDate($sold_date, $end_date, false, false);

											echo $this->Html->tag('div', sprintf(__('Tgl %s : %s'), $action_name, $this->Html->tag('strong', $customSoldDate)));
										}
									}

									if( $propertyUserID === $authUserID || !empty($isAdmin) ) {
										if( !empty($client_owner_name) ) {
											$customClientOwnerName = sprintf(__('Vendor: %s'), $this->Html->tag('strong', $client_owner_name));
											echo $this->Html->tag('div', $customClientOwnerName);
										}

										if( !empty($client_buyer_name) ) {
											$customClientBuyerName = sprintf(__('Klien: %s'), $this->Html->tag('strong', $client_buyer_name));
											echo $this->Html->tag('div', $customClientBuyerName);
										}
									}
							?>
						</div>
					</div>
				</div>
				<?php
							if( !empty($msgRejected) ) {
								echo $this->Html->tag('div', $msgRejected, array(
									'class' => 'col-sm-12',
								));
							}

							if( !empty($fullDisplay) && !empty($_action) ) {
				?>
				<div class="col-sm-12">
					<ul class="action-btn list-btn hidden-print taright">
						<?php 
							// Check OpenListing & Khusus sales manager hanya boleh edit properti yg dibawahnya aja
							$childList = !empty($childList)?$childList:array();
							$is_sales = Common::hashEmptyField($childList, 'is_sales');

							if( !empty($is_sales) ) {
								$user_ids = Common::hashEmptyField($childList, 'user_ids', array());

								if( !empty($user_ids) ) {
									if( is_array($user_ids) ) {
										$childListTmp = $user_ids;
										$childListTmp[] = $authUserID;
									} else {
										$childListTmp = array(
											$user_ids,
											$authUserID,
										);
									}
								} else {
									$childListTmp = array(
										$authUserID,
									);
								}
							} else {
								$childList = array();
								$childListTmp = array(
									$authUserID,
								);
							}
							// END

							if((empty($isAdmin) || !empty($childList) ) && $isOpenListing && !in_array($propertyUserID, $childListTmp)){
								$isPreview			= $this->Property->checkPratinjau($value);
								$isRestrictApproval	= $this->Rumahku->_callAllowAccess('is_restrict_approval_property');

								$cobrokeNote		= Common::hashEmptyField($value, 'Property.cobroke_note');

								$propertyStatus		= Common::hashEmptyField($value, 'Property.status', 0);
								$propertySold		= Common::hashEmptyField($value, 'Property.sold', 0);
								$propertyPublished	= Common::hashEmptyField($value, 'Property.published', 0);
								$propertyDeleted	= Common::hashEmptyField($value, 'Property.deleted', 0);
								$isInactive			= $propertySold || (empty($propertyStatus) && empty($propertyDeleted) && $propertyPublished);

								if($isBrochure && empty($isInactive) && (empty($isPreview) || $isPreview && empty($isRestrictApproval))){
									$ebrochures		= Common::hashEmptyField($value, 'UserCompanyEbrochure', array());
									$myEbrochures	= Hash::extract($ebrochures, sprintf('{n}.UserCompanyEbrochure[user_id=%s]', $authUserID));
									$myEbrochures	= array_shift($myEbrochures);
									$btnUrl			= array(
										'admin'			=> true, 
										'controller'	=> 'ebrosurs', 
									);

									if($myEbrochures){
										$ebrosurID	= Common::hashEmptyField($myEbrochures, 'id');
										$btnLabel	= __('Bagikan Ebrosur');
										$btnUrl		= array_merge($btnUrl, array('action' => 'detail', $ebrosurID));
									}
									else{
										$btnLabel	= __('Buat Ebrosur');
										$btnUrl		= array_merge($btnUrl, array(
											'action'		=> $isBuilder ? 'builder' : 'add', 
											'property_id'	=> $id, 
										));
									}

									if (!empty($cobrokeNote)) {
										$lblCobrokeNote = __('Co-Broke Note');
										$urlNote = array(
											'controller' => 'co_brokes',
											'action' => 'cobroke_note',
											$id,
											'backprocess' => true
										);

										echo($this->Html->tag('li', $this->Html->link($lblCobrokeNote, $urlNote, array(
											'title'		=> $lblCobrokeNote,
											'class'		=> 'btn btn-cobroke-note default ajaxModal',
											'target'	=> '_blank', 
										))));

									}

									if($myEbrochures){
										$check = true;
									}
									else{
										$check = $this->AclLink->aclCheck($btnUrl);
									}

									if($check){
										echo($this->Html->tag('li', $this->Html->link($btnLabel, $btnUrl, array(
											'title'		=> $btnLabel,
											'class'		=> 'btn default',
											'target'	=> '_blank', 
										))));
									}
								}
							}
							else{
								echo $this->Property->previewButton($value);

								$btn_premium   = array('btn_action' => false);
								$btn_unpremium = array('btn_action' => true);
								echo $this->Property->premiumButton($value, array_merge_recursive($opsi_packages, $btn_premium));
								echo $this->Property->unPremiumButton($value, array_merge_recursive($opsi_packages, $btn_unpremium));

								echo $this->Property->statusListingButton($value);
								echo $this->Property->coBrokeButton($value);

								if(!empty($_config['UserCompanyConfig']['is_refresh_listing']) || $isIndependent){
									echo $this->Property->refreshButton($value);	
								}
								
								echo $this->Property->soldButton($value);
								echo $this->Property->ActivateButton($value);
								echo $this->Property->deActivateButton($value);
								echo $this->Property->kprButton($value);

								$check = $this->AclLink->aclCheck(array(
									'admin' => true,
									'controller' => 'properties',
									'action' => $is_easy_mode ? 'easy_preview' : 'edit',
									$id,
								));

								if( !$sold && $check && $this->Rumahku->_callAllowAccess('is_edit_property') ){
									echo $this->Html->tag('li', $this->AclLink->link(__('Edit'), array(
										'admin' => true,
										'controller' => 'properties',
										'action' => $is_easy_mode ? 'easy_preview' : 'edit',
										$id,
									), array(
										'class' => 'btn default',
										'target' => $_target,
									)));
								}

								$urlDelete = array(
									'controller' => 'properties',
									'action' => 'delete',
									$id,
									'admin' => true,
								);
								$checkDelete = $this->AclLink->aclCheck($urlDelete);
								
								if( $this->Rumahku->_callAllowAccess('is_delete_property') && $checkDelete ) {
									echo $this->Html->tag('li', $this->AclLink->link(__('Hapus'), $urlDelete, array(
										'class' => 'btn default'
									), __('Anda yakin ingin menghapus properti ini?')));
								}
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