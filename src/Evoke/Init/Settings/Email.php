<?php
namespace Evoke;

/// Initialize email addresses for the evoke system.
class Init_Email extends Init
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