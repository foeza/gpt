<?php
        $options = array(
            'frameClass' => 'col-sm-7',
            'labelClass' => 'col-xl-2 col-sm-3',
            'class' => 'relative col-sm-8 col-xl-7',
        );
        echo $this->element('blocks/common/tab_content', array(
            '_id' => 'wrapper-outer-security',
            'content' => array(
                'change_email' => array(
                    'content_tab' => $this->element('blocks/users/forms/change_email', array(
                        'options' => $options,
                        'role' => 'client',
                    )),
                    'title_tab' => __('Email'),
                ),
                'change_password' => array(
                    'content_tab' => $this->element('blocks/users/forms/change_password', array(
                        'options' => $options,
                        'role' => 'client',
                        'modelName' => 'UserClient',
                    )),
                    'title_tab' => __('Password'),
                    'role' => 'client',
                ),
            ),
            '_type' => 'style2',
        ));
?>