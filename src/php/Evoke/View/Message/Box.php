<?php
/**
 * Message Box View
 *
 * @package View\Message
 */
namespace Evoke\View\Message;

use Evoke\View\View,
	InvalidArgumentException;

/**
 * Message Box View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\Message
 */
class Box extends View
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
	 * Get the view.
	 *
	 * @param mixed[] Parameters to the view.
	 */
	public function get()
	{
		if (!isset($this->data['Description']))
		{
			throw new InvalidArgumentException('needs Description');
		}

		if (!isset($this->data['Title']))
		{
			throw new InvalidArgumentException('needs Title');
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