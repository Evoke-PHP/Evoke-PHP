<?php
namespace Evoke\Page;
abstract class XML extends \Evoke\Page
{
   protected $tr;
   protected $xwr;
   
   public function __construct(Array $setup=array())
   {
      parent::__construct();

      $this->setup = array_merge(
	 array('Start'         => array(),
	       'Start_Base'    => array(
		  'CSS' => array('/csslib/global.css',
				 '/csslib/common.css'))),
	 $setup);

      $this->tr = $this->app->getTranslator();
      $this->xwr = $this->app->getXWR();
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function load()
   {
      $this->start();
      $this->content();
      $this->end();
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   protected function end()
   {
      $this->xwr->writeEnd();
   }
   
   protected function start()
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
   }
   
   /********************/
   /* Abstract Methods */
   /********************/

   abstract protected function content();
}
// EOF
