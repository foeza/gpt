<?php 
        $action_type = !empty($action_type)?$action_type:'top';
        $step = !empty($step)?$step:false;
        $urlBack = !empty($urlBack)?$urlBack:false;
        $id = !empty($id)?$id:false;

        $active_menu = !empty($active_menu)?$active_menu:false;

        $value = !empty($value)?$value:false;
        $userData = $this->Rumahku->filterEmptyField($value, 'User');
        $companyData = $this->Rumahku->filterEmptyField($value, 'UserCompany');
        
        $stepBasic = 'Basic';
        $stepCompany = 'Company';
        $stepSocialMedia = 'social_media';
?>
<div class="action-group <?php echo $action_type; ?>">
    <?php
            if( !empty($urlCancel) ) {
                echo $this->Html->tag('div', 
                    $this->Html->link(__('Batal'), $urlCancel, array(
                        'class'=> 'btn default',
                    )), array(
                    'class'=> 'floleft',
                ));
            }

            $options = array();
            if($active_menu){
                $options = array(
                    'type' => $active_menu,
                );
            }
            

            if( $action_type == 'top' ) {
                $urlBasic = array(
                    'action' => 'edit_principle',
                    $id,
                    'admin' => true,
                );
                $urlCompany = array_merge(array(
                    'action' => 'principle_company',
                    $id,
                    'admin' => true,
                ), $options);
                $urlSocialMedia = array_merge(array(
                    'action' => 'general_social_media',
                    $id,
                    'admin' => true,
                ), $options);

                $urlStepBasic = $this->Rumahku->getUrlStep($urlBasic, $stepBasic, false, $id);
                $urlStepCompany = $this->Rumahku->getUrlStep($urlCompany, $stepCompany, false, $id);
                $urlStepSocialMedia = $this->Rumahku->getUrlStep($urlSocialMedia, $stepSocialMedia, false, $id);
    ?>
    <div class="step floright">
        <div class="step floright">
            <ul>
                <?php 
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 1, array(
                            'class' => 'step-number',
                            'id' => 'step-1',
                        )), $this->Html->tag('label', __('Info Principal'), array(
                            'for' => '#step-1',
                        ))), $urlStepBasic, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, $stepBasic),
                        ));
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 2, array(
                            'class' => 'step-number',
                            'id' => 'step-2',
                        )), $this->Html->tag('label', __('Perusahaan'), array(
                            'for' => '#step-2',
                        ))), $urlStepCompany, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, $stepCompany),
                        ));
                        echo $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', 3, array(
                            'class' => 'step-number',
                            'id' => 'step-3',
                        )), $this->Html->tag('label', __('Media Sosial'), array(
                            'for' => '#step-3',
                        ))), $urlStepSocialMedia, array(
                            'escape' => false,
                        )), array(
                            'class' => $this->Rumahku->getActiveStep($step, $stepSocialMedia),
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
                    if( !empty($urlBack) ) {
                        echo $this->Html->link(__('Kembali'), $urlBack, array(
                            'escape' => false,
                            'class' => 'btn default',
                        ));
                    }

                    if( $step == 'social_media' || empty($step) ) {
                        $lblSave = __('Simpan');
                    } else {
                        $lblSave = __('Lanjut');
                    }

                    echo $this->Form->button($lblSave, array(
                        'type' => 'submit',
                        'class' => 'btn blue',
                    ));
                }
        ?>
    </div>
    <div class="clear"></div>
</div>