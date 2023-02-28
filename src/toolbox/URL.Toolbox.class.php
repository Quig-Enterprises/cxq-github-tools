<?php
namespace CxQ\Toolbox;
class URL{
    public static function buildURL($params,$page=''){
        //separate base url from url variables to avoid situations like: http://example.com?page=4?page=5
        $orig_params = self::get_url_vars($page);
        $params = array_merge($orig_params,$params);
        
        //remove the original query string:
        $page=\Toolbox::rightReplaceAfter('?','',$page,true);

        //build the new url and remove '=' from after null values. e.g., http://example.com?step2=&page=5
        $url = str_replace('=&','&',$page.'?'.\Toolbox::arrayToString($params,'=','&','',''));
        
        //remove '?' or '=' from end of url, if present. e.g., http://example.com/index.php? or http://example.com/index.php?step2=
        $url=\Toolbox::rightReplace('?','',\Toolbox::rightReplace('=','',$url));

        return $url;
    }
    public static function is_url($url){
        $reg_exUrl = "/^(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if(preg_match($reg_exUrl, $url)) {
            return true;
        }
        return false;
    }
    public static function get_url_vars($url=null){
        $query_string = '';
        if(is_null($url)){
            //use the query string from the current page url
            $query_string=isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
          
        }else{
            //extract the query string from the provided url
            $parts = parse_url($url);
            $query_string=isset($parts['query'])?$parts['query']:'';
        }

        

        //$qs = preg_replace('/(?!^|&)(\w+)(=&)/', '$1[]', $url);  //What was this for??

        parse_str($query_string, $new_GET);

        //remove unnecessary arrays:
        foreach($new_GET as $k=>$v){
            if(is_array($v) && count($v)==1) $new_GET[$k]=reset($v);
        }

        //new \dBug2(array($url,$new_GET));
        return $new_GET;
    }
    public static function extractURLSubdomains($url,$as_array=false){
        $parsed_url=parse_url($url);

        $host = isset($parsed_url['host'])?$parsed_url['host']:$parsed_url['path'];

        //new \dBug2(array($url,$parsed_url,$host));
        $domain = self::extractURLDomain($host);

        $subdomains = rtrim(strstr($host, $domain, true), '.');

        if($as_array){
            return explode('.',$subdomains);
        }else{
            return $subdomains;
        }
    }
    public static function extractURLDomain($url){
        if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $url, $matches))
        {
            return $matches['domain'];
        } else {
            return $url;
        }
    }
    public static function locateFileOnWeb($hosts,$filename,$on_fail_return_first_page = false,$return_host_only=false){
        if(!is_array($directories)) $directories=array($directories);

        foreach($hosts as $host){
            $path = \Toolbox::buildPath($host,$filename);
            if(self::urlExists($path)){
                return $return_host_only?$host:$path;
            }
            //new \dBug2($path);
        }

        if($on_fail_return_first_page){
            $host=reset($hosts);
            return $return_host_only?$host:\Toolbox::buildPath($host,$filename);
        }
        

        return false;
    }
    public static function urlExists($url){
        $exists = true;
        $file_headers = @get_headers($url);

        if(!$file_headers){
            //possible that https:// or http:// wrapper is disabled in the server configuration by allow_url_fopen=0.
            //new \dBug2(array($url,$file_headers,self::getHttpResponseCode_using_curl($url)));
            if(self::getHttpResponseCode_using_curl($url) != 200){
                $exists = false;
            }
        }else{
            $InvalidHeaders = array('404', '403', '500');
            foreach($InvalidHeaders as $HeaderVal)
            {
                    if(strstr($file_headers[0], $HeaderVal))
                    {
                            $exists = false;
                            break;
                    }
            }
        }
        return $exists;
    }
    public static function getHttpResponseCode_using_curl($url, $followredirects = true){
        // returns int responsecode, or false (if url does not exist or connection timeout occurs)
        // NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
        // if $followredirects == false: return the FIRST known httpcode (ignore redirects)
        // if $followredirects == true : return the LAST  known httpcode (when redirected)
        if(! $url || ! is_string($url)){
            return false;
        }
        $ch = @curl_init($url);
        if($ch === false){
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER         ,true);    // we want headers
        @curl_setopt($ch, CURLOPT_NOBODY         ,true);    // dont need body
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)
        if($followredirects){
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  // fairly random number, but could prevent unwanted endless redirects with followlocation=true
        }else{
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
        }
//      @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      @curl_setopt($ch, CURLOPT_TIMEOUT        ,6);   // fairly random number (seconds)... but could prevent waiting forever to get a result
//      @curl_setopt($ch, CURLOPT_USERAGENT      ,"Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   // pretend we're a regular browser
        @curl_exec($ch);
        if(@curl_errno($ch)){   // should be 0
            @curl_close($ch);
            return false;
        }
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);
        return $code;
    }
    public static function currentURL(){
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        //$url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        //$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
        //return $escaped_url;
    }
    public static function unparse_url($parsed_url) {
      //re-combines urls parsed by parse_url
      $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
      $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
      $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
      $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
      $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
      $pass     = ($user || $pass) ? "$pass@" : ''; 
      $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
      $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

      if($path=='/') $path='';
      
      $query='';
      if(isset($parsed_url['query'])){
          $query = is_array($parsed_url['query']) ? http_build_query($parsed_url['query']) : $parsed_url['query'];
          $query='?'.str_replace('=&','&',$query);
          if(\Toolbox::rightCompare($query,'='))  Toolbox_Strings::rightPop($query);

      }



      return "$scheme$user$pass$host$port$path$query$fragment";
    }
}