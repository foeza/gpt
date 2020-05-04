<?php
        $data = $this->request->data;

        echo $this->Form->create('UserCompanyConfig', array(
            'type' => 'file',
        ));

        $default_array = array(
            'content' => array(
                // 'general-content' => array(
                //     'content_tab' => $this->element('blocks/common/forms/tab_general_company'),
                //     'title_tab' => __('Pengaturan Umum')
                // ),
                'meta-content' => array(
                    'content_tab' => $this->element('blocks/common/forms/meta_setting'),
                    'title_tab' => __('SEO')
                )
            )
        );

        // if(!empty($is_co_broke)){
        //     $default_array['content']['co-broke-content'] = array(
        //         'content_tab' => $this->element('blocks/common/forms/co_broke_content'),
        //         'title_tab' => __('Co Broke'), 
        //     );
        // }

        echo $this->element('blocks/common/tab_content', $default_array);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group">
                <?php
                    echo $this->Form->button(__('Simpan Perubahan'), array(
                        'type' => 'submit', 
                        'class'=> 'btn blue',
                    ));
                ?>
            </div>
        </div>
    </div>
</div>

<?php
        echo $this->Form->end();
?>