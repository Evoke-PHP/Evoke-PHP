<?php
/**
 * Backtrace View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface,
	LogicException;

/**
 * Backtrace View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Backtrace implements ViewIface
{
	/**
	 * Backtrace data.
	 * @var mixed[]
	 */
	protected $backtrace;
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the backtrace.
	 *
	 * @return mixed[] The view.
	 */
	public function get()
	{
		if (empty($this->backtrace))
		{
			throw new LogicException('needs backtrace.');
		}
		
		$listItems = array();
		
		foreach ($this->backtrace as $info)
		{
			$infoElements = array(
				array('span',
				      array('class' => 'File'),
				      empty($info['file']) ? '<internal>' : $info['file']));
		
			if (isset($info['line']))
			{
				$infoElements[] = array(
					'span',
					array('class' => 'Line'),
					'(' . $info['line'] . ')');
			}
			
			if (isset($info['class']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Class'), $info['class']);
			}
			
			if (isset($info['type']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Type'), $info['type']);
			}
			
			if (isset($info['function']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Function'), $info['function']);
			}
			
			$listItems[] = array('li', array(), $infoElements);
		}

		return array('ol', array('class' => 'Backtrace'), $listItems);
	}

	/**
	 * Set the backtrace data.
	 *
	 * @param mixed[] The backtrace data.
	 */
	public function set(Array $backtrace)
	{
		$this->backtrace = $backtrace;
	}
}
// EOF
