<?php
/**
 * Message Box View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\MessageBoxIface;

/**
 * Message Box View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class MessageBox implements MessageBoxIface
{
	/**
	 * Protected properties.
	 *
	 * @var mixed[] $attribs  Attributes.
	 * @var mixed[] $elements Content elements.
	 * @var string  $title    Title.
	 */
	protected $attribs, $contentElements, $title;
	
	/**
	 * Construct a Box object.
	 *
	 * @param mixed[] Message Box attributes.
	 */
	public function __construct(
		Array $attribs = array('class' => 'Message_Box Info'))
	{
		$this->attribs         = $attribs;
		$this->contentElements = array();
		$this->title           = 'Message Box';
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a content element to the message box.
	 *
	 * @param mixed Message box element.
	 */
	public function addContent($element)
	{
		$this->contentElements[] = $element;
	}
	
	/**
	 * Get the output for the view.
	 *
	 * @return mixed[] Output of the view.
	 */
	public function get()
	{
		return array(
			'div',
			$this->attribs,
			array(array('div', array('class' => 'Title'), $this->title),
			      array('div',
			            array('class' => 'Content'),
			            $this->contentElements)));
	}
	
	/**
	 * Set the title for the message box.
	 *
	 * @param string Title of the message box.
	 */
	public function setTitle(/* String */ $title)
	{
		$this->title = $title;
	}
}
// EOF