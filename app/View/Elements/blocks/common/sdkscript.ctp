<?php
      $bodyTag = Common::hashEmptyField($_config, 'UserCompanyConfig.body_tag');

      if( !empty($bodyTag) ) {
        echo $bodyTag;
      }
?>
<div id="fb-root"></div>

<!-- Facebook -->
<?php 
    $app_id = Configure::read('Facebook.appId');
?>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $app_id; ?>',
      xfbml      : true,
      version    : 'v2.6'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     js.async = true;
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<!-- linkedin -->
<script src="//platform.linkedin.com/in.js" type="text/javascript">
 lang: en_US
</script>

<!-- pinterest -->
<script type="text/javascript">
(function(d){
    var f = d.getElementsByTagName('SCRIPT')[0], p = d.createElement('SCRIPT');
    p.type = 'text/javascript';
    p.async = true;
    p.src = '//assets.pinterest.com/js/pinit.js';
    f.parentNode.insertBefore(p, f);
}(document));
</script>

<!-- google -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>


<!-- twitter -->
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<script type="text/javascript">
function fbs_click(type, data, url) {
    var twtTitle = document.title;
    var twtUrl = url;

    if( typeof url == 'undefined' ) {
      twtUrl = location.href;
    }

    if(type=="twitter"){
	    var maxLength = 140 - (twtUrl.length + 1);
	} else {
		var maxLength = 255 - (twtUrl.length + 1);
	}

    if (twtTitle.length > maxLength) {
        twtTitle = twtTitle.substr(0, (maxLength - 3)) + '...';
    }

    if(type=="twitter"){
      var twtTitle = encodeURIComponent(twtTitle)+' - '+encodeURIComponent(twtUrl);
      if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
      	var twtLink = 'https://mobile.twitter.com/compose/tweet?status='+twtTitle;
      } else {
        var twtLink = 'https://twitter.com/intent/tweet?text='+encodeURIComponent(twtTitle)+'&url='+encodeURIComponent(twtUrl)+'&related=';
      }
    } else if(type=="facebook"){
      if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
          var twtLink = 'http://m.facebook.com/sharer.php?u='+encodeURIComponent(twtUrl)+'&t='+encodeURIComponent(twtTitle);
      } else {
          var twtLink = 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]='+encodeURIComponent(twtUrl)+'&p[images][0]=&p[title]='+encodeURIComponent(twtTitle)+'&p[summary]=' + encodeURIComponent(twtTitle + ' ' + twtUrl);
      }
    	
    } else if(type=="linkedin"){
    	var twtLink = 'http://www.linkedin.com/shareArticle?mini=true&url='+encodeURIComponent(twtUrl)+'&title='+encodeURIComponent(twtTitle)+'&ro=false&summary=&source=';
    } else if(type=="gplus"){
    	var twtLink = 'https://plus.google.com/share?url='+encodeURIComponent(twtUrl);
    } else if(type=="pinterest"){
      if(data != ''){
    	 var justimage = data;
      } else {
        var justimage = '/img/logo.png';
      }
    	var justdec = 'This is example of pinterest description';
    	var twtLink = 'http://pinterest.com/pin/create/button/?url='+encodeURIComponent(twtUrl)+'&media='+encodeURIComponent(justimage)+'&description='+encodeURIComponent(justdec);
    }
    popupwindow(twtLink,twtTitle, 600, 500);
}

function popupwindow(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
} 
</script>