<?php

App::uses('AppModel', 'Model');

class UserHistory extends AppModel {
	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id',
		),
		'Group' => array(
			'foreignKey' => 'group_id',
		),
		'Principle' => array(
			'className'		=> 'User', 
			'foreignKey'	=> 'principle_id',
		),
	);

	public function doSave($data = array()){
		$data		= (array) $data;
		$status		= 'error';
		$message	= 'Data yang Anda masukkan tidak valid';

		$recordID = Common::hashEmptyField($data, $this->alias.'.id');

		if($data){
			if($this->saveAll($data)){
				$recordID	= $this->id;
				$status		= 'success';
				$message	= __('Berhasil menyimpan data');
			}
			else{
				$message = __('Gagal menyimpan data');
			}
		}

		$result = array(
			'status'	=> $status,
			'msg'		=> $message,
			'data'		=> $data, 
			'id'		=> $recordID,
			'Log'		=> array(
				'document_id'	=> $recordID,
				'activity'		=> $message,
				'error'			=> $status == 'error', 
			),
		);

		if($this->validationErrors){
			$result = Hash::insert($result, 'validationErrors', $this->validationErrors);
		}

		return $result;
	}

	public function getData($find = 'all', $options = array(), $elements = array()){
		$defaultOptions = array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array(
				$this->alias.'.created' => 'desc', 
			),
		);

		return $this->merge_options($defaultOptions, $options, $find);
	}
}