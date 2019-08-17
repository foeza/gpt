<?php
		$mobile = Configure::read('Global.Data.MobileDetect.mobile');
		$tablet = Configure::read('Global.Data.MobileDetect.tablet');

		if( !empty($group_tour_guide) ) {
			echo $this->Html->css(array(
				'tour/bootstrap-tour.min',
			)).PHP_EOL;
			echo $this->Html->script(array(
				'tour/bootstrap-tour.min',
			)).PHP_EOL;
?>
<script type="text/javascript">
	<?php 
			echo "var placement = 'right'";
	?>

	function _callCloseSubMenu () {
		var submenu = $(".main-menu-user").attr('data-submenu');

		$('ul[data-menu="'+submenu+'"]').removeClass('menu__level--current');
		$('.sub-menu-divisi').removeClass('menu-active');
		$(".menu__back").addClass("menu__back--hidden");

		$('.menu__wrap > ul.menu__level[data-menu="main-menu"]').addClass("menu__level--current");
		$(".main-menu-user").addClass("menu-active");
	}
	
	// Instance the tour
	var optionTour = [
		{
			reflex: 'click',
			element: ".main-menu-user",
			title: "Fitur Baru",
			content: "<h4>Kini Anda dapat membuat Divisi User perusahaan. Fitur dan manfaat:</h4>\
			<ul>\
			<li>Pengelompokan user berdasarkan fungsional</li>\
			<li>Otorisasi fitur & module sesuai dengan tugas dan keahlian user</li>\
			<li>User lebih terstruktur, didukung fitur atasan/supervisor yg bertanggung jawab atas user bersangkutan</li>\
			</ul>",
			onNext: function(){
				$(".menu__level").removeClass("menu__level--current");
				$(".main-menu-user").removeClass("menu-active");

				var submenu = $(".main-menu-user").attr('data-submenu');

				$('ul[data-menu="'+submenu+'"]').addClass('menu__level--current');
				$('.sub-menu-divisi').addClass('menu-active');
				$(".menu__back").removeClass("menu__back--hidden");
			},
		},
		{
			reflex: 'click',
			element: ".sub-menu-user",
			title: "Fitur Baru",
			content: "<h4>Daftar User ( Admin, Agen, dll ) berada di menu ini. Fitur dan manfaat:</h4>\
			<ul>\
			<li>Dapat melihat, menambah, mengubah, ganti password, dan mengelola setiap bagian user</li>\
			<li>Dapat mengelompokan user berdasarkan divisi</li>\
			<li>Dapat melakukan pencarian user</li>\
			</ul>",
			onPrev: function(){
				_callCloseSubMenu();
			},
		},
		{
			reflex: 'click',
			element: ".sub-menu-divisi",
			title: "Fitur Baru",
			content: "<h4>Kelola divisi Anda, pastikan setiap user mendapatkan akses sesuai dengan tugas & keahlian-nya</h4>",
			onPrev: function(){
				// _callCloseSubMenu();
			},
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
		onStart: function (tour) {
			var takeTour = $('.take-tour');

			if( takeTour.length > 0 ) {
                takeTour.each(function(){
					var href = $(this).attr('href');

					$(this).attr('data-href', href);
					$(this).attr('href', '#');
                });
				
				// $("#main-menu").attr('data-rel', 'take-tour-menu');
			}
		},
		onShown: function (tour) {
			indicatorTour = tour.getCurrentStep() + 1;
			$('.tourCount .current').html(indicatorTour);

			if(indicatorTour == 1 ) {
				$('.tourControl [data-role="prev"]').addClass('disabled');
			} else {
				$('.tourControl [data-role="prev"]').removeClass('disabled');
			}

			if(indicatorTour == optionTour.length ) {
				$('.tourControl [data-role="next"]').html('Lihat');
			} else {
				$('.tourControl [data-role="next"]').removeClass('disabled').html('Next');
			}

			$('a[data-role="next"]').off('click').click(function(){
				if( indicatorTour >= optionTour.length ) {
					tour.end();
				}
			});

			// $('#main-menu[data-rel="take-tour-menu"] .menu__back').off('click').click(function(e){
			// 	e.preventDefault();
			// 	_callCloseSubMenu();

			// 	$('#main-menu[data-menu="main-menu"]').removeClass('animate-outToRight');
			// });
		},
		onEnd: function (tour) {
			$.ajax({
	            url: '/ajax/slide_tour/3/',
	            type: 'POST',
	            success: function(response, status) {
	                console.log('Berhasil membuka tour');
					var takeTour = $('.take-tour');

					if( takeTour.length > 0 ) {
		                takeTour.each(function(){
							var href = $(this).attr('data-href');

							$(this).attr('href', href);
							$(this).removeAttr('data-href');

							if( $(this).hasClass('sub-menu-divisi') ) {
                				window.location.href = href;                    
							}
		                });

					}

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
</script>
<?php
		}
?>