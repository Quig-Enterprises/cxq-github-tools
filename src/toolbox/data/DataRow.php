<?php
//Ref: https://developers.google.com/chart/interactive/docs/gallery/table
namespace CxQ\Toolbox\Data;
class DataRow{
    protected $cells=array();
    protected $properties=array();
    public function __construct(array $DataCells,array $properties=array()){
        $this->cells=$DataCells;
        $this->properties=$properties;
    }
    public function setProperty($k,$v){
        $this->properties[$k]=$v;
    }
    public function deleteProperty($k){
        unset($this->properties[$k]);
    }
    public function setCell($k, DataCell $Cell){
        $this->cells[$k]=$Cell;
    }
    public function deleteCell($k){
        unset($this->cells[$k]);
    }
    public function getCells(){
        return $this->cells;
    }
    public function toArray(){
        $data = array();
        foreach($this->cells as $c=>$cell){
            if(!empty($cell)){
                $this_data=array_filter(array('v'=>$cell->getValue(),'f'=>$cell->getFormattedValue(),'p'=>$cell->getProperties()));
            }
            $data[$c]=isset($this_data['v'])?$this_data:array('v'=>null);
        }
        return $data;
    }
    public function toJSON(){
        return json_encode($this->toArray());
    }
}