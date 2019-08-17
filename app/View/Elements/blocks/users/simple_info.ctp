<?php
		$User = !empty($User)?$User:false;
		$fileupload = isset($fileupload)?$fileupload:true;
		$security = isset($security)?$security:true;
        $is_rule = isset($is_rule)?$is_rule:true;
?>
<div id="simple-info">
    <div class="quick-response">
        <div id="user-action">
            <?php 
                    echo $this->element('blocks/users/profile', array(
                        'fileupload' => $fileupload,
                        'security' => $security,
                        'info_block_membership' => true,
                        'custom_wrapper' => array(
                            'wrap_row' => 'row mb30',
                            'column1' => 'col-xs-12 col-sm-4',
                            'column2' => 'col-xs-12 col-sm-8',
                        ),
                        'is_rule' => $is_rule,
                        'User' => $User,
                    ));
            ?>
        </div>
    </div>
</div>