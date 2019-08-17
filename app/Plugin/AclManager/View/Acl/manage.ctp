<?php

	$rowClass			= empty($rowClass) ? 'acl-controller-row' : $rowClass;
	$aroAlias			= empty($aroAlias) ? 'Group' : $aroAlias;
	$aroDisplayField	= empty($aroDisplayField) ? 'name' : $aroDisplayField;

	$acos		= empty($acos) ? array() : $acos;
	$aros		= empty($aros) ? array() : $aros;
	$aroLabels	= Hash::combine($aros, sprintf('{n}.%s.id', $aroAlias), sprintf('{n}.%s.%s', $aroAlias, $aroDisplayField));

?>
<div class="box">
	<div class="box-header">
		<h3 class="box-title"><?php echo sprintf(__("%s permissions"), $aroAlias); ?></h3>
	</div>
	<div class="box-body">
		<?php

			echo($this->element('blocks/acl/action_button'));

		?>
		<div class="form mt30 mb30">
			<?php

				echo($this->Form->create('Perms'));

				?>
				<div class="box-body table-responsive no-padding">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Action</th>
								<?php

									if($aroLabels){
										foreach($aroLabels as $aroID => $aroLabel){
											echo($this->Html->tag('th', $aroLabel, array(
												'width' => 200, 
												'style' => 'text-align:center;', 
											)));
										}
									}

								?>
							</tr>
						</thead>
						<tbody>
							<?php

							//	$uglyIdent = Configure::read('AclManager.uglyIdent'); 
							//	$lastIdent = null;

								$plugin			= $this->params->plugin;
								$controller		= $this->params->controller;
								$action			= $this->params->action;
								$currentPage	= Hash::get($this->params->named, 'page', 1);
								$colOptions		= array('align' => 'center');
								$inputOptions	= array(
									'inherit'	=> __('Inherit'), 
									'allow'		=> __('Allow'), 
									'deny'		=> __('Deny'), 
								);

								foreach($acos as $aco){
									$acoID		= Hash::get($aco, 'Aco.id');
									$parentID	= Hash::get($aco, 'Aco.parent_id');
									$alias		= Hash::get($aco, 'Aco.alias');
									$action		= Hash::get($aco, 'Action');
									$childCount	= Hash::get($aco, 'Aco.child_count');
									$permission	= str_replace('/', ':', $action);

								//	define input contents
									$icon	= $this->Html->tag('i', false, array('class' => 'acl-icon rv4-bold-plus'));
									$url	= $this->Html->url(array(
										'acoid'	=> $acoID, 
										'page'	=> $currentPage, 
									), true);

									$alias		= empty($childCount) ? $alias : $this->Html->tag('strong', $alias);
									$toggler	= empty($childCount) ? '' : $this->Html->link($icon, $url, array(
										'title'			=> __('Expand Action'),
										'class'			=> 'acl-controller-toggle', 
										'data-aco-id'	=> $acoID, 
										'escape'		=> false, 
									));

									$contents = array($toggler . $alias);

									foreach($aros as $aro){
										$aroID		= Hash::get($aro, sprintf('%s.id', $aroAlias));
										$aroLabel	= Hash::get($aro, sprintf('%s.%s', $aroAlias, $aroDisplayField));
										$fieldName	= sprintf('Perms.%s.%s:%s', $permission, $aroAlias, $aroID);

										$allowed	= $this->Form->value($fieldName); 
										$inherit	= $this->Form->value($fieldName.'-inherit');

										if($allowed){
											$value = 'allow';
										}
										else if($inherit){
											$value = 'inherit';
										}
										else{
											$value = 'deny';
										}

									//	$icon = sprintf('acl-icon rv4-bold-%s', ($allowed ? 'check' : 'cross'));
									//	$icon = $this->Html->tag('i', false, array('class' => $icon));

									//	$input = $icon . $this->Form->select($fieldName, $inputOptions, array(
									//	//	'empty'		=> __('No Change'), 
									//		'empty'		=> false, 
									//		'value'		=> $value, 
									//		'class'		=> 'acl-input ajax-change', 
									//		'data-ref'	=> $acoID.'-'.$aroID, 
									//		'data-form'	=> 'select.acl-input[data-ref="'.$acoID.'-'.$aroID.'"]', 
									//		'data-type'	=> 'json', 
									//	));

										$input = $this->Html->div('form-group no-margin', $this->Form->select($fieldName, $inputOptions, array(
											'empty'		=> false, 
											'value'		=> $value, 
											'class'		=> 'acl-input form-control ajax-change', 
											'data-ref'	=> $acoID.'-'.$aroID, 
											'data-form'	=> 'select.acl-input[data-ref="'.$acoID.'-'.$aroID.'"]', 
											'data-type'	=> 'json', 
										)));

									//	append input
										$contents[] = array($input, array('align' => 'center'));
									}

									$trOptions = array(
										'data-aco-id'	=> $acoID, 
										'class'			=> $rowClass, 
									);

								//	parent_id 1 root controller
									if($parentID > 1){
										$trOptions = Hash::insert($trOptions, 'data-parent-id', $parentID);
									}

									echo($this->Html->tableCells($contents, $trOptions, $trOptions));
								}

							?>
						</tbody>
					</table>
				</div>
				<?php

				echo($this->Form->end());
				echo($this->element('blocks/common/admin/pagination'));

			?>
			<div class="clear"></div>
		</div>
	</div>
</div>