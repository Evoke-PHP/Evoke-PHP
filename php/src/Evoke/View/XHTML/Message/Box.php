<?php
namespace Evoke\View\XHTML\Message;

use Evoke\View\ViewIface,
	InvalidArgumentException;

class Box implements ViewIface
{
	/** @property attribs
	 *  @array Message Box attributes.
	 */
	protected $attribs;

	/** Construct a Box object.
	 *  @param attribs @array Message Box attributes.
	 */
	public function __construct(
		Array $attribs = array('class' => 'Message_Box Info'))
	{
		$this->attribs = $attribs;
	}

	/******************/
	/* Public Methods */
	/******************/

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