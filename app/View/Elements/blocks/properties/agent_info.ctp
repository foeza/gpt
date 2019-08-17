<?php 
        $user_path = Configure::read('__Site.profile_photo_folder');

        $property = empty($property) ? array() : $property;
        $data_revision = empty($data_revision) ? array() : $data_revision;
        $userPhoto = $this->Rumahku->filterEmptyField($property, 'Agent', 'photo');
        $username = $this->Rumahku->filterEmptyField($property, 'Agent', 'full_name');
        $email = $this->Rumahku->filterEmptyField($property, 'Agent', 'email');
        $created = $this->Rumahku->filterEmptyField($property, 'Agent', 'created');

        $no_hp = $this->Rumahku->filterEmptyField($property, 'AgentProfile', 'no_hp');
        $no_hp_2 = $this->Rumahku->filterEmptyField($property, 'AgentProfile', 'no_hp_2');
        $phone = $this->Rumahku->filterEmptyField($property, 'AgentProfile', 'phone');

        $client_email = $this->Rumahku->filterEmptyField($property, 'Property', 'client_email');

        $customCreated = $this->Rumahku->formatDate($created, 'F Y');
        $price = $this->Property->getPrice($property);
        $price_rent = $this->Property->_callRentPrice($property);
        $userPhoto = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $user_path, 
            'src' => $userPhoto, 
            'size' => 'pxl',
        ), array(
            'class' => 'default-thumbnail',
        ));
?>
<div id="user-info">
    <?php 
            echo $this->Html->tag('div', $this->Property->getStatus($property, 'span'), array(
                'class' => 'block-lbl tacenter',
            ));

            if(!empty($price_rent)){
                $check_update = $this->Html->tag('label', $this->Rumahku->getCheckRevision('PropertyPrice', 'format_arr', $data_revision, __('Harga Sewa')));

                echo $this->Html->div('list-rent-price', $check_update.$price_rent);
            } else {
                echo $this->Html->tag('div', $this->Rumahku->getCheckRevision('Property', 'price,currency_id,period_id', $data_revision, $price), array(
                    'class' => 'property-price',
                ));
            }

            if( !empty($client_email) ) {
                $contentClient = $this->Html->tag('label', $this->Rumahku->getCheckRevision('Property', 'client_id', $data_revision, __('Klien')));
                $contentClient .= $this->Html->tag('p', $client_email);

                echo $this->Html->tag('div', $contentClient, array(
                    'class' => 'client-info',
                ));
            }

            if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() || Configure::read('User.admin') ) {
    ?>
    <div class="profile">
        <div class="clear">
            <?php 
                    echo $this->Html->tag('div', $userPhoto, array(
                        'class' => 'img',
                    ));
            ?>
            <div class="info">
                <?php 
                        echo $this->Html->tag('label', $username);
                ?>
                <ul>
                    <?php 
                            if( !empty($no_hp) ) {
                                echo $this->Html->tag('li', $this->Html->link($no_hp, 'tel:'.$no_hp));
                            }
                            if( !empty($no_hp_2) ) {
                                echo $this->Html->tag('li', $this->Html->link($no_hp_2, 'tel:'.$no_hp_2));
                            }
                            if( !empty($phone) ) {
                                echo $this->Html->tag('li', $this->Html->link($phone, 'tel:'.$phone));
                            }
                            if( !empty($email) ) {
                                echo $this->Html->tag('li', $this->Html->link($email, 'mailto:'.$email));
                            }
                            if( !empty($count_property) ) {
                                $count_property = sprintf('%s Properti', $count_property);

                                echo $this->Html->tag('li', sprintf(__('%s Tayang'), $this->Html->tag('strong', $count_property)));
                            }
                    ?>
                </ul>
            </div>
        </div>
        <?php 
                echo $this->Html->tag('div', sprintf(__('Terdaftar sejak: %s'), $customCreated), array(
                    'class' => 'date',
                ));
        ?>
    </div>
    <?php 
            }
    ?>
</div>