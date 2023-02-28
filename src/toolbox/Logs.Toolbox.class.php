<?php
namespace CxQ\Toolbox;
class Logs{
     static function log($text,$dir=null,$filename='') {

          if(is_array($text)){
               if (count($text) == count($text, COUNT_RECURSIVE)) {

                    $text = implode(PHP_EOL,$text);
               }else{
                    $text = json_encode($text);
               }


          }

          $log  = "@ ".date("F j, Y, g:i a").PHP_EOL.$text.PHP_EOL.
          "-------------------------".PHP_EOL;
          //Save string to log, use FILE_APPEND to append.
         return file_put_contents(\Toolbox::buildPath($dir??dirname(dirname(dirname(__DIR__))),$filename??'log_'.date("Ymd").'.txt'), $log, FILE_APPEND);
     }

}