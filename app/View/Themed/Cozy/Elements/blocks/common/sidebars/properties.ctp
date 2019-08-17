<?php
        $property_path = Configure::read('__Site.property_photo_folder');
        
        if(!empty($populers)){
            echo $this->Html->tag('h2', __('Listing Terkini'), array(
                'class' => 'section-title'
            ));

            $linkOpt = $this->Rumahku->_callIsDirector()?array(
                'target' => '_blank',
            ):array();
?>
<ul class="footer-listings">
    <?php
            foreach ($populers as $key => $value) {
                $label = $this->Property->getNameCustom($value);
                $location = $this->Property->getNameCustom($value, true);

                $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
                $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
                $photo = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');

                $slug = $this->Rumahku->toSlug($label);
                $url = $this->Rumahku->_callUrlProperty($value, $mls_id, $slug);
                $customPhoto = $this->Rumahku->photo_thumbnail(array(
                    'save_path' => $property_path, 
                    'src'=> $photo, 
                    'size' => 'm',
                ));

                $content = $this->Html->tag('div', $this->Html->link($customPhoto, $url, array_merge($linkOpt, array(
                    'escape' => false
                ))), array(
                    'class' => 'image'
                ));
                $content .= $this->Html->tag('p', $this->Html->link($title.$this->Html->tag('span', '+'), $url, array_merge($linkOpt, array(
                    'escape' => false,
                ))), array(
                    'class' => 'title',
                ));
                $content .= $this->Html->tag('small', $location);

                echo $this->Html->tag('li', $content);
            }
    ?>
</ul>
<?php
        }
?>