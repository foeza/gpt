<?php 
        $data = $this->request->data;
        $value = !empty($value)?$value:false;

        if( empty($idx) ) {
            $addClass = 'field-copy';
        } else {
            $addClass = '';
        }
?>
<li class="<?php echo $addClass; ?>">
    <?php 
            echo $this->Form->input('PropertyPointPlus.name.', array(
                'label' => false,
                'div' => array(
                    'class' => 'form-group',
                ),
                'required' => false,
                'placeholder' => __('Masukkan nilai lebih dari properti yang Anda tawarkan, disini'),
                'value' => $value,
            ));
            echo $this->Html->tag('span', $this->Html->link($this->Rumahku->icon('rv4-cross'), '#', array(
                'escape' => false,
            )), array(
                'class' => 'removed',
            ));
    ?>
</li>