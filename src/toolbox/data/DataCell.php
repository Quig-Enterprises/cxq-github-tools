<?php
//Ref: https://developers.google.com/chart/interactive/docs/gallery/table
namespace CxQ\Toolbox\Data;
class DataCell{
    protected $value='';
    protected $value_formatted='';
    protected $properties=array();

    public function __construct($value='',$value_formatted='',$properties=array()){
        if(empty($value_formatted)) $value_formatted=$value;
        $this->value=$value;
        $this->value_formatted=$value_formatted;
        $this->properties=$properties;
    }
    public function setProperty($k,$v){
        $this->properties[$k]=$v;
    }
    public function deleteProperty($k){
        unset($this->properties[$k]);
    }
    public function getProperties(){
        return $this->properties;
    }
    public function setValue($value){
        $this->value=$value;
    }
    public function getValue(){
        return $this->value;
    }
    public function setFormattedValue($value){
        $this->value_formatted=$value;
    }
    public function getFormattedValue(){
        return $this->value_formatted;
    }

}
