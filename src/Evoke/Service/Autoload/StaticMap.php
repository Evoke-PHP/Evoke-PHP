<?php
/**
 * StaticMap Autoload
 *
 * @package Service\Autoload
 */
namespace Evoke\Service\Autoload;

use RuntimeException;

/**
 * StaticMap Autoload
 *
 * Autoload using a fixed mapping of classnames to filenames.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Service\Autoload
 */
class StaticMap implements AutoloadIface
{
    /**
     * The static map of classnames to filenames.
     *
     * @var string[]
     */
    protected $staticMap;

    /**
     * Construct an Autoload object.
     *
     * @param string[] $staticMap The static map of classnames to filenames.
     */
    public function __construct(Array $staticMap)
    {
        $this->staticMap = $staticMap;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Autoload the specified class.
     *
     * @param string $name The fully namespaced class to load.
     * @throws RuntimeException If the file does not exist.
     */
    public function load($name)
    {
        if (isset($this->staticMap[$name])) {
            if (!file_exists($this->staticMap[$name])) {
                throw new RuntimeException('File: ' . $this->staticMap[$name] . ' does not exist.');
            }

            /** @noinspection PhpIncludeInspection */
            require $this->staticMap[$name];
        }
    }
}
// EOF
