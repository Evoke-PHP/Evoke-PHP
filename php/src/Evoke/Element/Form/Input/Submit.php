<?php
namespace Evoke\Element\Form\Input;

class Submit extends \Evoke\Element\Base
{ 
	public function __construct(Array $setup)
	{
		$setup += array('Attribs' => array());
		$setup['Attribs']['type'] = 'submit';
  
		parent::__construct($setup);
	}
	
   /******************/
   /* Public Methods */
   /******************/

	/** Set and return the submit element with any further attributes specified.
	 *  @param attribs \array Any attributes that need to be overriden.
	 *  \return The element as a simple array.
	 */
	public function set(Array $attribs=array())
	{
		return parent::set(array('input',
		                         array_merge($this->attribs, $attribs)));
	}
}
// EOF