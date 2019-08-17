<?php 
    echo $this->element('blocks/pages/style_ebrosurs');
    $this->Html->addCrumb($module_title);
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray">
    <?php 
        echo $this->element('blocks/pages/ebrosurs', array(
            'colMain' => 'col-sm-8',
            'colSide' => 'col-sm-3',
            'showSort' => false,
        ));
    ?>
</div>
<!-- END CONTENT WRAPPER -->