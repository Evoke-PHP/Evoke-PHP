<?php
namespace Evoke\Core\HTTP\MediaType\Rule;

class Equivalent extends Base
{
	/******************/
	/* Public Methods */
	/******************/

	/** Check to see if the rule matches.
	 *  @param mediaType \array The media type we are checking against.
	 *  @return \bool Whether the rule matches.
	 */
	public function isMatch(Array $mediaType)
	{
		// If the match rule we have does not modify the mediaType when it is
		// merged into it then they are equivalent. This means that there was
		// nothing in the match rule that differed from the mediaType.
		$mergedMediaType = array_merge_recursive($mediaType, $this->match);

		// Only an equivalent test of == is used as we don't care about types.
		return $mergedMediaType == $mediaType;
	}
}
// EOF