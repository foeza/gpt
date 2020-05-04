<?php
        $bodyTag = Common::hashEmptyField($_config, 'UserCompanyConfig.body_tag');
        $app_id  = Common::hashEmptyField($_config, 'UserCompanyConfig.facebook_appid');

        if( !empty($bodyTag) ) {
            echo $bodyTag;
        }
?>
<div id="fb-root"></div>

<?php 
        // facebook
        if (!empty($app_id)) {
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

<?php
        }
?>
