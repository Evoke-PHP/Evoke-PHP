<?php
/**
 * Message Box View
 *
 * @package View
 */
namespace Evoke\View;

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
class MessageBox extends View
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
		if (!isset($this->params['Description']))
		{
			throw new InvalidArgumentException('needs Description');
		}

		if (!isset($this->params['Title']))
		{
			throw new InvalidArgumentException('needs Title');
		}
      
		return array('div',
		             $this->attribs,
		             array(array('div',
		                         array('class' => 'Title'),
		                         $this->params['Title']),
		                   array('div',
		                         array('class' => 'Description'),
		                         $this->params['Description'])));
	}
}
// EOF