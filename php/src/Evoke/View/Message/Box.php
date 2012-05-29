<?php
namespace Evoke\View\Message;

use Evoke\Service\TranslatorIface,
	InvalidArgumentException;

class Box extends \Evoke\View
{
	/** @property attribs
	 *  @array Message Box attributes.
	 */
	protected $attribs;

	/** Construct a Box object.
	 *  @param translator @object Translator.
	 *  @param attribs    @array  Message Box attributes.
	 */
	public function __construct(
		TranslatorIface $translator,
		Array           $attribs = array('class' => 'Message_Box Info'))
	{
		parent::__construct($translator);
		
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