<?php 
        echo $this->Form->create('UserCompanyConfig', array(
            'type' => 'file',
        ));
?>
<div class="action-group top">
    <div class="btn-group floright">
        <?php 
                echo $this->Form->button(__('Simpan'), array(
                    'type' => 'submit', 
                    'class'=> 'btn blue',
                ));
        ?>
    </div>
</div>
<div class="mt30">
    <?php
            echo $this->element('blocks/common/tab_content', array(
                'content' => array(
                    'primer-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/primer_content'),
                        'title_tab' => __('Utama')
                    ),
                    'menu-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/role_menu'),
                        'title_tab' => __('Akses Menu')
                    ),
                    'config-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/config_content'),
                        'title_tab' => __('Konfigurasi')
                    ),
                    'additional-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/additional_content'),
                        'title_tab' => __('Info Tambahan')
                    ),
                    'property-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/property_content'),
                        'title_tab' => __('Properti')
                    ),
                    // 'mailchimp-content' => array(
                    //     'content_tab' => $this->element('blocks/common/forms/mailchimp_content'),
                    //     'title_tab' => __('Mailchimp')
                    // ),
                    'ebrosur-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/ebrosur_content'),
                        'title_tab' => __('Ebrosur')
                    ),
                    'cobroke-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/co_broke_content'),
                        'title_tab' => __('Co Broke')
                    ),
                    'meta-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/meta_setting'),
                        'title_tab' => __('SEO')
                    ),
                    'market-trend-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/market_trend_content'),
                        'title_tab' => __('Market Trend'), 
                    ),
                )
            ));
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="action-group bottom">
                <div class="btn-group floright">
                    <?php
                        echo $this->Form->button(__('Simpan'), array(
                            'type' => 'submit', 
                            'class'=> 'btn blue',
                        ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        echo $this->Form->end();
?>