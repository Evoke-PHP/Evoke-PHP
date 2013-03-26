<?php
namespace Evoke\View;

use Evoke\Model\Data\DataIface,
	Evoke\View\ViewIface,
	InvalidArgumentException;

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
	 * Attribs
	 * @var mixed[]
	 */
	protected $attribs;

	/**
	 * Construct a Backtrace object.
	 *
	 * @param mixed[] attribs
	 */
	public function __construct(
		Array     $attribs = array('class' => 'Backtrace'))
	{
		$this->attribs = $attribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the backtrace.
	 *
	 * @param mixed   The data for the view.
	 * @param mixed[] Paramaters to the view.
	 *
	 * @return mixed[] The view.
	 */
	public function get(/* Mixed */ $data = NULL, Arrray $params = array())
	{
		if (!is_array($data) && !$data instanceof Traversable)
		{
			throw new InvalidArguementException(
				'needs data as array or Traversable');
		}
		
		$listItems = array();
		
		foreach ($data as $level => $info)
		{
			$stackLineElements = array(
				array('span',
				      array('class' => 'File'),
				      empty($info['File']) ? '<internal>' : $info['File']));
		
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
			
			$listItems[] = array('li', array(), $stackLineElements);
		}

		return array('ol', $this->attribs, $listItems);
	}
}
// EOF
