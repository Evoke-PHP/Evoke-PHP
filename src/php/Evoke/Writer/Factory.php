<?php
/**
 * Factory
 *
 * @package Writer
 */
namespace Evoke\Writer;

use DomainException,
	XMLWriter;

/**
 * Factory
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
class Factory
{
	/******************/
	/* Public Methods */
	/******************/

	public function make(/* String */ $outputFormat)
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