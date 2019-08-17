<?php 
		$id = $this->Rumahku->filterEmptyField($value, 'PropertyDraft', 'id');
		$modified = $this->Rumahku->filterEmptyField($value, 'PropertyDraft', 'modified');

	//	$customModified = $this->Rumahku->formatDate($modified, 'd M Y H:i:s');
		$customModified = $this->Rumahku->getIndoDateCutom($modified);
		$customModified = sprintf(__('Terakhir diubah: %s'), $this->Html->tag('strong', $customModified));

		echo $this->Html->tag('div', $customModified, array(
			'class' => 'created-date',
		));
?>
<ul class="action-btn">
	<?php 
			$url = array(
				'controller' => 'properties',
				'action' => 'draft_edit',
				$id,
				'admin' => true,
			);
			$allow = $this->AclLink->aclCheck($url);

			if($allow){
				echo $this->Html->tag('li', $this->AclLink->link(__('Edit'), $url, array(
					'class' => 'btn default'
				)));
			}

			$url = array(
				'controller' => 'properties',
				'action' => 'draft_delete',
				$id,
				'admin' => true,
			);
			$allow = $this->AclLink->aclCheck($url);

			if($allow){
				echo $this->Html->tag('li', $this->AclLink->link(__('Hapus'), $url, array(
					'class' => 'btn default'
				), __('Anda yakin ingin menghapus draft properti ini?')));
			}
	?>
</ul>