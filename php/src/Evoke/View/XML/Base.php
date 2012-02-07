<?php
namespace Evoke\View\XML;

/// The base class for XML views.
abstract class Base extends \Evoke\View\Base
{ 
	protected $xwr; ///< The XML writer resource.
   
	/// Construct the View.
	public function __construct(Array $setup)
	{
		$setup += array('XWR' => NULL);
      
		parent::__construct($setup);

		if (!$this->setup['XWR'] instanceof \Evoke\Core\XWR)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires XWR');
		}

		$this->xwr =& $this->setup['XWR'];
	}
}
// EOF