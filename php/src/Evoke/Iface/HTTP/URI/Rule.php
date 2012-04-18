<?php
namespace Evoke\Iface\Core\HTTP\URI;

interface Rule extends \Evoke\Iface\Rule
{
	/** Get the classname.
	 *  @param uri \string The URI to get the classname from.
	 *  \return \string The uri mapped towards the classname with the rule.
	 */	
	public function getClassname($uri);

	/** Return the parameters for the URI.
	 *  @param uri \string The URI.
	 *  @return \array The parameters for the class.
	 */
	public function getParams($uri);
	
	/** Check whether the rule is authoritative.
	 *  @return \bool Whether the rule can definitely give the final route when
	 *  it matches the input.
	 */
	public function isAuthoritative();
}
// EOF