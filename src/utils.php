<?php


/// Utils
class Utils
{
   /*************************/
   /* Public Static Methods */
   /*************************/

   /** Expand an array using the separator given.
    *  @param arg \mixed The array to separate or a string to return.
    *  @param separator \string The separator to use between each element
    *  of the array.
    *  \returns A \string of the separated array or the string arg.
    */
   public static function expand($arg, $separator=',')
   {
      try
      {
	 if (is_array($arg))
	 {
	    return implode($separator, $arg);
	 }
	 else
	 {
	    return (string)$arg;
	 }
      }
      catch (Exception $e)
      {
	 throw new Exception_Base(
	    __METHOD__,
	    'arg: ' . var_export($arg, true) .
	    ' separator: ' . var_export($separator, true),
	    $e);
      }
   }

   /** Expand a keyed array using the between value between the key and the
    *  value and the separator between each element pair.
    *  @param arg \mixed Either a keyed array that is to be expanded or the
    *  value to be converted to a string.
    *  @param between \string The separator to use between each key and value.
    *  @param separator \string The separator to use between each key/value
    *  pair.
    *  \returns A \string of the separated keyed array or the string for arg.
    */
   public static function expandKeyedArr($arg, $between='=', $separator=' AND ')
   {
      try
      {
	 if (is_array($arg))
	 {
	    $str = '';
	    
	    if (!empty($arg))
	    {
	       foreach ($arg as $key => $val)
	       {
		  $str .= $key . $between . $val . $separator;
	       }
	       
	       // The array is not empty so we can cut the last separator which
	       // has definitely been added to str.
	       $str = substr($str, 0, -1 * strlen((string)$separator));
	    }
	 
	    return $str;
	 }
	 else
	 {
	    return (string)$arg;
	 }      
      }
      catch (Exception $e)
      {
	 throw new Exception_Base(
	    __METHOD__,
	    'arg: ' . var_export($arg, true) . ' separator: ' .
	    var_export($separator, true) . ' between: ' .
	    var_export($between, true),
	    $e);
      }
   }

   /** Create a string with unnamed placeholders for each item specified.
    *  @param arg \mixed Either an array where every item is replaced or a
    *  single placeholder for an object or string entry. An empty string will
    *  be returned for an empty array.
    *  @param separator \string The separator to place between each placeholder.
    *  \return A \string of the placeholders correctly separated.
    */
   public static function placeholders($arg, $separator=',')
   {
      if (!is_array($arg))
      {
	 return '?';
      }
      
      $str = '';
      
      if (!empty($arg))
      {
	 foreach ($arg as $item)
	 {
	    $str .= '?' . $separator;
	 }
	 
	 // The array is not empty so we can cut the last separator which has
	 // definitely been added to str.
	 $str = substr($str, 0, -1 * strlen($separator));
      }
      
      return $str;
   }

   /** Create a string with the array keys and unnamed placeholders. The string
    *  will be of the format: 'key1=? AND key2=? AND key3=?' with default
    *  parameters.
    *  @param arg \mixed Either a keyed array that is to be expanded or the
    *  value to be converted to a string.
    *  @param between \string String between each key and unnamed placeholder.
    *  @param separator \string String between each key/placeholder pair.
    *  \returns A \string with the keys and placeholders in it.
    */
   public static function placeholdersKeyed(
      $arg, $between='=', $separator=' AND ')
   {
      /** \todo Fix for NULL placeholders.  So where conditions can accept NULL
       *  values.
       */
      try
      {
	 if (is_array($arg))
	 {
	    $str = '';
	    
	    if (!empty($arg))
	    {
	       foreach ($arg as $key => $val)
	       {
		  $str .= $key . $between . '?' . $separator;
	       }
	       
	       // The array is not empty so we can cut the last separator which
	       // has definitely been added to str.
	       $str = substr($str, 0, -1 * strlen((string)$separator));
	    }
	 
	    return $str;
	 }
	 else
	 {
	    return (string)$arg;
	 }      
      }
      catch (Exception $e)
      {
	 throw new Exception_Base(
	    __METHOD__,
	    'arg: ' . var_export($arg, true) .
	    ' separator: ' . var_export($separator, true) .
	    ' between: ' . var_export($between, true),
	    $e);
      }
   }

   /** Get a message suitable for logging with information from the setup
    *  details supplied.
    *  @param setup \array The information to use in the message.
    *  \return A \string containing the information passed in.
    */
   public static function getMessage($setup=array())
   {
      $setup = array_merge(array('Method' => '',
				 'Message' => '',
				 'DB' => NULL,
				 'Exception' => NULL),
			   $setup);
      $msg = array();

      // Build the message in the array for later expansion.
      if (!empty($setup['Method']))
      {
	 $msg[] = $setup['Method'];
      }
      
      if (!empty($setup['Message']))
      {
	 $msg[] = $setup['Message'];
      }

      if (method_exists($setup['DB'], 'errorCode') &&
	  method_exists($setup['DB'], 'errorInfo'))
      {
	 if ($setup['DB']->errorCode() != '00000')
	 {
	   $msg[] = 'Error: ' . self::expand($setup['DB']->errorInfo(), ' ');
	 }
      }
     
      if (method_exists($setup['Exception'], 'getMessage'))
      {
	 $msg[] = 'Exception: ' . $setup['Exception']->getMessage();
      }

      return self::expand($msg, ' ');
   }
   
   /** Return the maximum upload size in bytes.  This is the smaller of the
    *   post_max_size and upload_max_filesize from the PHP configuration.
    */
   public static function getMaxUploadSize()
   {
      $postMaxSize = ini_get('post_max_size');
      $uploadMaxFilesize = ini_get('upload_max_filesize');

      if (self::getBytes($postMaxSize) > self::getBytes($uploadMaxFilesize))
      {
	 return $uploadMaxFilesize;
      }
      else
      {
	 return $postMaxSize;
      }
   }

   /// Return the number of bytes from a string with K M G byte size specifiers.
   public static function getBytes($str)
   {
      $str = trim($str);
      $val = (int)($str);
      $last = strtolower($str[strlen($str) - 1]);

      // Drop through and progressively multiply through the switch.
      switch($last)
      {
      case 'g':
	 $val *= 1024;
      case 'm':
	 $val *= 1024;
      case 'k':
	 $val *= 1024;
      }
	 
      return $val;
   }
}

// EOF