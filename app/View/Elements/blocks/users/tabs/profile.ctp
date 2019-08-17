<?php 
        $currUser = Configure::read('User.data');
        $id = $this->Rumahku->filterEmptyField($currUser, 'id');
        $group_id = $this->Rumahku->filterEmptyField($currUser, 'group_id');

        $step = !empty($step)?$step:false;
        $step_current = !empty($step_current)?$step_current:false;

        if( in_array($group_id, array( 2,3,4,5 )) ) {
            $contentArr = array(
                array(
                    'label' => __('Profil'),
                    'url' => array(
                        'controller' => 'users',
                        'action' => 'edit',
                        'admin' => true,
                    ),
                    'step' => 'profile',
                    'data_prev' => true,
                ),
            );

            if( in_array($group_id, array( 3,4,5 )) ) {
                $contentArr = array_merge($contentArr, array(
                    array(
                        'label' => __('Perusahaan'),
                        'url' => array(
                            'plugin' => false,
                            'controller' => 'users',
                            'action' => 'company',
                            'admin' => true,
                        ),
                        'step' => 'company',
                        'data_prev' => 'profile',
                    ),
                    array(
                        'label' => __('Media Sosial'),
                        'url' => array(
                            'plugin' => false,
                            'controller' => 'users',
                            'action' => 'company_social_media',
                            'admin' => true,
                        ),
                        'step' => 'social_media',
                        'data_prev' => 'company',
                    ),
                ));
            } else {
                $contentArr = array_merge($contentArr, array(
                    array(
                        'label' => __('Profesi'),
                        'url' => array(
                            'controller' => 'users',
                            'action' => 'profession',
                            'admin' => true,
                        ),
                        'step' => 'profession',
                        'data_prev' => 'profile',
                    ),
                    array(
                        'label' => __('Media Sosial'),
                        'url' => array(
                            'controller' => 'users',
                            'action' => 'social_media',
                            'admin' => true,
                        ),
                        'step' => 'social_media',
                        'data_prev' => 'profession',
                    ),
                ));
            }

            $contentTab = '';
            $idx = 1;

            foreach ($contentArr as $key => $value) {
                if( !empty($value) ) {
                    $label = $this->Rumahku->filterEmptyField($value, 'label');
                    $url = $this->Rumahku->filterEmptyField($value, 'url');
                    $step = $this->Rumahku->filterEmptyField($value, 'step');
                    $data = $this->Rumahku->filterEmptyField($value, 'data');
                    $data_prev = $this->Rumahku->filterEmptyField($value, 'data_prev');

                    $urlStep = $this->Rumahku->getUrlStep($url, $step, $data_prev);

                    $contentTab .= $this->Html->tag('li', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', $idx, array(
                        'class' => 'step-number',
                        'id' => sprintf('step-%s', $idx),
                    )), $this->Html->tag('label', $label, array(
                        'for' => sprintf('#step-%s', $idx),
                    ))), $urlStep, array(
                        'escape' => false,
                    )), array(
                        'class' => $this->Rumahku->getActiveStep($step_current, $step, $data),
                    ));
                    
                    $idx++;
                }
            }
?>
<div class="action-group user-manage top">
    <div class="step floright">
        <div class="step floright">
            <ul>
                <?php 
                        echo $contentTab;
                ?>
            </ul>
        </div>
    </div>
</div>
<?php
        }
?>