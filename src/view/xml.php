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

      $this->app->needs(
	 array('Instance' => array('XWR' => $this->setup['XWR'])));

      $this->xwr =& $this->setup['XWR'];
   }
}
// EOF