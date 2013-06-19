<?php
/**
 * Settings Interface
 *
 * @package Service
 */
namespace Evoke\Service;

/**
 * Settings Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
interface SettingsIface extends \ArrayAccess
{
	/**
	 * Freeze the setting at the given offset.
	 *
	 * @param string|string[] The offset within the settings as a string (for
	 *                        first level only) or an array of levels of the
	 * form:
	 * <pre><code>
	 * $offset = array('Level_1', 'Level_2', 'Level_3');
	 * $this->variable(array('Level_1' => array(
	 * 	                      'Level_2' => array(
	 * 		                      'Level_3' => 'val'))));
	 * </code></pre>
	 */
	public function freeze($offset);

	/**
	 * Freeze all of the settings so that they are read only.
	 */
	public function freezeAll();

	/**
	 * Get the value of the setting at the specified offset.
	 *
	 * @param offset string|string[] Offset to the setting.
	 *
	 * @return @mixed The value of the setting.
	 */
	public function get($offset);

	/**
	 * Whether the setting is frozen.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return bool Whether the offset is frozen.
	 */
	public function isFrozen($offset);
	
	/**
	 * Set the setting at the offset with the value.
	 *
	 * @param string|string[] Offset to the setting.
	 * @param mixed           The value to set the setting to.
	 */
	public function set($offset, $value);
	
	/**
	 * Unfreeze the setting so that it can be modified.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @throw OutOfBoundsException If the offset does not exist in the frozen
	 *                             and variable settings (It is okay to unfreeze
	 *                             and already unfrozen setting).
	 */
	public function unfreeze($offset);

	/**
	 * Unfreeze all of the settings so that they can be modified.
	 */
	public function unfreezeAll();
}
// EOF