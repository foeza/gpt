<?php 
		$_site_name = Configure::read('__Site.site_name');
		$id = $this->Rumahku->filterEmptyField($params, 'KprBank', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprBank', 'code');
		$bank_name = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$status_KPR = $this->Rumahku->filterEmptyField($params, 'status_KPR');
		$kprBankDate = $this->Rumahku->filterEmptyField($params, $status_KPR);
		$action_date = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'action_date');
		$note = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'note');
?>
<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
  	<tbody>
	  	<tr>
	  		<td style="padding: 20px 0;">
	      		<?php
		      			echo $this->Html->tag('h1', sprintf(__('Admin %s telah melanjutkan/meneruskan pengajuan KPR %s ke %s'), $_site_name, $code, $bank_name), array(
							'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #4a4a4a; text-align:center'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	  		<td>
	      		<?php
						echo $this->element('emails/html/kpr/bank');
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td>
	      		<?php
						if( !empty($mls_id) ) {
			      			echo $this->Html->tag('h4', __('Informasi Pengajuan sebagai berikut:'), array(
								'style' => 'font-weight:bold;text-align: left;padding-bottom:5px;font-size: 14px;margin: 0;'
							));
							echo $this->element('emails/html/properties/info');
						}
				?>
	    	</td>
	  	</tr>
	  	<tr>
	  		<td style="padding: 10px 0;">
	      		<?php
						echo $this->element('emails/html/kpr/client');
				?>
	    	</td>
	  	</tr>
	  	<tr>
	  		<td style="padding: 10px 0;">
	      		<?php
						echo $this->element('emails/html/kpr/info');
				?>
	    	</td>
	  	</tr>
	  	<tr>q
	    	<td>
	    		<?php
		      			echo $this->Html->tag('h4', __('Untuk melihat detil permohonan KPR Anda, klik di sini:'), array(
							'style' => 'border-top: 1px solid #ccc; font-weight:400; padding-top:20px;'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="height: 70px;text-align: center;">
	      		<?php
		      			$link = $domain.$this->Html->url(array(
							'controller' => 'kpr', 
							'action' => 'application_detail',
							$id,
							'admin' => true,
						));

		      			echo $this->Html->link(__('Lihat Detil Permohonan'), $link, array(
		      				'target' => '_blank',
							'style' => 'padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px;text-align: center;'
						));
	      		?>
	    	</td>
	  	</tr>
	</tbody>
</table>