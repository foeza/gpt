<?php
        $property_path = Configure::read('__Site.property_photo_folder');

        $_attributes = !empty($_attributes)?$_attributes:false;
        $_class = !empty($_class)?$_class:false;
        $displayShow = !empty($displayShow)?$displayShow:'grid';
        $i = 0;

        foreach ($properties as $key => $value) {
            $label = $this->Property->getNameCustom($value);
            $price = $this->Property->getPrice($value, false, false, false);

            $mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
            $title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
            $photo = $this->Rumahku->filterEmptyField($value, 'Property', 'photo');
            $description = $this->Rumahku->filterEmptyField($value, 'Property', 'description');
            $status = $this->Property->getShortStatus($value, 'span', true);

            // custom badge
            $name = $this->Rumahku->filterEmptyField($value, 'PropertyStatusListing', 'name');
            $badge_color = $this->Rumahku->filterEmptyField($value, 'PropertyStatusListing', 'badge_color', '');

            $status_listing = array(
                'name' => $name,
                'badge_color' => $badge_color,
            );

            $type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'name');

            $slug = $this->Rumahku->toSlug($label);
            $spec = $this->Property->getSpec($value, false, array(
                'class' => 'amenities',
                'display' => 'frontend',
            ));
            $spec = str_replace(array(
                'L. Bangunan',
                'L. Tanah',
                'L. Unit',
                'K. Tidur',
                'Sertifikat',
                'Lantai',
                'Dimensi',
            ), array(
                $this->Rumahku->icon('icon-area'),
                $this->Rumahku->icon('icon-area'),
                $this->Rumahku->icon('icon-area'),
                $this->Rumahku->icon('icon-bedrooms'),
                $this->Rumahku->icon('fa fa-file-text-o'),
                $this->Rumahku->icon('fa fa-bars'),
                $this->Rumahku->icon('fa fa-arrows-alt'),
            ), $spec);

            $url = $this->Rumahku->_callUrlProperty($value, $mls_id, $slug);
            $customPhoto = $this->Rumahku->photo_thumbnail(array(
                'save_path' => $property_path, 
                'src'=> $photo, 
                'size' => 'm',
            ), array(
                'class' => 'img-responsive',
                'alt' => $this->Rumahku->getALtImage($value)
            ));

            if( !empty($mod) ) {
                if( $i%$mod == 0 ) {
                    echo $this->Rumahku->clearfix();
                }
            }

            echo $this->element('blocks/properties/styles/'.$displayShow, array(
                '_attributes' => $_attributes,
                '_class' => $_class,
                'title' => $title,
                'label' => $label,
                'url' => $url,
                'photo' => $customPhoto,
                'type' => $type,
                'price' => $price,
                'spec' => $spec,
                'description' => $description,
                'status' => $status,
                'status_listing' => $status_listing,
            ));

            $i++;
        }
?>