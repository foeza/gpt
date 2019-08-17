<?php
        $save_path = Configure::read('__Site.profile_photo_folder');
        $agent_list = isset($agent_list) ? $agent_list : false;
        $with_social_media = (isset($with_social_media)) ? $with_social_media : false;

        $dataProfile = $this->Rumahku->filterEmptyField($value, 'UserProfile');

        $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
        $slug = $this->Rumahku->filterEmptyField($value, 'User', 'username');
        $email = $this->Rumahku->filterEmptyField($value, 'User', 'email');
        $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
        $photo = $this->Rumahku->filterEmptyField($value, 'User', 'photo');

        $phone = $this->Rumahku->filterEmptyField($dataProfile, 'phone', NULL, '', true, 'formatNumber');
        $description = $this->Rumahku->filterEmptyField($dataProfile, 'description');

        $linkedin = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'linkedin');
        $facebook = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'facebook');
        $twitter = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'twitter');
        $google_plus = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'google_plus');
        $pinterest = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'pinterest');
        $instagram = $this->Rumahku->filterEmptyField($value, 'UserConfig', 'instagram');

        $address = $this->Rumahku->getFullAddress($dataProfile);
        $location = $this->Rumahku->getFullAddress($dataProfile, '<br>', true);
        $customPhoto = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $save_path, 
            'src'=> $photo, 
            'size' => 'pxl',
        ), array(
            'title' => $name,
            'alt' => sprintf('%s %s', $name, Configure::read('__Site.domain')),
        ));

        $userUrl = array(
            'controller' => 'users',
            'action' => 'profile',
            $id,
            $slug,
            'admin' => false,
        );

        $this->Html->addCrumb(__('Daftar Agen'), array(
            'controller' => 'users',
            'action' => 'agents',
            'admin' => false,
        ));
        $this->Html->addCrumb($module_title);
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content gray">
    <div class="container">
        <div class="row">
        
            <!-- BEGIN MAIN CONTENT -->
            <div class="main col-sm-12 col-md-8">
                
                <!-- BEGIN AGENT DETAIL -->
                <div class="agent-detail clearfix">
                    <?php
                            echo $this->Html->tag('div', $customPhoto, array(
                                'class' => 'image col-md-5',
                            ));
                    ?>
                    
                    <div class="col-md-7">
                        <?php
                                echo $this->element('blocks/users/list_agent', array(
                                    'user_data' => $value,
                                    'with_social_media' => true,
                                    'agent_list' => true,
                                    'no_pict' => true,
                                ));
                        ?>
                    </div>
                </div>
                <!-- END AGENT DETAIL -->
                
                <?php
                        if( !empty($description) ) {
                            echo $this->Html->tag('p', str_replace(PHP_EOL, '<br>', strip_tags($description)), array(
                                'class' => 'center'
                            ));
                        }

                        if(!empty($properties)){
                            echo $this->Html->tag('h1', __('Assigned Properties'), array(
                                'class' => 'section-title',
                                'id' => 'title-properties',
                            ));
                            echo $this->Rumahku->divider('thin');
                            echo $this->Html->tag('div', 
                                $this->Html->tag('div', 
                                    $this->element('blocks/properties/frontend/items', array(
                                        '_class' => 'col-md-4',
                                        'mod' => 3,
                                    )).
                                    $this->Html->tag('div', $this->element('custom_pagination', array(
                                        'options' => array(
                                            'class' => 'ajax-link',
                                            'class-link' => 'ajax-link',
                                            'data-wrapper-write' => '#assigned-properties',
                                            'data-scroll' => '#title-properties',
                                            'data-scroll-top' => '-180',
                                            'data-scroll-time' => '0',
                                        ),
                                    )), array(
                                        'class' => 'paginate-profile',
                                    )), array(
                                    'class' => 'row',
                                )), array(
                                'id' => 'assigned-properties',
                                'class' => 'grid-style1 clearfix',
                            ));
                        }
                ?>
            </div>  
            <!-- END MAIN CONTENT -->
            
            <div class="sidebar gray col-sm-12 col-md-4">
                <?php 
                        echo $this->element('widgets/company');
                        echo $this->element('widgets/contact', array(
                            'label' => __('Hubungi Agen'),
                        ));
                        echo $this->element('blocks/pages/advices');
                ?>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT WRAPPER -->