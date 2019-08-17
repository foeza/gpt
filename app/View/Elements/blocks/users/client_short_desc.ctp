<?php
		$empty = '-';
        $address = $this->Rumahku->filterEmptyField($user, 'UserClient', 'address');
        $phone = $this->Rumahku->filterEmptyField($user, 'UserClient', 'phone');
        $no_hp = $this->Rumahku->filterEmptyField($user, 'UserClient', 'no_hp');
        $email = $this->Rumahku->filterEmptyField($user, 'User', 'email');

        $customAddress = $this->Rumahku->getFullAddress($user, ', ', false, false);
		$customPhone = array(
			$no_hp ? $this->Html->link($no_hp, sprintf('tel:%s', $no_hp)) : $empty, 
			$phone ? $this->Html->link($phone, sprintf('tel:%s', $phone)) : $empty, 
		);
		$customPhone = implode(' / ', $customPhone);

	//	$customPhone = false;

	//	if( !empty($phone) || !empty($no_hp) ) {
	//		$customPhone = sprintf('%s / %s', $no_hp, $phone);
	//	}
?>
<div id="client_short_desc" class="row">
    <div class="col-sm-12">
        <?php
                $contentLi = '';
                if( !empty($customAddress) ) {
                    $content = $this->Html->tag('p', __('ALAMAT'));
                    $content .= $this->Html->tag('span', $customAddress);
                    $content = $this->Rumahku->wrapTag('li', $content);
                    $contentLi .= $content;
                }
			//	if( !empty($customPhone) ) {
					$content = $this->Html->tag('p', __('HANDPHONE / TELEPON'));
					$content .= $this->Html->tag('span', $customPhone);
					$content = $this->Rumahku->wrapTag('li', $content);
					$contentLi .= $content;
			//	}
                if( !empty($email) ) {
                	$email = $this->Html->link($email, sprintf('mailto:%s', $email));

                    $content = $this->Html->tag('p', __('EMAIL'));
                    $content .= $this->Html->tag('span', $email);
                    $content = $this->Rumahku->wrapTag('li', $content);
                    $contentLi .= $content;
                }

                if( !empty($contentLi) ) {
                    $contentLi = $this->Rumahku->wrapTag('ul', $contentLi, array(
                        'class' => 'mb20',
                    ));
                    echo $contentLi;
                }
        ?>
    </div>
</div>