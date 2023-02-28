<?php
namespace CxQ\Toolbox\Data\Presentation;
class Chart{
    private $div_name='chart_div';
    private $nodes=array();
    private $type='';
    private $options=array();
    private $parameters=array();
    private $visualization_method;
    private $DataTable=null;

    public function __construct($type, \CxQ\Toolbox\Data\DataTable $DataTable){
        $this->DataTable=$DataTable;
        $this->type=$type;

        $this->options['allowHtml']=false;
        $this->options['alternatingRowStyle']=true;
        $this->options['cssClassNames']=array('headerRow'=>null,'tableRow'=>null,'oddTableRow'=>null,'selectedTableRow'=>null,'hoverTableRow'=>null,'headerCell'=>null,'tableCell'=>null,'rowNumberCell'=>null);
        $this->options['firstRowNumber']=1;
        $this->options['frozenColumns']=null;
        $this->options['height']='automatic';
        $this->options['page']='disable'; //enable, event, disable
        $this->options['pageSize']=10;
        $this->options['pagingButtons']='auto';
        $this->options['rtlTable']=false;
        $this->options['scrollLeftStartPosition']=0;
        $this->options['showRowNumber']=false;
        $this->options['sort']='enable';
        $this->options['sortAscending']=true;
        $this->options['sortColumn']=-1;
        $this->options['startPage']=0;
        $this->options['width']='automatic';
    }
    public function setDivName($div_name){
        $this->div_name=$div_name;
    }
    public function getDivName(){
        return $this->div_name;
    }
    public function setOptions(array $options=array()){
        //new \dBug2(array($this->options,$options));
        $this->options=array_merge($this->options,$options);
    }
    public function setOption($k,$v){
        $this->options[$k]=$v;
    }

    public function getOption($k){
        return $this->options[$k];
    }

    public function getSortInfo(){
        //TODO: write this
    }
    public function getHTML(){
        //TODO: add properties to table
        $html=sprintf('<table name="%s">',$this->getDivName());

        $header_class=empty($this->options['cssClassNames']['headerRow'])?'':sprintf('class="%s"',$this->options['cssClassNames']['headerRow']);

        $html.="\n<tr{$header_class}>";
        $cell_class=empty($this->options['cssClassNames']['headerCell'])?'':sprintf('class="%s"',$this->options['cssClassNames']['headerCell']);

        if($this->getOption('showRowNumber')){
            $html.=sprintf('<th%s>%s</th>',$cell_class,'');

        }
        foreach($this->DataTable->columns as $col){
            $html.=sprintf('<th%s>%s</th>',$cell_class,$col->getLabel());
        }
        $html.='</tr>';

        foreach($this->DataTable->rows as $r=>$row){
            $row_class=empty($this->options['cssClassNames']['tableRow'])?'':sprintf('class="%s"',$this->options['cssClassNames']['tableRow']);
            $cell_class=empty($this->options['cssClassNames']['tableCell'])?'':sprintf('class="%s"',$this->options['cssClassNames']['tableCell']);
            if($r%2==0 && $this->getOption('alternatingRowStyle')){
                //Odd Numbered Row (zero-based)
                $row_class=empty($this->options['cssClassNames']['oddTableRow'])?'':sprintf('class="%s"',$this->options['cssClassNames']['oddTableRow']);
            }

            $html.="\n<tr{$row_class}>";
            if($this->getOption('showRowNumber')){
                $html.=sprintf('<td%s>%s</td>',$cell_class,$this->getOption('firstRowNumber')+$r);
    
            }
            foreach($row->getCells() as $c=>$cell){
                $properties=$cell->getProperties();
                $html.=sprintf('<td%s>%s</td>',$cell_class,$cell->getFormattedValue());
            }
            $html.='</tr>';
        }
        $html.="\n</table>";
        
        return $html;
    }





}