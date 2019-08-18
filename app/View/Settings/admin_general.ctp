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
                    'config-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/config_content'),
                        'title_tab' => __('Konfigurasi')
                    ),
                    'property-content' => array(
                        'content_tab' => $this->element('blocks/common/forms/property_content'),
                        'title_tab' => __('Produk')
                    ),
                    // 'market-trend-content' => array(
                    //     'content_tab' => $this->element('blocks/common/forms/market_trend_content'),
                    //     'title_tab' => __('Market Trend'), 
                    // ),
                )
            ));
    ?>
</div>
<?php
        echo $this->Form->end();
?>