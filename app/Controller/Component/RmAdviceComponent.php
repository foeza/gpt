<?php
class RmAdviceComponent extends Component {
	public $components = array(
		'RmCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callGeneratePhoto ( $params ) {
        $options = array(
            'conditions' => array(),
            'order' => array(
                'Advice.id' => 'ASC',
            ),
            'limit' => 1,
        );

        $user_id = $this->RmCommon->filterEmptyField($params, 'named', 'user_id');
        $id = $this->RmCommon->filterEmptyField($params, 'named', 'id');
        $min_id = $this->RmCommon->filterEmptyField($params, 'named', 'min_id');
        $limit = $this->RmCommon->filterEmptyField($params, 'named', 'limit', 10);

        if( !empty($user_id) ) {
            $options['conditions']['Advice.user_id'] = $user_id;
        }
        if( !empty($id) ) {
            $options['conditions']['Advice.id'] = $id;
        }
        if( !empty($min_id) ) {
            $options['conditions']['Advice.id >'] = $min_id;
        }
        if( !empty($limit) ) {
            $options['limit'] = $limit;
        }

        $values = $this->controller->User->Advice->find('all', $options);

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->RmCommon->filterEmptyField($value, 'Advice', 'id');
                $user_id = $this->RmCommon->filterEmptyField($value, 'Advice', 'user_id');

                $dataCompany = $this->controller->User->getDataCompany(false, array(
                    'company_principle_id' => $user_id,
                ));
                Configure::read('Config.Company.data', $dataCompany);

                $file_path = $this->RmCommon->filterEmptyField($value, 'Advice', 'photo');
                $savePath = Configure::read('__Site.advice_photo_folder');
                $result = $this->controller->RmImage->restore($file_path, $savePath);
                $status = $this->RmCommon->filterEmptyField($result, 'status');
                $message = $this->RmCommon->filterEmptyField($result, 'message');

                if( !empty($status) ) {
                    printf(__('Berhasil recreate thumbnail #%s<br><br>'), $id);
                } else {
                    printf(__('%s - #%s<br><br>'), $message, $id);

                }
            }
        } else {
            printf(__('Berita tidak tersedia<br><br>'));
        }
    }

    function saveApiDataMigrate($data){
        $data['Advice'] = $data['CompanyAdvice'];

        unset($data['CompanyAdvice']);

        $this->User = $this->controller->User;

        $email      = $this->RmCommon->filterEmptyField($data, 'User', 'email');

        $exist_user_data = $this->User->getData('first', array(
            'conditions' => array(
                'User.email' => $email
            )
        ), array(
            'status' => 'all'
        ));

        $user_id = $this->RmCommon->filterEmptyField($exist_user_data, 'User', 'id', 0);

        if(!empty($user_id)){
            if(isset($data['User'])){
                unset($data['User']);
            }

            $data = $this->RmCommon->_callUnset(array(
                'Advice' => array(
                    'id'
                )
            ), $data);

            $data['Advice']['author_id'] = $data['Advice']['user_id'] = $user_id;
            
            $result = $this->User->Advice->doSave($data, $user_id, false, false, true);

            $this->RmCommon->setProcessParams($result, false, array(
                'noRedirect' => true
            ));
        }
    }
}
?>