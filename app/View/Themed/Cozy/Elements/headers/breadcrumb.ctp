<!-- BEGIN PAGE TITLE/BREADCRUMB -->
<div class="parallax colored-bg pattern-bg" data-stellar-background-ratio="0.5">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php 
						if( !empty($module_title) ) {
							echo $this->Html->tag('h1', $module_title, array(
								'class' => 'page-title',
							));
						}

	    				$breadcrumbTemp = '';

						if($this->Html->getCrumbs()):
					    	$home = 'Home ';

					    	$breadcrumbTemp .= $this->Html->tag('li', $this->Html->getCrumbs('</li><li>', array(
					    		'text' => $home,
					    		'escape' => false
					    	)));
					    endif;

					    if( !empty($breadcrumbTemp) ) {
					    	echo $this->Html->tag('ul', $breadcrumbTemp, array(
					    		'class' => 'breadcrumb',
				    		));
					    }
				?>
			</div>
		</div>
	</div>
</div>
<!-- END PAGE TITLE/BREADCRUMB -->