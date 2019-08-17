<?php
		if (!empty($spec)) {
?>
			<div class="specs-sidebar">
				<?php

					echo $this->Html->tag('h2', __('Spesifikasi'), array(
						'class' => 'section-title',
					));
					echo $this->Html->div('property-list', $spec);

				?>
			</div>
<?php 
		}

		if (!empty($listUnitMaterial)) {
?>
			<div class="specs-sidebar">
				<?php

					echo $this->Html->tag('h2', __('Unit Material'), array(
						'class' => 'section-title',
					));
					echo $this->Html->div('property-list', $listUnitMaterial);

				?>
			</div>
<?php 
		}
?>