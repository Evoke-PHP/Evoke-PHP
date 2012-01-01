<?php
/// Initialize email addresses for the evoke system.
class Evoke_Init_Email extends Evoke_Init
{
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      $this->set(
	 'Email',
	 array('Administrator' => ''));
   }
}

// EOF
