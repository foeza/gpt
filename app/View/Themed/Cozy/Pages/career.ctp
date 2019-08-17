<?php 
        $this->Html->addCrumb($module_title);
?>
<!-- BEGIN HIGHLIGHT -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="main col-sm-8 center">
                <?php
                    if( !empty($values) ) {
                                     ?>
                    <ul class="career">
                        <?php
                            foreach($values as $value) {

                                $name = $this->Rumahku->filterEmptyField($value, 'Career', 'name');
                                $email = $this->Rumahku->filterEmptyField($value, 'Career', 'email');
                                $description = $this->Rumahku->filterEmptyField($value, 'Career', 'description', false, false);
                                $requirements = $this->Rumahku->filterEmptyField($value, 'CareerRequirement');
                                
                                // $description = str_replace(PHP_EOL, '<br>', $description);
                                $customLink = $this->Html->link($email, sprintf('mailto:%s', $email), array(
                                    'escape' => false,
                                ));

                                $content = $this->Html->tag('h2', $name, array(
                                    'class' => 'section-highlight'
                                ));
                                $content .= $description;
                          ?>
                        <li>
                        <?php
                            echo $content;

                            if( !empty($requirements) ) {
                                       ?>
                                <div class="row">
                                    <div class="col-sm-10 center">
                                        <div class="requirements">
                                            <?php
                                                    echo $this->Html->tag('h3', __('Requirements:'));
                                                    $content_req = '';
                                                    foreach( $requirements as $req_value ) {
                                                        $requirement = $this->Rumahku->filterEmptyField($req_value, 'name');
                                                        $content_req .= $this->Html->tag('li', $requirement);
                                                    }
                                                    $content_req = $this->Rumahku->wrapTag('ul', $content_req);
                                                    echo $content_req;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                        <?php   }
                            echo $this->Html->tag('p', sprintf('%s %s', __('Jika Anda tertarik dan memenuhi kriteria, kirim CV Anda ke'), $customLink));
                                           ?>
                            <div class="row">
                                <div class="col-sm-4 center">
                                    <?php
                                        echo $this->Html->link(__('Apply Sekarang'), sprintf('mailto:%s', $email), array(
                                            'escape' => false,
                                            'class' => 'btn btn-fullcolor',
                                        ));
                                    ?>
                                </div>
                            </div>
                        </li>
                        <?php
                            }
                        ?>
                    </ul>
                    <?php echo $this->element('custom_pagination');

                    } else {
                        echo $this->Html->tag('div', __('Karir belum tersedia.'), array(
                            'class' => 'alert alert-danger',
                        ));
                    }

                    echo $this->Html->div('hidden-print', $this->element('blocks/common/share', array(
                        'share_type' => 'career',
                        'title' => __('Karir'),
                        'url' => $this->Html->url($this->here, true),
                        '_print' => false,
                    )));
                ?>
            </div>
        </div>
    </div>
</div>
<!-- END HIGHLIGHT -->