<?php
        echo $this->Form->create('User', array(
            'url'=> array(
                'action'=> 'search', 
                'index', 
                'admin'=> true
            ),  
            'inputDefaults'=> array('div'=> false)
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php
                    echo $this->Form->input('fullname',array(
                        'type'=> 'text',
                        'label'=> __('Nama User'), 
                        'class'=>'form-control',
                        'title'=> __('Nama User'),
                        'required' => false
                    ));
            ?>
        </div>
        <div class="form-group">        
            <?php
                    echo $this->Form->input('email',array(
                        'type'=> 'text',
                        'label'=> __('Email'), 
                        'class'=>'form-control',
                        'title'=> __('Email'),
                        'required' => false
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php
                    echo $this->Form->input('status',array(
                        'label'=> __('Status User'), 
                        'options' => array(
                            '1' => __('Aktif'),
                            '0' => __('Tidak Aktif'),
                        ),
                        'title'=> __('Pilih Status'),
                        'class' => 'form-control',
                        'required' => false,
                        'empty'=> __('Pilih Status'),
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php
                    echo $this->Form->input('User.group_id',array(
                        'label'=> __('Group'), 
                        'options' => $groups,
                        'title'=> __('Pilih Group'),
                        'class' => 'form-control',
                        'required' => false,
                        'empty'=> __('Pilih Group'),
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success',
                        'type' => 'submit'
                    ));
                    echo '&nbsp;';
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'action'=> 'index', 
                        'admin'=> true
                    ), array(
                        'class'=> 'btn',
                        'escape' => false, 
                    ));
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end()?>