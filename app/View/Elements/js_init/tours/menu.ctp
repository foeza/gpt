<?php
		$mobile = Configure::read('Global.Data.MobileDetect.mobile');
		$tablet = Configure::read('Global.Data.MobileDetect.tablet');

		if( !empty($tour_guide) ) {
			echo $this->Html->css(array(
				'tour/bootstrap-tour.min',
			)).PHP_EOL;
			echo $this->Html->script(array(
				'tour/bootstrap-tour.min',
			)).PHP_EOL;
		}
?>
<script type="text/javascript">
	<?php 
			if( !empty($tour_guide) ) {
				if( !empty($mobile) || !empty($tablet) ) {
					echo "var placement = 'bottom'";
				} else {
					echo "var placement = 'right'";
				}
	?>
	
	// Instance the tour
	var optionTour = [
		{
			<?php
					if( !empty($mobile) || !empty($tablet) ) {
			?>
			element: "#main-menu-device",
			title: "Menu Utama",
			content: "<h4>Terdapat beberapa perubahan pada menu:</h4>\
			<ul>\
			<li>Perubahan struktur menu</li>\
			<li>Penyederhanaan nama menu</li>\
			<li>CRM diganti menjadi Agenda & Kegiatan</li>\
			</ul>",
			placement: placement,
			<?php
					} else {
			?>
			element: "#main-menu",
			title: "Menu Utama",
			content: "<h4>Terdapat beberapa perubahan pada menu:</h4>\
			<ul>\
			<li>Perubahan struktur menu</li>\
			<li>Penyederhanaan nama menu</li>\
			<li>CRM diganti menjadi Agenda & Kegiatan</li>\
			</ul>"
			<?php
					}
			?>
		},
		{
			element: "#message-tour",
			title: "Pesan Inbox",
			content: "<h4>Keunggulan:</h4>\
			<ul>\
			<li>Notifikasi apabila ada pesan masuk</li>\
			<li>Cara cepat membuka pesan anda</li>\
			</ul>",
			placement: placement,
		},
		{
			element: "#notif-tour",
			title: "Notifikasi",
			content: "<h4>Keunggulan:</h4>\
			<ul>\
			<li>List Informasi pemberitahuan</li>\
			<li>Cara cepat membuka informasi anda</li>\
			</ul>",
			placement: placement,
		},
	];

	var indicatorTour = 1;
	var tour = new Tour({
		steps: optionTour,
		storage: false,
		autoscroll: false,
		backdrop: true,
		template: "<div class='popover tour tourPosition-onLeftTop'>\
		<div class='arrow'></div>\
		<div class='tourPart-title'>\
		<a href='javascript:void(0);' class='tourClose' data-role='end'><i class='rv4-bold-cross'></i></a>\
		<span class='popover-title'></span>\
		</div>\
		<div class='popover-content tourPart-content'></div>\
		<div class='tourPart-control'>\
		<div class='tourControl'>\
		<a href='javascript:void(0);' class='disabled' data-role='prev'>Prev</a>\
		<a href='javascript:void(0);' data-role='next'>Next</a>\
		</div>\
		<div class='tourCount'>\
		<span class='current'>1</span>\
		<span>/</span>\
		<span class='total'>"+optionTour.length+"</span>\
		</div>\
		</div>\
		</div>",
		onShown: function (tour) {
			$('.tourCount .current').html(indicatorTour);

			if(indicatorTour == 1 ) {
				$('.tourControl [data-role="prev"]').addClass('disabled');
			} else {
				$('.tourControl [data-role="prev"]').removeClass('disabled');
			}

			if(indicatorTour == optionTour.length ) {
				$('.tourControl [data-role="next"]').html('Tutup');
			} else {
				$('.tourControl [data-role="next"]').removeClass('disabled').html('Next');
			}

			$('a[data-role="prev"]').off('click').click(function(){
				indicatorTour--;
			});
			$('a[data-role="next"]').off('click').click(function(){
				indicatorTour++;

				if( indicatorTour > optionTour.length ) {
					tour.end();
				}
			});
		},
		onEnd: function (tour) {
			$.ajax({
	            url: '/ajax/slide_tour/2/',
	            type: 'POST',
	            success: function(response, status) {
	                console.log('Berhasil membuka tour');

	                return false;
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown) {
	                console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
	                return false;
	            }
	        });
		},
	});

	// Initialize the tour
	tour.init();

	// Start the tour
	tour.start(true);
<?php
		}
?>
</script>