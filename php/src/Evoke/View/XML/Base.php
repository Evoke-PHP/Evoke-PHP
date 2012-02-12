<?php
namespace Evoke\View\XML;

/// The base class for XML views.
abstract class Base extends \Evoke\View\Base
{ 
	protected $XWR; ///< The XML Writer Resource.
   
	/// Construct the View.
	public function __construct(Array $setup)
	{
		$setup += array('XWR' => NULL);

		if (!$setup['XWR'] instanceof \Evoke\Core\XWR)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires XWR');
		}
      
		parent::__construct($setup);

		$this->XWR = $setup['XWR'];
	}
}
// EOF