<?php
namespace CxQ;
class Toolbox{
    // list of implemented classes
    private static $classes = array('Strings',
                                    'Arrays',
                                    'Numerical',
                                    'Time',
                                    'System',
                                    'Files',
                                    'Net',
                                    'JSON',
                                    'XML',
                                    'Logical',
                                    'SQL',
                                    'URL',
                                    'Logs',
                                    'DataTable',
                                    'DataTableNode');


     // creating all objects
     function __construct() {
          foreach($this->classes as $className){
               require_once(__DIR__.$className.'.class.php');
               $this->objects[] = new $className;
          }
     }

     // looking for class method in all the objects
     public static function __callStatic($method, $args) {
          $objects = array();
          // if($method!='cyclecheck') \Toolbox::cyclecheck(__FILE__.'-'.$method,__LINE__);

          foreach(self::$classes as $i=>$className){
               require_once(__DIR__.'/'.$className.'.Toolbox.class.php');

               $className='CxQ\\Toolbox\\'.$className;
               $objects[$i] = new $className;
               if(method_exists($objects[$i],$method)){
                    break;
               }

          }

          //\Toolbox::backtrace($method, true);
          //new \dBug2($method);
          //This function runs if the requested static function is not defined within this class
          foreach($objects as $object) {
               $callback = array($object, $method);
               if(is_callable($callback)){
                    return call_user_func_array($callback, $args);
               }
          }

          //new dBug2("Method `{$method}` not recognized");
          throw new Exception("Method `{$method}` not recognized");
     }

}
