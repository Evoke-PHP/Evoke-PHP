<?php
namespace Evoke\HTTP\MediaType;

interface RuleIface extends \Evoke\HTTP\Rule
{
	/** Get the output format for the media type.
	 *  @param mediaType \array The media type.
	 */
	public function getOutputFormat(Array $mediaType);
}
// EOF