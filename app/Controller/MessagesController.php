<?php
App::uses('AppController', 'Controller');

class MessagesController extends AppController {
	public $name = 'Messages';
	public $uses = array(
		'Message'
	);
	public $components = array(
		'RmMessage',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'admin_index' => array(
				  	'extract' => array(
				  		'paging', 'msg', 'status', 'link', 'appversion', 'device',
				     	'data'
				 	),
			 	),
			 	'admin_delete' => array(
			 		'extract' => array(
			 			'msg', 'status', 'link', 'appversion', 'device'
			 		)
			 	),
			 	'admin_read' => array(
			 		'extract' => array(
				  		'paging', 'msg', 'status', 'link', 'appversion', 'device',
				     	'readMessages'
				 	),
			 	),
			 	'admin_reply' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_write' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
    		),
    	),
	);

	function beforeFilter() {
		parent::beforeFilter();

    	$this->limit = Configure::read('__Site.config_admin_pagination');

    	$this->set('class_body', 'wrapper-inbox');
    	$this->set('_flash', false);
	}

	function admin_search ( $action = 'index', $addParam = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => true,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function admin_filter ( $action = 'index' ) {
		$params = $this->RmCommon->processRefine($this->request->data);
		$params['controller'] = 'messages';
		$params['action'] = $action;
		$params['admin'] = true;

		$this->redirect($params);
	}

	function dataContent () {
		$keyword = $this->RmCommon->getRefine($this->params, 'keyword');
		$numPage = $this->RmCommon->getRefine($this->params, 'page');
		
		$this->Message->virtualFields['max_id'] = 'MAX(Message.id)';

		$options = array(
			'group' => array(
				'Message.cnt_group',
			),
			'limit' => $this->limit,
			'order' => array(
				'Message.max_id' => 'DESC'
			)
		);

		if( !empty($keyword) ) {
			$options['conditions'] = array(
				'OR' => array(
					'Message.name LIKE' => '%'.$keyword.'%',
					'Message.message LIKE' => '%'.$keyword.'%',
					'Message.email LIKE' => '%'.$keyword.'%',
				),
			);
		}

		$this->paginate = $this->Message->getData('paginate', $options, array(
			'mine' => true,
		));
		$messages = $this->paginate('Message');

		unset($this->Message->virtualFields['max_id']);
		
		$data = $messages = $this->Message->getDataList($messages);
		
		$data = $this->RmMessage->formatListRest($data);

		$this->set(compact(
			'messages', 'data'
		));

		if( !empty($this->is_ajax) && !empty($numPage) && !$this->Rest->isActive()) {
			$this->render('/Elements/blocks/messages/items');
		}

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

    function admin_index() {
		$this->set('module_title', __('Pesan'));
		$this->set('_widget_help', false);
		$this->dataContent();
	}

	function admin_read( $from_id = false, $to_id = false, $is_read = true ) {
		$this->set('module_title', __('Baca Pesan'));
		$this->set('_widget_help', false);

		if( $this->RmCommon->_callCheckAccessMsg($from_id, $to_id) ) {
			$fromUser = $this->User->getMerge(array(), $from_id);

			$order_diff = 'ASC';
			if($this->Rest->isActive()){
				$order_diff = 'DESC';
			}

			$default_options = array(
				'conditions'=> array(
					'OR' => array(
						array(
							'Message.from_id' => $from_id,
							'Message.to_id' => $to_id,
						),
						array(
							'Message.from_id' => $to_id,
							'Message.to_id' => $from_id,
						),
					),
				),
				'order' => array(
					'Message.created' => $order_diff,
					'Message.id' => $order_diff,
				),
			);

			if($this->Rest->isActive()){
				$this->paginate = $this->Message->getData('paginate', $default_options);
				$readMessages = $this->paginate('Message');
			}else{
				$readMessages = $this->Message->getData('all', $default_options);
			}

			if( !empty($readMessages) ) {
				$readMessages = $this->Message->getDataList($readMessages);
				$send_from = $from_id;
				$send_to = $to_id;
				$admin_rku = Configure::read('User.Admin.Rumahku');

				$urlBack = array(
					'controller' => 'messages',
					'action' => 'index',
					'admin' => true,
				);

				if($admin_rku){
					$admin_id = $this->User->getAgents( $this->parent_id, true, 'list', false, array('role' => 'admin') );
				}else{
					$admin_id = array();
				}

				if($is_read){
					$this->Message->doRead($from_id, $to_id);	
				}

				$notificationMessages = $this->User->Message->getNotif();

				if($this->Rest->isActive()){
					$readMessages = $this->RmMessage->formatMessage($from_id, $to_id, $readMessages, $admin_id);
				}

				$this->set(compact(
					'readMessages', 'send_from', 'fromUser',
					'urlBack', 'send_to', 'admin_id', 'notificationMessages'
				));

				$this->dataContent();

				if($this->is_ajax){
					$this->layout = 'admin';
				}
			} else {
				$this->RmCommon->redirectReferer(__('Pesan tidak ditemukan'), 'error', array(
					'action' => 'index',
					'admin' => true,
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Pesan tidak ditemukan'), 'error', array(
				'action' => 'index',
				'admin' => true,
			));
		}

		$this->RmCommon->renderRest(array(
			'is_paging' => true,
			'params' => array(
				$from_id, 
				$to_id
			)
		));
	}

	function admin_reply( $send_from = false, $send_to = false ) {
		if( $this->RmCommon->_callCheckAccessMsg($send_from, $send_to) ) {
			$result = $this->Message->doSave( $this->request->data, $send_from, $send_to );

			/*FCM Notifier*/
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$message = $this->RmCommon->filterEmptyField($result, 'msg');

			if(!empty($status) && $status == 'success'){
				$this->RmCommon->mobileNotif($message, $send_to);
			}
			/*FCM Notifier*/

			$this->RmCommon->setProcessParams($result, array(
				'action' => 'read',
				$send_from,
				$send_to,
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));

			$this->admin_read($send_from, $send_to, false);
			$this->render('admin_read');
		} else {
			$this->RmCommon->redirectReferer(__('Pesan tidak ditemukan'), 'error', array(
				'action' => 'index',
				'admin' => true,
			));
		}
	}

	function admin_write() {
		$this->set('module_title', __('Tulis Pesan'));
		$this->set('_widget_help', false);
		
		$result = $this->Message->doSave( $this->request->data );
		$send_to = $this->RmCommon->filterEmptyField($result, 'id');
		$from_id = $this->RmCommon->filterEmptyField($result, 'from_id');

		/*FCM Notifier*/
		$status = $this->RmCommon->filterEmptyField($result, 'status');
		$message = $this->RmCommon->filterEmptyField($result, 'msg');

		if(!empty($status) && $status == 'success'){
			$this->RmCommon->mobileNotif($message, $send_to);
		}
		/*FCM Notifier*/

		$urlBack = array(
			'controller' => 'messages',
			'action' => 'index',
			'admin' => true,
		);
		$this->set('write_message', true);
		$this->set(compact(
			'urlBack'
		));

		$this->RmCommon->setProcessParams($result, array(
			'action' => 'read',
			$from_id,
			$send_to,
			'admin' => true,
		), array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		$this->dataContent();
	}

	function admin_delete( $from_id = false, $to_id = false ) {
		if( $this->RmCommon->_callCheckAccessMsg($from_id, $to_id) ) {
			$messages = $this->Message->getData('all', array(
				'conditions'=> array(
					'OR' => array(
						array(
							'Message.from_id' => $from_id,
							'Message.to_id' => $to_id,
						),
						array(
							'Message.from_id' => $to_id,
							'Message.to_id' => $from_id,
						),
					),
				),
			));

			$result = $this->Message->MessageTrash->doMoveToTrash($messages);

			$this->RmCommon->setProcessParams($result, array(
				'action' => 'index',
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Pesan tidak ditemukan'), 'error', array(
				'action' => 'index',
				'admin' => true,
			));
		}

		$this->RmCommon->renderRest();
	}

	public function admin_info( $recordID = NULL ) {
		$title = __('Daftar Pesan (Hot Leads)');
		$user = $this->RmUser->getUser($recordID);

		if( !empty($user) ) {
			$values = array();
        	$this->RmCommon->_callRefineParams($this->params);
			$this->RmUser->_callRoleActiveMenu($user);

			$options = $this->RmMessage->_callRoleCondition($user);

			if(!empty($options)){
				$options = $this->Message->_callRefineParams($this->params, array_merge_recursive($options, array(
					'order' => array(
						'Message.id' => 'DESC',
					),
	            	'limit' => Configure::read('__Site.config_admin_pagination'),
				)));
				$this->paginate	= $this->Message->getData('paginate', $options, array(
					'mine' => false,
				));
				$values = $this->paginate('Message');
			}


			if( !empty($values) ) {
				foreach ($values as $key => &$value) {
					$value = $this->Message->getMergeList($value, array(
						'contain' => array(
							'User',
							'ToUser' => array(
								'uses' => 'User',
								'foreignKey' => 'to_id',
								'primaryKey' => 'id',
								'elements' => array(
									'status' => 'all',
								),
							),
							'Property',
						),
					));

					$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
					$parent_id = $this->RmCommon->filterEmptyField($value, 'ToUser', 'parent_id', $parent_id);

					$value = $this->User->Property->getMergeList($value, array(
						'contain' => array(
							'UserAgent' => array(
								'uses' => 'User',
								'foreignKey' => 'from_id',
								'primaryKey' => 'id',
								'elements' => array(
									'status' => 'all',
								),
							),
						),
					));

					$parent_id = $this->RmCommon->filterEmptyField($value, 'UserAgent', 'parent_id', $parent_id);
					$value = $this->User->UserCompany->getMerge($value, $parent_id);
				}
			}

			$this->RmCommon->_layout_file('freeze');
			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'values' => $values,
				'currUser' => $user,
				'recordID' => $recordID,
				'active_tab' => 'Pesan (Hot Leads)',
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}
}
?>