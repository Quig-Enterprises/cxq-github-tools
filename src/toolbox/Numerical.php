<?php
namespace CxQ\Toolbox;
class Numerical{

    /******************************************
    *   Numerical Operations
    *******************************************/

    static function randomNumberString($len=20){
        //TODO: submit this to stackexchange as solution for generating random number of any length
        $str='';
        while(strlen($str)<$len){
            $str.=trim(mt_rand());
        }
        $str=substr($str,0,$len);


        //new dBug2(array($len,$str));
        return $str;
    }

}
