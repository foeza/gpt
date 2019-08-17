<?php 
        echo $this->Form->create('User');
?>
<div class="user-info-form text-center">
	<?php
            echo $this->element('blocks/common/welcome_words');
    ?>
    <div class="wrapper-user-info">
        <div class="form-group">
            <?php
                    echo $this->Html->tag('label', __(' Pilih tipe akun Anda.'), array(
                        'class' => 'title',
                    ));
            ?>
            <div class="btn-group tracker-group-id" data-toggle="buttons">
                <label class="btn btn-default action" data-value="2">
                    <?php 
                            echo $this->Form->input('tmp_group_id', array(
                                'type' => 'radio',
                                'label' => false,
                                'checked' => true,
                                'options' => false,
                                'div' => false,
                                'value' => 2,
                            ));
                            echo $this->Rumahku->icon('users', false, 'i', 'icon');
                            echo $this->Html->tag('span', __('Agen Properti'));
                    ?>
                </label>
                <label class="btn btn-default action" data-value="1">
                    <?php 
                            echo $this->Form->input('tmp_group_id', array(
                                'type' => 'radio',
                                'label' => false,
                                'checked' => true,
                                'options' => false,
                                'div' => false,
                                'value' => 1,
                            ));
                            echo $this->Rumahku->icon('user', false, 'i', 'icon');
                            echo $this->Html->tag('span', __('Non-Agent'));
                    ?>
                </label>
            </div>
            <?php 
                    echo $this->Form->error('group_id');
            ?>
        </div>
        <div class="form-group">
            <?php
                    echo $this->Html->tag('small', sprintf(__(' Anda dapat mengklasifikasikan diri Anda di kemudian hari. %s.'), $this->Html->link(__('Pelajari lebih lanjut'), '#')));
            ?>
        </div>
        <div class="form-group">
            <?php
                    echo $this->Form->button(__('Lanjut'), array(
                        'type' => 'submit', 
                        'class' => 'btn btn-default'
                    ));
                    echo $this->Html->link(__('Lewati'), array(
                        'controller' => 'users', 
                        'action'=> 'account',
                        'admin' => false,
                    ), array(
                        'class' => 'btn btn-link'
                    ));
                    echo $this->Form->hidden('group_id', array(
                        'class' => 'info-group-id',
                    ));
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end() ?>