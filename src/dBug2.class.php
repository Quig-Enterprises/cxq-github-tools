<?php
namespace CxQ;
if(!class_exists('dBug')) require_once('dBug.class.php');
class dBug extends \dBug{
    function __construct($var,$forceType="",$bCollapsed=false){
        $backtrace=debug_backtrace();
        $fileName=basename($backtrace[0]['file']);
        $line=$backtrace[0]['line'];
        $var=array("value"=>$var,"called_at"=>$fileName.':'.$line);


         parent::__construct($var,$forceType,$bCollapsed);

        //return new dBug2(array($var,"called_at"=>$fileName.':'.$line),$forceType,$bCollapsed);
    }
    function asString($var,$forceType="",$bCollapsed=false){
          $backtrace=debug_backtrace();
          $fileName=basename($backtrace[0]['file']);
          $line=$backtrace[0]['line'];
          $var=array("value"=>$var,"called_at"=>$fileName.':'.$line);

          ob_start();
          new dBug($var,$forceType,$bCollapsed);
          $html = ob_get_contents();
          ob_end_clean();
          return $html;
    }



    	//override in order to get variable name that was sent to dBug2
    	function getVariableName() {
    		$arrBacktrace = array_reverse(debug_backtrace());
    
    		//possible 'included' functions
    		$arrInclude = array("include","include_once","require","require_once");

    		//check for any included/required files. if found, get array of the last included file (they contain the right line numbers)
    		for($i=0; $i<count($arrBacktrace); $i++) {
                    $arrCurrent = $arrBacktrace[$i];
                    if($i>0){
                        $arrPrevious = $arrBacktrace[$i-1];
                    }

                    if(array_key_exists("function", $arrCurrent) &&
                        (in_array($arrCurrent["function"], $arrInclude) || (0 != strcasecmp($arrCurrent["function"], "dBug")))){
                            $arrFile = $arrCurrent;
                            continue;
                        }

                    //$arrFile = $arrCurrent;
                    $arrFile =$arrPrevious;

                    break;

    		}


    		if(isset($arrFile)) {
    			$arrLines = file($arrFile["file"]);
    			$code = $arrLines[($arrFile["line"]-1)];

    			//find call to dBug2 class
    			preg_match('/\bnew dBug2\s*\(\s*(.+)\s*\);/i', $code, $arrMatches);



                        return isset($arrMatches[1])?$arrMatches[1]:'';

    		}
    		return "";
	}
}