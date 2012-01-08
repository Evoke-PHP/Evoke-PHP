<?php
/** Initialize the system constants by loading all of the Evoke_Init objects.
 *  These are found in the /evoke/init path by default and are loaded if they
 *  are the highest priority object within the php include path.  This allows
 *  components of the initialization constants to be overriden.
 */
class Evoke_Initialize
{
   protected $initializedComponents;
   
   public function __construct(Array $setup=array())
   {
      $setup += array('Container'         => NULL,
		      'Dir'               => '/evoke/init',
		      'File_System'       => NULL,
		      'Ignored_Files'     => array(),
		      'Settings'          => NULL,
		      'Use_Include_Paths' => true);

      if (!$setup['Container'] instanceof Container)
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires Container');
      }

      if (!$setup['Settings'] instanceof Settings)
      {
	 throw new InvalidArgumentException(__METHOD__ . ' requires Settings');
      }
      
      $paths = array();
      
      if ($setup['Use_Include_Paths'])
      {
	 $includePaths = explode(":", get_include_path());

	 foreach ($includePaths as $includePath)
	 {
	    if (is_dir($includePath . $setup['Dir']))
	    {
	       $paths[] = $includePath . $setup['Dir'];
	    }
	 }
      }
      else
      {
	 $paths[] = $setup['Dir'];
      }

      $requireFiles = array();
      $orderedObjects = array();
      $reversePath = array_reverse($paths);
      
      // Work through the include path backwards so that lowest priority
      // objects are loaded first and can be overriden.
      foreach ($reversePath as $path)
      {
	 $pathLen = strlen($path);
	 $rDir = new RecursiveDirectoryIterator(
            $path, FilesystemIterator::SKIP_DOTS);
	 $rFilter = new RecursiveRegexIterator($rDir, '/\.php$/i');
         $files = new RecursiveIteratorIterator(
	    $rFilter, RecursiveIteratorIterator::SELF_FIRST);

         foreach ($files as $file)
         {
            $filename = $file->getPathname();
	    
	    if (in_array($filename, $setup['Ignored_Files']))
	    {
	       continue;
	    }
	    
	    $includeObject = mb_substr($filename, $pathLen + 1, -strlen('.php'));
	    $includeObject = mb_convert_case(
	       mb_ereg_replace('/\\/', '_', $includeObject),
	       MB_CASE_TITLE);

	    // If we already have the object in the list we need to remove it so
	    // that the higher priority one can be used.
	    if (isset($requireFiles[$includeObject]))
	    {
	       foreach ($orderedObjects as $pos => $obj)
	       {
		  if ($obj === $includeObject)
		  {
		     unset($orderedObjects[$pos]);
		  }
	       }		  
	    }

	    $requireFiles[$includeObject] = $filename;
	    $orderedObjects[] = $includeObject;
	 }
      }

      // Now go through in the correct order initializing the objects.
      foreach ($orderedObjects as $object)
      {
	 require_once $requireFiles[$object];
	 
	 $setup['Container']->getShared(
		  'Evoke_Init_' . $object,
		  array('Container' => $setup['Container'],
			'Settings'  => $setup['Settings']));
      }
   }
}
//EOF