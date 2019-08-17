<?php 
		echo $this->Form->create('CrmProject', array(
    		'class' => 'form-target',
		));

		if( !empty($values) ) {
?>
<ul class="row same-height" data-type="fix-height">
	<?php 
			foreach ($values as $key => $value) {
				$id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
				$property_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'property_id');
				$user_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'user_id');

				$agent_id = $this->Rumahku->filterEmptyField($value, 'Agent', 'id');
				$agent = $this->Rumahku->filterEmptyField($value, 'Agent', 'full_name');

				$closing = $this->Crm->_callProjectClosing($value, false);

				$photoProperty = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');
				$titleProperty = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
				$statusProperty = $this->Property->getStatus($value, 'span');
				$createdProperty = sprintf(__('Dipasarkan oleh: %s'), $this->Html->tag('strong', $agent));
				$priceProperty = $this->Property->getPrice($value);
				$labelProperty = $this->Property->getNameCustom($value);
				$photoProperty = $this->Rumahku->photo_thumbnail(array(
					'save_path' => Configure::read('__Site.property_photo_folder'), 
					'src'=> $photoProperty, 
					'size' => 'm',
				), array(
					'alt' => $titleProperty,
					'title' => $titleProperty,
					'class' => 'default-thumbnail',
				));
	?>
	<li class="col-sm-6 col-md-4 label-layer">
		<div class="project">
			<div class="wrapper-layer">
				<div class="relative">
					<?php 
							echo $photoProperty;
					?>
				</div>
				<div class="relative">
					<div class="project-content">
						<div class="relative">
							<?php 
									echo $this->Html->tag('div', $labelProperty, array(
										'class' => 'label',
									));
									echo $this->Html->tag('div', $titleProperty, array(
										'class' => 'title',
									));
									echo $this->Html->tag('div', $priceProperty, array(
										'class' => 'price',
									));
									echo $this->Html->tag('div', $createdProperty, array(
										'class' => 'created-by',
									));
									echo $this->Html->tag('div', sprintf(__('Status: %s'), $statusProperty), array(
										'class' => 'status',
									));
									$className = sprintf('wrapper-project-list-%s-%s', $property_id, $user_id);
							?>
						</div>
					</div>
					<?php 
							echo $this->Html->tag('div', '', array(
								'id' => $className,
								'class' => 'popover-client'
							));
					?>
				</div>
			</div>
			<?php
					if( !empty($value['CrmProjectRelation']) ) {
						$relations = $value['CrmProjectRelation'];
						$relationCount = $this->Rumahku->filterEmptyField($value, 'CrmProjectRelationCount', false, 0);
			?>
			<div class="project-agent">
				<div class="project-agent-info">
					<ul>
						<?php 
								echo $this->element('blocks/crm/list_clients', array(
									'relations' => $relations,
									'relationCount' => $relationCount,
								));

								if( empty($closing) ) {
									$url = array(
										'controller' => 'crm',
					                    'action' => 'project_add',
					                    $property_id,
					                    'admin' => true,										
									);
									$check = $this->AclLink->aclCheck($url);

									if($check){
										echo $this->Html->tag('li', $this->Html->link($this->Html->tag('div', $this->Html->tag('span', $this->Rumahku->icon('rv4-plus')), array(
											'class' => 'agent-photo crm-add',
										)), $url, array(
						                    'escape' => false,
						                )));
									}
								}
						?>
					</ul>
				</div>
			</div>
			<?php 
					}
			?>
		</div>
	</li>
	<?php 
			}
	?>
</ul>
<?php 
		} else {
			echo $this->Html->tag('p', __('Data belum tersedia'), array(
				'class' => 'alert alert-warning'
			));
		}
		echo $this->Form->end(); 
?>