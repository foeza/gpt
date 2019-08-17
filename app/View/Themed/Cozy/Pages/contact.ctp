<?php 
        $name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
        $longitude = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'longitude');
        $latitude = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'latitude');

        $company = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany');
        $address = $this->Rumahku->getFullAddress($company);

        $logo_path = Configure::read('__Site.logo_photo_folder');
        $logo = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'logo');
        $customLogo = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $logo_path, 
            'src'=> $logo, 
            'size' => 'xxsm',
            'url' => true,
        ));

        $this->Html->addCrumb($module_title);

        $_global_variable = !empty($_global_variable)?$_global_variable:false;
        
        $language = $this->Rumahku->filterEmptyField($_global_variable, 'translates');
        $lang = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'language', 'id');        
        $contact = $this->Rumahku->filterEmptyField($language, $lang, 'contact');
?>
<!-- BEGIN CONTENT WRAPPER -->
<div class="content contacts">
    <!-- Sementara
    <div id="contacts_map"></div> -->
    <div class="container">
        <div class="row">
            <div id="contacts-overlay" class="col-sm-7">
                <?php
                        echo $this->Html->tag('h2', $contact, array(
                            'class' => 'section-title',
                        ));

                        echo $this->Html->tag('i', '', array(
                            'id' => 'contacts-overlay-close',
                            'class' => 'fa fa-minus',
                        ));
                        echo $this->element('blocks/common/info', array(
                            '_class' => false,
                            '_class_li' => 'col-sm-6',
                        ));
                ?>
            </div>
            <div class="main col-sm-4 col-sm-offset-8 contact-box">
                <?php 
                        echo $this->Html->tag('h2', __('Kotak Pesan'), array(
                            'class' => 'section-title',
                        ));
                        echo $this->Html->tag('p', __('Jika Anda membutuhkan keterangan lebih lanjut tentang kami, silahkan hubungi kami, kepuasan Anda adalah kebanggan bagi kinerja kami.'), array(
                            'class' => 'col-sm-12 center',
                        ));
                        echo $this->element('blocks/common/forms/contact', array(
                            '_class' => 'col-sm-12',
                            '_classInput' => false,
                            '_classFrom' => false,
                            '_url' => array(
                                'controller' => 'pages',
                                'action' => 'contact',
                                'admin' => false,
                            ),
                        ));
                ?>
            </div>  
        </div>
    </div>
</div>

<script type="text/javascript">
    // var singleMarker = [
    //     {
    //         "id": 0,
    //         "title": "<?php // echo $name;?>",
    //         "latitude": <?php // echo $latitude; ?>,
    //         "longitude": <?php // echo $longitude; ?>,
    //         "image": "<?php // echo $customLogo; ?>",
    //         "description": "<?php // echo $address; ?>",
    //         "map_marker_icon":"/theme/Cozy/images/markers/darkgrey-marker-cozy.png"
    //     }
    // ];
</script>