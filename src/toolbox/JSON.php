<?php
namespace CxQ\Toolbox;
class JSON{
    /******************************************
    *   JSON
    *******************************************/
    static function is_json($string) {
        if(!is_string($string)) return false;

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    static function json_encode_javascript($array,$vars_containing_javascript=array()){


        $result= str_replace('  ', ' ',str_replace(':', ': ',str_replace(', ',', ',str_replace('":"', '" : "',json_encode($array)))));
        
        //these items contain javascript, so do no represent them as strings
        foreach($vars_containing_javascript as $key){
            $result = preg_replace('/"'.$key.'"\s*:\s*"([^"]+)"/', '"'.$key.'" : $1', $result);
        }


        return $result;

    }



}
