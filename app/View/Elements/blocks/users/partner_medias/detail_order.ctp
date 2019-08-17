<?php
		$left_column  = isset($left_column) ? $left_column : 'col-xs-12 col-sm-6 col-md-4';
		$right_column = isset($right_column) ? $right_column : 'col-xs-12 col-sm-6 col-md-8';

		$addon_r123		= isset($addon_r123) ? $addon_r123 : false;
		$addon_olx		= isset($addon_olx) ? $addon_olx : false;

		$orderNumber 	= Common::hashEmptyField($record, 'UserIntegratedOrder.order_number');
		$name 			= Common::hashEmptyField($record, 'UserIntegratedOrder.name_applicant');
		$phone 			= Common::hashEmptyField($record, 'UserIntegratedOrder.phone');
		$companyName 	= Common::hashEmptyField($record, 'UserIntegratedOrder.company_name');
		$status 		= Common::hashEmptyField($record, 'UserIntegratedOrder.status');

		$id_configure 	= Common::hashEmptyField($record, 'UserIntegratedConfig.id');
		$user_id 		= Common::hashEmptyField($record, 'UserIntegratedConfig.user_id');
		$is_verified 	= Common::hashEmptyField($record, 'UserIntegratedConfig.is_verified');
		$icon_verified 	= $this->Rumahku->_callStatusChecked($is_verified);

		if ($is_verified) {
			$lbl_icon = __('User Verified');
		} else {
			$url = array(
				'controller' => 'users',
				'action' => 'verified_user',
				'admin' => true,
				$id_configure,
				$user_id,
			);
			$lbl_icon = $this->Html->link(__('Verify Now'), $url, array(
				'class' => 'ajaxModal',
			));
		}

		$custom_label   = sprintf('%s ( %s )', $icon_verified, $lbl_icon);
		
		$is_all_addon 	= Common::hashEmptyField($record, 'UserIntegratedOrder.is_email_all_addon');

		$mail_all_addon = Common::hashEmptyField($record, 'UserIntegratedOrder.email_all_addon');
		$email_r123 	= Common::hashEmptyField($record, 'UserIntegratedOrder.email_r123');
		$email_olx 		= Common::hashEmptyField($record, 'UserIntegratedOrder.email_olx');

		$statuses = array(
			'pending'	=> $this->Html->tag('span', __('Pending'), array('class' => 'badge')), 
			'request'	=> $this->Html->tag('span', __('Request'), array('class' => 'badge badge-success')), 
			'renewal'	=> $this->Html->tag('span', __('Renewal'), array('class' => 'badge badge-info')),
			'rejected'	=> $this->Html->tag('span', __('Rejected'), array('class' => 'badge badge-danger')), 
			'expired'	=> $this->Html->tag('span', __('Expired'), array('class' => 'badge badge-inverse')), 
			'waiting'	=> $this->Html->tag('span', __('Waiting'), array('class' => 'badge badge-warning')), 
		);

		echo $this->Html->tag('h3', __('Detail Order'), array('class' => 'custom-heading'));

		$badge = !empty($statuses[$status]) ? $statuses[$status] : '-';
		$template = $this->Html->tag('div', $this->Html->tag('b', __('Status Order')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $badge, array('class' => $right_column));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Nomor Order')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $this->Html->tag('b', $orderNumber), array('class' => $right_column));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);
		
		$template = $this->Html->tag('div', $this->Html->tag('b', __('Nama')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $name, array('class' => $right_column));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		if ($is_all_addon) {
			$email = $this->Html->link($mail_all_addon, sprintf('mailto:%s', $mail_all_addon));

			$template = $this->Html->tag('div', $this->Html->tag('b', __('Email All Addon')), array('class' => $left_column));
			$template.= $this->Html->tag('div', $email, array('class' => $right_column));
			$template = $this->Html->tag('div', $template, array('class' => 'row'));
		} else {
			if ($addon_r123) {
				$email = $this->Html->link($email_r123, sprintf('mailto:%s', $email_r123));

				$template = $this->Html->tag('div', $this->Html->tag('b', __('Email Addon R123')), array('class' => $left_column));
				$template.= $this->Html->tag('div', $email, array('class' => $right_column));
				$template = $this->Html->tag('div', $template, array('class' => 'row'));
			}

			if ($addon_olx) {
				$email = $this->Html->link($email_olx, sprintf('mailto:%s', $email_olx));

				$template = $this->Html->tag('div', $this->Html->tag('b', __('Email Addon OLX')), array('class' => $left_column));
				$template.= $this->Html->tag('div', $email, array('class' => $right_column));
				$template = $this->Html->tag('div', $template, array('class' => 'row'));
			}
		}

		echo($template);

		$template = $this->Html->tag('div', $this->Html->tag('b', __('No. Telepon')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $phone, array('class' => $right_column));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Perusahaan')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $companyName, array('class' => $right_column));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		echo($this->Html->tag('hr'));
		$template = $this->Html->tag('div', $this->Html->tag('b', __('Verify User')), array('class' => $left_column));
		$template.= $this->Html->tag('div', $custom_label, array('class' => $right_column.' verified-icon'));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

?>