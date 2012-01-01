<?php
class Evoke_Handler_Autoload extends Evoke_Handler
{
   // We only use the extension php.
   protected $extensions = array('.php');

   public function __construct()
   {
      $this->register('spl_autoload_register', array($this, 'handler'));
   }

   /******************/
   /* Public Methods */
   /******************/
   
   /** Provide autoloading for classes.
    *  Map classnames to their filenames and require_once the file.
    *  \verbatim
    *  If the class was:    Primary_Double_Secondary_Tertiary
    *  The file could be:  /Primary/Double_Secondary/Tertiary
    *              _OR_ :  /Primary/Double/Secondary_Tertiary
    *
    *  so we need to test all permutations of joining with '_' or '/' when
    *  mapping the classnames to filenames.
    *
    *  Files named /event_one.php and /event/one.php are not distinguished by
    *  this autoloader.  Speed and simplicity win, require those yourself.
    *  \endverbatim
    *  @param className \string Class Name of the file to be required.
    */
   public function handler($className)
   {
      $parts = explode('_', strtolower($className));
      $numParts = count($parts);
      
      // We have a binary type of join (either a '/' or a '_').  Between n
      // elements there will be n - 1 joins.
      $permutations = pow(2, $numParts - 1);

      // Start with the first ordering.
      $order = 1;

      while ($order <= $permutations)
      {
	 $order = $this->checkRequire($parts, $order, $numParts, $permutations);

	 if ($order === true)
	 {
	    return;
	 }
      }

      // If we make it to here we could not find the file, but that does not
      // mean that there is not another autoload function defined in the
      // autoload stack that could find it - so we just return.      
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Check for the required file with the given ordering.
    *  \return The new ordering to check or (true if the file got required).
    */
   private function checkRequire($parts, $order, $numParts, $permutations)
   {
      // Our join choices count backwards through binary as we usually have '/'
      // rather than '_'.
      $joinChoices = str_pad(decbin($permutations - $order),
			     $numParts - 1,
			     '0',
			     STR_PAD_LEFT);
      $filename = '';
      
      // Loop up till the last part which has to be a part of the filename.
      for ($p = 0; $p < $numParts - 1; ++$p)
      {
	 $filename .= $parts[$p];

	 if ($joinChoices[$p] === '1')
	 {
	    $filename .= '/';

	    if (!$this->dirExistsInIncludePath($filename))
	    {
	       // We can skip any options below this directory choice (you can't
	       // have a directory beneath one that does not exist).
	       // numParts counts from 1, $p counts from 0 which means we should
	       // minus 1.  But the joins are also 1 down so we need to make it
	       // minus 2.  This is easier to see by testing.
	       return $order + pow(2, $numParts - $p - 2);	       
	    }
	 }
	 else
	 {
	    $filename .= '_';
	 }
      }

      $filename .= $parts[$numParts - 1];
      $extension = $this->getIncludableExtension($filename);
      
      if ($extension !== false)
      {
	 // We found our class - require it and get out by returning an order
	 // that is greater than permutations.
	 require $filename . $extension;
	 return true;
      }
      else
      {
	 // Increment the order and keep checking.
	 return ++$order;
      }
   }

   private function dirExistsInIncludePath($dir)
   {
      $paths = explode(":", get_include_path());
      
      foreach ($paths as $path)
      {
	 if (is_dir($path . '/' . $dir))
	 {
	    return true;
	 }
      }

      return false;
   }

   /** Return an includable extension for the filename or false if it cannot be
    *  included.
    *  @param filename \string The filename to find (in the include path).
    *  \return The extension (including the '.') or false if no includable file
    *  could be found.
    */
   private function getIncludableExtension($filename)
   {
      $paths = explode(":", get_include_path());

      foreach ($paths as $path)
      {
	 foreach ($this->extensions as $ext)
	 {
	    if (is_readable($path . '/' . $filename . $ext))
	    {
	       return $ext;
	    }
	 }
      }

      return false;     
   }	 
}
// EOF