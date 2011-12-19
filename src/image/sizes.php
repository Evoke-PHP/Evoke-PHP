<?php


class Image_Sizes extends Image implements IteratorAggregate
{
   protected $formats;
   
   public function __construct()
   {
      $this->setStandardFormats();
   }

   /******************/
   /* Public Methods */
   /******************/

   public function addFixed($name, $width, $height)
   {
      $this->formats[$name] = array('Width' => $width, 'Height' => $height);
   }
   
   public function addFixedHeights($heights)
   {
      foreach ($heights as $name => $height)
      {
	 $this->formats[$name] = array('Width' => 0, 'Height' => $height);
      }
   }

   public function addFixedWidths($widths)
   {
      foreach ($widths as $name => $width)
      {
	 $this->formats[$name] = array('Width' => $width, 'Height' => 0);
      }
   }

   public function addOriginal()
   {
      $this->formats['Original'] = array('Width' => 0, 'Height' => 0);
   }
   

   public function get($name)
   {
      if (!isset($this->formats[$name]))
      {
	 throw new RuntimeException(__METHOD__ . ' unknown format: ' . $name);
      }

      return $this->formats[$name];
   }
   
   public function getAll()
   {
      return $this->formats;
   }

   public function getFixedHeights()
   {
      $fixedHeights = array();
      
      foreach ($this->formats as $name => $format)
      {
	 if ($format['Width'] === 0 && $format['Height'] !== 0)
	 {
	    $fixedHeights[$name] = $format;
	 }
      }

      return $fixedHeights;
   }

   public function getFixedWidths()
   {
      $fixedWidths = array();
      
      foreach ($this->formats as $name => $format)
      {
	 if ($format['Height'] === 0 && $format['Width'] !== 0)
	 {
	    $fixedWidths[$name] = $format;
	 }
      }

      return $fixedWidths;
   }   
   
   public function getIterator()
   {
      return new ArrayIterator($this->formats);
   }

   public function resetFormats()
   {
      $this->formats = array();
   }

   public function setStandardFormats()
   {
      $this->sizeFormats = array();

      $this->addOriginal();
      $this->addFixedHeights(
	 array('H_Super' => 960,
	       'H_Max'   => 640,
	       'H_Mid'   => 480,
	       'H_Min'   => 320,
	       'H_Mic'   => 160,
	       'H_Cent'  => 100,
	       'H_Nano'  =>  80,
	       'H_Pico'  =>  40));      
      $this->addFixedWidths(
	 array('W_Super' => 960,
	       'W_Max'   => 640,
	       'W_Mid'   => 480,
	       'W_Min'   => 320,
	       'W_Mic'   => 160,
	       'W_Cent'  => 100,
	       'W_Nano'  =>  80,
	       'W_Pico'  =>  40));
   }
}

// EOF