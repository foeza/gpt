<?php 
		if( !empty($attributeSets) ) {
			$status = $this->Rumahku->filterEmptyField($this->params, 'named', 'status');
			$addClass = '';

			if( empty($status) ) {
				$addClass = 'active';
			}
?>
<ul>
	<?php 
			echo $this->Html->tag('li', $this->Html->link(__('Semua'), array(
				'status' => false,
				'admin' => true,
			), array(
				'class' => $addClass,
			)));

			foreach ($attributeSets as $id => $value) {
				$slug = $this->Rumahku->toSlug($value);
				$addClass = '';

				if( !empty($status) && $status == $slug ) {
					$addClass = 'active';
				}

				echo $this->Html->tag('li', $this->Html->link($value, array(
					'status' => $slug,
					'admin' => true,
				), array(
					'class' => $addClass,
				)));
			}
	?>
</ul>
<?php 
		}
?>