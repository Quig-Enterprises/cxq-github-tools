<?php
namespace CxQ\Toolbox;
class Arrays{
    /******************************************
    *   Array Operations
    *******************************************/
    static function arrayToString_constantKey($data,$key_override,$glue_map='=',$glue_implode='&',$quotechar_keys='', $quotechar_values=''){
        if(!is_array($data)) $data=array($data);
        if(count($data)==0) return '';
        //return implode($glue_implode, array_map(function ($v, $k) { return $k . $glue_map . $v; }, $data, array_keys($data)));
        return implode($glue_implode, self::arrayMap($data, $glue_map,$quotechar_keys, $quotechar_values,$key_override));
    }
    static function arrayToString($data,$glue_map='=',$glue_implode='&',$quotechar_keys='', $quotechar_values=''){
        //return implode($glue_implode, array_map(function ($v, $k) { return $k . $glue_map . $v; }, $data, array_keys($data)));
        if(!is_array($data) || count($data)==0) return '';
        return implode($glue_implode, self::arrayMap($data, $glue_map,$quotechar_keys, $quotechar_values));
    }
    static function arrayToString_sprntf($data,$glue_map='=',$glue_implode='&',$sprintf_keys='%s', $sprintf_values='%s'){
        if(!is_array($data) || count($data)==0) return '';
        //return implode($glue_implode, array_map(function ($v, $k) { return $k . $glue_map . $v; }, $data, array_keys($data)));
        return implode($glue_implode, self::arrayMap_sprntf($data, $glue_map,$sprintf_keys,$sprintf_values));
    }
    static function findAvailableKey($arr, $prefix='', $start_i=0){
        $i=$start_i;
        while(empty($key) || isset($arr[$key])){
            $key=$prefix.$i++;
        }
        return $key;
    }
    static function arrayMap($data, $glue='&',$quotechar_keys='', $quotechar_values='',$key_override=''){
        if(!is_array($data) || count($data)==0) return '';

        foreach($data as $k=>$v){
            if(is_array($v)){
                foreach($v as $k2=>$v2){
                    $data["{$k}[{$k2}]"]=$v2;
                }
                unset($data[$k]);
            }
        }

        $data_keys=array();
        $data_vals=array();
        if($quotechar_values!='' || $quotechar_keys!=''){
            foreach($data as $key=>$val){
                $data_keys[]=$quotechar_keys.($key_override!=''?$key_override:$key).$quotechar_keys;
                $data_vals[]=$quotechar_values.$val.$quotechar_values;
            }

        }else{
            $data_keys=array_keys($data);
            $data_vals=$data;
        }



        $glue_arr=array_fill(0,count($data),$glue);
        //new \dBug2($data);
        $result= array_map(function ($v, $k,$glue_map) { return $k . $glue_map . $v; }, $data_vals, $data_keys,$glue_arr);
        //new \dBug2( json_encode($result));
        return $result;
    }
    static function arrayMap_sprntf($data, $glue='&',$sprintf_keys='%s', $sprintf_values='%s'){
        if(count($data)==0) return '';

        $data_keys=array();
        $data_vals=array();
        if($sprintf_keys!='%s' || $sprintf_values!='%s'){
            foreach($data as $key=>$val){
                $data_keys[]=sprintf($sprintf_keys,$key);
                $data_vals[]=sprintf($sprintf_values,$val);
            }

        }else{
            $data_keys=array_keys($data);
            $data_vals=$data;
        }

        $glue_arr=array_fill(0,count($data),$glue);

        $result= array_map(function ($v, $k,$glue_map) { return $k . $glue_map . $v; }, $data_vals, $data_keys,$glue_arr);
        //echo json_encode($result);
        return $result;
    }
    static function arrayIntersection($array, $needle1,$needle2) {
        //finds the value at the intersection of two keys within a two-dimensional array

        if (isset($array[$needle1][$needle2]))
            return $array[$needle1][$needle2];

        if (isset($array[$needle2][$needle1]))
            return $array[$needle2][$needle1];

        return false; //nothing found
    }
    static function is_assoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
    static function is_multidimensional($array){
        //checks whether the provided value is an array containing one or more arrays
        if(!is_array($array)) return false;

        foreach ($array as $v) {
            new \dBug2($v);
            if (is_array($v)){
                return true;
            }
        }
        return false;
    }
    static function endc( $array ) {
      $end = end( $array );
      return $end;
    }
    static function array_merge_recursive_objectsafe($array1, $array2,$append=true){
        //Merges the elements of one or more arrays together so that the values of one are appended to the end of the previous one. It returns the resulting array.
        //If the input arrays have the same string keys, then the values for these keys are merged together into an array,and this is done recursively.
        //If one of the values is an array itself, the function will merge it with a corresponding entry in another array too.
        //If, however, $append==false and the arrays have the same numeric key, the later value will not overwrite the original value, but will be appended.
        //Unlike array_merge_recursive(), this function will not convert objects into arrays.
        //if $append==true, and corresponding values are not arrays, then the second value will overwrite the first.
        $array_out=array();
        if(is_array($array1) && !is_array($array2)){
            //Only one input is an array, so append the other input to it.
            $array_out=$array1;
            $array_out[]=$array2;
            return $array_out;
            
        }elseif(!is_array($array1) && is_array($array2)){
           //Only one input is an array, so append the other input to it.
            $array_out=$array2;
            $array_out[]=$array1;
            return $array2; 
            
        }elseif(is_array($array1) && is_array($array2)){
            //Both inputs are arrays
            $array_out=$array1;
            foreach($array2 as $i=>$item){
                if(is_numeric($i)){
                    $array_out[]=$item;
                }elseif(isset($array_out[$i])){
                    $array_out[$i]=self::array_merge_recursive_objectsafe($array_out[$i],$item,$append);
                }else{
                    $array_out[$i]=$item;
                }
            }
            return $array_out;
        }else{
            //Neither input is an array
            if($append){
                return array($array1,$array2);
            }else{
                return $array2;
            }
        }
    }
    //Prepend one or more elements to the beginning of an associative array
    static function array_unshift_assoc(&$arr, $key, $val){

        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        $arr= array_reverse($arr, true);

        return count($arr);
    }
    static function moveArrayKeyToTop($arr, $key){

        if(isset($arr[$key])){
            $val=$arr[$key];
            unset($arr[$key]);
       
            self::array_unshift_assoc($arr,$key,$val);

        }

        return $arr;
    }
    static function prefix_keys($array,$prefix){
        $array_new = $array();
        foreach($array as $key_old=>$value){
            $key_new=$prefix.$key_old;
            $array_new=replace_key($array_new, $key_old, $key_new);
        }
        return $array_new;
    }
    static function replace_key($array, $key_old, $key_new){
        //ref: http://stackoverflow.com/questions/10182684/how-to-change-a-key-in-an-array-while-maintaining-the-order
        if(is_null($array)){
            //\Toolbox::backtrace();
            die(basname(__FILE__).':'.__LINE__);
        }
        $keys = array_keys($array);
        $index = array_search($key_old, $keys,true);
        /*$i = 0;
        foreach($array as $k => $v){
            if($key_old === $k){
                $index = $i;
                break;
            }
            $i++;
        }*/
//new \dBug2(array($array,$key_old, $key_new,$index,$keys));
        if ($index !== false) {
            $keys[$index] = $key_new;
            $array = array_combine($keys, $array);
        }
        return $array;

    }
    static function replace_value(&$array, $value_old, $value_new, $case_sensitive=true){
        foreach($array as $k=>$v){
            if($v==$value_old || (!$case_sensitive && strtoupper($v)==strtoupper($value_old))){
                $array[$k]=$value_new;
            }

        }
       //new \dBug2(array($array,$value_old, $value_new));
    }
    static function remove_value(&$array, $value_old, $case_sensitive=true){
        foreach($array as $k=>$v){
            if($v==$value_old || (!$case_sensitive && strtoupper($v)==strtoupper($value_old))){
                unset($array[$k]);
            }

        }
       //new \dBug2(array($array,$value_old, $value_new));
    }
  
    static function array_sortBySubKey($array,$key,$asc=true){

        if($asc){
            uasort($array, self::build_sorter_asc($key));
        }else{
            uasort($array, self::build_sorter_desc($key));
        }
        return $array;
    }
    private static function build_sorter_asc($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }
    private static function build_sorter_desc($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($b[$key],$a[$key]);
        };
    }
    static function firstKey($arr){
        reset($arr);
        return key($arr);
    }
    static function lastKey($arr){
        end($arr);
        return key($arr);
    }
    static function firstElement($arr){
        return reset($arr);
    }
    static function lastElement($arr){
        return end($arr);
    }
    static function elementAt($arr,$i){
        return $arr[$i];
    }
    /**
    * Parses a string into variables to be stored in an array.
    *
    * Uses {@link http://www.php.net/parse_str parse_str()} and stripslashes if
    * {@link http://www.php.net/magic_quotes magic_quotes_gpc} is on.
    *
    * Based On: wp_parse_str from WordPress wp-includes/formatting.php
    *
    * @since 2.2.1
    *
    * @param string $string The string to be parsed.
    * @param array $array Variables will be stored in this array.
    */
    static function cxq_parse_str( $string, &$array ) {
        parse_str( $string, $array );  //calls the PHP version
        if ( get_magic_quotes_gpc() )
            $array = self::stripslashes_deep( $array );
        /**
        * Filter the array of variables derived from a parsed string.
        *
        * @since 2.3.0
        *
        * @param array $array The array populated with variables.
        */
        //$array = apply_filters( 'self::cxq_parse_str', $array );
    }
    /**
     * Navigates through an array and removes slashes from the values.
     *
     * If an array is passed, the array_map() function causes a callback to pass the
     * value back to the function. The slashes from this value will removed.
     *
     * Based On: stripslashes_deep from WordPress wp-includes/formatting.php
     *
     * @since 2.0.0
     *
     * @param mixed $value The value to be stripped.
     * @return mixed Stripped value.
     */
    static function stripslashes_deep($value) {
        if ( is_array($value) ) {
            $value =  self::array_map('stripslashes_deep', $value);
        } elseif ( is_object($value) ) {
            $vars = get_object_vars( $value );
            foreach ($vars as $key=>$data) {
                $value->{$key} = self::stripslashes_deep( $data );
            }
        } elseif ( is_string( $value ) ) {
            $value = stripslashes($value);
        }
    
        return $value;
    }
    public static function parseTree($tree, $root = null) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($tree as $child => $parent) {
            # A direct child is found
            if($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$child]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'name' => $child,
                    'children' => self::parseTree($tree, $child)
                );
            }
        }
        return empty($return) ? null : $return;    
    }

}
