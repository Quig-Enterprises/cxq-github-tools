<?php
namespace CxQ\Toolbox;
class Strings{
    /******************************************
    *   String Operations
    *******************************************/
    static function left($inString,$numchars=1){
        //backtrace();
        return substr($inString,0,$numchars);

    }
    static function right($inString,$numchars=1){
        return substr($inString, strlen($inString)-$numchars,$numchars);
    }
    static function rightCompare($haystack, $needle){
        return (self::right($haystack, strlen($needle))==$needle);
    }
    static function leftCompare($haystack, $needle){
      //new dBug2(array($haystack,$needle,(self::left($haystack, strlen($needle))==$needle)));
        return (self::left($haystack, strlen($needle))==$needle);
    }
    static function leftReplace($needle, $new_needle,$haystack){
        if (substr($haystack, 0, strlen($needle)) == $needle) {
            return $new_needle.substr($haystack, strlen($needle));
        }
        return $haystack;
    }
    static function rightReplace($needle, $new_needle,$haystack){
        if (substr($haystack, -strlen($needle)) == $needle) {
            return substr($haystack, 0, -strlen($needle)).$new_needle;
        }
        return $haystack;
    }

    static function rightReplaceAfter($needle, $new_needle,$haystack,$replace_needle_too=true){
        if ($pos=strrpos($haystack, $needle)) {
            return substr($haystack, 0, $pos+($replace_needle_too===false)*strlen($needle)).$new_needle;
        }
        return $haystack;
    }
    static function leftReplaceBefore($needle, $new_needle,$haystack,$replace_needle_too=true){
        if ($pos=stripos($haystack, $needle)) {

            return $new_needle.substr($haystack, $pos+($replace_needle_too===true)*strlen($needle), strlen($haystack));
        }
        return $haystack;
    }
    static function rightPop(&$inStr,$length=1){
        if (strlen($inStr)<$length) $length=strlen($length);

        $popped=self::right($inStr, $length);
        $inStr=substr($inStr, 0, -$length);

        return $popped;

    }
    static function leftPop(&$inStr,$length=1){
        if (strlen($inStr)<$length) $length=strlen($length);

        $popped=self::left($inStr, $length);
        $inStr=self::leftReplace($popped,'',$inStr);

        return $popped;

    }
    static function str_contains($haystack, $needle){
        //return preg_match("/{$needle}/",$haystack);
        return stripos($haystack, $needle) !== false;
    }

    static function format_number($value,$max_decimals=2,$min_decimals=2){
        $dec=$value-floor($value);


        if($max_decimals<$min_decimals) $max_decimals=$min_decimals;
        $result=number_format(floor($value));

        if($dec>0){
            //get dec without leading zero and round off extra decimals
            $dec=substr(round($dec,$max_decimals),1);


        }else{
            $dec="";
        }

        if($min_decimals>0){
            //ensure the minumim number of decimals
            $result.='.'.str_pad(str_replace('.', '',$dec),$min_decimals,"0");
        }
        //new dBug2(array($value,$result));
        return $result;


    }
    static function format_superscripts($str){
       //wraps numbers which ^ within super script tags
        preg_match_all('/\b\^(\d+\.*\d*)\b/', $str,$matches);

        foreach ($matches[0] as $i=>$match){
           $str=str_replace($match,'<sup>'.$matches[1][$i].'</sup>',$str);

        }

        return $str;
    }
    static function can_be_string($var){
        return $var === null || is_scalar($var) || is_callable([$var, '__toString']);
    }
    static function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    static function is_serialized( $data ) {
        // if it isn't a string, it isn't serialized
        if ( !is_string( $data ) )
            return false;
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
            return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                    return true;
                break;
        }
        return false;
    }
}

