<?php
declare(strict_types = 1);
/**
 * Writer Factory
 *
 * @package Writer
 */
namespace Evoke\Writer;

use DomainException;
use XMLWriter;

/**
 * Writer Factory
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
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
     * @param string $outputFormat The output format for the writer to create.
     * @return Writer
     * @throws DomainException If no writer can be found for the output format.
     */
    public function create($outputFormat)
    {
        $upperOutputFormat = strtoupper($outputFormat);

        switch ($upperOutputFormat) {
            case 'JSON':
                return new JSON;
            case 'TEXT':
                return new Text;
        }

        $xmlWriter = new XMLWriter;

        switch ($upperOutputFormat) {
            case 'HTML5':
                return new XML($xmlWriter, 'HTML5');
            case 'XHTML':
                return new XML($xmlWriter, 'XHTML_1_1');
            case 'XML':
                return new XML($xmlWriter, 'XML');
            default:
                throw new DomainException('No writer for output format: ' . $outputFormat);
        }
    }
}
// EOF
