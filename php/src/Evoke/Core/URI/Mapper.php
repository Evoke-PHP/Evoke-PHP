<?php
namespace Evoke\Core\URI;

abstract class Mapper implements \Evoke\Core\Iface\URI\Mapper
{
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('Authoritative' => NULL),
				 $setup);

      if (!is_bool($this->setup['Authoritative']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Authoritative as a bool');
      }
   }
   
   /******************/
   /* Public Methods */
   /******************/
   
   public function isAuthoritative()
   {
      return $this->setup['Authoritative'];
   }
}
// EOF