<?php
namespace Evoke\Core\Iface\HTTP\MediaType;

interface Rule extends \Evoke\Core\Iface\Rule
{
	/** Get the output format for the media type.
	 *  @param mediaType \array The media type.
	 */
	public function getOutputFormat(Array $mediaType);
}
// EOF