<?php
namespace Evoke\Page;
abstract class XML extends Base
{
   protected $tr;
   protected $xwr;
   
   public function __construct(Array $setup)
   {
      $setup += array('Start'      => array(),
		      'Start_Base' => array(
			 'CSS' => array('/csslib/global.css',
					'/csslib/common.css')),
		      'Translator' => NULL,
		      'XWR'        => NULL);

      if (!$setup['Translator'] instanceof \Evoke\Core\Translator)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Translator');
      }
      
      if (!$setup['XWR'] instanceof \Evoke\Core\XWR)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' requires XWR');
      }
      
      parent::__construct($setup);

      $this->tr = $this->setup['Translator'];
      $this->xwr = $this->setup['XWR'];
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function load()
   {
      $this->start();
      $this->content();
      $this->end();
      $this->output();
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   protected function end()
   {
      $this->xwr->writeEnd();
   }

   protected function output()
   {
      $this->xwr->output();
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
