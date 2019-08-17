<?php
class ToUser extends AppModel {
	var $name = 'ToUser';
	public $useTable = 'users';

	/*mesti ada ketika mau pake ACL*/
	public $actsAs = array('Acl' => array('type' => 'requester', 'enabled' => false));

	public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        }
        return array('Group' => array('id' => $groupId));
    }
    /*end mesti ada ketika mau pake ACL*/

	var $hasOne = array(
		'UserConfig' => array(
			'className' => 'UserConfig',
			'foreignKey' => 'user_id',
		),
		'UserProfile' => array(
			'className' => 'UserProfile',
			'foreignKey' => 'user_id'
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'user_id'
		),
		'UserSetting' => array(
			'className' => 'UserSetting',
			'foreignKey' => 'user_id'
		),
		'UserCompanyConfig' => array(
			'className' => 'UserCompanyConfig',
			'foreignKey' => 'user_id'
		),
		'UserCompanySetting' => array(
			'className' => 'UserCompanySetting',
			'foreignKey' => 'user_id'
		),
		'UserCompanyLauncher' => array(
			'className' => 'UserCompanyLauncher',
			'foreignKey' => 'user_id'
		),
	);

	var $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id'
		),
		'ClientType' => array(
			'className' => 'ClientType',
			'foreignKey' => 'client_type_id',
		),
		'Parent' => array(
			'className' => 'User',
			'foreignKey' => 'parent_id'
		),
	);

	var $hasMany = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'to_id'
		),
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'user_id'
		),
		// 'KprApplicationRequest' => array(
		// 	'className' => 'KprApplicationRequest',
		// 	'foreignKey' => 'user_id'
		// ),
		'UserRemoveAgent' => array(
			'className' => 'UserRemoveAgent',
			'foreignKey' => 'user_id'
		),
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'user_id'
		),
		'UserClientType' => array(
			'className' => 'UserClientType',
			'foreignKey' => 'user_id'
		),
		'UserPropertyType' => array(
			'className' => 'UserPropertyType',
			'foreignKey' => 'user_id'
		),
		'UserSpecialist' => array(
			'className' => 'UserSpecialist',
			'foreignKey' => 'user_id'
		),
		'UserLanguage' => array(
			'className' => 'UserLanguage',
			'foreignKey' => 'user_id'
		),
		'UserAgentCertificate' => array(
			'className' => 'UserAgentCertificate',
			'foreignKey' => 'user_id'
		),
		'Advice' => array(
			'className' => 'Advice',
			'foreignKey' => 'user_id'
		),
		'Partnership' => array(
			'className' => 'Partnership',
			'foreignKey' => 'user_id'
		),
		'UserView' => array(
			'className' => 'UserView',
			'foreignKey' => 'user_id'
		),
		'PasswordReset' => array(
			'className' => 'PasswordReset',
			'foreignKey' => 'user_id'
		),
		'UserCompanyEbrochure' => array(
			'className' => 'UserCompanyEbrochure',
			'foreignKey' => 'user_id'
		),
		'Notification' => array(
			'className' => 'Notification',
			'foreignKey' => 'user_id'
		),
		'Kpr' => array(
			'className' => 'Kpr',
			'foreignKey' => 'user_id'
		),
		'LogKpr' => array(
			'className' => 'LogKpr',
			'foreignKey' => 'user_id'
		),
		'EbrosurRequest' => array(
			'className' => 'EbrosurRequest',
			'foreignKey' => 'user_id'
		),
		'CrmProject' => array(
			'className' => 'CrmProject',
			'foreignKey' => 'user_id'
		),
		'UserClientRelation' => array(
			'className' => 'UserClientRelation',
			'foreignKey' => 'user_id'
		),
		'MailchimpCampaign' => array(
			'className' => 'MailchimpCampaign',
			'foreignKey' => 'user_id'
		),
		'UserClient' => array(
			'className' => 'UserClient',
			'foreignKey' => 'user_id'
		),
		'MailchimpPersonalCampaign' => array(
			'className' => 'MailchimpPersonalCampaign',
			'foreignKey' => 'user_id'
		),
		'CoBrokeUser' => array(
			'className' => 'CoBrokeUser',
			'foreignKey' => 'user_id'
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'user_id',
		),
	);

	var $validate = array(
		'photo' => array(
			'valPhoto' => array(
				'rule' => array('valPhoto'),
				'message' => 'Mohon unggah foto profil Anda.',
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Username harap diisi',
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Username telah terdaftar',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'Panjang username maksimal 15 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 3),
				'message' => 'Panjang username minimal 3 karakter',
			),
			'validateSlug' => array(
				'rule' => array('validateSlug'),
				'message' => 'Karakter yang diijinkan hanya huruf, angka, ".", "-" dan harus diawali serta diakhiri dengan huruf atau angka'
			),
			'validateUsername' => array(
				'rule' => array('validateUsername'),
				'message' => 'Anda telah melakukan perubahan username sebelumnya. Silahkan hubungi Administrator Kami untuk informasi lebih detail.'
			),
		),
		'full_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama lengkap harap diisi',
			),
			// 'maxLength' => array(
			// 	'rule' => array('maxLength', 50),
			// 	'message' => 'Panjang nama lengkap maksimal 50 karakter',
			// ),
			// 'minLength' => array(
			// 	'rule' => array('minLength', 3),
			// 	'message' => 'Panjang nama lengkap minimal 3 karakter',
			// ), 
			'alphabetSpace' => array(
				'rule' => array('custom', '/^[a-zA-Z ]*$/i'),
				'message' => 'Nama Lengkap hanya boleh mengandung karakter alphabet dan spasi.',
			), 
		),
		'gender_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis Kelamin harap diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Format Jenis Kelamin harus angka',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Email telah terdaftar',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'agent_pic_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'agent_pic_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email agen yang Anda masukkan tidak terdaftar.',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Password harap diisi',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 64),
				'message' => 'Panjang password maksimal 64 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Panjang password minimal 6 karakter',
			),
		),
		'current_password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan password Anda',
			),
			'checkCurrentPassword' => array(
				'rule' => array('checkCurrentPassword'),
				'message' => 'Password lama Anda salah',
			),
		),
		'password_confirmation' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Konfirmasi password harap diisi',
			),
			'notMatch' => array(
				'rule' => array('matchPasswords'),
				'message' => 'Konfirmasi password anda tidak sesuai',
			),
		),
		'new_password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				// 'on' => 'update',
				'message' => 'Password baru harap diisi',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				// 'on' => 'update',
				'message' => 'Panjang password baru minimal 6 karakter',
			),
		),
		'new_password_confirmation' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Konfirmasi password baru harap diisi',
			),
			'matchNewPasswords' => array(
				'rule' => array('matchNewPasswords'),
				'message' => 'Konfirmasi password anda tidak sesuai',
			),
		),
		'forgot_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Silahkan masukkan Email Anda',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format Email salah',
			),
			'forgot_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email yang Anda masukkan belum terdaftar atau Anda belum mengaktifkan akun ini.',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih tipe akun Anda.',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih tipe akun Anda.',
			),
		),
		'client_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih tipe klien',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih tipe klien',
			),
		),
		'parent_email' => array(
			'validateUserEmail' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email yang Anda masukkan tidak terdaftar.',
			),
			'validateParent' => array(
				'rule' => array('validateParent'),
				'message' => 'Email tidak boleh kosong.',
			),
		),
	);

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->virtualFields['full_name'] = sprintf('CONCAT(%s.first_name, " ", IFNULL(%s.last_name, \'\'))', $this->alias, $this->alias);
    	$this->virtualFields['client_email'] = sprintf('CONCAT(%s.email, \' | \', %s.first_name, \' \', IFNULL(%s.last_name, \'\'))', $this->alias, $this->alias, $this->alias);
	}
}
?>