<?php
namespace Evoke\Core\Init\Command;
// Parse the variables given to a command line script as Query Strings of JSON.
// Variables can be passed as separate arguments or as part of a query string:
//    _GET='{ "key1": "val1", "key2": "val2" }' foo='"bar"'
// OR
//    _GET='{ "key1": "val1", "key2": "val2" }'\&foo='"bar"' 
if ($argc > 1)
{
	$parsedArgs = array(); 
   
	for ($i = 1; $i < $argc; $i++)
	{
		parse_str($argv[$i], $parsedArgs[$i]);
	}
   
	foreach ($parsedArgs as $arg)
	{
		foreach ($arg as $key => $val)
		{
			// Set the global variable of name $key to the json decoded value.
			$$key = json_decode($val, true);
		}
	}

	unset($parsedArgs);
}

// EOF