<?php
declare(strict_types = 1);
/**
 * HTTP Media Type Router Interface
 *
 * @package Network\HTTP\MediaType
 */
namespace Evoke\Network\HTTP\MediaType;

use Evoke\Network\HTTP\MediaType\Rule\RuleIface;

/**
 * HTTP Media Type Router Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType
 */
interface RouterIface
{
    /**
     * Add a rule to the router.
     *
     * @param RuleIface $rule The rule to add to the router.
     */
    public function addRule(RuleIface $rule);

    /**
     * Perform the routing based on the rules.
     *
     * @param mixed[] $acceptedMediaTypes The media types accepted by the browser.
     */
    public function route(Array $acceptedMediaTypes);
}
// EOF
