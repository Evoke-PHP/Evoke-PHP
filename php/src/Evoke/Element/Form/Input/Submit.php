<?php
namespace Evoke\Element\Form\Input;

class Submit extends \Evoke\Element
{
	/** Construct an input submit element.
	 *  @param attribs @array Attributes for the input submit element.
	 */
	public function __construct(Array $attribs = array())
	{
		$attribs['type'] = 'submit';
  
		parent::__construct($attribs);
	}
	
   /******************/
   /* Public Methods */
   /******************/

	/** Set and return the submit element with any further attributes specified.
	 *  @param attribs @array Any attributes that need to be overriden.
	 *  @return The element as a simple array.
	 */
	public function set(Array $attribs=array())
	{
		return parent::set(array('input', $attribs));
	}
}
// EOF