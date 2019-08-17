<div class="my-properties">
	<div class="row">
		<div class="col-sm-3">
			<?php
				echo $this->element('sidebars/left_sidebar_menu');
			?>
		</div>
		<div class="col-sm-9">
			<?php 
					if( !empty($properties) ) {
						foreach ($properties as $key => $value) {
							$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
							$photo = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');
							$title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
							$change_date = $this->Rumahku->filterEmptyField($value, 'Property', 'change_date');

							$status = $this->Html->tag('span', $this->Property->getStatus($value), array(
								'class' => 'label label-default',
							));
							$label = $this->Property->getNameCustom($value);
							$price = $this->Property->getPrice($value);
							$specs = $this->Property->getSpec($value);

							$photoProperty = $this->Rumahku->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.property_photo_folder'), 
								'src'=> $photo, 
								'size' => 'm',
							), array(
								'alt' => $title,
								'title' => $title,
								'class' => 'img-thumbnail',
							));
			?>
			<div class="list-property row">
				<div class="col-sm-4">
					<?php 
							echo $photoProperty;
					?>
				</div>
				<div class="col-sm-8">
					<?php 
							echo $this->Html->tag('div', sprintf(__('Status Listing: %s'), $status));
							echo $this->Html->tag('div', $label);
							echo $this->Html->tag('div', $title);
							echo $this->Html->tag('div', $price);
							echo $specs;
							echo $this->Html->tag('div', sprintf(__('Properti ID: #%s'), $mls_id));
							echo $this->Html->tag('div', sprintf(__('Terakhir update %s'), $this->Rumahku->customDate($change_date, 'd F Y')));
					?>
				</div>
			</div>
			<?php
						}
					} else {
						echo $this->Html->tag('div', __('Properti belum tersedia'), array(
							'class' => 'alert alert-warning',
						));
					}
			?>
		</div>
	</div>
</div>