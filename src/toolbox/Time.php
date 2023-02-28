<?php
namespace CxQ\Toolbox;
class Time{
    /******************************************
    *   Time Operations
    *******************************************/
    static function ConvertTime_ExcelToPHP($excelTime,$tz_offset_hrs=0) {
        // $tz_offset_hrs represents hours to offset the time to account for timezone changes
        $unixValue = ($excelTime - 25569) * 86400+$tz_offset_hrs*60*60;
        $d = new DateTime('@' . (int)$unixValue, new DateTimeZone('UTC'));
        //new dBug2($d->format('Y-m-d H:i:s'));
        return $d->format('Y-m-d H:i:s');
    }
    static function is_date($datetime){
        return (strtotime($datetime)!==false);
    }
    static function formatDate($datetime,$format='m/d/y g:i A',$flags=array()){
        //Ex: \Toolbox::formatDate($this->$key,'SQL','hide_epoch');
        //Ex: \Toolbox::formatDate($this->$key,'SQL',array('hide_epoch'));
        //Ex: \Toolbox::formatDate($this->$key,'SQL',array('hide_epoch'=>'-'));

        $datetime = strtotime($datetime);

        if(!is_array($flags)) $flags = array($flags);

        foreach($flags as $flag=>$value){
            if(is_numeric($flag)){
                $flag = $value;
                $value = '';
            }
            switch(trim($flag)){
                case 'hide_epoch':
                    if(strtotime('1970-01-01 01:00:00')==$datetime){
                        return $value;
                    }
                    break;

            }
        }

        //Check for named formats:
        //Reference: https://msdn.microsoft.com/en-us/library/362btx8f(v=vs.90).aspx
        switch($format){
            //Dates:
            case 'Short Date':
                $format = 'n/d/y';
                break;
            case 'Long Date':
                $format = 'F j, Y';
                break;

            //Times:
            case 'Short Time':
                $format = 'g:i A';
                break;
            case 'Long Time':
                $format = 'g:i:s A';
                break;

            //Dates AND Times
            case 'General':
            case 'General Date':
                $format = 'n/d/y g:i A';
                break;
            case 'Long Date, Long Time':
                $format = 'F j, Y g:i:s A';
                break;
            case 'Long Date, Short Time':
                $format = 'F j, Y g:i A';
                break;
            case 'SQL':
                $format = 'Y-m-d H:i:s';
                break;
        }

        return date($format, $datetime);
    }


}
