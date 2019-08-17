<?php

	$items = empty($items) ? array() : (array) $items;

	if($items){
		$active		= empty($active) ? false : $active;
		$activeText	= empty($activeText) ? false : $activeText;
		$options	= empty($options) ? array() : (array) $options;
		$counter	= 0;

		foreach($items as $key => $item){
			$text		= Hash::get($item, 'text', false);
			$url		= Hash::get($item, 'url');
			$options	= Hash::get($item, 'options', array());
			$allow		= $this->AclLink->aclCheck($url);

			if($allow){
				$class	= false;
				$url	= empty($url) ? 'javascript:void(0);' : $url;

				if((empty($counter) && empty($active)) || $active == (is_numeric($key) ? $text : $key)){
					$class		= 'active';
					$activeText	= __($text);

					$counter++;
				}

				$link = $this->AclLink->link(__($text), $url, array(
					'escape'	=> false,
					'class'		=> $class,
				));

				$items[$key] = $this->Html->tag('li', $link, $options);
			}
			else{
				unset($items[$key]);
			}
		}

		$uuid	= String::uuid();
		$items	= implode('', array_filter($items));

		if($items){

		//	<div class="crm">
			?>
			<div class="detail-project-menu">
				<?php

					echo($this->Html->tag('ul', $items, array(
						'class' => 'desktop-only', 
					)));

				?>
				<div class="mobile-only">
					<div class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" id="<?php echo($uuid); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo($activeText); ?>
							<span class="caret"></span>
						</button>
						<?php

							echo($this->Html->tag('ul', $items, array(
								'class'				=> 'dropdown-menu', 
								'aria-labelledby'	=> $uuid, 
							)));

						?>
					</div>
				</div>
			</div>
			<?php
		//	</div>

		}
	}

?>