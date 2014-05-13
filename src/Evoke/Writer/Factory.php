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
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
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
        $upperOutputFormat = strtoupper($outputFormat);

        switch ($upperOutputFormat)
        {
        case 'JSON':
            return new JSON;
        case 'TEXT':
            return new Text;
        }

        $xmlWriter = new XMLWriter;

        switch ($upperOutputFormat)
        {
        case 'HTML5':
            return new XML($xmlWriter, 'HTML5');
        case 'XHTML':
            return new XML($xmlWriter, 'XHTML_1_1');
        case 'XML':
            return new XML($xmlWriter, 'XML');
        default:
            throw new DomainException(
                'No writer for output format: ' . $outputFormat);
        }
    }
}
// EOF
