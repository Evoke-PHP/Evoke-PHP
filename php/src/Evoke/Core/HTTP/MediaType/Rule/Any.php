<?php
namespace Evoke\Core\HTTP\MediaType\Rule;

/// A Media Type rule that matches any media type from the accept header.
class Any extends Base
{
	/******************/
	/* Public Methods */
	/******************/

	/** Check to see if the rule matches which it does this matches anything!
	 *  @return \bool True.
	 */
	public function isMatch($mediaType)
	{
		return true;
	}
}
// EOF