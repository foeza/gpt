<?php
		$contactInfo = '';
		$email = $this->Rumahku->filterEmptyField($dataCompany, 'User', 'email');
        $phone = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'phone', '', true, 'formatNumber');
        $email = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'contact_email', $email);

        if(!empty($phone)){
	        $contactInfo .= $this->Html->link($this->Rumahku->icon('fa fa-phone').' '.$phone, 'tel:'.$phone, array(
	        	'escape' => false,
	        	'class' => 'topBarText'
	        ));
	    }
	    if(!empty($email)){
	        $contactInfo .= $this->Html->link($this->Rumahku->icon('fa fa-envelope').' '.$email, 'mailto:'.$email, array(
	        	'escape' => false,
	        	'class' => 'topBarText'
	        ));
	    }

?>
<div class="topBar hidden-print">
    <div class="container">
    	<?php
    			if( !empty($contactInfo) ) {
    				echo $this->Html->tag( 'div', $contactInfo, array(
    					'class' => 'contact-info-top'
    				));
    			}

    			echo $this->element('headers/social_media');
    	?>
    </div>
</div>