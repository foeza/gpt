<?php 
        $linkOpt = $this->Rumahku->_callIsDirector()?array(
            'target' => '_blank',
        ):array();
?>
<div class="item col-lg-12 col-md-12 <?php echo $_class; ?>" <?php echo $_attributes; ?>>
    <div class="propertyItem">
        <div class="propertyContent row">
            <div class="col-lg-5 col-md-4 col-sm-4">
                <?php
                        echo $this->Html->tag('span', $status, array(
                            'class' => 'status-property',
                        ));

                        if (!empty($status_listing)) {
                            $name = $status_listing['name'];
                            $badge_color = $status_listing['badge_color'];
                            echo $this->Html->tag('span', $name, array(
                                'class' => 'status-property status-listing',
                                'style' => 'background-color:'.$badge_color.';',
                            ));
                        }
                        
                        echo $this->Html->link($photo, $url, array_merge($linkOpt, array(
                            'escape' => false,
                            'class' => 'propertyImgLink'
                        )));
                ?>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-8 rowText">
                <div class="head-info">
                    <?php
                            echo $this->Html->tag('p', $type.$this->Html->tag('span', $price), array(
                                'class' => 'price',
                            ));

                            echo $this->Html->tag('h3', $this->Html->link($title, $url, $linkOpt) );

                            echo $this->Html->tag('p', $label);
                    ?>
                </div>
                <?php
                        echo $this->Html->tag('p', $this->Text->truncate($description, 300, array(
                            'ending' => '..',
                            'exact' => false
                        )));

                        echo $spec;
                ?>
            </div>
        </div>
    </div>
</div>