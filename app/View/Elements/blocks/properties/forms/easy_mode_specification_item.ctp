<?php
	
	$items = empty($items) ? array() : (array) $items;

	if($items){

		?>
		<div class="row">
			<?php

				$counter = 0;

				foreach($items as $key => $item){
					$label = Common::hashEmptyField($item, 'label');
					$value = Common::hashEmptyField($item, 'value');

					if($value){

						?>
						<div class="col-sm-6">
							<div class="form-group no-margin">
								<div class="row">
									<div class="col-xs-6 no-pright">
										<?php echo($this->Html->tag('label', __($label), array('class' => 'control-label'))); ?>
									</div>
									<div class="col-xs-6 relative no-pleft"><?php echo($value); ?></div>
								</div>
							</div>
						</div>
						<?php

						$counter++;

						if($counter % 2 == 0){

							?></div><div class="row"><?php

						}
					}
				}

			?>
		</div>
		<?php

	}

?>