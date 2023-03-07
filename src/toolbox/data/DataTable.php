<?php
//Ref: https://developers.google.com/chart/interactive/docs/gallery/table
namespace CxQ\Toolbox\Data;
class DataTable{
    public $columns=array();
    public $rows=array();

    public function __construct(){      //array $options=array()
        //$this->options=array_merge($this->options,$options);
    }
    public function addColumn($type,$id=null,$label=null,array $properties=array()){
        $this->columns[]=new DataColumn($type,$id,$label,$properties);
        end($this->columns);
        return key($this->columns);
    }
    public function addRow(){
        $cell_arr = func_get_args();
        $cells=array();
            
        //Allow column values to be passed as an array
        if(count($cell_arr)==1 && is_array($cell_arr[0]) &&!isset($cell_arr[0]['v']) &&!isset($cell_arr[0]['f'])){
             $cell_arr=$cell_arr[0];
        }
        for($c=0;$c<$this->getNumberOfColumns();$c++){
            $cell_data=$cell_arr[$c]??array('v'=>null);
            
            if(!is_array($cell_data)){
                $cells[$c]=new DataCell($cell_data);
            }else{
                $cells[$c]=new DataCell($cell_data['v'], empty($cell_data['f'])?'':$cell_data['f'], empty($cell_data['p'])?array():$cell_data['p']);
            }
        }
        $this->rows[] = new DataRow($cells);
        end($this->rows);
        return key($this->rows);
    }
    public function setRow($i,$cell_arr){
        //$cell_arr = func_get_args();
        $cells=array();

        for($c=0;$c<$this->getNumberOfColumns();$c++){
            $cell_data=isset($cell_arr[$c])?$cell_arr[$c]:array('v'=>null);
            if(!is_array($cell_data)){
                $cells[$c]=new DataCell($cell_data);
            }else{
                $cells[$c]=new DataCell($cell_data['v'], empty($cell_data['f'])?'':$cell_data['f'], empty($cell_data['p'])?array():$cell_data['p']);
            }
        }
        $this->rows[$i] = new DataRow($cells);
    }
    public function addRows($numOrArray=array()){

        if(is_array($numOrArray)){
            foreach($numOrArray as $row){
                call_user_func_array(array($this,'addRow'),$row);
            }
        }elseif(is_numeric($numOrArray )){
            for($r=0;$r<$numOrArray;$r++){
                $this->addRow();
            }
        }else{
            Throw new Exception('Data type not recognized');
        }
    }

    //Returns a clone of the data table.
    //clone()
    
    //Returns the identifier of a given column specified by the column index in the underlying table.
    //getColumnId(columnIndex)

    //Returns the label of a given column specified by the column index in the underlying table.
    //getColumnLabel(columnIndex)
    
    //Returns the formatting pattern used to format the values of the specified column.
    //getColumnPattern(columnIndex)
    
    //Returns a map of all properties for the specified column BY REFERENCE
    //getColumnProperties(columnIndex)
    
    //Returns the value of a named property, or null if no such property is set for the specified column. The return type varies, depending on the property.
    //getColumnProperty(columnIndex, name)
    
    //Returns the minimal and maximal values of values in a specified column.
    //The returned object has properties min and max. If the range has no values, min and max will contain null.
    //getColumnRange(columnIndex)


    //getColumnRole(columnIndex)	String	Returns the role of the specified column.
    

    //Returns the type of a given column specified by the column index.
    public function getColumnType($columnIndex){
        return $this->columns[$columnIndex]->getType();
    }
    //getDistinctValues(columnIndex)	Array of objects  Returns the unique values in a certain column, in ascending order.
    //getFilteredRows(filters)	Array of objects  Returns the row indexes for rows that match all of the given filters. The indexes are returned in ascending order. The output of this method can be used as input to DataView.setRows() to change the displayed set of rows in a visualization.
    //getFormattedValue(rowIndex, columnIndex)	String Returns the formatted value of the cell at the given row and column indexes.

    //Returns the number of columns in the table.
    public function getNumberOfColumns(){
        return count($this->columns);
    }
     public function getNumberOfRows(){//	Number	Returns the number of rows in the table.
          return count($this->rows);
     }
    //getProperties(rowIndex, columnIndex)	Object  Returns a map of all the properties for the specified cell. Note that the properties object is returned by reference, so changing values in the retrieved object changes them in the DataTable.
    //getProperty(rowIndex, columnIndex, name)	Auto    Returns the value of a named property, or null if no such property is set for the specified cell. The return type varies, depending on the property.
    //getRowProperties(rowIndex)	Object       Returns a map of all properties for the specified row. Note that the properties object is returned by reference, so changing values in the retrieved object changes them in the DataTable.
    //getRowProperty(rowIndex, name)	Auto        Returns the value of a named property, or null if no such property is set for the specified row. The return type varies, depending on the property.
    //getSortedRows(sortColumns)	Array of numbers  Returns a sorted version of the table without modifying the order of the underlying data. To permanently sort the underlying data, call sort(). You can specify sorting in a number of ways, depending on the type you pass in to the sortColumns parameter:
    //getTableProperties	Object	Returns a map of all properties for the table.
    //getTableProperty(name)	Auto  Returns the value of a named property, or null if no such property is set for the table. The return type varies, depending on the property.
    //getValue(rowIndex, columnIndex)	Object   Returns the value of the cell at the given row and column indexes.
    //insertColumn(columnIndex, type [,label [,id]])	None   Inserts a new column to the data table, at the specifid index. All existing columns at or after the specified index are shifted to a higher index.
    //insertRows(rowIndex, numberOrArray)	None   Insert the specified number of rows at the specified row index.

    //removeColumn(columnIndex)	None   Removes the column at the specified index.
    public function removeColumn($columnIndex){
         foreach($this->rows as $r=>$row){
              $this->rows[$r]->deleteCell($columnIndex);
         }
         unset($this->columns[$columnIndex]);
    }

    //removeColumns(columnIndex, numberOfColumns)	None   Removes the specified number of columns starting from the column at the specified index.
    //numberOfColumns is the number of columns to remove.    columnIndex should be a number with a valid column index.
    //removeRow(rowIndex)	None Removes the row at the specified index.
    //removeRows(rowIndex, numberOfRows)	None	Removes the specified number of rows starting from the row at the specified index.

    //Sets the value, formatted value, and/or properties, of a cell.
    public function setCell($rowIndex, $columnIndex , $value=null, $formattedValue=null, $properties=array()){
        //if(is_null($this->rows[$rowIndex])) $this->setRow($rowIndex,array());
        $this->rows[$rowIndex]->setCell($columnIndex,new DataCell($value, $formattedValue,$properties));
    }

    //setColumnLabel(columnIndex, label)	None     Sets the label of a column.
    //setColumnProperty(columnIndex, name, value)	None   Sets a single column property. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setColumnProperties(columnIndex, properties)	None   Sets multiple column properties. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setFormattedValue(rowIndex, columnIndex, formattedValue)	None Sets the formatted value of a cell.
    //setProperty(rowIndex, columnIndex, name, value)	None	Sets a cell property. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setProperties(rowIndex, columnIndex, properties)	None	   Sets multiple cell properties. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setRowProperty(rowIndex, name, value)	None	Sets a row property. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setRowProperties(rowIndex, properties)	None Sets multiple row properties. Some visualizations support row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setTableProperty(name, value)	None	Sets a single table property. Some visualizations support table, row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setTableProperties(properties)	None	 Sets multiple table properties. Some visualizations support table, row, column, or cell properties to modify their display or behavior; see the visualization documentation to see what properties are supported.
    //setValue(rowIndex, columnIndex, value)	None	Sets the value of a cell. In addition to overwriting any existing cell value, this method will also clear out any formatted value and properties for the cell.
    //sort(sortColumns)	None	Sorts the rows, according to the specified sort columns. The DataTable is modified by this method. See getSortedRows() for a description of the sorting details. This method does not return the sorted data.
    //toJSON()	String	Returns a JSON representation of the DataTable that can be passed into the DataTable constructor



    public function x_addNode($id,$value='',$id_parent='',$tooltip=''){
        $this->nodes[$id]=new \CxQ\Toolbox\Data\DataTable\Node($id,$value,$id_parent,$tooltip);
    }

}
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
