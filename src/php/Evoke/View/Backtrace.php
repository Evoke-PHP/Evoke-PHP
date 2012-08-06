<?php
namespace Evoke\View\XHTML;

use Evoke\Model\Data\DataIface,
	Evoke\View\ViewIface;

/**
 * Backtrace
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Backtrace implements ViewIface
{ 
	/**
	 * Data
	 * @var Evoke\Model\Data\DataIface
	 */
	protected $data;

	/**
	 * Attribs
	 * @var mixed[]
	 */
	protected $attribs;

	/**
	 * Construct a Backtrace object.
	 *
	 * @param Evoke\Model\Data\DataIface Data
	 * @param mixed[]                    attribs
	 */
	public function __construct(
		DataIface $data,
		Array     $attribs = array('class' => 'Backtrace'))
	{
		$this->data            = $data;
		$this->attribs         = $attribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the backtrace.
	 *
	 * @param mixed[] Paramaters to the view.
	 *
	 * @return mixed[] The view.
	 */
	public function get(Array $params = array())
	{
		$listItems = array();
		
		foreach ($this->data as $level => $info)
		{			
			$stackLineElements = array(
				'span',
				array('class' => 'File'),
				empty($info['File']) ? '<internal>' : $info['File']);
		
			if (!empty($info['Line']))
			{
				$stackLineElements[] = array(
					'span',
					array('class' => 'Line'),
					'(' . $info['Line'] . ')');
			}
			
			$stackLineElements[] = array(
				'span',	array('class' => 'Class'), $info['Class']);
			$stackLineElements[] = array(
				'span',	array('class' => 'Type'), $info['Type']);
			$stackLineElements[] = array(
				'span',	array('class' => 'Function'), $info['Function']);
			
			$listItems[] = array('li', array(), $stackLine);
		}

		return array('ol', $this->attribs, $listItems);
	}
}
// EOF
