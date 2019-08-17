<div class="row">
    <div class="col-sm-12">
        <?php
                if( !empty($values) ) {
                    $contentNotif = '';
                    foreach( $values as $key => $value ) {

                        $id = $this->Rumahku->filterEmptyField($value, 'Notification', 'id');
                        $message = $this->Rumahku->filterEmptyField($value, 'Notification', 'name');
                        $date = $this->Rumahku->filterEmptyField($value, 'Notification', 'created');
                        $read = $this->Rumahku->filterEmptyField($value, 'Notification', 'read');
                        $link = $this->Rumahku->filterEmptyField($value, 'Notification', 'link');

                        $addClass = '';
                        $url = array(
                            'controller' => 'users',
                            'action' => 'redirect_notification',
                            $id,
                            'admin' => true,
                        );

                        $customDate = $this->Rumahku->formatDate($date, 'd M Y, H:i');
                        $customLink = $this->Html->url($url);

                        if( empty($read) ) {
                            $addClass .= 'new';
                        }

                        $content = $this->Html->tag('p', $customDate);
                        $content .= $this->Html->tag('span', sprintf("%s. ",$message));
                        if( !empty($link) ) {
                            $content .= $this->Html->link(__('Lihat Detail'), $customLink);
                        }
                        $content = $this->Rumahku->wrapTag('li', $content, array(
                            'class' => $addClass,
                        ));

                        $contentNotif .= $content;
                    }

                    $contentNotif = $this->Rumahku->wrapTag('ul', $contentNotif);
                    echo $this->Html->tag('div', $contentNotif, array(
                        'id' => 'notification-list'
                    ));

                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia'), array(
                        'class' => 'alert alert-warning'
                    ));
                }
        ?>
    </div>
</div>
<?php 
        echo $this->element('blocks/common/pagination');
?>