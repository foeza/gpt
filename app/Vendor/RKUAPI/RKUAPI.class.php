<?php

class RKUAPI {
    var $version = "1.3";
    var $errorMessage;
    var $errorCode;
    
    /**
     * Cache the information on the API location on the server
     */
    var $apiUrl;
    
    /**
     * Default to a 300 second timeout on server calls
     */
    var $timeout = 300; 
    
    /**
     * Default to a 8K chunk size
     */
    var $chunkSize = 8192;
    
    /**
     * Cache the user api_key so we only have to log in once per client instantiation
     */
    var $api_key;

    /**
     * Cache the user api_key so we only have to log in once per client instantiation
     */
    var $secure = false;
    
    /**
     * Connect to the MailChimp API for a given list.
     * 
     * @param string $apikey Your MailChimp apikey
     * @param string $secure Whether or not this should use a secure connection
     */
    function RKUAPI($apikey, $passwd, $secure=false, $host) {
        $this->secure = $secure;
        $this->api_key = $apikey;
        $this->api_pass = $passwd;
        $this->apiUrl = parse_url($host);
    }

    function api_access( $merge_vars = NULL, $action = false ) {
        $params = array();

        if(!empty($merge_vars)){
            $params["merge_vars"] = $merge_vars;
        }

        $params["action"] = $action;
        
        return $this->callServer("listProperties", $params);
    }
    
    /**
     * Actually connect to the server and call the requested methods, parsing the result
     * You should never have to call this function manually
     */
    function callServer($method, $params) {
        $host = $this->apiUrl["host"];
        $params["apikey"] = $this->api_key;
        $params["apipass"] = $this->api_pass;

        $this->errorMessage = "";
        $this->errorCode = "";
        $sep_changed = false;
        //sigh, apparently some distribs change this to &amp; by default
        if (ini_get("arg_separator.output")!="&"){
            $sep_changed = true;
            $orig_sep = ini_get("arg_separator.output");
            ini_set("arg_separator.output", "&");
        }
        $post_vars = http_build_query($params);
        if ($sep_changed){
            ini_set("arg_separator.output", $orig_sep);
        }
        $payload = "POST " . $this->apiUrl["path"] . "?method=" . $method . " HTTP/1.0\r\n";
        $payload .= "Host: " . $host . "\r\n";
        $payload .= "User-Agent: RKU/" . $this->version ."\r\n";
        $payload .= "Content-type: application/x-www-form-urlencoded\r\n";
        $payload .= "Content-length: " . strlen($post_vars) . "\r\n";
        $payload .= "Connection: close \r\n\r\n";
        $payload .= $post_vars;
        
        ob_start();
        if ($this->secure){
            $sock = fsockopen("ssl://".$host, 443, $errno, $errstr, 30);
        } else {
        // debug($host);die();
            $sock = fsockopen($host, 80, $errno, $errstr, 240);
        }
        if(!$sock) {
            $this->errorMessage = "Could not connect (ERR $errno: $errstr)";
            $this->errorCode = "-99";
            ob_end_clean();
            return false;
        }
        
        $response = "";
        fwrite($sock, $payload);
        stream_set_timeout($sock, $this->timeout);
        $info = stream_get_meta_data($sock);
        while ((!feof($sock)) && (!$info["timed_out"])) {
            $response .= fread($sock, $this->chunkSize);
            $info = stream_get_meta_data($sock);
        }
        fclose($sock);
        ob_end_clean();
        if ($info["timed_out"]) {
            $this->errorMessage = "Could not read response (timed out)";
            $this->errorCode = -98;
            return false;
        }

        list($headers, $response) = explode("\r\n\r\n", $response, 2);
        $headers = explode("\r\n", $headers);
        $errored = false;
        foreach($headers as $h){
            if (substr($h,0,26)==="X-Rumahku-API-Error-Code"){
                $errored = true;
                $error_code = trim(substr($h,27));
                break;
            }
        }
        
        if(ini_get("magic_quotes_runtime")) $response = stripslashes($response);
        
        $serial = $response;
        if($response && $serial === false) {
            $response = array("error" => "Bad Response.  Got This: " . $response, "code" => "-99");
        } else {
            $response = $serial;
        }
        if($errored && is_array($response) && isset($response["error"])) {
            $this->errorMessage = $response["error"];
            $this->errorCode = $response["code"];
            return false;
        } elseif($errored){
            $this->errorMessage = "No error message was found";
            $this->errorCode = $error_code;
            return false;
        }
        
        return $response;
    }

}

?>