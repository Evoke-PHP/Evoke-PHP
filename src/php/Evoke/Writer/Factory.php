<?php
/**
 * Writer Factory
 *
 * @package Writer
 */
namespace Evoke\Writer;

use DomainException,
	XMLWriter;

/**
 * Writer Factory
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class Factory
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Create a writer object.
	 *
	 * @param string The output format for the writer to create.
	 */
	public function create(/* String */ $outputFormat)
	{
		$classname = 'Evoke\Writer\\' . $outputFormat;
		
		if (!class_exists($classname))
		{
			throw new DomainException('No writer for classname: ' . $classname);
		}

		switch ($outputFormat)
		{
		case 'HTML5':
		case 'XHTML':
		case 'XML':
			return new $classname(new XMLWriter);
		default:
			return new $classname;
		}
	}
}
// EOF