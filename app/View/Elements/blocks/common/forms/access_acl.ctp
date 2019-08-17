<?php
		$is_super_admin = Configure::read('__Site.is_super_admin');

		$group_name = Common::hashEmptyField($group, 'Group.name');
		$group_id = Common::hashEmptyField($group, 'Group.id');

		$uglyIdent = Configure::read('AclManager.uglyIdent'); 

		$aco_id = Common::hashEmptyField($aco, 'Aco.id');
		$Child = Common::hashEmptyField($aco, 'Child');
		$action = Common::hashEmptyField($aco, 'Action');
		$alias = Common::hashEmptyField($aco, 'Aco.alias');
		$alias = Common::hashEmptyField($aco, 'Aco.label', $alias);

		$slug = $this->Rumahku->toSlug($alias.'-box-list');
		$id_slug = '#'.$slug;

		$link_checkall = $this->Html->link('Allow All', array(
			'controller' => 'groups',
			'action' => 'checkall',
			$group_id,
			$aco_id,
			'allow',
			'admin' => true,
			'plugin' => false
		), array(
			'class' => 'ajax-link color-green',
			'data-wrapper-write' => $id_slug,
			'data-show-loading-bar' => 'true'
		));

		$link_uncheckall = $this->Html->link('Deny All', array(
			'controller' => 'groups',
			'action' => 'checkall',
			$group_id,
			$aco_id,
			'deny',
			'admin' => true,
			'plugin' => false
		), array(
			'class' => 'ajax-link color-red',
			'data-wrapper-write' => $id_slug,
			'data-show-loading-bar' => 'true'
		));

		$list = $this->Html->tag('li', sprintf('%s%s', $link_checkall, $link_uncheckall), array(
			'class' => 'first-action-acl taleft'
		));

	?>
<div class="content-input">
	<div class="project-list">
		<?php
				$label_header = $this->Html->tag('label', h($alias).'<span class="rv4-angle-down pull-right"></span>', array(
					'class' => 'content-label pointer-collapse'
				));

				echo $this->Html->link($label_header, 'javascript:void(0)', array(
					'class' => 'display-toggle disblock',
					'data-target' => $id_slug,
					'escape' => false
				));
		?>
		<div id="<?php echo $slug;?>" class="hide">
			<div class="project-list-item acl-list-item">
				<ul>
					<?php
							if(!empty($childs[$aco_id])){

								$child_data = Set::extract('/Aco/order', $childs[$aco_id]);
								asort($child_data);

								foreach ($child_data as $key => $val) {
									$value = !empty($childs[$aco_id][$key])?$childs[$aco_id][$key]:false;

									foreach ($aros as $aro){
										$action = Common::hashEmptyField($value, 'Action');
										$alias = Common::hashEmptyField($value, 'Aco.alias');
										$id = Common::hashEmptyField($value, 'Aco.id');
										$alias = Common::hashEmptyField($value, 'Aco.label', $alias);

										$inherit = $this->Form->value("Perms." . str_replace("/", ":", $action) . ".{$aroAlias}:{$aro[$aroAlias]['id']}-inherit");
										$allowed = $this->Form->value("Perms." . str_replace("/", ":", $action) . ".{$aroAlias}:{$aro[$aroAlias]['id']}"); 
										$value = $inherit ? 'inherit' : null; 
										$icon = ($allowed) ? 'rv4-check' : 'rv4-cross';
										$class_allow = ($allowed) ? '' : 'deny';

										if($allowed){
											$to = 'deny';
										}else{
											$to = 'allow';
										}

										$icon = $this->Rumahku->icon($icon, false, 'span', 'add floright '.$class_allow);

										$alias = $this->Html->tag('span', $alias, array(
											'class' => 'project-list-name disblock'
										));

										$link = $this->Html->link(sprintf('%s %s', $icon, $alias), array(
											'controller' => 'groups',
											'action' => 'grant_toggle',
											$group_id,
											str_replace("/", "-", $action),
											$to,
											'admin' => true,
											'plugin' => false
										), array(
											'escape' => false,
											'class' => 'ajax-link',
											'data-wrapper-write' => '#'.$slug.'-'.$id,
											'data-show-loading-bar' => 'true'
										));

										$list .= $this->Html->tag('li', $link, array(
											'class' => 'taleft',
											'id' => $slug.'-'.$id
										));
									}
								}

								echo $list;
							}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>