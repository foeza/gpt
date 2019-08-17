<?php
		if( $this->theme == 'EasyLiving' ) {
			echo $this->element('blocks/common/sub_header', array(
	            'title' => __('Syarat & Ketentuan KPR'),
	        ));
		}
?>

<div class="content">
	<div class="container">
		<div class="row">
			<div class="col-sm-9 double-pright" id="static-page">
				<?php
						echo $this->Html->tag('h1', __('Syarat dan Ketentuan KPR Bank BTN'));
				?>
					<ul>
						<?php 
								echo $this->Html->tag('li', __('WNI usia min. 21 tahun atau sudah menikah.'));
								echo $this->Html->tag('li', __('Usia pemohon tidak melebihi 65 tahun pada saat kredit lunas.'));
								echo $this->Html->tag('li', __('Memiliki penghasilan yang menurut Bank dapat menjamin kelangsungan pembayaran angsuran sampai dengan kredit lunas.'));
								echo $this->Html->tag('li', __('Bank memperlakukan debitur atau nasabah suami dan istri sebagai satu debitur atau nasabah kecuali terdapat perjanjian pisah harta yang disahkan / dilegalisasi oleh Notaris.'));
								echo $this->Html->tag('li', __('Terdapat Surat Pernyataan Debitur & pasangan.'));
								echo $this->Html->tag('li', __('Maksimal angsuran per bulan sebesar 70% take home pay.'));
								echo $this->Html->tag('li', __('Luas tanah minimal 60 M<sup>2</sup>.'));
								echo $this->Html->tag('li', __('Maksimal angsuran per bulan sebesar 70% take home pay.'));
								echo $this->Html->tag('li', __('Untuk rumah tinggal yang berada di luas lingkungan perumahan, jalan lingkungan di depan rumah minimal dapat dilalui kendaraan roda 4.'));
								echo $this->Html->tag('li', __('Khusus pengajuan kurs USD maka nilai tukar USD terhadap IDR hanya berdasarkan perkiraan yang mendekati nilai sebenarnya. Dan nilai tukar USD bisa berubah sewaktu - waktu'));
								echo $this->Html->tag('li', __('Maksimal angsuran per bulan sebesar 70% take home pay.'));
						?>
					</ul>

				<?php
						echo $this->Html->tag('h3', __('Biaya Proses'));
				?>
					<ul>
						<?php
								echo $this->Html->tag('li', __('Biaya Provisi 1%.'));
								echo $this->Html->tag('li', __('Biaya Administrasi.'));
								echo $this->Html->tag('li', __('Biaya Notaris dan Hak Tanggungan.'));
								echo $this->Html->tag('li', __('Premi Asuransi Jiwa Kredit & Kebakaran.'));
								echo $this->Html->tag('li', __('Biaya Appraisal.'));
						?>
					</ul>

				<?php
						echo $this->Html->tag('h3', __('Untuk Penjual / Pembeli'));
				?>
					<ul>
						<?php
								echo $this->Html->tag('li', __('Konfirmasi untuk memastikan bahwa yang bersangkutan adalah benar pemilik agunan dan berniat menjual agunan tersebut.'));
								echo $this->Html->tag('li', __('Menanyakan harga penawaran / jual beli yang sesuai dengan kondisi rumah yang di jual. Menanyakan seberapa besar kebutuhan penjual (mendesak atau tidak) mengapa untuk segera melepas rumahnya.'));
								echo $this->Html->tag('li', sprintf("%s<br>%s",__('Bagaimana fasilitas infrastruktur di lokasi tersebut?'), __('Melakukan survei sendiri. Semakin baik dan lengkap fasilitas yang ada, semakin bernilai pula rumah yang diincar. (memastikan apakah penjual benar - benar menguasai lokasi rumah tersebut).')));
								echo $this->Html->tag('li', sprintf("%s<br>%s",__('Bagaimana status rumah tersebut?'), __('Status rumah harus ditanyakan kepada penjual. yang bermasalah dalam hal kepemilikan atau dalam sengketa akan rumit urusan jual belinya, membeli rumah seperti ini mengandung risiko tinggi.')));

								echo $this->Html->tag('li', __('Tanyakan rumah yang akan di beli di peruntukkan untuk ?'));
								echo $this->Html->tag('li', __('Tanyakan motivasi pembelian rumah tersebut untuk ?'));
						?>
					</ul>
			</div>
		</div>
	</div>
</div>