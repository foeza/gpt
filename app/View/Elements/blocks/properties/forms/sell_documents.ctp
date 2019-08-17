<?php 
        $id = !empty($id)?$id:false;
        $session_id = !empty($session_id)?$session_id:false;
        $draft_id = Configure::read('__Site.PropertyDraft.id');
?>
<div class="step-medias documents">
    <div class="wrapper-upload-medias upload-video">
        <?php
                echo $this->element('blocks/properties/media_action', array(
                    'active' => 'document',
                ));
        ?>
        <div class="content-upload-photo">
            <div class="info-full alert photo-info-top">
                <?php 
                        echo $this->Html->tag('p', sprintf(__('%s Dengan mengunggah dokumen-dokumen properti akan membantu Anda dalam menjual dan memproses transaksi properti.'), $this->Html->tag('strong', __('Tahukah Anda?'))));
                ?>
            </div>
			<?php 
					echo $this->Form->create('CrmProjectDocument', array(
		        		'class' => 'form-target',
		    		));
					echo $this->Html->tag('div', $this->element('blocks/crm/document_items', array(
						'urlAdd' => array(
							'controller' => 'properties',
							'action' => 'document_add',
							$id,
                            'draft' => $draft_id,
							'admin' => true,
						),
						'urlEdit' => array(
							'controller' => 'properties',
							'action' => 'document_edit',
							'admin' => true,
						),
						'urlDelete' => array(
							'controller' => 'properties',
							'action' => 'document_delete',
							'admin' => true,
						),
					)), array(
						'class' => 'mt20',
					));
        			echo $this->Form->end(); 
			?>
        </div>
    </div>
    <?php
            echo $this->Form->create('PropertyVideos', array(
                'class' => 'form-horizontal',
                'id' => 'sell-property',
            ));
            
            echo $this->element('blocks/properties/sell_action', array(
                'action_type' => 'bottom',
                'labelBack' => __('Kembali'),
            ));

            echo $this->Form->hidden('Property.session_id', array(
                'value' => $session_id, 
            ));
            echo $this->Form->end();
    ?>
</div>