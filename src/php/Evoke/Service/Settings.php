<?php
namespace Evoke\Service;

use OutOfBoundsException,
	OutOfRangeException,
	RuntimeException;	

/**
 * Settings
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Settings implements SettingsIface
{
	/**
	 * The settings that have been frozen.  Attempting to modify these will
	 * throw an exception.
	 * @var mixed[]
	 */
	protected $frozen;

	/**
	 * The settings that are available for modification.
	 * @var mixed[]
	 */
	protected $variable;

	/**
	 * Construct the Settings object.
	 *
	 * @param mixed[] The initial frozen settings.
	 * @param mixed[] The initial variable settings.
	 */
	public function __construct(Array $frozen   = array(),
	                            Array $variable = array())
	{
		$this->frozen   = $frozen;
		$this->variable = $variable;
	}
	
	/******************/
	/* Public Methods */
	/******************/

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
	public function freeze($offset)
	{
		$variableReference = $this->getVariableReference($offset);
		$frozenReference = $this->getFrozenReference($offset);
      
		if ($variableReference !== NULL)
		{
			$frozenReference = $variableReference;
			$variableReference = NULL;
		}
		elseif (!isset($this->frozenReference))
		{
			throw new OutOfRangeException(
				__METHOD__ . ' offset: ' . var_export($offset, true) .
				' does not exist to be frozen.');
		}
	}

	/**
	 * Freeze all of the settings so that they are read only.
	 */
	public function freezeAll()
	{
		$this->frozen = array_merge_recursive($this->frozen, $this->variable);
		$this->variable = array();
	}

	/**
	 * Get the value of the setting at the specified offset.
	 *
	 * @param offset string|string[] Offset to the setting.
	 *
	 * @return @mixed The value of the setting.
	 */
	public function get($offset)
	{
		return $this->offsetGet($offset);
	}

	/**
	 * Whether the setting is frozen.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return bool Whether the offset is frozen.
	 */
	public function isFrozen($offset)
	{
		$frozenValue = $this->getFrozenReference($offset);
		return isset($frozenValue);
	}

	/**
	 * Set the setting at the offset with the value.
	 *
	 * @param string|string[] Offset to the setting.
	 * @param mixed           The value to set the setting to.
	 */
	public function set($offset, $value)
	{
		$this->offsetSet($offset, $value);
	}

	/**
	 * Unfreeze the setting so that it can be modified.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @throw OutOfBoundsException If the offset does not exist in the frozen
	 *                             and variable settings (It is okay to unfreeze
	 *                             and already unfrozen setting).
	 */
	public function unfreeze($offset)
	{
		if (!$this->offsetSet($offset))
		{
			throw new OutOfBoundsException(
				__METHOD__ . ' offset: ' . var_export($offset, true) .
				' does not exist to be unfrozen.');
		}

		$this->offsetUnset($offset);
	}

	/**
	 * Unfreeze all of the settings so that they can be modified.
	 */
	public function unfreezeAll()
	{
		$this->variable = array_merge_recursive($this->frozen, $this->variable);
		$this->frozen = array();
	}

	/******************************************/
	/* Public Methods - ArrayAccess interface */
	/******************************************/

	/**
	 * Whether there is a setting (frozen or variable) at the given offset.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return bool Whether the offset exists for the (frozen or variable)
	 *              setting.
	 */
	public function offsetExists($offset)
	{
		$frozenValue = $this->getFrozenReference($offset);
		$variableValue = $this->getVariableReference($offset);

		return isset($frozenValue) || isset($variableValue);
	}

	/**
	 * Get the value at the offset.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return mixed The setting at the offset.
	 */
	public function offsetGet($offset)
	{
		$frozenValue = $this->getFrozenReference($offset);

		if (isset($frozenValue))
		{
			return $frozenValue;
		}
      
		$variableValue = $this->getVariableReference($offset);

		if (isset($variableValue))
		{
			return $variableValue;
		}
      
		throw new RuntimeException(
			__METHOD__ . ' offset: ' . var_export($offset, true) . ' not set.');
	}

	/**
	 * Set the setting at the offset with the value.  New settings are created
	 * as variable.  They are modifiable until they are frozen.
	 *
	 * @param string|string[] Offset to the setting.
	 * @param mixed           The value to set the setting to.
	 */
	public function offsetSet($offset, $value)
	{
		$frozenValue = $this->getFrozenReference($offset);
      
		if (isset($frozenValue))
		{
			throw new RuntimeException(
				__METHOD__ . ' offset: ' . var_export($offset, true) .
				' is already frozen and cannot be set.');
		}

		if (is_array($offset))
		{
			$last = array_pop($offset);
			$variableRef =& $this->getVariableReference($offset);
			$variableRef[$last] = $value;
		}
		else
		{
			$this->variable[$offset] = $value;
			return;
		}
	}

	/**
	 * Unset the setting (frozen or variable) at the offset.
	 *
	 * @param string|string[] Offset to the setting.
	 */
	public function offsetUnset($offset)
	{
		if (is_array($offset))
		{
			$last = array_pop($offset);

			$frozenRef = $this->getFrozenReference($offset);
			unset($frozenRef[$last]);
	 
			$variableRef = $this->getVariableReference($offset);
			unset($variableRef[$last]);
		}
		else
		{
			unset($this->frozen[$offset]);
			unset($this->variable[$offset]);
		}
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Get the reference to the frozen setting.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return mixed[] A reference to the frozen setting value.
	 */
	private function &getFrozenReference($offset)
	{
		if (!is_array($offset))
		{
			$offset = array($offset);
		}
      
		$reference =& $this->frozen;
      
		foreach ($offset as $part)
		{
			if (!isset($reference[$part]))
			{
				$noReference = NULL;
				return $noReference;
			}
	 
			$reference =& $reference[$part];
		}

		return $reference;
	}

	/**
	 * Get the reference to the variable setting.
	 *
	 * @param string|string[] Offset to the setting.
	 *
	 * @return mixed[] A reference to the variable setting value.
	 */
	private function &getVariableReference($offset)
	{
		if (!is_array($offset))
		{
			$offset = array($offset);
		}
      
		$reference =& $this->variable;
      
		foreach ($offset as $part)
		{
			if (!isset($reference[$part]))
			{
				$noReference = NULL;
				return $noReference;
			}
	 
			$reference =& $reference[$part];
		}

		return $reference;
	}
}
// EOF