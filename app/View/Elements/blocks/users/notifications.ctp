<?php 
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->Rumahku->filterEmptyField($value, 'Notification', 'id');
                $message = $this->Rumahku->filterEmptyField($value, 'Notification', 'name');
                $date = $this->Rumahku->filterEmptyField($value, 'Notification', 'created');
                $read = $this->Rumahku->filterEmptyField($value, 'Notification', 'read');
                $global = $this->Rumahku->filterEmptyField($value, 'Notification', 'global');

                $addClass = '';
                $addAttrLink = '';
                $url = array(
                    'controller' => 'users',
                    'action' => 'redirect_notification',
                    $id,
                    'admin' => true,
                );

                $customDate = $this->Rumahku->formatDate($date, 'd M Y');
                $customLink = $this->Html->url($url);

                if( empty($read) ) {
                    $addClass .= 'new';
                }
                if( !empty($global) ) {
                    $addAttrLink = 'target="_blank"';
                }
?>
<li class="<?php echo $addClass; ?>">
    <a href="<?php echo $customLink; ?>" <?php echo $addAttrLink; ?>>
        <div class="short-message">
            <?php 
                    echo $this->Html->tag('div', $message, array(
                        'class' => 'message-content',
                    ));
            ?>
        </div>
    </a>
</li>
<?php
            }
        }
?>