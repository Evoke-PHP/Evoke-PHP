<?php


class Model_Session_Clear extends Model_Session
{
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      $this->em->connect('Post.', function() {});
      $this->em->connect('Post.Clear', array($this, 'clear'));
   }

   /******************/
   /* Public Methods */
   /******************/

   public function clear()
   {
      $this->setup['Session_Manager']->remove();
   }
}

// EOF