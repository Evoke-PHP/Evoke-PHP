<?php
namespace Evoke\View\XML;

/// The base class for XML views.
abstract class Base extends \Evoke\View\Base
{ 
	/// Construct the View.
	public function __construct(Array $setup)
	{
		$setup += array('Writer' => NULL);

		// The writer must additionaly be an XWR type writer for the XML Base.
		if (!$setup['Writer'] instanceof \Evoke\Core\XWR)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires XWR');
		}
      
		parent::__construct($setup);
	}
}
// EOF