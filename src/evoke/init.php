<?php

abstract class Evoke_Init
{
   protected $container;
   protected $settings;
   
   public function __construct(Array $setup)
   {
      $setup += array('Container' => NULL,
		      'Settings'  => NULL);

      if (!$setup['Container'] instanceof Container)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Container');
      }
      
      if (!$setup['Settings'] instanceof Settings)
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires Settings');
      }

      $this->container = $setup['Container'];
      $this->settings = $setup['Settings'];
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /** Helper function to set a setting.
    *  @param name \string The name of the setting to set.
    *  @param value \mixed The value to set the setting to.
    */
   protected function set($name, $value)
   {
      $this->settings->set($name, $value);
   }

   /** Helper function to set multiple settings.
    *  @param settingArr \array The name to value array of settings to set.
    */   
   protected function setSettings($settingArr)
   {
      foreach ($settingArr as $name => $value)
      {
	 $this->settings->set($name, $value);
      }
   }
}

// EOF
