<?php
		$status_KPR = !empty($status_KPR)?$status_KPR:false;

		if(!empty($status_KPR)){
			$body_text = null;

			switch($status_KPR){
				case 'proposal_without_comiission' :
					$body_text = __('Mohon maaf, Anda tidak mendapatkan provisi untuk Pengajuan KPR ini. Klik %s kemudian klik %s pada list Bank terkait untuk melanjutkan proses Pengajuan KPR.', $this->Html->tag('b', __('Lihat Detail Permohonan')), $this->Html->tag('b', 'lanjutkan'));
					break;
			}

			if( !empty($body_text) ) {
				echo $body_text;
	    		echo "\n\n";
			}
		}
?>