<?php
        $save_path = Configure::read('__Site.profile_photo_folder');
        $agent_list = isset($agent_list) ? $agent_list : false;
        $with_social_media = (isset($with_social_media)) ? $with_social_media : false;
        $no_pict = (isset($no_pict)) ? $no_pict : false;

        $dataProfile = $this->Rumahku->filterEmptyField($value, 'UserProfile');

        $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
        $slug = $this->Rumahku->filterEmptyField($value, 'User', 'username');
        $email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '', true, 'mailto');
        $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name');
        $photo = $this->Rumahku->filterEmptyField($value, 'User', 'photo');

        $phone = $this->Rumahku->filterEmptyField($dataProfile, 'phone', NULL, '', true, 'phone');
        $no_hp = $this->Rumahku->filterEmptyField($dataProfile, 'no_hp');
        $no_hp_2 = $this->Rumahku->filterEmptyField($dataProfile, 'no_hp_2');
    
        $line = $this->Rumahku->filterEmptyField($dataProfile, 'line');
        $bbm = $this->Rumahku->filterEmptyField($dataProfile, 'pin_bb');

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

        if( empty($no_pict) ) {
?>
<div class="image text-center">
    <?php
            if( !empty($agent_list) ){    
                echo $this->Html->link($this->Html->tag('span', $this->Rumahku->icon('fa fa-plus').__(' Details')), $userUrl, array(
                    'escape' => false
                ));
            }
            
            echo $customPhoto;
    ?>
</div>
<?php 
        }
?>
<div class="info">
    <?php
            // Assign Properti Blm di develop
            // $assignt_property = '';
            // if(isset($count_properties) && !empty($count_properties)){
            //     $assignt_property =  $this->Html->tag('ul', sprintf('<li>%s Assigned Properties</li>', $count_properties), array(
            //         'class' => 'assigned'
            //     ));
            // }

            echo $this->Html->tag('header', $this->Html->tag('h2', $this->Html->link($name, array(
                'controller' => 'users',
                'action' => 'profile',
                $id,
                $slug,
                'admin' => false,
            ))));
    ?>
    
    <ul class="contact-us">
        <?php
                echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-envelope').$email);

                if(!empty($address)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-map-marker').$address);
                }

                if(!empty($no_hp)){
                    echo $this->Html->tag('li', $this->Rumahku->_callDisplayPhoneNumber($dataProfile, 'default', array(
                        'between' => '</li><li>',
                    )));
                }

                if(!empty($no_hp_2)){
                    echo $this->Html->tag('li', $this->Rumahku->_callDisplayPhoneNumber($dataProfile, 'inline', array(
                        'field' => 'no_hp_2',
                    )));
                }

                if(!empty($phone)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-home').$phone);
                }

                if(!empty($line)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('rv4-line').$line);
                }

                if(!empty($bbm)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('rv4-bbm').$bbm);
                }
        ?>
    </ul>

    <?php
            if( !empty($with_social_media) ){
    ?>
    <ul class="social-networks">
        <?php
                if(!empty($linkedin)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-linkedin'), $linkedin, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
                if(!empty($facebook)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-facebook'), $facebook, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
                if(!empty($twitter)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-twitter'), $twitter, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
                if(!empty($google_plus)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-google-plus'), $google_plus, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
                if(!empty($pinterest)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-pinterest'), $pinterest, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
                if(!empty($instagram)){
                    $link = $this->Html->link($this->Rumahku->icon('fa fa-instagram'), $instagram, array(
                        'escape' => false,
                        'target' => '_blank',
                    ));
                    echo $this->Html->tag('li', $link);
                }
        ?>
    </ul>
    <?php
        }
    ?>
</div>