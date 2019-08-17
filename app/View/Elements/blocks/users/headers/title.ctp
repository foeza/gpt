<?php 
        $user = !empty($user)?$user:false;
        $name = $this->Rumahku->filterEmptyField($user, 'User', 'full_name');
        $client_id = $this->Rumahku->filterEmptyField($user, 'UserClient', 'id');
        
        $urlEdit = $this->Html->link($this->Rumahku->icon('rv4-pencil').__('Edit'), array(
            'controller' => 'users',
            'action' => 'edit_client',
            $client_id,
            'admin' => true,
        ), array(
            'escape' => false,
        ));

        if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
            $urlBack = array(
                'controller' => 'users',
                'action' => 'client_info',
                'admin' => true,
            );
        } else {
            $urlBack = array(
                'controller' => 'users',
                'action' => 'user_info',
                'admin' => true,
            );
        }

        if( !empty($urlBack) ) {
?>
<div class="action-group">
    <div class="btn-group">
        <?php
                echo $this->Html->link(__('Kembali'), $urlBack, array(
                    'class'=> 'btn default',
                ));
        ?>
    </div>
</div>
<?php
        }

        echo $this->Html->tag('h2', $this->Html->tag('span', $name).$urlEdit, array(
            'class' => 'mt30 mb20'
        ));
?>