<?php 
        if( !empty($urlBack) ) {
?>
<div class="action-group">
    <div class="btn-group">
        <?php
                echo $this->Html->link(__('Kembali'), $urlBack, array(
                    'class'=> 'btn default',
                ));
        ?>
    </div>
</div>
<?php
        }

        echo $this->Html->tag('h2', $title, array(
            'class' => 'mt30 mb20'
        ));
?>