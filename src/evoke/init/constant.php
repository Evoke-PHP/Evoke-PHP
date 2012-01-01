<?php
/// Initialize constant values for the evoke system.
class Evoke_Init_Constant extends Evoke_Init
{
   /** Initialize constant values for the evoke system.
       \verbatim
       Default_Language    - Default Language for the site.
       Development_Servers - List of Development servers (for logging etc.)
       \endverbatim
   */
   public function __construct(Array $setup)
   {
      parent::__construct($setup);

      $this->settins['Constant'] = array('Default_Language'    => 'EN',
					 'Development_Servers' => array());
   }
}
// EOF