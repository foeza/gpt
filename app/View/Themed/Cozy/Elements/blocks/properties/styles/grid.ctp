<?php 
        $linkOpt = $this->Rumahku->_callIsDirector()?array(
            'target' => '_blank',
        ):array();

        if( empty($price) ) {
            $price = '&nbsp;';
        }

?>
<div class="item <?php echo $_class; ?>" <?php echo $_attributes; ?>>
    <div class="image">
        <?php
                $content = $this->Html->tag('h3', $title, array('class' => 'title-default'));

                echo $this->Html->link($this->Html->tag('div', $content, array(
                    'class' => 'header-title',
                )), $url, array_merge($linkOpt, array(
                    'class' => 'info',
                    'escape' => false
                )));
                echo $photo;

                if (!empty($status_listing)) {
                    $name = $status_listing['name'];
                    $badge_color = $status_listing['badge_color'];
                    echo $this->Html->tag('span', $name, array(
                        'class' => 'status-property status-listing',
                        'style' => 'background-color:'.$badge_color.';',
                    ));
                }
        ?>
    </div>
    <div class="price">
        <?php
                echo $this->Html->tag('span', $price);
        ?>
    </div>
    <div class="short-desc visible-xs-block">
        <?php
                echo $this->Html->tag('p', $title, array(
                    'class' => 'fbold',
                ));
                echo $label;
        ?>
    </div>
    <?php 
            echo $spec; 
    ?>
</div>