<?php
namespace CxQ\Toolbox;
class XML{
    /******************************************
    *   XML Operations
    *******************************************/
    static function GetXMLFromFile($filepath){
        $contents = file_get_contents($filepath);
        $xml=new \SimpleXMLElement($contents);
        return $xml;

       //new \dBug2((array)($xml->core->security->AUTH_API_CONFIG));
    }
    static function SimpleXML2Array($xml){
        //works, but drops attributes
        return json_decode(json_encode($xml),TRUE);
        /*
        if(empty((array)$xml)){
            //prevent it from populating these with an empty array
            return '';
        }
       //new \dBug2(json_encode($xml));
        if(is_string($xml)) new \dBug2(($xml));



        //recursive Parser
        $array=array();
        foreach ((array)$xml as $key => $value){
            //new \dBug2($key);
            //if($key=='@attributes') new \dBug2(array_keys((array)$value));
            if(is_array($value)){
                $array[$key] = $value;
                new \dBug2(array_keys($value));
                foreach($value as $k2=>$v2){
                    $array[$key][$k2] = self::SimpleXML2Array($v2);
                }
            }elseif( is_string($value) || is_numeric($value)){
                $array[$key] = $value;
                //new \dBug2($value);
            }elseif(strpos(get_class($value),"SimpleXML")!==false){
                {
                    //new \dBug2(array_keys((array)$value));
                    //new \dBug2((string)$value);
                    $array[$key] = self::SimpleXML2Array($value);
                }
            }else{
                //new \dBug2($value);
            }
        }

        //Prevent creation of nested arrays with one elements and numeric keys
        if(count($array)==1){
            $keys = array_keys($array);
            if(is_numeric($keys[0])) $array=$array[0];
        }
        //new \dBug2($array);
        return $array;
        */
    }
    static function getSimpleXMLAttributes($xml){
        if(is_string($xml)) $xml=simplexml_load_string($xml);

        $attributes=array();
        foreach($xml->attributes() as $k=>$v){
           $attributes[(string)$k]= (string)$v;
        }
        return $attributes;
    }
    static function arrayToXML($arr,$root_element,$file_header='<?xml version="1.0" encoding="utf-8"?>', $strip_header = false){
        new \dBug2(self::SimpleXML2Array( '<xml><form name="test"><input name="test2">val</input></form></xml>'));

        $xml = new \SimpleXMLElement($file_header."<{$root_element}></{$root_element}>");

        self::arrayToXML_helper($arr,$xml);

        new \dBug2(self::SimpleXML2Array( $xml->asXML()));
        return $strip_header?str_replace($file_header,'',$xml->asXML()):$xml->asXML();
    }

    //Based on http://stackoverflow.com/a/5965940/3424147, but modified to properly tolerate numeric keys and support attributes
    private static function arrayToXML_helper($data,\SimpleXMLElement &$xml,$parent_key=null,&$xml_parent=null){
         if(!is_object($xml)) throw new \Exception('Invalid XML object');

         foreach($data as $key => $value ) {
            if($key=='@attributes'){
                if( is_array($value) ) {
                    foreach($value as $k=>$v){
                        $xml->addAttribute($k,$v);
                    }
                }
            }else{

                if( is_numeric($key) & $xml_parent!= null){
                    //dealing with <0/>..<n/> issues
                    $key = $parent_key;
                    $xml=$xml_parent;
                }
                if( is_array($value) ) {
                    $subnode=null;
                    if(!is_numeric(key($value)) ){
                        $subnode = $xml->addChild($key);
                        //TODO: look for @attributes and convert to attributes using str_replace against ">"?
                        
                        self::arrayToXML_helper($value, $subnode,$key,$xml);

                    }else{
                       foreach($value as $k2=>$v2){
                           $subnode = $xml->addChild($key);
                           self::arrayToXML_helper($v2, $subnode,$key,$xml);
                       }
                    }
                } else {
                    $xml->addChild("$key",htmlspecialchars("$value"));
    
                }
            }
        }
    }

}
