<?php

	$record				= empty($record) ? array() : $record;
	$propertyMedias		= empty($propertyMedias) ? array() : $propertyMedias;

	if($record){
		$draftID	= Configure::read('__Site.PropertyDraft.id');
		$savePath	= Configure::read('__Site.property_photo_folder');

		$recordID	= Common::hashEmptyField($record, 'Property.id');
		$sessionID	= Common::hashEmptyField($record, 'Property.session_id');

		?>
		<div id="file-drop-zone">
			<div class="content-upload-photo">
				<div class="info-full alert photo-info-top">
					<?php 

						$note = $this->Html->tag('strong', __('Tahukah Anda?'));
						$note = __('%s Dengan mengunggah foto untuk setiap ruangan (lebih dari 1 foto), membuat iklan properti Anda 40 kali lebih menarik, dan cepat terjual/tersewa.', $note);

						echo($this->Html->tag('p', $note));

					?>
				</div>
				<?php 

					echo($this->UploadForm->load($this->Html->url(array(
						'admin'			=> false, 
						'controller'	=> 'ajax',
						'action'		=> 'property_photo',
						'draft'			=> $draftID,
						$recordID,
					)), $propertyMedias, $savePath, array(
						'id'			=> $recordID,
						'session_id'	=> $sessionID,
					)));

					if(empty($propertyMedias)){

						?>
						<div class="info-upload-photo text-center">
							<?php

								echo $this->Html->tag('div', $this->Rumahku->icon('picture-o'), array(
									'class' => 'pict',
								));

							?>
							<div class="line1">
								<?php 

									echo($this->Html->tag('label', __('Geser dan taruh berkas disini atau klik untuk menggunggah gambar.')));
									echo($this->Html->tag('p', __('Anda dapat menambahkan judul pada foto, setelah proses unggah selesai.')));
								?>
							</div>
							<div class="line2">
								<?php 

									echo($this->Html->tag('p', __('Maksimum ukuran foto yang diunggah 10Mb.')));
									echo($this->Html->tag('p', __('Foto yang diunggah harus memenuhi syarat dan ketentuan dari %s.', Configure::read('__Site.site_name'))));

								?>
							</div>
						</div>
						<?php 

					}

				?>
				<div class="row photo-info-bottom">
					<ul>
						<?php 

							echo($this->Html->tag('label', __('Keterangan:')));

							$allowedExts = implode(', ', Configure::read('__Site.allowed_ext'));

							echo($this->Html->tag('li', __('Kami akan memberikan watermark pada foto yang Anda unggah untuk kepentingan perlindungan hak cipta')));
							echo($this->Html->tag('li', __('Klik dan geser untuk mengubah posisi/urutan foto setelah proses unggah semua foto selesai')));
							echo($this->Html->tag('li', __('Klik tombol %s untuk menentukan foto utama yang ditampilkan sebagai thumbnail dan foto pertama di halaman pencarian properti. Hanya ada 1 foto utama untuk setiap iklan properti', $this->Html->tag('strong', __('"Jadikan Foto Utama"')))));
							echo($this->Html->tag('li', __('Berikan judul untuk setiap foto properti yang diunggah')));
							echo($this->Html->tag('li', __('Mohon hanya mengunggah file berekstensi %s', $allowedExts)));

						?>
					</ul>
				</div>
			</div>
			<?php 

				echo($this->Html->link(__('%s Hapus Foto', $this->Rumahku->icon('rv4-cross')), array(
					'admin'			=> false,
					'controller'	=> 'ajax', 
					'action'		=> 'property_photo_delete',
					'draft'			=> $draftID,
					$sessionID,
					$recordID, 
				), array(
					'escape'				=> false,
					'class'					=> 'btn red fly-button-media ajax-link',
					'data-form'				=> '#fileupload',
					'data-alert'			=> __('Anda yakin ingin menghapus foto ini?'),
					'data-action'			=> 'reset-file-upload',
					'data-wrapper-write'	=> '.wrapper-upload-medias',
				)));

				$_wrapper_ajax =  empty($_wrapper_ajax) ? 'wrapper-modal-write' : $_wrapper_ajax;

				echo($this->Form->create(false, array(
					'class' => 'form-target', 
				)));

				echo($this->Form->hidden(false, array(
					'name'  => 'is_easy_mode', 
					'value' => true, 
				)));

				echo($this->Form->hidden(false, array(
					'name'	=> '_wrapper_ajax', 
					'value'	=> $_wrapper_ajax, 
				)));

				echo($this->Form->end());

			?>
		</div>
		<?php

	}

?>