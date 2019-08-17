<?php
        /*
            variable $attribute_data di gunakan untuk melempar parameter ke dalam element content section
        */
        $open_box = isset($open_box) ? $open_box : false;

        $class_fa = 'fa fa-plus';
        $collapse = 'collapsed-box';
        $display = 'none';

        if($open_box){
            $class_fa = 'fa fa-minus';
            $collapse = '';
            $display = 'block';
        }
?>
<div class="box box-info <?php echo $collapse?>">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo __('Pencarian');?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="<?php echo $class_fa;?>"></i></button>
        </div>
    </div>
    <div class="box-body" style="display:<?php echo $display?>;">
        <?php
            if(!empty($path_content)){
                /*content section*/
                $attribute_data = isset($attribute_data) ? $attribute_data : array();
                echo $this->element($path_content, $attribute_data);
            }else{
                echo 'Template tidak ditemukan.';
            }
        ?>
    </div>
</div>