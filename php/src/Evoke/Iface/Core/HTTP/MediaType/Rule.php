<?php
namespace Evoke\Iface\Core\HTTP\MediaType;

interface Rule extends \Evoke\Iface\Core\Rule
{
	/** Get the output format for the media type.
	 *  @param mediaType \array The media type.
	 */
	public function getOutputFormat(Array $mediaType);
}
// EOF