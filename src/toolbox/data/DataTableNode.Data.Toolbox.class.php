<?php
//Note: This MUST remain compatible with the nodes used by Google Org Chart, Google Data Table, etc.
namespace CxQ\Toolbox\Data\DataTable;
class Node{
    private $id='';
    private $value='';
    private $id_parent='';
    private $tooltip='';
    public function __construct($id,$value='',$id_parent='',$tooltip=''){
        $this->id=$id;
        $this->value=($value!='')?$value:$id;
        $this->id_parent=$id_parent;
        $this->tooltip=$tooltip;
    }
    public function getNodeHTML(){
        //[{v:'Jim',  f:'Jim<div style="color:red; font-style:italic">Vice President</div>'},   'Mike', 'VP'],
        return sprintf("[{v:'%s',  f:'%s'}, '%s', '%s']",$this->id, $this->value, $this->id_parent,$this->tooltip);
    }
}