<?php
namespace Evoke\Core\Iface;

/** The Evoke Provider interface, derived from the Artax Provider interface with
 *  permission from Daniel Lowrey.  Artax is an event-driven Application engine.
 *  It can be found here: https://github.com/rdlowrey/Artax-Core
 * 
 *  Note: This file was forked from the Atrax-Core dev branch: 648624a3cb.
 *
 *  @author Daniel Lowrey <rdlowrey@gmail.com>
 */
interface Provider
{
	/**
	 * Factory method for auto-injecting dependencies upon instantiation
	 *
	 * @param string $class Class name
	 * @param mixed $custom An optional array specifying custom instantiation
	 * parameters for this construction
	 */
	public function make($class, array $custom);
    
	/**
	 * Defines custom instantiation parameters for the specified class
	 *
	 * @param string $class Class name
	 * @param array $definition An array specifying custom instantiation params
	 */
	public function define($class, array $definition);
    
	/**
	 * Defines multiple custom instantiation parameters at once
	 *
	 * @param mixed $iterable The variable to iterate over: an array, StdClass
	 * or ArrayAccess instance
	 */
	public function defineAll($iterable);
    
	/**
	 * Clear the injection definition for the specified class
	 *
	 * @param string $class Class name
	 */
	public function remove($class);
    
	/**
	 * Clear all injection definitions from the container
	 */
	public function removeAll();
    
	/**
	 * Forces re-instantiation of a shared class the next time it is requested
	 *
	 * @param string $class Class name
	 */
	public function refresh($class);
    
	/**
	 * Determines if a shared instance of the specified class is stored
	 *
	 * @param string $class Class name
	 */
	public function isShared($class);
    
	/**
	 * Determines if an injection definition exists for the specified class
	 *
	 * @param string $class Class name
	 */
	public function isDefined($class);
}
// EOF