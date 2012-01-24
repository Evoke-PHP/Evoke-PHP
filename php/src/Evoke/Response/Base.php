<?php
namespace Evoke\Response;

abstract class Base
{
   protected $app;
   protected $setup;

   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('App' => NULL), $setup);

      if (!$this->setup['App'] instanceof \Evoke\Core\App)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' requires App');
      }

      $this->app =& $this->setup['App'];
   }
   
   abstract public function execute();
}
// EOF
