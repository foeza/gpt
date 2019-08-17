/*================================
            Smooth Scroll
==================================*/
//
// if (window.addEventListener) window.addEventListener('DOMMouseScroll', wheel, false);
// window.onmousewheel = document.onmousewheel = wheel;
//
// function wheel(event) {
//     var delta = 0;
//     if (event.wheelDelta) delta = event.wheelDelta / 120;
//     else if (event.detail) delta = -event.detail / 3;
//
//     handle(delta);
//     if (event.preventDefault) event.preventDefault();
//     event.returnValue = false;
// }
//
// var goUp = true;
// var end = null;
// var interval = null;
//
// function handle(delta) {
// 	var animationInterval = 30; //lower is faster
//   var scrollSpeed = 10; //lower is faster
//
// 	if (end == null) {
//   	end = $(window).scrollTop();
//   }
//   end -= 30 * delta;
//   goUp = delta > 0;
//
//   if (interval == null) {
//     interval = setInterval(function () {
//       var scrollTop = $(window).scrollTop();
//       var step = Math.round((end - scrollTop) / scrollSpeed);
//       if (scrollTop <= 0 || 
//           scrollTop >= $(window).prop("scrollHeight") - $(window).height() ||
//           goUp && step > -1 || 
//           !goUp && step < 1 ) {
//         clearInterval(interval);
//         interval = null;
//         end = null;
//       }
//       $(window).scrollTop(scrollTop + step );
//     }, animationInterval);
//   }
// }


/*================================
            Plugin
==================================*/

$(document).ready(function() {
   
   
   /***********/
   /* Counter */
   /***********/
   var count = $(".counter");
   
   if (typeof $.fn.counterUp != "undefined" && count.length) {
      count.counterUp({
         delay: 10,
         time: 2000
      });
   }
   
   /***********/
   /* Parallax */
   /***********/
   var scene = $("#scene");
   
   if (typeof $.fn.parallax != "undefined") {
      
      scene.parallax();
   }
   
   /***********/
   /*   WOW  */
   /***********/
   
   new WOW().init();
   
   /***********/
   /* Rellax */
   /***********/
   
   var rellax = new Rellax();

   $.trigger_popup();
   
});




/******************************************/
/*         Request Demo in Index          */
/******************************************/

$.trigger_popup = function(){
  // Get the modal
  var tryMe = document.getElementById('tryContent');

  // Get the button that opens the modal
  var btnTry = document.getElementById("iWantToTry");

  // Get the <span> element that closes the modal
  var spanTry = document.getElementsByClassName("tryClose")[0];

  if (typeof modal === 'undefined'){
    modal = false;
  }
  if (typeof modalTry === 'undefined'){
    modalTry = false;
  }

  // When the user clicks on the button, open the modal 
  if(btnTry){
    btnTry.onclick = function() {
          tryMe.style.display = "block";
          $("body").addClass("tryOpen");
    }
  }

  // When the user clicks on <span> (x), close the modal

  if(tryMe){
    spanTry.onclick = function() {
        tryMe.style.display = "none";
          $("body").removeClass("tryOpen");
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            tryMe.style.display = "none";
        }
    }

    window.onclick = function(event) {
        if (event.target == modalTry) {
            tryMe.style.display = "none";
        }
    }
  }
  
}










































