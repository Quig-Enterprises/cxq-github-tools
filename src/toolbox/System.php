<?php
namespace CxQ\Toolbox;
class System{
    /******************************************
    *   Misc Tools
    *******************************************/
    public static function is_empty($var){
        return empty($var);
    }
    public static function onMobile(){
        return 1==preg_match('/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|bo??ost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $_SERVER['HTTP_USER_AGENT']);
    }

    /******************************************
    *   System Tools
    *******************************************/
    public static function backtrace_array($remove_line_numbers=false){
        ob_start();
        debug_print_backtrace();
        $ob=ob_get_clean();
        $arr=explode('#',Strings::leftReplace('#','',$ob));
        
        if($remove_line_numbers){
            //Remove line numbers:
            foreach($arr as $line_number=>$text){
                $arr[$line_number]=trim(\Toolbox::leftReplace($line_number,'',$text));
            }
        }
        return $arr;

    }
    public static function backtrace($moreInfo='',$clear_between_parenthesis = false){
        ob_start();
        debug_print_backtrace();
        $ob=ob_get_clean();

        //echo $ob;


        if(isset($_REQUEST['o']) && $_REQUEST['o']='json'){
            echo $ob;
        }else{
            if(true){
                //Strip out lines representing trace between this file and wherever backtrace was called
                $arr=explode('#',Strings::leftReplace('#','',$ob));
                $ob_edited='';
                $cnt=0;
                $found = false;

                foreach($arr as $i=>$text){
                    $text = trim(Strings::leftReplace($i,'',$text));
                    if(strtoupper($clear_between_parenthesis)=='ARRAY'){
                        $text = preg_replace('/Array\s?\([^)]+\)/','Array(...)',$text);
                    }else if($clear_between_parenthesis === true || strtoupper($clear_between_parenthesis)=='ALL'){
                        $text = preg_replace('/\(.+\)/','(...)',$text);
                    }
                 
                    if(!$found){
                        $found=Strings::leftCompare($text,'CxQ\Toolbox::'.__FUNCTION__.'(');
                    }
                    if(!$found){
                        $found=Strings::leftCompare($text,'\CxQ\Toolbox::'.__FUNCTION__.'(');
                    }
                    if(!$found){
                        $found=Strings::leftCompare($text,'\CxQ\Toolbox\System::'.__FUNCTION__.'(');
                    }

                    if($found){
                        $ob_edited.=sprintf('#%s %s',$cnt++,$text);
                    }

                }
            }else{
                $ob_edited=$ob;
            }

            $content= str_replace(' called at','</td><td><i>called at</i>',str_replace('#','</td></tr><tr><td>'.'#',$ob_edited));
            $html='<table style=border: 1px solid gray><tr><td colspan=2><b>Backtrace'.(!empty($moreInfo)?"({$moreInfo})":'').'</b>'.$content.'</tr></table>';
            new \dBug($html);
        }
    }

    public static function cyclecheck($file,$line,$max_cycles=200){
        if(!defined('IS_INITIALIZED')) return null;
        //call via: \Toolbox::cyclecheck(__FILE__,__LINE__);
        $namespace=__NAMESPACE__;
        $key='cycle_count';

        $value=\CxQ::RegKeyExists($namespace,$key)?\CxQ::RegGetValue($namespace, $key):array();

        if(!isset($value[$file][$line])) $value[$file][$line]=0;
        
        $value[$file][$line]+=1;



        if($value[$file][$line]>=$max_cycles){
            new \dBug2($value);
            //debug_print_backtrace();
            \CxQ\Toolbox\System::backtrace();
            throw new \CxQ\Core\SystemException(sprintf('Maximum cycle count (%s) reached in %s on line %s', $max_cycles, basename($file),$line));
            die();
        }
        \CxQ::RegSetValue($namespace,$key,$value);
    }
    public static function memcheck($file,$line,$max_percent=0){
        //call via: \Toolbox::memcheck(__FILE__,__LINE__);
        $usage=memory_get_usage(true);
        $limit=str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), ini_get('memory_limit'));
        $percent=round($usage/$limit*100,2);

        new \dBug2(array('memcheck'=>"{$usage}/{$limit}",'%'=>$percent,'file'=>$file,'line'=>$line));
        if($max_percent>0 && $percent>=$max_percent) die("MEMORY LIMIT OF {$limit}% EXCEEDED");
    }

    public static function getClassShortName($class){
        if( is_object($class)) $class = get_class($class);

        $arr = explode('\\', $class);
        return array_pop($arr);
    }



}
