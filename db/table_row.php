<?php

Loader::load('SqlAbstract', 'db');

class TableRowState
{
	const ISOLATED = 0;
	const ORIGINAL = 1;
	const UPDATED  = 2;
	const INSERTED = 3;
	const DELETED  = 4;	
}

/**
 * SqlSelect
 *
 * @category   Curry
 * @package    db
 * @copyright  Copyright (c) 2011 www.curryfw.net.
 * @license    MIT License
 */
class TableRow extends ArrayObject
{
	protected $_parentTable;
	protected $row;
	protected $values = array();
	protected $isNewRow = false;
	protected $_state = TableRowState::ISOLATED;
	
	public function __construct($row = array(), $parentTable = null)
	{
		$this->setFlags(ArrayObject::ARRAY_AS_PROPS);		
		if ($tableRows instanceof TableRows) {
			$this->_parentTable = $parentTable;
		}		
		if (is_array($row) == false) {
			$row = array();
		}
		if (!$row) {
			$this->isNewRow = true;
		}
		parent::__construct($row);
	}
	
	public function __get($columnName)
	{
		return $this->offsetGet($column);
	}
	
	public function __set($columnName, $value)
	{
		return $this->offsetSet($columnName, $value);
	}
	
	public function offsetSet($column, $value)
	{
		if (isset($this->_values[$column]) && $this->_values[$column] != $value) {
			$this->_values[$column] = $value;
			$this->_state = TableRowState::UPDATED;
		}
	}
	
	public function offsetGet($index)
	{
		if (!$this->offsetExists($index)) {
			return null;
		}
		return parent::offsetGet($index);
	}
	
	public function setState($state)
	{
		$this->_state = $state;
	}
	
	public function getState()
	{
		return $this->_state;
	}
	
	public function toArray()
	{
		return $this->getArrayCopy();
	}
	
	public function delete()
	{
		$this->_state = TableRowState::DELETED;
	}
	
	
}