<?php 
		$text_align = false;
		$export = !empty($export)?$export:false;
		$print 	= !empty($print)?$print:false;

		$app_name = Configure::read('__Site.company_profile.app_name');
		$ceo = Configure::read('__Site.company_profile.ceo');

		$bank_code = $this->Rumahku->filterEmptyField($value, 'Bank', 'code');
		$alias = $this->Rumahku->filterEmptyField($value, 'Bank', 'alias');
		$agent_name = $this->Rumahku->filterEmptyField($value, 'Agent', 'full_name');
		$company_name = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'name');
		$logo_company = $this->Rumahku->filterEmptyField($value, 'UserCompany', 'logo');
		$client_name = $this->Rumahku->filterEmptyField($value, 'Kpr', 'client_name');	
		$addressCustom = $this->Property->getNameCustom($value, true);
		$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
		$title = $this->Rumahku->filterEmptyField($value, 'Property', 'title');
		// KPR
		$loan_price = $this->Rumahku->filterEmptyField($value, 'KprBankInstallment', 'loan_price');
		$code = $this->Rumahku->filterEmptyField($value, 'KprBank', 'code');
		$created = $this->Rumahku->filterEmptyField($value, 'Kpr', 'created');
		$sold_date = $this->Rumahku->filterEmptyField($value, 'Kpr', 'sold_date', $created);

		$account_name = $this->Rumahku->filterEmptyField($value, 'BankConfirmation', 'account_name');
		$account_number = $this->Rumahku->filterEmptyField($value, 'BankConfirmation', 'account_number');
		$npwp = $this->Rumahku->filterEmptyField($value, 'BankConfirmation', 'npwp');

		$propertyCustom = sprintf('%s | %s', $mls_id, $addressCustom);
		$bankCustom = strtoupper($bank_code);
		$aliasCustom = strtoupper($alias);
		$clientCustom = ucwords($client_name);
		$loanPriceCustom = $this->Rumahku->getCurrencyPrice($loan_price);
		$soldDateCustom = $this->Rumahku->getIndoDateCutom($sold_date, array(
			'type' => 'day',
		));

		$date_now = $this->Rumahku->getIndoDateCutom(date('Y-m-d'), array(
			'type' => 'day',
		));

		$companyLogo = $this->Rumahku->photo_thumbnail(array(
            'save_path' => Configure::read('__Site.logo_photo_folder'), 
            'src'=> $logo_company, 
            'size' => !empty($export) ? 'xsm' : 'xxsm',
            'fullbase' => true,
        ), array(
        	'alt' => $company_name,
        	'name' => $company_name,
        	'width' => !empty($export) ? false : '70%',
       	));
        $primeLogo = $this->Html->image(sprintf('%s.%s', FULL_BASE_URL, sprintf('/img/%s.png', !empty($export) ? 'logo' : 'primesystem')), array(
        	'alt' => 'primesystem',
        	'name' => 'primesystem',
        	'width' => !empty($export) ? '15%' : false,
        ));

		$documents = array(
			'ktp' => __('Fotocopy KTP Pemohon'),
			'ktp-spouse' => __('Fotocopy KTP Suami/Istri'),
			'kk' => __('Fotocopy Kartu Keluarga'),
			'sn' => __('Fotocopy Surat Nikah'),
			'npwp' => __('Fotocopy NPWP'),
			'siup' => __('Fotocopy SIUP'),
			'tdp' => __('Fotocopy TDP'),
			'akta-building' => __('Fotocopy Akta Pendirian/Perubahan*)'),
			'akta-ratification' => __('Fotocopy Akta Pengesahan Menkeh*)'),
			'izin-praktek' => __('Fotocopy Izin Praktek'),
			'slip-salary' => __('Asli Slip Gaji (1 bln Terakhir)/ Surat Ket Penghasilan**)'),
			'savings-3-month' => __('Fotocopy R/K atau tabungan 3 bln terakhir'),
			'recommendation' => __('surat Rekomendasi Perusahaan**)'),
			'debtor_statement' => __('Asli Surat Pernyataan Debitur Mengenai Kredit Pemilikan Properti atau Kredit Beragun Properti yang Sedang Diajukan atau Sudah Dimiliki'),
		);

		$document_homes = array(
			'sertifikat' => __('Fotocopy Sertifikat HM/HGB'),
			'ktp-spouse' => __('Fotocopy IMB'),
			'building-plans' => __('Fotocopy Denah Bangunan'),
			'sell-buy' => __('Fotocopy Akte Jual Beli'),
			'pbb' => __('Fotocopy PBB terakhir'),
		);

		$employee = array(
			'siup',
			'tdp',
			'akta-building',
			'akta-ratification',
			'izin-praktek',
		);

		$businessman = array(
			'izin-praktek',
			'slip-salary',
			'recommendation',
		);

		$profession = array(
			'siup',
			'tdp',
			'akta-building',
			'akta-ratification',
			'slip-salary',
			'recommendation',
		);

		$name_save = sprintf('coverletter_%s_%s', $mls_id, $code);

		if(!empty($export)){
			$text_align = 'style=text-align:right;';
   		 	header('Pragma: public');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	        header('Content-Type: application/ms-excel');
   		 	header(sprintf('Content-Disposition: attachment; filename=%s.xls', $name_save));
	        header('Content-Transfer-Encoding: binary');
		}

?>
<html>
	<body onload="<?php echo sprintf("document.title = '%s'", $name_save);?>;<?php echo !empty($print)?'window.print()':false;?>">
		<div id="kpr-content" class='visible-print'>
			<div>
				<table border="0" width="100%" align="center">
					<tr>
						<td colspan="8">
							<div style="float:left;">
								<?php
										echo $companyLogo;
								?>
							</div>
						</td>
						<td colspan="2" <?php echo $text_align; ?>>
							<div style="float:right;">
								<?php
										echo $primeLogo;
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<table border=0 align="center" >
				<tr>
					<td colspan=10>
						<?php
							echo $this->Html->tag('h3', sprintf('%s<br>%s', __('SURAT PENGANTAR'), __('PENGAJUAN APLIKASI KPR %s', $alias)), array(
								'style' => 'text-align:center'
							));
							// echo $this->Html->tag('h3', __('PENGAJUAN APLIKASI KPR %s', $aliasCustom), array(
							// 	'style' => 'text-align:center;margin-top:-15px;'
							// ));
						?>
					</td>
				</tr>
			</table>
			<table border = 0 width="100%" style="margin-bottom:5px;">
				<tr>
					<td colspan = 9><?php echo $this->Html->tag('strong', __('Bersama surat ini :'));?></td>
				</tr>
				<tr>
					<td width="35%" colspan = 2>Nama Marketing Associate</td>
					<td colspan= 7>: <?php echo ucwords($agent_name);?></td>
				</tr>
				<tr>
					<td width="35%" colspan = 2>Dari kantor</td>
					<td colspan= 7>: <?php echo ucwords($company_name);?></td>
				</tr>
			</table>
			<table border = 0 width="100%" style="margin-bottom:5px;">
				<tr>
					<td colspan = 9><?php echo $this->Html->tag('strong', __('Mengajukan aplikasi KPR %s untuk :', $alias));?></td>
				</tr>
				<tr>
					<td width="35%" colspan = 2>Nama calon debitur</td>
					<td colspan= 7>: <?php echo ucwords($clientCustom);?></td>
				</tr>
				<tr>
					<td colspan = 2>Rumah yang akan dibeli</td>
					<td colspan= 7>: <?php echo $propertyCustom;?></td>
				</tr>
				<tr>
					<td colspan = 2>Besar Kredit yang diajukan</td>
					<td colspan= 7>: <?php echo $loanPriceCustom;?></td>
				</tr>
				<tr>
					<td colspan = 2>Tanggal Pengajuan</td>
					<td colspan= 7>: <?php echo $soldDateCustom;?></td>
				</tr>
			</table>
			<table border=0>
				<tr>
					<td colspan = 9>
						<?php
							echo $this->Html->tag('strong', __('Kami sertakan juga kelengkapan dokumen sbb :'), array(
								'style' => 'display:block;'
							));
						?>
					</td>
				</tr>
				<tr>
					<td colspan = 9>
						<?php
							echo $this->Html->tag('i', __('(beri tanda &#8730 data yang disertakan pada kolom dibawah ini)'), array(
								'style' => 'margin-bottom:5px;'
							));
						?>
					</td>
				</tr>
			</table>
			<table border = "0" width="100%">
				<tr>
					<td width="60%">
						<table border = "1" width="100%" style="border-collapse: collapse;">
							<tr style="text-align:center;font-weight: bold;">
								<th style="border: 1px solid black;background-color:#e2e0e0;" width="5%"> <font size="2"> No. </font> </th>
								<th style="border: 1px solid black;background-color:#e2e0e0;" width="50%"> <font size="2"> Dokumen </font> </th>
								<th style="border: 1px solid black;background-color:#e2e0e0;" width="15%"> <font size="2"> Karyawan </font> </th>
								<th style="border: 1px solid black;background-color:#e2e0e0;" width="15%"> <font size="2"> Pengusaha </font> </th>
								<th style="border: 1px solid black;background-color:#e2e0e0;" width="15%"> <font size="2"> Profesi </font> </th>
							</tr>
							<?php
								if(!empty($documents)){
									$no = 1;
									foreach($documents AS $key => $document){
							?>
										<tr>
											<td style="border: 1px solid black;" align="center"><font size="2"><?php echo sprintf('%s.', $no);?></font></td>
											<td style="border: 1px solid black;"><font size="2"><?php echo $document;?></font></td>
											<td style="border: 1px solid black;<?php echo (in_array($key, $employee))?'background-color:#000;':false;?>"><font size="2"></font></td>
											<td style="border: 1px solid black;<?php echo (in_array($key, $businessman))?'background-color:#000;':false;?>"><font size="2"></font></td>
											<td style="border: 1px solid black;<?php echo (in_array($key, $profession))?'background-color:#000;':false;?>"><font size="2"></font></td>
										</tr>
							<?php
										$no++;
									}
								}
							?>
						</table>
					</td>
					<td width="1%"></td>
					<td width="35%" valign="top">
						<table border = 1 width="100%" style="border-collapse: collapse;">
							<tr style="text-align:center;font-weight: bold;">
								<td style="border: 1px solid black;background-color:#e2e0e0;" width="10%"><font size="2"> No. </font></td>
								<td style="border: 1px solid black;background-color:#e2e0e0;"><font size="2"> Dokumen Rumah </font></td>
								<td style="border: 1px solid black;background-color:#e2e0e0;"><font size="2"> Rumah baru </font></td>
								<td style="border: 1px solid black;background-color:#e2e0e0;"><font size="2"> Rumah bekas </font></td>
							</tr>
							<?php
								if(!empty($document_homes)){
									$no = 1;
									foreach($document_homes AS $key => $document_home){
							?>
										<tr>
											<td style="border: 1px solid black;" width="10%" align="center"><font size="2"><?php echo sprintf('%s.', $no);?></font></td>
											<td style="border: 1px solid black;"><font size="2"><?php echo $document_home;?></font></td>
											<td style="border: 1px solid black;<?php echo (in_array($key, array('sell-buy')))?'background-color:#000;':false;?>"><font size="2"></font></td>
											<td style="border: 1px solid black;"><font size="2"></font></td>
										</tr>
							<?php
										$no++;
									}
								}
							?>
						</table>
						<br>
						<table border = "1" width="100%" style="border-collapse: collapse;">
							<tr>
								<td colspan="2" width="30%"><font size="2">Nama PT.</font></td>
								<td colspan="2" width="70%">: <?php echo ucwords($account_name);?></td>
							</tr>
							<tr>
								<td colspan="2" width="30%"><font size="2">NPWP</font></td>
								<td colspan="2" width="70%">: <?php echo $npwp;?></td>
							</tr>
							<tr>
								<td colspan="2" width="30%"><font size="2"><?php printf('Acc. %s', $alias)?> </font></td>
								<td colspan="2" width="70%">: <?php echo $account_number;?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div style="margin-bottom:5px;">
				<table border = "0" >
					<tr>
						<td colspan = "9"><i style="display:block;"><b><font size="2">*) khusus untuk pengusaha dengan badan hukum PT.</font></b></i></td>
					</tr>
					<tr>
						<td colspan = "9"><i style="display:block;"><b><font size="2">**) untuk joint income suami dan istri dokumen yang dimasukkan adalah dokumen suami dan istri</font></b></i></td>
					</tr>
					<!-- <tr>
						<td colspan = 9><i style="display:block;"><b><font size="2">***) untuk karwyawan plafon &#8805; Rp. 100 juta wajib melampirkan</font></b></i></td>
					</tr> -->
				</table>
			</div>
			<div>
				<table border="0" width="100%">
					<tr>
						<td>
							<div style="float:left;">
								<table border = "0" >
									<tr>
										<td colspan = "4"><font size="2" style="display:block;">Jakarta, <?php echo $date_now;?></font></td>
									</tr>
									<tr>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<td colspan = "4"><font size="2" style="display:block;"><b>Member Broker</b></font></td>
									</tr>
									<tr>
										<td colspan = "4"><br><br><br>(<?php echo ucwords($agent_name);?>)<?php echo !empty($export) ? "<br><br><br>" : false; ?></td>
									</tr>
								</table>		
							</div>
						</td>
						<td valign="top" colspan="2">
							<table border = "0" >
								<tr>
									<td>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td>
										<font size="2" >Mengetahui,</font>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<div style="float:right;">
								<table border = "0" >
									<tr>
										<td colspan = "4"> &nbsp;
										</td>
									</tr>
									<tr>
										<td colspan = "4"> &nbsp;
										</td>
									</tr>
									<tr>
										<td colspan = "4" <?php echo $text_align; ?>><font size="2" style="display:block;"><?php echo empty($export) ? "&nbsp;&nbsp;&nbsp;" : false; ?><b><?php echo $app_name; ?></b></font></td>
									</tr>
									<tr>
										<td colspan = "4" <?php echo $text_align; ?>><br><br><br><?php echo sprintf('(%s)%s', $ceo, empty($export) ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : false);?><?php echo !empty($export) ? "<br><br><br>" : false; ?></td>
									</tr>
								</table>	
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
