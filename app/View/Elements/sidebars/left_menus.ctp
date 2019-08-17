<?php 
		$active_menu = !empty($active_menu)?$active_menu:false;
?>
<!-- NEW VERSION -->
<nav class="sidebar hidden-print" id="main-menu">
	<?php
			echo $this->element('blocks/common/v2_menus');
	?>
</nav>
<!--  -->