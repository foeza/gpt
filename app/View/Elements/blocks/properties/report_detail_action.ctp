<?php
        $property = !empty($property)?$property:false;
        $id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
        $mls_id = $this->Rumahku->filterEmptyField($property, 'Property', 'mls_id');

        $_isClient = !empty($_isClient)?$_isClient:false;
        $_backUrl = !empty($_backUrl)?$_backUrl:false;
        $_sharePropertyAction = !empty($_sharePropertyAction)?$_sharePropertyAction:false;
        
        if( empty($_backUrl) ) {
            $_backUrl = array(
                'controller' => 'properties',
                'action' => 'report',
                'admin' => true,
            );
        }
?>
<div class="row hidden-print">
    <div class="col-sm-12">
        <div class="action-group">
        	<div class="btn-group floleft">
                <?php
	                    echo $this->Html->link(__('Kembali'), $_backUrl, array(
	                        'class'=> 'btn default',
	                    ));
                ?>
            </div>

            <?php
                    if( empty($_isClient) ) {
            ?>
            <div class="btn-group floright">
                <?php
                    echo $this->Html->link(__('Cetak Laporan'), '#', array(
                        'class' => 'btn default print-page',
                    ));

                    if( !empty($_sharePropertyAction) ) {
                        echo $this->Html->link(__('Kirim Laporan'), array(
                            'controller' => 'properties',
                            'action' => 'share',
                            $id,
                            $_sharePropertyAction,
                            'admin' => true,
                        ), array(
                            'class'=> 'btn blue ajaxModal',
                            'title' => sprintf(__('Kirim Laporan Properti #%s'), $mls_id),
                        ));
                    }
                ?>
            </div>
            <?php
                    }
            ?>            
        </div>
    </div>
</div>