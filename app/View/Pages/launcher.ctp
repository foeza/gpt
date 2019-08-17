<?php 
		$launcherUrl = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'launcher_url');
?>
<div id="download-launcher">
	<header role="banner" id="guide">
		<div class="container">
			<div class="row">
				<div class="download-message">
					<div class="download-content">
						<?php 
								echo $this->Html->tag('h1', __('Unduh gratis aplikasi mobile Android kami'));
								echo $this->Html->tag('h2', __('Kendalikan secara penuh listing properti Anda, dimana saja dan kapan saja'));
								echo $this->Html->link($this->Html->image('/img/launchers/download-now.png'), $launcherUrl, array(
									'escape' => false,
								));
						?>
					</div>
				</div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="container">
			<?php 
					echo $this->Html->tag('div', $this->Html->tag('h1', __('Panduan Instalasi'), array(
						'class' => 'title',
					)), array(
						'class' => 'row',
					));
			?>
			<div class="row">
				<div class="col-sm-10 centered">
					<div class="row">
						<div class="step-1">
							<div class="col-sm-6 col-sm-push-6">
								<?php 
										echo $this->Html->tag('div', $this->Html->image('/img/launchers/install-1.png'), array(
											'class' => 'image-container',
										));
								?>
							</div>
							<div class="col-sm-6 col-sm-pull-6">
								<div class="how-container">
									<span class="numb">01</span>
									<p>Setelah menekan tombol "Unduh Sekarang" di halaman ini, akan muncul pemberitahuan mengenai aplikasi yang akan di unduh. Tekan tombol "OK" untuk mengunduh aplikasi mobile Android.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="step-2">
							<div class="col-sm-6">
								<?php 
										echo $this->Html->tag('div', $this->Html->image('/img/launchers/install-2.png'), array(
											'class' => 'image-container',
										));
								?>
							</div>
							<div class="col-sm-6">
								<div class="how-container">
									<span class="numb">02</span>
									<p>Setelah file aplikasi selesai di unduh, buka file tersebut dengan menekan tombol "Open". Proses instalasi akan dimulai setelah langkah ini.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="step-3">
							<div class="col-sm-6 col-sm-push-6">
								<?php 
										echo $this->Html->tag('div', $this->Html->image('/img/launchers/install-3.png'), array(
											'class' => 'image-container',
										));
								?>
							</div>
							<div class="col-sm-6 col-sm-pull-6">
								<div class="how-container">
									<span class="numb">03</span>
									<p>Aplikasi mobile Android ini akan menjelaskan permintaan untuk mengakses sistem pada perangkat Anda. Cukup tekan tombol "Install" dan proses instalasi segera dimulai di perangkat Android Anda.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="step-4">
							<div class="col-sm-6">
								<?php 
										echo $this->Html->tag('div', $this->Html->image('/img/launchers/install-4.png'), array(
											'class' => 'image-container',
										));
								?>
							</div>
							<div class="col-sm-6">
								<div class="how-container">
									<span class="numb">04</span>
									<p>Setelah aplikasi mobile Android selesai di-install, tekan tombol "Open" untuk membuka aplikasi tersebut. Selanjutnya, aplikasi ini dapat di akses melalui icon yang terdapat pada layar perangkat Android Anda.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="unblock">
				<div class="row">
					<h1 class="title">Pengaturan perangkat Android</h1>
					<div class="col-sm-7 centered text-center">
						<p>Berikut adalah panduan untuk memasang aplikasi dari sumber selain dari google play pada perangkat Android.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-10 centered">
						<div class="row">
							<div class="step-1">
								<div class="col-sm-6 col-sm-push-6">
									<?php 
											echo $this->Html->tag('div', $this->Html->image('/img/launchers/block-1.png'), array(
												'class' => 'image-container',
											));
									?>
								</div>
								<div class="col-sm-6 col-sm-pull-6">
									<div class="how-container">
										<span class="numb">01</span>
										<p>Tekan tombol "Settings", untuk mengijinkan perangkat Anda memasang aplikasi mobile dari sumber selain google play.</p>
									</div>
								</div>
							</div>
							<div class="step-2">
								<div class="col-sm-6">
									<?php 
											echo $this->Html->tag('div', $this->Html->image('/img/launchers/block-2.png'), array(
												'class' => 'image-container',
											));
									?>
								</div>
								<div class="col-sm-6">
									<div class="how-container">
										<span class="numb">02</span>
										<p>Pastikan menu "Unknown Sources" di centang, untuk dapat memasang aplikasi mobile Android kami.</p>
									</div>
								</div>
							</div>
							<div class="step-3">
								<div class="col-sm-6 col-sm-push-6">
									<?php 
											echo $this->Html->tag('div', $this->Html->image('/img/launchers/block-3.png'), array(
												'class' => 'image-container',
											));
									?>
								</div>
								<div class="col-sm-6 col-sm-pull-6">
									<div class="how-container">
										<span class="numb">03</span>
										<p>Tekan tombol OK, untuk memasang aplikasi mobile Android kami. Perangkat Anda sudah siap untuk memasang aplikasi mobile Android kami.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-7 centered text-center">
						<?php 
								echo $this->Html->tag('p', sprintf(__('Lihat panduan instalasi aplikasi mobile Android, %s.'), $this->Html->link(__('di sini'), '#guide', array(
									'escape' => false,
								))));
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="download-foot">
					<?php 
							echo $this->Html->link($this->Html->image('/img/launchers/download-now.png'), $launcherUrl, array(
								'escape' => false,
							));
					?>
				</div>
			</div>
		</div>
	</section>
</div>