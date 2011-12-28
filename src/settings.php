<?php

class Settings
{
   protected $frozen;
   protected $variable;
   
   public function __construct()
   {
      $this->frozen = array();
      $this->variable = array();
   }

   /******************/
   /* Public Methods */
   /******************/

   public function exists($name)
   {
      return (isset($this->frozen[$name]) || isset($this->variable[$name]));
   }
   
   public function freeze($name)
   {
      if (isset($this->variable[$name]))
      {
	 $this->frozen[$name] = $this->variable[$name];
	 unset($this->variable[$name]);
      }
      elseif (!isset($this->frozen[$name]))
      {
	 throw new OutOfRangeException(
	    __METHOD__ . ' name: ' . $name . ' does not exist to be frozen.');
      }
   }
   
   public function freezeAll()
   {
      $this->frozen += $this->variable;
      $this->variable = array();
   }

   public function get($name)
   {
      if (isset($this->variable[$name]))
      {
	 return $this->variable[$name];
      }
      elseif (isset($this->frozen[$name]))
      {
	 return $this->frozen[$name];
      }
      else
      {
	 throw new OutOfRangeException(
	    __METHOD__ . ' name: ' . $name . ' does not exist.');
      }
   }

   public function getValue($name, $key)
   {
      if (isset($this->variable[$name][$key]))
      {
	 return $this->variable[$name][$key];
      }
      elseif (isset($this->frozen[$name][$key]))
      {
	 return $this->frozen[$name][$key];
      }
      else
      {
	 if (!isset($this->variable[$name], $this->frozen[$name]))
	 {
	    throw new OutOfRangeException(
	       __METHOD__ . ' name: ' . $name . ' does not exist.');
	 }

	 throw new OutOfBoundsException(
	    __METHOD__ . ' name: ' . $name . ' does not contain key: ' . $key);	   
      }
      
   }
   
   public function set($name, $value)
   {
      if (isset($this->frozen[$name]))
      {
	 throw new RuntimeException(
	    __METHOD__ . ' name: ' . $name . ' is already frozen.  Cannot ' .
	    'change the frozen value from: ' .
	    var_export($this->frozen[$name], true) . ' to: ' .
	    var_export($value, true));
      }

      $this->variable[$name] = $value;
   }

   public function unfreeze($name)
   {
      if (isset($this->frozen[$name]))
      {
	 $this->variable[$name] = $this->frozen[$name];
	 unset($this->frozen[$name]);
      }
      elseif (!isset($this->variable[$name]))
      {
	 throw new OutOfBoundsException(
	    __METHOD__ . ' name: ' . $name . ' does not exist to be unfrozen.');
      }
   }

   public function unfreezeAll()
   {
      $this->variable += $this->frozen;
      $this->frozen = array();
   }
}

// EOF
