<?php
		// debug($packages);die();
		$packages = !empty($packages)?$packages:false;
		$value 	  = !empty($value)?$value:false;

		if ($packages && $value) {

			$status_msg 	 = Common::hashEmptyField($packages, 'status');

			$package_name 	 = Common::hashEmptyField($packages, 'MembershipPackage.name', false);
			$package_status	 = Common::hashEmptyField($packages, 'MembershipPackage.status');
			$package_deleted = Common::hashEmptyField($packages, 'MembershipPackage.deleted');
			$limit_property  = Common::hashEmptyField($packages, 'MembershipPackage.limit_premium_property');

			// agent info
			$property_mine   = Common::hashEmptyField($value, 'PropertyPremium', 0);
			$email_agent   	 = Common::hashEmptyField($value, 'User.email');

			$search_premium  = '';
			if ($property_mine > 0) {
				$url_search = $this->Html->url(array(
					'controller' => 'properties',
					'action' 	 => 'index',
					'keyword' 	 => $email_agent,
					'status' 	 => 'premium',
					'filter' 	 => 'property_updated-desc',
					'admin' => true,
				));
				$search_premium  = $this->Html->link(__('( Lihat Premium Properti )'), $url_search, array(
					'escape' => false,
					'target' => '_blank',
				));
			}

			$info_property_mine = sprintf('%s %s', $property_mine, $search_premium);

			// available quota premium
			$quota_summary 	 = ($limit_property - $property_mine);

			// init block premium listing
			if ( $package_status && empty($package_deleted) ) {
				$label = __('Aktif');
				$badge = 'success';
			} elseif ( $package_deleted ) {
				$label = __('Dihapus');
				$badge = 'danger';
			} elseif ( empty($package_status) ) {
				$label = __('Non-Aktif');
				$badge = 'warning';
			} else {
				$label = $badge = '';
			}

			$badge = $this->Html->tag('span', $label, array(
				'class' => sprintf('badge badge-%s', $badge),
			));

			if ( $status_msg != 'error' && !empty($package_name) ) {

				echo $this->Html->tag('h3', __('Status Premium Listing'), array(
					'class' => 'mt15',
				));

				echo $this->Html->tag('div', 
					$this->Html->tag('div',
						$this->Html->tag('label', __('Paket Membership (RKU)')), array(
						'class' => 'col-xs-12 col-md-4'
					)).
					$this->Html->tag('div', 
						$this->Html->tag('p', $package_name, array(
							'class' => 'form-control-static'
						)), array(
						'class' => 'col-xs-12 col-md-8'
					)), array(
					'class' => 'row form-group-static'
				));

				echo $this->Html->tag('div', 
					$this->Html->tag('div',
						$this->Html->tag('label', __('Status Paket')), array(
						'class' => 'col-xs-12 col-md-4'
					)).
					$this->Html->tag('div', 
						$this->Html->tag('p', $badge, array(
							'class' => 'form-control-static'
						)), array(
						'class' => 'col-xs-12 col-md-8'
					)), array(
					'class' => 'row form-group-static'
				));

				echo $this->Html->tag('div', 
					$this->Html->tag('div',
						$this->Html->tag('label', __('Max Premium Listing')), array(
						'class' => 'col-xs-12 col-md-4'
					)).
					$this->Html->tag('div', 
						$this->Html->tag('p', $limit_property, array(
							'class' => 'form-control-static'
						)), array(
						'class' => 'col-xs-12 col-md-8'
					)), array(
					'class' => 'row form-group-static'
				));

				echo $this->Html->tag('div', 
					$this->Html->tag('div',
						$this->Html->tag('label', __('Premium Digunakan')), array(
						'class' => 'col-xs-12 col-md-4'
					)).
					$this->Html->tag('div', 
						$this->Html->tag('p', $info_property_mine, array(
							'class' => 'form-control-static'
						)), array(
						'class' => 'col-xs-12 col-md-8'
					)), array(
					'class' => 'row form-group-static'
				));

				echo $this->Html->tag('div', 
					$this->Html->tag('div',
						$this->Html->tag('label', __('Sisa Kuota Premium')), array(
						'class' => 'col-xs-12 col-md-4'
					)).
					$this->Html->tag('div', 
						$this->Html->tag('p', $quota_summary, array(
							'class' => 'form-control-static'
						)), array(
						'class' => 'col-xs-12 col-md-8'
					)), array(
					'class' => 'row form-group-static'
				));
			
			}

		}
		
?>