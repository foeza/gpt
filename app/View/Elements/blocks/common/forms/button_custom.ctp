<?php 
		if( !empty($buttonDelete) ) {
			$class = isset( $buttonDelete['class'] ) ? $buttonDelete['class'] : 'btn red';
			$column_class = isset( $buttonDelete['column_class'] ) ? $buttonDelete['column_class'] : 'col-sm-2';
			echo $this->Rumahku->buildButton($buttonDelete, $column_class.' button-type button-style-1', $class.' hide');

			if( !empty($overflowDelete) ) {
?>
<div class="delete-overflow clear">
	<div class="counter floleft">
		<?php 
				echo $this->Html->tag('span', 0);
				echo __(' Data dihapus');
		?>
	</div>
	<div class="action-delete floright">
		<?php 
				$buttonDelete['text'] = $this->Rumahku->icon('rv4-cross').__('Hapus');
				echo $this->Rumahku->buildButton($buttonDelete);
		?>
	</div>
</div>
<?php
			}
		}
?>