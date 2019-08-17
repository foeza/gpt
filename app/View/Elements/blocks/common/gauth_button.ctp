<?php

	$globalGauth	= Configure::read('__Site.gauth');
	$clientID		= Common::hashEmptyField($globalGauth, 'client_id');
	$clientScopes	= Common::hashEmptyField($globalGauth, 'client_scopes');
	$clientRedirect	= Common::hashEmptyField($globalGauth, 'client_redirect');

	$options		= empty($options) ? array() : $options;
	$popup			= Common::hashEmptyField($options, 'popup');
	$clientScopes	= Common::hashEmptyField($options, 'client_scopes', $clientScopes);
	$clientScopes	= is_array($clientScopes) ? implode(' ', $clientScopes) : $clientScopes;

	if($clientID && $clientScopes){
		$text	= Common::hashEmptyField($options, 'text', __('Masuk dengan Google'));
		$url	= 'https://accounts.google.com/o/oauth2/v2/auth';
		$params	= array(
			'client_id'		=> $clientID, 
			'scope'			=> urldecode($clientScopes), 
			'redirect_uri'	=> urldecode($clientRedirect), 
			'response_type'	=> 'code', 
			'access_type'	=> 'online', 
		);

		$params	= http_build_query($params);
		$url	= $url.'?'.$params;

		if($popup){
			$onclick = sprintf('window.open(\'%s\', \'_blank\', \'width=600,height=500,left=200,top=100\')', $url);

			echo($this->Form->button($text, array(
				'type'		=> 'button', 
				'class'		=> 'btn red mt10',
				'onclick'	=> $onclick, 
			)));
		}
		else{
			echo($this->Html->link($text, $url, array(
				'class' => 'btn red mt10', 
			)));
		}
	}

?>