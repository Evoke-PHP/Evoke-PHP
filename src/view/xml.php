<?php
/// View_XML is an XML view of data.
abstract class View_XML extends View
{ 
   protected $xwr; ///< XWR
   
   /// Construct the View.
   public function __construct(Array $setup)
   {
      $setup += array('XWR' => NULL);
      
      parent::__construct($setup);

      if (!$this->setup['XWR'] instanceof XWR)
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires XWR');
      }

      $this->xwr =& $this->setup['XWR'];
   }
}
// EOF