<?php
namespace Evoke\Init\Settings;

/// Initialize constant values for the evoke system.
class Constant extends \Evoke\Init\Settings
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
      echo 'LOAD';
      $this->settings['Constant'] = array(
	 'Default_Language'             => 'EN',
	 'Development_Servers'          => array(),
	 'Max_Length_Exception_Message' => 6000);
   }
}
// EOF