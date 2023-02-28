<?php
namespace CxQ\Toolbox;
class SQL{

    /******************************************
    *   SQL Operations
    *******************************************/
    static function sql_escape($str){
         if(is_null($str)) return '';
         if(!is_string($str) && !is_numeric($str)) Throw New Exception('Cannot escape non-string');
         $str=mysql_real_escape_string($str);

         return $str;
    }
    static function sql_escape_array($arr){
        $arr_clean=array();
        foreach($arr as $k=>$v){
            $arr_clean[self::sql_escape($k)]= self::sql_escape($v);
        }
        return $arr_clean;
    }
}
