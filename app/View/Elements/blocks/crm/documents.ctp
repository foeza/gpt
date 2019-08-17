<?php 
		if( !empty($values) ) {
			$crm_project_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
			$contentLi = '';

			foreach ($values as $key => $value) {
				$id = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'id');
				$title = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'name');
				$session_id = $this->Rumahku->filterEmptyField($value, 'CrmProjectDocument', 'session_id');

				// $action = $this->Html->link($this->Rumahku->icon('rv4-bold-cross'), array(
				// 	'controller' => 'ajax',
				// 	'action' => 'document_delete',
				// 	$id,
				// 	$session_id,
				// 	'admin' => true,
				// ), array(
				// 	'escape' => false,
				// 	'class' => 'document-delete ajax-link',
				// 	'data-alert' => __('Anda yakin ingin menghapus dokumen ini?'),
				// 	'data-remove' => '.ajax-parent[rel=\''.$id.'\']',
				// ));

				$contentLi .= $this->Html->tag('li', $this->Html->link($title, array(
					'controller' => 'settings',
					'action' => 'download',
					'crm_document',
					$id,
					'admin' => true,
				)), array(
					'class' => 'ajax-parent',
					'rel' => $id,
				)).$this->Rumahku->clearfix();
			}

			$result = $this->Html->tag('ul', $contentLi, array(
				'class' => 'documents clear',
			));
			$result .= $this->Html->link(__('Lihat Detil'), array(
				'controller' => 'crm',
				'action' => 'project_document',
				$crm_project_id,
				'admin' => true,
			), array(
				'class' => 'more-info',
			));

			echo $this->Html->tag('div', $result, array(
				'class' => 'crm-documents mb15',
			));
		}
?>