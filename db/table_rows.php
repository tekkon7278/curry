<?php

Loader::load('SqlAbstract', 'db');

/**
 * TableRowsIterator
 *
 * @category   Curry
 * @package    db
 * @copyright  Copyright (c) 2011 www.curryfw.net.
 * @license    MIT License
 */
class TableRowsIterator extends ArrayIterator
{
	protected $_model;
	
	public function setModel($model)
	{
		$this->_model = $model;
	}
	
	public function current()
	{
    	$row = parent::current(); 
    	$tableRow = new TableRow($row, $this->_model);
		return $tableRow;
	}
}

/**
 * TableRows
 *
 * @category   Curry
 * @package    db
 * @copyright  Copyright (c) 2011 www.curryfw.net.
 * @license    MIT License
 */
class TableRows extends ArrayObject
{
	protected $_model;
	
	public function __construct($rows, $model)
	{
		if (!($model instanceof Model)) {
			$model = new Model($model);
		}
		$this->_model = $model; 
		parent::__construct($rows);
		$this->setIteratorClass('TableRowsIterator');
		$iterator = $this->getIterator();
		$iterator->setModel($model);
	}
	
	public function getRow($index)
	{
		if (!$this->offsetExists($index)) {
			return false;
		}
		return $this->offsetGet($index);
	}
	
	public function add($row)
	{
		if (is_array($row)) {
			$row = new TableRow($row, $this->_model);
		}
		if (!($row instanceof TableRow)) {
			throw new Exception('First argument must be instance of TableRow or array.');
		}
		$this->offsetSet($this->count(), $row);
		$row->setState(TableRowState::INSERTED);
		return true;
	}
	
	public function deleteRow($index)
	{
		if ($this->offsetExists($index)) {
			$row = $this->offsetGet($index);
			if ($row instanceof TableRow) {
				$row->delete();
			}
		}
	}
	
	public function offsetGet($index)
	{
		$row = parent::offsetGet($index);
		if (!$row) {
			return false;
		}
		return $row;
	}
	
	public function offsetSet($index, $row)
	{
	}
				
	public function toArray()
	{
		return $this->getArrayCopy();
	}
	
	
}