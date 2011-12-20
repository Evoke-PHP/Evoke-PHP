<?php


/// View_Page is the view for an XHTML page.
abstract class View_Page extends View
{ 
   protected $xwr; ///< XWR
   
   /// Construct the View.
   public function __construct(Array $setup)
   {
      $setup += array('Joint_Key'     => 'Joint_Data',
		      'Start'         => array(),
		      'Start_Base'    => array(
			 'CSS' => array('/csslib/global.css',
					'/csslib/common.css')),
		      'XWR'           => NULL);
      
      parent::__construct($setup);
      
      $this->app->needs(
	 array('Instance' => array('XWR' => $this->setup['XWR'])));

      $this->xwr =& $this->setup['XWR'];
   }

   /******************/
   /* Public Methods */
   /******************/

   public function write($data)
   {
      $this->writeStart();

      if (isset($data['Header']))
      {
	 $this->writeHeader($data['Header']);
	 unset($data['Header']);
      }

      $this->writeContent($data);
      $this->writeEnd();
   }

   abstract public function writeContent($data);
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /// Finish the page content and XHTML page.
   protected function writeEnd()
   {
      $this->xwr->write(array('div', array(), array('Start' => false)));
      $this->xwr->writeEnd();
   }

   /// Write the standard menu bar and header for the page.
   protected function writeHeader($data)
   {
      $menu = $this->app->get('Data_Menu');
      $menu->setData($data['Menu']);
      
      $this->xwr->write($this->app->get(
			   'Element_Header',
			   array('App'        => $this->app,
				 'Menu'       => $menu->getMenu('Main_Menu'),
				 'Translator' => $this->tr)));
   }
   
   /// Write the head and start of the page content.
   protected function writeStart()
   {
      $start = $this->setup['Start_Base'];

      foreach ($this->setup['Start'] as $key => $entry)
      {
	 // Arrays should be appended to with only the new elements.
	 if (isset($start[$key]) && is_array($start[$key]))
	 {
	    $start[$key] = array_merge($start[$key],
				       array_diff($entry, $start[$key]));
	 }
	 else
	 {
	    $start[$key] = $entry;
	 }
      }

      if (!isset($start['Title']))
      {
	 $start['Title'] = $this->tr->get('Title', $_SERVER['PHP_SELF']);
      }

      if (!isset($start['Keywords']))
      {
	 $start['Keywords'] = $this->tr->get('Keywords', $_SERVER['PHP_SELF']);
      }
      
      $this->xwr->writeStart($start);
      $this->xwr->write(array('div',
			      array('id' => 'Page_Content'),
			      array('Finish' => false)));
   }
}

// EOF