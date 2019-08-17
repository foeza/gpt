<?php
		if(!empty($media)){
			$property_path = Configure::read('__Site.property_photo_folder');
			$mediaPhoto = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'name');
			$mediaTitle = $this->Rumahku->filterEmptyField($media, 'PropertyMedias', 'title');

			echo $this->Rumahku->photo_thumbnail(array(
                'save_path' => $property_path, 
                'src' => $mediaPhoto, 
                'size' => 'l',
            ), array(
                'title'=> $mediaTitle, 
                'alt'=> $mediaTitle, 
                'class' => 'default-thumbnail',
            ));
		}else{
			echo __('Foto tidak ditemukan');
		}
?>