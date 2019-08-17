<div class="minimalist-menu">
	<!-- <div class="box"></div> -->
	<nav id="ml-menu">
		<div class="menu__wrap">
			<?php
					$active_menu = !empty($active_menu) ? $active_menu : false;
					$data_arr = $this->Rumahku->generateMenu($data_arr, array(
						'data_menu'		=> 'navigation-menu', 
						'active_menu'	=> $active_menu,
					));

					if(!empty($data_arr)){
						for ($i = (count($data_arr) - 1) ; $i >= 0 ; $i--) { 
							$show = !empty($data_arr[$i]) ? $data_arr[$i] : false;

							if($show){
								echo $show;
							}
						}
					}
					
			?>
		</div>
	</nav>
</div>