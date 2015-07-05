<?php
/**
 * Request Interface
 *
 * @package Network
 */
namespace Evoke\Network;

/**
 * Request Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network
 */
interface RequestIface
{
    /**
     * Get the URI of the request (without any parameters).
     *
     * @return string The URI of the request.
     */
    public function getURI();

    /**
     * Get the named parameter from the request.
     *
     * @param string $param The parameter to get.
     * @return mixed The parameter.
     */
    public function getParam($param);

    /**
     * Get the parameters of the request.
     *
     * @return mixed[] The parameters of the request.
     */
    public function getParams();

    /**
     * Whether the named parameter is set.
     *
     * @param string $param The parameter to check.
     * @return bool Whether the parameter is set.
     */
    public function issetParam($param);
}
// EOF
