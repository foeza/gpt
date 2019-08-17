<?php 
		$User = !empty($User)?$User:array();
		$slide_tour = Hash::get($User, 'UserConfig.slide_tour');
		$sign_integrated = Hash::get($User, 'UserConfig.sign_integrated');
		$group_id = Hash::get($User, 'group_id');

		if( empty($slide_tour) ) {
?>
<div data-role="modal-slide-tour">
	<div id="carousel-tour-generic" class="carousel carousel-tour slide">
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<li data-target="#carousel-tour-generic" data-slide-to="0" class="active"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="1"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="2"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="3"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="4"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="5"></li>
			<li data-target="#carousel-tour-generic" data-slide-to="6"></li>
		</ol>

  		<!-- Wrapper for slides -->
  		<div class="carousel-inner" role="listbox">
    		<div class="item active">
    			<div class="tacenter">
    				<div class="head-title">
		    			<h2>Selamat Datang di Prime System</h2>
		    			<p>Prime System adalah sebuah sistem pemasaran properti yg dikhususkan untuk kantor properti.</p>
		    			<p>Terdapat berbagai fitur menarik yang dapat mendukung pemasaran properti Anda.</p>
	    			</div>
	      			<img src="/img/tours/devices.png" class="img-default">
	    			<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="1">Mulai</a>
      			</div>
    		</div>
    		<div class="item fitur">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<img src="/img/tours/zoom-report-2016.png" class="img-default">
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>Laporan Properti</h2>
				    			<p class="desc">Detil Jumlah pengunjung, dan hot lead (Pesan) ditampilkan secara terperinci dan akurat, dengan didukung fitur Export Excel</p>
	    						<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="2">Lanjut</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
    		<div class="item fitur">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<img src="/img/tours/zoom-ebrosur.png" class="img-default">
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>eBrosur</h2>
				    			<p class="desc">Kirim ebrosur properti Anda melalui email atau media social pilihan, untuk memudahkan distribusi pemasaran properti Anda.</p>
	    						<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="3">Lanjut</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
    		<div class="item fitur kpr">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<img src="/img/tours/zoom-kpr.png" class="img-default">
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>Pengajuan KPR</h2>
				    			<div class="desc">
					    			<p>Pengajuan KPR ke banyak bank dengan mudah dan cepat, dimanapun, kapanpun</p>
					    			<p>Didukung dengan perhitungan dan simulasi KPR</p>
				    			</div>
	    						<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="4">Lanjut</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
    		<div class="item fitur">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<img src="/img/tours/zoom-cobroke.png" class="img-default">
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>Co-Broke</h2>
				    			<p class="desc">Kini Anda bisa Co-Broke listing kepada semua Agen pengguna Prime System, yang terhubung lebih dari 7,000 Agen Properti.</p>
	    						<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="5">Lanjut</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
    		<div class="item fitur media-partner">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<div class="wrapper-content">
							<img src="/img/tours/mediapartner.png" class="img-default">
						</div>
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>Media Partner</h2>
				    			<p class="desc">Properti yang ditayangkan melalui situs properti Anda, secara otomatis tayang di media partner yang bekerjasama dengan Prime System.</p>
	    						<a class="btn dark" data-target="#carousel-tour-generic" data-slide-to="6">Lanjut</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
    		<div class="item fitur support">
    			<div class="row">
    				<div class="col-md-6 col-sm-12">
    					<div class="wrapper-content">
							<img src="/img/tours/zoom-contact.png" class="img-default">
						</div>
    				</div>
    				<div class="col-md-6 col-sm-12">
		    			<div class="tacenter wrapper-content">
		    				<div class="head-title">
				    			<h2>Terima Kasih</h2>
				    			<div class="desc">
				    				<p>Untuk informasi & bantuan silakan hubungi:</p>
				    				<?php 
				    						echo $this->Html->tag('p', __('%s (WA)', Configure::read('__Site.company_profile.phone2')));
				    						echo $this->Html->tag('p', Configure::read('__Site.company_profile.email'));
				    				?>
			    				</div>
	    						<a class="btn blue close-modal">Selesai</a>
			    			</div>
		      			</div>
    				</div>
    			</div>
    		</div>
  		</div>
  	</div>
</div>
<?php
		} elseif ( empty($sign_integrated) && $group_id == 2 ) {
			echo $this->element('blocks/common/modals/sign_integrated');
		}
?>
