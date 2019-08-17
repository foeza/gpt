<?php
        $linkedin    = Common::hashEmptyField($dataCompany, 'UserConfig.linkedin');
        $facebook    = Common::hashEmptyField($dataCompany, 'UserConfig.facebook');
        $twitter     = Common::hashEmptyField($dataCompany, 'UserConfig.twitter');
        $googlePlus  = Common::hashEmptyField($dataCompany, 'UserConfig.google_plus');
        $pinterest   = Common::hashEmptyField($dataCompany, 'UserConfig.pinterest');
        $instagram   = Common::hashEmptyField($dataCompany, 'UserConfig.instagram');
        $youtube     = Common::hashEmptyField($dataCompany, 'UserConfig.youtube');
        
?>
<ul class="socialIcons">
    <?php

            if( $youtube ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-youtube-play'), $youtube, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( $facebook ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-facebook'), $facebook, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( $twitter ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-twitter'), $twitter, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( $googlePlus ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-google-plus'), $googlePlus, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( !empty($linkedin) ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-linkedin'), $linkedin, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( !empty($pinterest) ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-pinterest'), $pinterest, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }
            if( !empty($instagram) ) {
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-instagram'), $instagram, array(
                    'escape' => false,
                    'target' => '_blank',
                )));
            }

            //  link ke dashboard, dimunculkan hanya jika user sudah login
            echo $this->element('blocks/common/direct_backend', array(
                'style' => 'padding-left: 15px;',
                'divStyle' => 'margin-left: 10px;',
            ));

  //       echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-rss'), Configure::read('__Site.site_default').'/rss/', array(
        //  'escape' => false,
        //  'target' => '_blank'
        // )));

    ?>
</ul>