<?php
namespace Evoke\HTTP\MediaType\Rule;

interface RuleIface extends \Evoke\HTTP\RuleIface
{
	/** Get the output format for the media type.
	 *  @param mediaType \array The media type.
	 */
	public function getOutputFormat(Array $mediaType);
}
// EOF