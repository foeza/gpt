<?php
        $_classFrame = !empty($_classFrame)?$_classFrame:'agents-grid';
        $_class = !empty($_class)?$_class:'col-sm-3';
        $title_label = isset($title_label)?$title_label:__('TOP Agen');

        if(!empty($agents)){
            if( !empty($title_label) ) {
                echo $this->Html->tag('h1', $title_label, array(
                    'data-animation-direction' => 'from-bottom',
                    'data-animation-delay' => '50',
                    'class' => 'section-title'
                ));
            }
?>
<ul class="<?php echo $_classFrame; ?>">
    <?php
            $i = 0;

            foreach ($agents as $key => $value) {
                $id = $this->Rumahku->filterEmptyField($value, 'User', 'id');
                $slug = $this->Rumahku->filterEmptyField($value, 'User', 'username');

                $url = array(
                    'controller' => 'users',
                    'action' => 'profile',
                    $id,
                    $slug,
                    'admin' => false,
                );

                if( !empty($mod) ) {
                    if( $i%$mod == 0 ) {
                        echo $this->Rumahku->clearfix();
                    }
                }

                echo $this->Html->tag('li', $this->element('blocks/users/list_agent', array(
                    'value' => $value,
                    'with_social_media' => true,
                    'agent_list' => true,
                    'no_pict' => false
                )), array(
                    'class' => $_class,
                    'data-animation-direction' => 'from-bottom',
                    'data-animation-delay' => '250',
                ));
                
                $i++;
            }
    ?>
</ul>
<?php
        }
?>