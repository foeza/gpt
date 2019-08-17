<?php
class UserMembership extends AppModel {
	var $name = 'UserMembership';
	var $displayField = 'user_id';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Membership' => array(
			'className' => 'Membership',
			'foreignKey' => 'membership_id',
		)
	);

	function getMerge ( $data, $user_id, $fields=false, $is_sub = false, $is_merge = false ) {
		if(!$fields){
			$fields = '*';
		}

		if( empty($data['UserMembership']) || $is_merge ) {
			if( !empty($data['User']['parent_id']) ) {
				$data_user_id = $data['User']['parent_id'];
			} else {
				$data_user_id = $user_id;
			}

			$optionMembership = array(
				'conditions' => array(
					'UserMembership.user_id' => $data_user_id,
					'UserMembership.status' => 1,
					"UserMembership.until >= DATE_FORMAT(NOW(), '%Y-%m-%d')",
				),
			);

			$userMembership = $this->find('first', $optionMembership);
			
			if( empty($userMembership) && !empty($data['User']['parent_id']) ) {
				$optionMembership['conditions']['UserMembership.user_id'] = $data['User']['id'];
				$userMembership = $this->find('first', $optionMembership);
			}

			if( !empty($userMembership) ) {
				if( $is_sub ) {
					$data['User'] = array_merge($data['User'], $userMembership);
				} else {
					$data = array_merge($data, $userMembership);
				}

				if( empty($data['UserMembership']['Membership']) ) {
					$membership = $this->Membership->getData('first', array(
						'fields' => $fields,
						'conditions' => array(
							'Membership.id' => $userMembership['UserMembership']['membership_id'],
							'Membership.status' => 1,
						),
					));

					if( !empty($membership) ) {
						if( $is_sub ) {
							$data['User']['UserMembership'] = array_merge($data['User']['UserMembership'], $membership);
						} else {
							$data['UserMembership'] = array_merge($data['UserMembership'], $membership);
						}
					}
				}
			}
		}

		return $data;
	}
}
?>