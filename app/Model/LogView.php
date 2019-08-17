<?php
class LogView extends AppModel {
	var $name = 'LogView';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(),
			'order'=> array(
				'LogView.created' => 'ASC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function doSave ( $data, $value, $options = array() ) {
		if( !empty($data) ){

			// set data
			$user_id = Common::hashEmptyField($value, 'User.id');
			$user_id = Common::hashEmptyField($value, 'id', $user_id);

			$group_id = Common::hashEmptyField($value, 'User.group_id');
			$group_id = Common::hashEmptyField($value, 'group_id', $group_id);

			$parent_id = Common::hashEmptyField($value, 'User.parent_id', Configure::read('Principle.id'));
			$parent_id = Common::hashEmptyField($value, 'parent_id', $parent_id);

			$data['LogView']['type'] = Common::hashEmptyField($options, 'slug');
			$data['LogView']['group_id'] = $group_id;
			$data['LogView']['user_id'] = $user_id;
			$data['LogView']['date'] = date('Y-m-d');
			$data['LogView']['time'] = date('H:i:s');
			$data['LogView']['parent_id'] = $parent_id;
			// 

			$this->create();
			$this->set($data);
			
			if($this->save()){
				return true;
			} else {
				return false;
			}
		}
	}

	function _callGetDataView ( $value, $logViewOptions = array() ) {
		$this->virtualFields['cnt'] = 'COUNT(LogView.user_id)';
		$this->virtualFields['slug'] = 'CASE WHEN LogView.type = \'daily\' THEN \'view\' ELSE LogView.type END';
		$this->virtualFields['max_date'] = 'MAX(LogView.created)';

		$logViews = $this->getData('all', array_merge_recursive($logViewOptions, array(
			'fields' => array(
				'LogView.id',
				'LogView.slug',
				'LogView.cnt',
				'LogView.max_date',
			),
			'group' => array(
				'LogView.type',
			),
		)));

		if( !empty($logViews) ) {
			foreach ($logViews as $logViews) {
				$type = Common::hashEmptyField($logViews, 'LogView.slug');
				$cnt = Common::hashEmptyField($logViews, 'LogView.cnt');
				$max_date = Common::hashEmptyField($logViews, 'LogView.max_date');

				$value['Log'.ucwords($type).'Count'] = $cnt;
				$value['Log'.ucwords($type)] = array(
					'created' => $max_date,
				);

			}
		}

		$LogLoginCount = Common::hashEmptyField($value, 'LogLoginCount', 0);
		$LogViewCount  = Common::hashEmptyField($value, 'LogViewCount', 0);

		$value['TotalActivity'] = $LogLoginCount+$LogViewCount;

		return $value;
	}
}
?>