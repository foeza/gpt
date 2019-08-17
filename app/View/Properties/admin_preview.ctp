<?php
		$user_path = Configure::read('__Site.profile_photo_folder');
        $this->request->data = $property;
        $kolisting_koselling = Common::hashEmptyField($_config, 'UserCompanyConfig.is_kolisting_koselling');

        $customName = $this->Property->getNameCustom($property);
        $customSpec = $this->Property->_callGetSpecification($property, array(
            'list_options' => array(
                'class' => 'col-xs-6 col-md-4'
            ),
            'class' => 'row'
        ), true, $data_revision);
        $dataAddress = Common::hashEmptyField($property, 'PropertyAddress');
        $dataMedias = Common::hashEmptyField($property, 'PropertyMedias');
        $dataVideos = Common::hashEmptyField($property, 'PropertyVideos');

        $property_id = Common::hashEmptyField($property, 'Property.id');
        $photo = Common::hashEmptyField($property, 'Property.photo');
        $title = Common::hashEmptyField($property, 'Property.title');
        $description = Common::hashEmptyField($property, 'Property.description');
        $change_date = Common::hashEmptyField($property, 'Property.change_date');
        $kolisting_koselling = Common::hashEmptyField($property, 'Property.kolisting_koselling');

        $address = Common::hashEmptyField($property, 'PropertyAddress.address');
        $address2 = Common::hashEmptyField($property, 'PropertyAddress.address2');
        $zip = Common::hashEmptyField($property, 'PropertyAddress.zip');
        $no = Common::hashEmptyField($property, 'PropertyAddress.no');
        $rt = Common::hashEmptyField($property, 'PropertyAddress.rt');
        $rw = Common::hashEmptyField($property, 'PropertyAddress.rw');

        $subarea = Common::hashEmptyField($dataAddress, 'Subarea.name');
        $city = Common::hashEmptyField($dataAddress, 'City.name');
        $region = Common::hashEmptyField($dataAddress, 'Region.name');

        $created = Common::hashEmptyField($property, 'User.created');
        $in_update = Common::hashEmptyField($property, 'Property.in_update');

        $customCreated = $this->Rumahku->formatDate($last_modify, 'F Y');
        $customChangeDate = $this->Rumahku->formatDate($change_date, 'd F Y');
        $customDescription = $this->Rumahku->_callGetDescription($description);
        
        $PropertyPointPlus = $this->Property->_callPointPlus($property);
        $PropertyFacility = $this->Property->_callFacility($property, $facilities);
        $msgRejected = $this->Property->getNotifRejected($property);

        $is_revision = false;
        if( Configure::read('User.admin') && (!empty($data_revision) || !empty($in_update))){
            $is_revision = true;
        }

		if($is_revision){
			echo $this->Form->create('PropertyRevision');
		}
?>
<div class="preview-content">
    <?php
    		echo $this->element('blocks/common/forms/search/header_admin_preview', array(
                'is_revision' => $is_revision
            ));

            if( $is_revision || !empty($msgRejected) ){
                $contentMsg = '';

                if( !empty($is_revision) ) {
                    $contentMsg .= $this->Html->div('info-full alert', __('Anda dapat melakukan seleksi terhadap revisi yang Anda terima dengan mencentang poin-poin yang di nyatakan revisinya.'));
                }
                if( !empty($msgRejected) ) {
                    $contentMsg .= $msgRejected;
                }

                if( !empty($contentMsg) ) {
                    echo $this->Html->tag('div', $contentMsg, array(
                        'class' => 'msg-block',
                    ));
                }
            }
    ?>
    <div id="wrapper-content">
        <div id="property-detail">
            <div class="row">
                <?php 
                        echo $this->Html->tag('div', $this->element('blocks/properties/agent_info'), array(
                            'class' => 'col-sm-4 no-pright',
                        ));
                ?>
                <div class="col-sm-8 no-pleft">
                    <div class="galleries">
                        <?php
                                $tabContent = array(
                                    'image_approval' => array(
                                        'content_tab' => $this->element('blocks/common/image_approval', array(
                                            'medias' => $dataMedias,
                                            'property_id' => $property_id,
                                        )),
                                        'title_tab' => __('Foto')
                                    ),
                                    'video_approval' => array(
                                        'content_tab' => $this->element('blocks/common/video_approval', array(
                                            'videos' => $dataVideos,
                                            'property_id' => $property_id,
                                        )),
                                        'title_tab' => __('Video')
                                    ),
                                );

                                if( !empty($in_update) ) {
                                    $tabContent['check-box-all'] = array(
                                        'title_tab' => $this->Form->input('Property.checkall', array(
                                            'type' => 'checkbox',
                                            'label' => __('Semua'),
                                            'div' => false,
                                            'required' => false,
                                            'error' => false,
                                            'checked' => true,
                                            'class' => 'checkAll',
                                        )),
                                        'type' => 'checkbox',
                                    );
                                }

                                echo $this->element('blocks/common/tab_content', array(
                                    'content' => $tabContent,
                                ));
                        ?>
                    </div>
                    <div class="properti-info">
                        <?php 
                                echo $this->Html->tag('h2', $this->Rumahku->getCheckRevision('Property', 'property_type_id,property_action_id,keyword', $data_revision, $customName));

                                echo $this->Html->tag('p', sprintf(__('Properti ini telah diperbarui pada %s'), $customCreated), array(
                                    'class' => 'date',
                                ));

                                echo $customSpec;
                                echo $this->Property->_callGetCustom($property, 'div', $data_revision);
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 no-pright">
                    <?php
                            if(isset($property['PropertyPointPlus'])){
                                $content = $this->Html->tag('h2', $this->Rumahku->getCheckRevision('PropertyPointPlus', 'format_arr', $data_revision, __('Nilai Tambah Properti')));
                                $content .= !empty($PropertyPointPlus)?$PropertyPointPlus:__('Tidak ada nilai tambah');
                                
                                echo $this->Html->div('point-plus', $content);
                            }

                            if(!empty($PropertyFacility)){
                                $content = $this->Html->tag('h2', $this->Rumahku->getCheckRevision('PropertyFacility', 'format_arr', $data_revision, __('Fasilitas Properti')));
                                $content .= $PropertyFacility;
                                
                                echo $this->Html->div('point-plus', $content);
                            }
                    ?>
                </div>
                <div class="col-sm-8 no-pleft">
                    <div class="properti-info" id="properti-info-second">
                        <?php 
                                echo $this->Html->tag('h2', $this->Rumahku->getCheckRevision('Property', 'title', $data_revision, $title), array(
                                    'class' => 'title',
                                ));
                                
                                echo $this->Html->tag('div', $this->Rumahku->getCheckRevision('Property', 'description', $data_revision, $customDescription), array(
                                    'class' => 'description',
                                ));
                        ?>
                        <div class="address">
                            <?php 
                                    echo $this->Html->tag('h2', __('Alamat Properti'));
                            ?>
                            <ul>
                                <?php 
                                        echo $this->Html->tag('li', $this->Rumahku->getCheckRevision('PropertyAddress', 'address', $data_revision, $address));
                                        echo $this->Html->tag('li', $this->Rumahku->getCheckRevision('PropertyAddress', 'address2', $data_revision, $address2));
                                        echo $this->Html->tag('li', $this->Rumahku->getCheckRevision('PropertyAddress', 'no,rw,rt', $data_revision, sprintf('No. %s RT.%s RW.%s', $no, $rt, $rt)));
                                        echo $this->Html->tag('li', $this->Rumahku->getCheckRevision('PropertyAddress', 'city_id,region_id,subarea_id,zip', $data_revision, sprintf('%s - %s %s %s', $city, $region, $subarea, $zip)));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        /*
        Sementara
        <div class="row">
            <div class="col-sm-12">
                <div id="map_container">
                    <?php 
                            echo $this->Html->tag('h2', $this->Rumahku->getCheckRevision('PropertyAddress', 'latitude,longitude,location', $data_revision, __('Revisi Map')));

                            echo $this->Rumahku->setFormAddress( 'PropertyAddress' );
                    ?>
                    <div id="gmap-rku"></div>
                </div>
            </div>
        </div>
        */
        ?>
    </div>
</div>
<?php
		if($is_revision){
			echo $this->Form->end();
		}
?>