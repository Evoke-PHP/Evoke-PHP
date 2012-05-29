<?php
namespace Evoke\HTTP\MediaType;

interface RouterFactoryIface
{
	/** Build an HTML5 only router.
	 */
	public function buildHTML5Only();

	/** Build a JSON only router.
	 */
	public function buildJSONOnly();
	
	/** Build the standard HTTP Media Type Router that handles the common media
	 *  types.
	 *  @param fallback @string The output format to use as a fallback for the
	 *                          ALL (* / *) rule.
	 */
	public function buildStandard($fallback='HTML5');

	/** Build a media type router that only serves text.
	 */
	public function buildTextOnly();

	/** Build an XHTML only router.
	 */
	public function buildXHTMLOnly();
}
// EOF