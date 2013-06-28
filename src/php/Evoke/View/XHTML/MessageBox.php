<?php
/**
 * Message Box View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\Data,
	LogicException;

/**
 * Message Box View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class MessageBox extends Data
{
	/**
	 * Message Box attributes.
	 * @var mixed[]
	 */
	protected $attribs;

	/**
	 * Construct a Box object.
	 *
	 * @param mixed[] Message Box attributes.
	 */
	public function __construct(
		Array $attribs = array('class' => 'Message_Box Info'))
	{
		$this->attribs = $attribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the output for the view.
	 */
	public function get()
	{
		if (!isset($this->data['Description'], $this->data['Title']))
		{
			throw new LogicException('needs Data with Description and Title');
		}
      
		return array('div',
		             $this->attribs,
		             array(array('div',
		                         array('class' => 'Title'),
		                         $this->data['Title']),
		                   array('div',
		                         array('class' => 'Description'),
		                         $this->data['Description'])));
	}
}
// EOF