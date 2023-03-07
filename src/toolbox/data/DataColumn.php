<?php
//Ref: https://developers.google.com/chart/interactive/docs/gallery/table
namespace CxQ\Toolbox\Data;
class DataColumn{
    protected $type;
    protected $id;
    protected $label;
    protected $properties=array();
    public function __construct($type,$id=null,$label=null,array $properties=array()){
        $this->type=$type;
        $this->id=$id;
        $this->label=$label;
        $this->properties=$properties;
    }
    public function setProperty($k,$v){
        $this->properties[$k]=$v;
    }
    public function getProperties(){
        return $this->properties;
    }
    public function deleteProperty($k){
        unset($this->properties[$k]);
    }
    public function getID(){
        return $this->id;
    }
    public function getType(){
        return $this->type;
    }
    public function getLabel(){
        return $this->label;
    }

}
