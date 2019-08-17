<?php
$group_id = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'group_id');

$addclass = '';
if($group_id == 2){
    $addclass = '';
} else {
    $addclass = 'hide';
}
?>
<div class="row handling-group2 <?php echo $addclass; ?>">
<?php 
        if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
            echo $this->Html->tag('h2', __('Komisi'), array(
                'class' => 'sub-heading'
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.commission', array(
                'type' => 'text',
                'label' => __('Target Komisi'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('/ Bulan'),
                'textGroupSecond' => Configure::read('__Site.config_currency_symbol'),
                'inputClass' => 'input_price',
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.sharingtocompany', array(
                'type' => 'text',
                'label' => __('Sharing to Company'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('%'),
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.royalty', array(
                'type' => 'text',
                'label' => __('Royalty'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('%'),
            ));
        }
?>
</div>