<?php 
	    echo $this->element('blocks/ebrosurs/style_ebrosurs');
	    
	    $this->Html->addCrumb($module_title);
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray">
    <?php 
	        echo $this->element('blocks/ebrosurs/ebrosurs', array(
	            'colMain' => 'col-sm-12 col-md-8',
	            'colSide' => 'col-sm-12 col-md-4',
	            'showSort' => true,
	            'is_form' => true
	        ));
    ?>
</div>
<!-- END CONTENT WRAPPER -->