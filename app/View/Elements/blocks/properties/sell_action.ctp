<?php 
        $action_type = !empty($action_type)?$action_type:'top';
        $step = !empty($step)?$step:false;
        $sub_step = !empty($sub_step)?$sub_step:false;
        $urlBack = !empty($urlBack)?$urlBack:'#';
        $id = !empty($id)?$id:false;
        $draft_id = Configure::read('__Site.PropertyDraft.id');

        $dataBasic = !empty($dataBasic)?$dataBasic:false;
        $dataAddress = !empty($dataAddress)?$dataAddress:false;
        $dataAsset = !empty($dataAsset)?$dataAsset:false;
        $dataMedias = !empty($dataMedias)?$dataMedias:false;

        $stepBasic   = 'Basic';
        $stepAddress = 'Address';
        $stepAsset   = 'Asset';
        $stepMedia   = 'Medias';
?>
<div class="action-group <?php echo $action_type; ?>">
<!--     <div class="btn-group floleft">
        <?php 
                // if( empty($id) ) {
                //     echo $this->AclLink->link(__('Simpan Draft'), array(
                //         'controller' => 'properties',
                //         'action' => 'add_draft',
                //         'draft' => $draft_id,
                //         'admin' => true,
                //     ), array(
                //         'escape' => false,
                //         'class' => 'btn default submit-custom-form',
                //         'data-alert' => __('Anda yakin ingin menyimpan data properti ini kedalam draft?'),
                //         'data-form' => '.sell-form form#sell-property',
                //     ));
                // }
        ?>
    </div> -->
    <?php 
            if( $action_type == 'top' ) {
                if( !empty($id) ) {
                    $urlBasic = array(
                        'action' => 'edit',
                        $id,
                        'admin' => true,
                    );
                    $urlAddress = array(
                        'action' => 'edit_address',
                        $id,
                        'admin' => true,
                    );
                    $urlAsset = array(
                        'action' => 'edit_specification',
                        $id,
                        'admin' => true,
                    );
                    $urlMedias = array(
                        'action' => 'edit_medias',
                        $id,
                        'admin' => true,
                    );
                } else {
                    $urlBasic = array(
                        'action' => 'sell',
                        'admin' => true,
                    );
                    $urlAddress = array(
                        'action' => 'address',
                        'admin' => true,
                    );
                    $urlAsset = array(
                        'action' => 'specification',
                        'admin' => true,
                    );
                    $urlMedias = array(
                        'action' => 'medias',
                        'admin' => true,
                    );
                }

                $urlStepBasic = $this->Rumahku->getUrlStep($urlBasic, $stepBasic, true, $id);
                $urlStepAddress = $this->Rumahku->getUrlStep($urlAddress, $stepAddress, $dataBasic, $id);
                $urlStepAsset = $this->Rumahku->getUrlStep($urlAsset, $stepAsset, $dataAddress, $id);
                $urlStepmedia = $this->Rumahku->getUrlStep($urlMedias, $stepMedia, $dataAsset, $id);
    ?>
    <div class="step floright">
        <div class="step floright">
            <ul>
                <?php 
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 1, array(
                            'class' => 'step-number',
                            'id' => 'step-1',
                        )), $this->Html->tag('label', __('Info Dasar'), array(
                            'for' => '#step-1',
                        ))), $urlStepBasic, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, $stepBasic, $dataBasic, $id),
                        ));
                        // echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 2, array(
                        //     'class' => 'step-number',
                        //     'id' => 'step-2',
                        // )), $this->Html->tag('label', __('Alamat'), array(
                        //     'for' => '#step-2',
                        // ))), $urlStepAddress, array(
                        //     'escape' => false,
                        // )), array(
                        //     'class' => $this->Rumahku->getActiveStep($step, $stepAddress, $dataAddress, $id),
                        // ));
                        // echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 3, array(
                        //     'class' => 'step-number',
                        //     'id' => 'step-3',
                        // )), $this->Html->tag('label', __('Spesifikasi'), array(
                        //     'for' => '#step-3',
                        // ))), $urlStepAsset, array(
                        //     'escape' => false,
                        // )), array(
                        //     'class' => $this->Rumahku->getActiveStep($step, $stepAsset, $dataAsset, $id),
                        // ));
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 2, array(
                            'class' => 'step-number',
                            'id' => 'step-4',
                        )), $this->Html->tag('label', __('Foto & Video'), array(
                            'for' => '#step-4',
                        ))), $urlStepmedia, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, $stepMedia, $dataMedias, $id),
                        ));
                ?>
            </ul>
        </div>
    </div>
    <?php 
            }
    ?>
    <div class="btn-group floright">
        <?php
                if( $action_type == 'bottom' ) {
                    echo $this->Html->link(__('Kembali'), $urlBack, array(
                        'escape' => false,
                        'class' => 'btn default',
                    ));

                    if( $step == $stepMedia ) {
                        $lblSave = __('Simpan');
                    } else {
                        $lblSave = __('Lanjut');
                    }

                    if( !empty($id) && ( ($step == $stepMedia && $sub_step != 'documents') || $sub_step == 'documents' ) ) {
                        echo $this->Html->link($lblSave, array(
                            'controller' => 'properties',
                            'action' => 'index',
                            'admin' => true,
                        ), array(
                            'type' => 'submit',
                            'class' => 'btn blue',
                        ));
                    } else {
                        echo $this->Form->button($lblSave, array(
                            'type' => 'submit',
                            'class' => 'btn blue',
                        ));
                    }
                }
        ?>
    </div>
</div>