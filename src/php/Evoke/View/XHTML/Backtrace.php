<?php
/**
 * Backtrace View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\Data,
	LogicException;

/**
 * Backtrace View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Backtrace extends Data
{
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
		if (!isset($this->data))
		{
			throw new LogicException('needs data');
		}
		
		$listItems = array();
		
		foreach ($this->data as $info)
		{
			$infoElements = array(
				array('span',
				      array('class' => 'File'),
				      empty($info['File']) ? '<internal>' : $info['File']));
		
			if (isset($info['Line']))
			{
				$infoElements[] = array(
					'span',
					array('class' => 'Line'),
					'(' . $info['Line'] . ')');
			}
			
			if (isset($info['Class']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Class'), $info['Class']);
			}
			
			if (isset($info['Type']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Type'), $info['Type']);
			}
			
			if (isset($info['Function']))
			{
				$infoElements[] = array(
					'span',	array('class' => 'Function'), $info['Function']);
			}
			
			$listItems[] = array('li', array(), $infoElements);
		}

		return array('ol', array('class' => 'Backtrace'), $listItems);
	}
}
// EOF
