<?php
namespace Evoke;

interface SettingsIface extends \ArrayAccess
{
	/** Freeze the setting at the given offset.
	 *  \param offset \mixed The offset within the settings as a string (for
	 *  first level only) or an array of levels of the form:
	 *  \code
	 *  $offset = array('Level_1', 'Level_2', 'Level_3');
	 *  $this->variable(array('Level_1' => array(
	 *  	                      'Level_2' => array(
	 *  		                      'Level_3' => 'val'))));
	 *  \endcode
	 */
	public function freeze($offset);

	/// Freeze all of the settings so that they are read only.
	public function freezeAll();

	/** Get the value of the setting at the specified offset.
	 *  @param offset \mixed String or Array for the offset to the setting.
	 *  @return \mixed The value of the setting.
	 */
	public function get($offset);

	/** Whether the setting is frozen.
	 *  @param offset \mixed String or Array for the offset to the setting.
	 *  @return \bool Whether the offset is frozen.
	 */
	public function isFrozen($offset);

	/** Set the setting at the offset with the value.
	 *  @param offset \mixed String or Array for the offset to the setting.
	 *  @param value \mixed The value to set the setting to.
	 */
	public function set($offset, $value);

	/** Unfreeze the setting so that it can be modified.
	 *  @param offset \mixed String or Array for the offset to the setting.
	 *  @throws OutOfBoundsException if the offset does not exist in the frozen
	 *  and variable settings (It is okay to unfreeze and already unfrozen
	 *  setting).
	 */
	public function unfreeze($offset);

	/// Unfreeze all of the settings so that they can be modified.
	public function unfreezeAll();
}
// EOF