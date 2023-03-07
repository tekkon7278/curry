<?php

/**
 * SqlExpression
 *
 * Copyright (c) 2011 Curry PHP Framework developers.
 * This software is released under the MIT License.
 *
 * @category   Curry
 * @package    db
 * @copyright  Copyright (c) 2011 Curry PHP Framework developers
 * @link       http://www.curryfw.net
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SqlExpression
{
	protected $expression;
	
	public function __construct($expression)
	{
		$this->expression = $expression;
	}
	
	public function getExpression()
	{
		return $this->expression;
	}
}