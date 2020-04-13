<?php 
        $id     = Common::hashEmptyField($value, 'Property.id');
        $title  = Common::hashEmptyField($value, 'Property.title');
        $mls_id = Common::hashEmptyField($value, 'Property.mls_id');
        
        $label  = $this->Property->getNameCustom($value);
        $slug   = $this->Rumahku->toSlug($label);

        $url = $this->Html->url( array(
            'controller'=> 'properties', 
            'action' => 'detail',
            'mlsid' => $mls_id,
            'slug'=> $slug, 
            'admin'=> false,
        ), true);

        $facebook   = sprintf('https://www.facebook.com/share.php?u=%s?title=%s', $url, $title);
        $twitter    = sprintf('https://twitter.com/home?status=%s+%s', $title, $url);
        $linkwa     = Common::_callPhoneWA(array('text' => $url));
?>
<div class="share-wraper">
    <?php
            echo $this->Html->tag('h5', __('Bagikan Produk ini:'));
    ?>
    <ul class="social-networks">
        <?php 
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('rv4-wa'), $linkwa, array(
                    'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $id,
                        'property',
                        'whatsapp',
                        '?' => array(
                            'url' => $linkwa,
                        ),
                        'admin' => false,
                    )),
                    'data-type' => 'redirect',
                )));
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-facebook'), $facebook, array(
                    'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $id,
                        'property',
                        'facebook',
                        '?' => array(
                            'url' => $facebook,
                        ),
                        'admin' => false,
                    )),
                )));
                echo $this->Html->tag('li', $this->Html->link($this->Rumahku->icon('fa fa-twitter'), $twitter, array(
                    'escape' => false,
                    'class' => 'popup-window',
                    'data-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'share',
                        $id,
                        'property',
                        'twitter',
                        '?' => array(
                            'url' => $twitter,
                        ),
                        'admin' => false,
                    )),
                )));
        ?>
    </ul>
</div>