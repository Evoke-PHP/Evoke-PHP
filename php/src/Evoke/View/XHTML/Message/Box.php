<?php
namespace Evoke\View\XHTML\Message;

use Evoke\View\ViewIface,
	InvalidArgumentException;

/**
 * Message Box
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Box implements ViewIface
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
	public function get(Array $message=array())
	{
		if (!isset($message['Description']))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' needs Description');
		}

		if (!isset($message['Title']))
		{
			throw new InvalidArgumentException(__METHOD__ . ' needs Title');
		}
      
		return array(
			'div',
			$this->attribs,
			array(array('div',
			            array('class' => 'Title'),
			            $message['Title']),
			      array('div',
			            array('class' => 'Description'),
			            $message['Description'])));
	}
}
// EOF