<?php

/**
 * CakePHP HybridauthComponent
 * @author mike
 */
class HybridauthComponent extends Component {
	public $hybridauth = null;
	public $adapter = null;
	public $user_profile = null;
	public $error = "no error so far";
	public $provider = null;
	public $debug_mode = false;
	public $debug_file = "";

	protected function init(){
		App::import('Vendor', 'hybridauth/Hybrid/Auth');

		$baseURL	= Router::url("/social_endpoint", true);
		$config		= array(
			'base_url'		=> $baseURL,
			'providers'		=> Configure::read('Hybridauth'),
			'debug_mode'	=> $this->debug_mode,
			'debug_file'	=> $this->debug_file,
		);

		$this->hybridauth = new Hybrid_Auth( $config );
	}
	
	/**
	 * process the 
	 * 
	 * @return string
	 */
	public function processEndpoint(){
		App::import('Vendor', 'hybridauth/Hybrid/Endpoint');
		
		if( !$this->hybridauth ) $this->init ();

		try{
			Hybrid_Endpoint::process();
		}
		catch(Exception $e){
		//	display the recived error
			$provider = Hash::get($_GET, 'hauth_done');

			$this->setExceptionError($e);
			return false;
		}
	}
	
	/**
	 * get serialized array of acctual Hybridauth from provider...
	 * 
	 * @return string
	 */
	public function getSessionData(){
		if( !$this->hybridauth ) $this->init ();
		return $this->hybridauth->getSessionData();
	}
	
	/**
	 * 
	 * @param string $hybridauth_session_data pass a serialized array stored previously
	 */
	public function restoreSessionData( $hybridauth_session_data ){
		if( !$this->hybridauth ) $this->init ();
		$hybridauth->restoreSessionData( $hybridauth_session_data );
	}
	
	/**
	 * logs you out
	 */
	public function logout(){
		if( !$this->hybridauth ) $this->init ();
		$providers = $this->hybridauth->getConnectedProviders();
		
		if( !empty( $providers ) ){
			foreach( $providers as $provider ){
				$adapter = $this->hybridauth->getAdapter($provider);
				$adapter->logout();
			}
		}
	}
	
	/**
	 * connects to a provider
	 * 
	 * 
	 * @param string $provider pass Google, Facebook etc...
	 * @return boolean wether you have been logged in or not
	 */
	public function connect($provider) {
		if(!$this->hybridauth) $this->init();

		$this->provider = $provider;

		try{
		//	try to authenticate the selected $provider
			$this->adapter = $this->hybridauth->authenticate($this->provider);
			
		//	grab the user profile
			$this->user_profile = $this->normalizeSocialProfile($provider);

			return true;
		}
		catch (Exception $e){
			$this->setExceptionError($e);
			return false;
		}
	}
	
	/**
	 * creates a social profile array based on the hybridauth profile object
	 * 
	 * 
	 * @param string $provider the provider given from hybridauth
	 * @return boolean wether you have been logged in or not
	 */
	protected function normalizeSocialProfile($provider){
		// convert our object to an array
		$incomingProfile = (Array)$this->adapter->getUserProfile();
		
		// populate our social profile
		$socialProfile['SocialProfile']['social_network_name'] = $provider;
		$socialProfile['SocialProfile']['social_network_id'] = $incomingProfile['identifier'];
		$socialProfile['SocialProfile']['email'] = $incomingProfile['email'];
		$socialProfile['SocialProfile']['display_name'] = $incomingProfile['displayName'];
		$socialProfile['SocialProfile']['first_name'] = $incomingProfile['firstName'];
		$socialProfile['SocialProfile']['last_name'] = $incomingProfile['lastName'];
		$socialProfile['SocialProfile']['link'] = $incomingProfile['profileURL'];
		$socialProfile['SocialProfile']['picture'] = $incomingProfile['photoURL'];
		$socialProfile['SocialProfile']['created'] = date('Y-m-d h:i:s');
		$socialProfile['SocialProfile']['modified'] = date('Y-m-d h:i:s');
			
		// twitter does not provide email so we need to build someting
		if($provider == 'Twitter'){
			$names = explode(' ', $socialProfile['SocialProfile']['first_name']);
			$socialProfile['SocialProfile']['first_name'] = $names[0];
			$socialProfile['SocialProfile']['last_name'] = (count($names)>1 ? end($names) : '');
			$socialProfile['SocialProfile']['display_name'] = $socialProfile['SocialProfile']['first_name'] .'_'. $socialProfile['SocialProfile']['last_name'];
			$socialProfile['SocialProfile']['email'] = $socialProfile['SocialProfile']['display_name'] .'@Twitter.com';
		}
		
		return $socialProfile;
	}

	protected function setExceptionError($e){
		if($e){
			$provider = Hash::get($_GET, 'hauth_done', $this->provider);

			switch($e->getCode()){
				case 0 :
					$this->error = "Terjadi kesalahan. Mohon coba lagi";
				break;
				case 1 :
					$this->error = "Kesalahan pada konfigurasi koneksi media sosial.";
				break;
				case 2 :
					$this->error = "Konfigurasi Provider [".$provider."] salah.";
				break;
				case 3 :
					$this->error = "Provider [".$provider."] tidak valid.";
				break;
				case 4 :
					$this->error = "Kredensial untuk [".$provider."] tidak ditemukan.";
				break;
				case 5 :
					$this->error = "Otentifikasi gagal. User membatalkan akses, atau [".$provider."] menolak akses tersebut.";
				break;
				case 6 :
					$this->error = "Gagal mendapatkan profil User. Mohon coba lagi"; // Most likely the user is not connected to the provider [" .$provider. "] and he/she should try to authenticate again.";
					$this->adapter->logout();
				break;
				case 7 :
					$this->error = "User tidak terhubung dengan [".$provider."].";
					$this->adapter->logout();
				break;
			}

		//	well, basically your should not display this to the end user, just give him a hint and move on..
			if( $this->debug_mode ){
				$this->error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
				$this->error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>"; 
			}
		}
	}
}
