<?php
namespace Evoke\View\Form;

use Evoke\View\ViewIface;

/** \abstract form class providing a generic interface to write a form.
 *  Elements are added to the Form using the \ref setElements method which
 *  must be defined by derived classes.  The form is written using the
 *  \ref write method.
 *
 * @todo Check whether this class is obsolete.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
abstract class Form implements ViewIface
{
	/** The elements that should be written to make the form. These must be
	 *  added to using the \ref addElement method.
	 */
	private $elements = array();

	/// The form setup options.
	protected $setup;
	    
	/// Construct the Form object.
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		$this->setup = array_merge(
			array('Append_Elements'       => array(),
			      'Attribs'               => array('action' => '',
			                                       'method' => 'post'),
			      'Encasing'              => true,
			      'Encasing_Tag'          => 'div',
			      'Encasing_Attribs'      => array('class' => 'Form_Row'),
			      'Prepend_Elements'      => array(),
			      'Submit_Button_Attribs' => array('class' => 'Form_Buttons'),
			      'Submit_Buttons'        => array()),
			$setup);

		$this->buildFormChildren();

		parent::__set(array('form', array(), $this->elements));
	}

	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $params = array())
	{
		/** @todo Implement this method.
		 */
		throw new \RuntimeException(__METHOD__ . ' not yet implemented.');
	}
		
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Add an element to the Form.
	 *  @param elem \mixed The element or elements to add to the form.
	 */
	protected function addElement($elem)
	{
		$this->elements[] = $elem;
	}

	/// Build elements at the end of the form (but before the buttons).
	protected function buildAppendElements()
	{
		foreach ($this->appendElements as $appendElement)
		{
			$this->addElement($appendElement);
		}
	}

	/// Build the buttons that are contained in the form.
	protected function buildFormButtons()
	{
		$this->addElement(array('div',
		                        $this->submitButtonAttribs,
		                        $this->submitButtons));
	}

	/// Build all of the elements that make up the form.
	protected function buildFormChildren()
	{
		$this->buildPrependElements();
		$this->buildFormElements();
		$this->buildAppendElements();
		$this->buildFormButtons();
	}

	/** @todo Remove buildFormElements and replace with View::get.
	 */
	/// \abstract function to build the non button elements of the form.
	/// abstract protected function buildFormElements();

	/// Build elements at the start of the form.
	protected function buildPrependElements()
	{
		foreach ($this->prependElements as $prependElement)
		{
			$this->addElement($prependElement);
		}
	}
   
	/** Build a row for the form.
	 *  @param rowElems \mixed An element or array of elements for the row.
	 */
	protected function buildRow(Array $rowElems)
	{
		if (!$this->encasing)
		{
			return $rowElems;
		}

		return array($this->encasingTag, $this->encasingAttribs, $rowElems);
	}
}
// EOF