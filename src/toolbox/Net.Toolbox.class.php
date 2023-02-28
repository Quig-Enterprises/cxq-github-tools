<?php
namespace CxQ\Toolbox;
class Net{

    /******************************************
    *   Net Operations
    *******************************************/
    
    static function currentURL($includeURLVars=true){
        if($includeURLVars){
            $current_url = $_SERVER['REQUEST_URI'];
        }else{
            $current_url_arr = explode("?", $_SERVER['REQUEST_URI']);
            $current_url = $current_url_arr[0] ;
        }
        return $current_url;
    }

    /**
     * Retrieve a modified URL query string.
     *
     * You can rebuild the URL and append a new query variable to the URL query by
     * using this function. You can also retrieve the full URL with query data.
     *
     * Adding a single key & value or an associative array. Setting a key value to
     * an empty string removes the key. Omitting oldquery_or_uri uses the $_SERVER
     * value. Additional values provided are expected to be encoded appropriately
     * with urlencode() or rawurlencode().
     *
     * Source: add_query_arg from WordPress wp-includes/functions.php
     *
     * @since 1.5.0
     *
     * @param mixed $param1 Either newkey or an associative_array
     * @param mixed $param2 Either newvalue or oldquery or uri
     * @param mixed $param3 Optional. Old query or uri
     * @return string New URL query string.
     */
    static function add_query_arg() {
        $ret = '';
        $args = func_get_args();
        if ( is_array( $args[0] ) ) {
            if ( count( $args ) < 2 || false === $args[1] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[1];
        } else {
            if ( count( $args ) < 3 || false === $args[2] )
                $uri = $_SERVER['REQUEST_URI'];
            else
                $uri = $args[2];
        }
    
        if ( $frag = strstr( $uri, '#' ) )
            $uri = substr( $uri, 0, -strlen( $frag ) );
        else
            $frag = '';
    
        if ( 0 === stripos( $uri, 'http://' ) ) {
            $protocol = 'http://';
            $uri = substr( $uri, 7 );
        } elseif ( 0 === stripos( $uri, 'https://' ) ) {
            $protocol = 'https://';
            $uri = substr( $uri, 8 );
        } else {
            $protocol = '';
        }
    
        if ( strpos( $uri, '?' ) !== false ) {
            list( $base, $query ) = explode( '?', $uri, 2 );
            $base .= '?';
        } elseif ( $protocol || strpos( $uri, '=' ) === false ) {
            $base = $uri . '?';
            $query = '';
        } else {
            $base = '';
            $query = $uri;
        }
    
        self::cxq_parse_str( $query, $qs );
        $qs = self::urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
        if ( is_array( $args[0] ) ) {
            $kayvees = $args[0];
            $qs = array_merge( $qs, $kayvees );
        } else {
            $qs[ $args[0] ] = $args[1];
        }
    
        foreach ( $qs as $k => $v ) {
            if ( $v === false )
                unset( $qs[$k] );
        }
    
        $ret = self::build_query( $qs );
        $ret = trim( $ret, '?' );
        $ret = preg_replace( '#=(&|$)#', '$1', $ret );
        $ret = $protocol . $base . $ret . $frag;
        $ret = rtrim( $ret, '?' );
        return $ret;
    }
    
    /**
     * Removes an item or list from the query string.
     *
     * Based On: remove_query_arg from WordPress wp-includes/functions.php
     *
     * @since 1.5.0
     *
     * @param string|array $key Query key or keys to remove.
     * @param bool $query When false uses the $_SERVER value.
     * @return string New URL query string.
     */
    static function remove_query_arg( $key, $query=false ) {
        if ( is_array( $key ) ) { // removing multiple keys
            foreach ( $key as $k )
                $query = self::add_query_arg( $k, false, $query );
            return $query;
        }
        return self::add_query_arg( $key, false, $query );
    }
    /**
    * Build URL query based on an associative and, or indexed array.
    *
    * This is a convenient function for easily building url queries. It sets the
    * separator to '&' and uses _http_build_query() function.
    *
    * Based On: build_query from WordPress wp-includes/functions.php
    *
    * @see _http_build_query() Used to build the query
    * @link http://us2.php.net/manual/en/function.http-build-query.php more on what
    *      http_build_query() does.
    *
    * @since 2.3.0
    *
    * @param array $data URL-encode key/value pairs.
    * @return string URL encoded string
    */
    static function build_query( $data ) {
        return self::_http_build_query( $data, null, '&', '', false );
    }

    // from php.net (modified by Mark Jaquith to behave like the native PHP5 function)
    private static function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
        $ret = array();
        
        foreach ( (array) $data as $k => $v ) {
            if ( $urlencode)
                $k = urlencode($k);
            if ( is_int($k) && $prefix != null )
                $k = $prefix.$k;
            if ( !empty($key) )
                $k = $key . '%5B' . $k . '%5D';
            if ( $v === null )
                continue;
            elseif ( $v === FALSE )
                $v = '0';

            if ( is_array($v) || is_object($v) )
                array_push($ret,self::_http_build_query($v, '', $sep, $k, $urlencode));
            elseif ( $urlencode )
                array_push($ret, $k.'='.urlencode($v));
            else
                array_push($ret, $k.'='.$v);
        }
        
        if ( null === $sep )
            $sep = ini_get('arg_separator.output');
        
        return implode($sep, $ret);
    }
    /**
    * Navigates through an array and encodes the values to be used in a URL.
    *
    *
    * Based On: urlencode_deep from WordPress wp-includes/functions.php
    *
    * @since 2.2.0
    *
    * @param array|string $value The array or string to be encoded.
    * @return array|string $value The encoded array (or string from the callback).
    */
    static function urlencode_deep($value) {
        $value = is_array($value) ? array_map('self::urlencode_deep', $value) : urlencode($value);
        return $value;
    }

}
