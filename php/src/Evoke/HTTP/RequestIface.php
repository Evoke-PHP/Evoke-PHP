<?php
namespace Evoke\HTTP;

interface RequestIface
{
	/** Parse the Accept header field from the request according to:
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
	 *
	 *  This field specifies the preferred media types for responses.
	 *
	 *  @return @array of Accepted media types with their quality factor,
	 *  ordered by preference according to @ref compareAccept.  Each element is
	 *  of the form:
	 *  @verbatim
	 *  array(array('Q_Factor' => 0.5,
	 *              'Subtype'  => 'html',
	 *              'Type'     => 'text'),
	 *        etc.
	 *  @endverbatim
	 */
	public function parseAccept();	

	/** Parse the Accept-Language header from the request according to:
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.10
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 *
	 *  This field specifies the preferred languages for responses.
	 */
	public function parseAcceptLanguage();
}
// EOF