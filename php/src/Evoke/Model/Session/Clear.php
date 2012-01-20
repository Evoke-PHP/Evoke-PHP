<?php
namespace Evoke\Model\Session;

class Clear extends Session
{
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      $this->em->connect('Post.', array($this, 'doNothing'));
      $this->em->connect('Post.Clear', array($this, 'clear'));
   }

   /******************/
   /* Public Methods */
   /******************/

   public function clear()
   {
      $this->setup['Session_Manager']->remove();
   }

   public function doNothing()
   {

   }
}
// EOF