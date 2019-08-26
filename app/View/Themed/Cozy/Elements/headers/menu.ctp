<?php 
		$active_menu = !empty($active_menu)?$active_menu:false;
?>
<nav class="navbar">
	<button id="nav-mobile-btn"><i class="fa fa-bars"></i></button>
	<ul class="nav navbar-nav">
		<?php 
				echo $this->Html->tag('li', $this->Html->link(__('Beranda'), '/', array(
					'class' => ( $active_menu == 'home' ) ? 'active': '',
				)));
		?>
		<li class="dropdown">
			<?php 
					echo $this->Html->link(__('Produk'), '#', array(
						'class' => 'dropdown-toggle '.(( $active_menu == 'list_properties' ) ? 'active': ''),
						'data-toggle' => 'dropdown',
						'role' => 'button',
						'aria-haspopup' => 'true',
						'aria-expanded' => 'false',
						'data-toggle' => 'dropdown',
					));
			?>
			<ul class="dropdown-menu">
				<?php 
						// echo $this->Html->tag('li', $this->Html->link($sell, array(
						// 	'controller' => 'properties',
						// 	'action' => 'find',
						// //	'property_action' => 1,
						// 	'property_action' => 'dijual',
						// )));
						// echo $this->Html->tag('li', $this->Html->link($rent, array(
						// 	'controller' => 'properties',
						// 	'action' => 'find',
						// //	'property_action' => 2,
						// 	'property_action' => 'disewakan',
						// )));

				?>
			</ul>
		</li>
		<?php
				// echo $this->Html->tag('li', $this->Html->link(__('Tentang GPT'), array(
				// 	'controller' => 'pages',
				// 	'action' => 'about'
				// ), array(
				// 	'class' => ( $active_menu == 'about' ) ? 'active': '',
				// )));
				// echo $this->Html->tag('li', $this->Html->link(__('Hubungi Kami'), array(
				// 	'controller' => 'pages',
				// 	'action' => 'contact'
				// ), array(
				// 	'class' => ( $active_menu == 'contact' ) ? 'active': '',
				// )));

				echo $this->Html->tag('li', $this->Html->link(__('Artikel'), array(
					'controller' => 'advices',
					'action' => 'index'
				), array(
					'class' => ( $active_menu == 'advices' ) ? 'active': '',
				)));

		?>
	</ul>
</nav>