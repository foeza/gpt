<?php
		$title = !empty($title)?$title:false;
		$wrapperClass = !empty($wrapperClass)?$wrapperClass:false;
		$values = !empty($values)?$values:false;
		$daterangeClass = !empty($daterangeClass)?$daterangeClass:false;
		$is_calender = isset($is_calender)?$is_calender:true;
		$labelClass = !empty($is_calender)?'col-sm-8 col-xs-10':'col-sm-12';
		$action_type = !empty($action_type)?$action_type:'active';
		$link_name = isset($link_name)?$link_name:true;
		$currency = !empty($currency)?$currency:false;
		$colClass = !empty($colClass)?$colClass:'col-sm-6';
		$label = !empty($label)?$label:__('Nama');
		$optionLink = !empty($optionLink)?$optionLink:array();
		$url = !empty($url)?$url:false;
		$urlAjax = !empty($urlAjax)?$urlAjax:array(
            'controller' => 'ajax',
            'action' => 'get_dashboard_table',
            $action_type,
            'admin' => true,
        );
		
		$fromDate = !empty($fromDate)?$fromDate:false;
		$toDate = !empty($toDate)?$toDate:false;

		$urlTitle = !empty($urlTitle)?$urlTitle:false;

		$labelName = !empty($labelName)?$labelName:__('Tayang');
		$modelName = !empty($modelName)?$modelName:'Property';
		$fieldName = !empty($fieldName)?$fieldName:'total_listing';

		$thClass = !empty($thClass) ? $thClass : 'tacenter';
?>

<div id="<?php echo $wrapperClass; ?>">
	<div class="<?php echo $colClass; ?>">
		<div class="dashbox mt30">
			<div class="applications-list user-list">
				<div class="table-head">
					<div class="row">
						<div class="<?php echo $labelClass; ?>">
							<?php
									echo $this->Html->tag('h4', $title);
							?>
						</div>
						<?php
							if(!empty($is_calender)){
						?>
						<div class="col-sm-4 col-xs-2">
							<div class="form-group taright">
								<?php 	
										if( !empty($action_type) ) {
											echo $this->Html->link($this->Rumahku->icon('rv4-calendar'), 'javascript:void(0);', array(
			                                	'escape' => false,
				                                'class' => 'daterange-dasboard-table '.$daterangeClass,
				                                'title' => __('Tanggal'),
				                                'url' => $this->Html->url($urlAjax),
				                            ));
										}
								?>
							</div>
						</div>
						<?php
							}
						?>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table">
						<?php 
								$dataColumns = array(
						            'name' => array(
						                'name' => $label,
						                'style' => 'width: 45%;',
						            ),
						            'total_properti' => array(
						                'name' => __('Total ').$labelName,
						                'style' => 'width: 30%;',
						                'class' => $thClass,
						            ),
						        );
						        $fieldColumn = $this->Rumahku->_generateShowHideColumn( $dataColumns, 'field-table' );

				                if( !empty($fieldColumn) ) {
				                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
				                }
				        ?>
						<tbody>
							<?php 	
								if( !empty($values) ) {
									$group_id = $this->Rumahku->filterEmptyField($User, 'group_id');

	      							foreach( $values as $key => $value ) {
	      								$total = $this->Rumahku->filterEmptyField($value, $modelName, $fieldName);
	      								$total = !empty($currency)?$this->Rumahku->getCurrencyPrice($total):$total;
	      								$id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
	      								$customName = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');

	      								if($modelName <> 'Kpr'){
		      								switch ($group_id) {
		      									case '2':
			      									$customName = $this->Rumahku->_callUserFullName($value, $link_name, array(
				      									'escape' => false,
				      									'target' => '_blank',
				      								), $optionLink);
		      										break;
		      									
		      									default:
				      								switch ($modelName) {
				      									case 'UserCompanyEbrochure':
						      								$total = $this->Html->link($total, array(
						      									'controller' => 'ebrosurs',
						      									'action' => 'info',
						      									$id,
																'date_from' => $fromDate,
																'date_to' => $toDate,
						      									'admin' => true,
					      									), array(
						      									'escape' => false,
						      									'target' => '_blank',
						      								));
				      										break;
				      									
				      									default:
						      								$total = $this->Html->link($total, array(
						      									'controller' => 'properties',
						      									'action' => 'info',
						      									$id,
						      									'status' => 'active-pending',
																'date_from' => $fromDate,
																'date_to' => $toDate,
						      									'admin' => true,
					      									), array(
						      									'escape' => false,
						      									'target' => '_blank',
						      								));
				      										break;
				      								}
		      										break;
		      								}
	      								}else{
	      									$customName = $this->Rumahku->_callUserFullName($value, $link_name, array(
		      									'escape' => false,
		      									'target' => '_blank',
		      								), $optionLink);
	      								}

	      								echo $this->Html->tableCells(array(
								            $customName,
								            array(
								         		$total,
									            array(
									            	'class' => $thClass,
								            	),
									        ),
								        ));
				                	}
				                } else {
							        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data Tidak ditemukan.'), array(
							            'class' => 'alert-danger',
							            'colspan' => 2,
							        )) );
							    }
							?>
						</tbody>
					</table>
				</div>
				<?php 	
						if( !empty($url) ) {
							echo $this->Html->link(sprintf(__('%s &raquo;'), $urlTitle), $url, array(
								'escape' => false,
								'class' => 'see-all',
							));
						}
				?>
			</div>
		</div>
	</div>
</div>