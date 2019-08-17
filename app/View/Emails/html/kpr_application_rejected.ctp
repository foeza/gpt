<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'Kpr', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'Kpr', 'code');
		$status_KPR = $this->Rumahku->filterEmptyField($params, 'status_KPR');
		$kprBankDate = $this->Rumahku->filterEmptyField($params, $status_KPR);
		$action_date = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'action_date');
		$note = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'note');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$bank = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');
?>
<table align="center" width="600" style="background:#fff" cellpadding="0" cellspacing="0" border="0">
  	<tbody>
	  	<tr>
	  		<td style="padding: 0 20px;">
	      		<?php
	      				echo $this->Html->tag('h1', sprintf(__('Menolak aplikasi KPR %s oleh %s'), $code, $bank), array(
							'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #4a4a4a; text-align:center;margin: 20px 0;line-height: 24px;'
						));
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	      		<?php
	      				echo $this->Html->tag('h4', __('Kami informasikan bahwa bank dibawah ini:'), array(
							'style' => 'margin-top:-10px; font-weight:400; text-align:left; padding-bottom: 10px;font-size: 14px;line-height: 24px;margin: 0;'
						));
						echo $this->element('emails/html/kpr/bank');
	      		?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	      		<?php
		      			echo $this->Html->tag('h4', __('Menolak aplikasi KPR yang Anda ajukan untuk properti:'), array(
							'style' => 'font-weight:bold;text-align: left;padding-bottom:10px;font-size: 14px;margin: 0;'
						));
						echo $this->element('emails/html/properties/info');
				?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 20px;">
	      		<?php
						echo $this->element('emails/html/kpr/client');
				?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	      		<?php
		      			echo $this->Html->tag('h4', __('Alasan Menolak :'), array(
							'style' => 'font-weight:bold;text-align: left;padding-bottom:10px;font-size: 14px;margin: 0;'
						));
						echo $this->Html->tag('p', $note, array(
							'style' => 'margin:0;font-size: 16px;font-weight:400;',
						));
				?>
	    	</td>
	  	</tr>
	  	<tr>
	    	<td style="padding: 0 20px;">
	    		<?php
		      			echo $this->Html->tag('h4', __('Untuk melihat detil Aplikasi KPR Anda, klik di sini:'), array(
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
		      			echo $this->Html->link(__('Lihat Detil'), $link, array(
		      				'target' => '_blank',
							'style' => 'padding:10px 15px; background:#204798; color: #fff; text-decoration: none; border-radius: 3px; text-align: center;'
						));
	      		?>
	    	</td>
	  	</tr>
	</tbody>
</table>
