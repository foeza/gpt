  var modal = document.getElementById('videoContent');
  var btn = document.getElementById("videoModalBtn");
  var span = document.getElementsByClassName("closeVideo")[0];

    btn.onclick = function() {
      	modal.style.display = "block";
      	$("#videoAds").attr("src", $("#videoAds").data("src"))
    }

  	span.onclick = function() {
     	modal.style.display = "none";
  	}

  	window.onclick = function(event) {
      	if (event.target == modal) {
          	modal.style.display = "none";
   		}
  	}

  	$(function(){
     	var closeVideoTrigger = $("#cutVideo");
     	closeVideoTrigger.click(function(e){
          var modal = $(this).closest(".modal");
          var iframe = modal.find("iframe");
          iframe.attr("data-src", iframe.attr("src")).attr("src", "");
     	});
  	});