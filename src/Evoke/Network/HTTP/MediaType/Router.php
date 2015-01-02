<?php
/**
 * HTTP Media Type Router Interface
 *
 * @package Network\HTTP\MediaType
 */
namespace Evoke\Network\HTTP\MediaType;

use Evoke\Network\HTTP\MediaType\Rule\RuleIface;
use OutOfBoundsException;

/**
 * HTTP Media Type Router Interface
 *
 * Route the Accepted Media Types from the request to the correct output format.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType
 */
class Router implements RouterIface
{
    /**
     * Rules that the router uses to route.
     *
     * @var RuleIface[]
     */
    protected $rules = [];

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a rule to the router.
     *
     * @param RuleIface $rule HTTP MediaType Rule object.
     */
    public function addRule(RuleIface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Select the output format (that responds to the routed MediaType).
     *
     * @param  mixed[] $acceptedMediaTypes Accepted media types of the browser.
     * @return string The output format.
     * @throws OutOfBoundsException
     *                                     When no output format can be chosen
     *                                     that matches the Accepted Media
     *                                     Types.
     */
    public function route(Array $acceptedMediaTypes)
    {
        foreach ($acceptedMediaTypes as $mediaType) {
            foreach ($this->rules as $rule) {
                $rule->setMediaType($mediaType);

                if ($rule->isMatch()) {
                    return $rule->getOutputFormat();
                }
            }
        }

        throw new OutOfBoundsException(
            'no output formats match the Accepted Media Types.');
    }
}
// EOF
