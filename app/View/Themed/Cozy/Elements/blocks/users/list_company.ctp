<?php
        $save_path = Configure::read('__Site.logo_photo_folder');

        $id = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'id');
        $slug = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'slug');
        $name = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name');
        $logo = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'logo');
        $phone = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone');
        $phone_2 = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'phone_2');
        $fax = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'fax');
        $description = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'description', false, false);

        $dataCompany = $this->Rumahku->filterEmptyField($value, 'UserCompany');
        $location = $this->Rumahku->getFullAddress($dataCompany, '<br>', true);
        $logo = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $save_path, 
            'src'=> $logo, 
            'size' => 'xxsm',
        ), array(
            'title' => $name,
            'alt' => $name,
        ));

        $url = $this->Html->url(array(
            'controller' => 'users',
            'action' => 'company',
            $id,
            $slug,
        ));
?>
<div class="image text-center">
    <?php
            echo $this->Html->link($logo, $url, array(
                'escape' => false,
            ));
    ?>
</div>
<div class="info">
    <?php
            echo $this->Html->tag('header', $this->Html->tag('h2', $this->Html->link($name, $url)));
    ?>
    
    <ul class="contact-us">
        <?php
                if(!empty($location)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-map-marker').$location);
                }

                if(!empty($phone)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone);
                }

                if(!empty($phone_2)){
                    echo $this->Html->tag('li', $this->Rumahku->icon('fa fa-phone').$phone_2);
                }
        ?>
    </ul>
</div>