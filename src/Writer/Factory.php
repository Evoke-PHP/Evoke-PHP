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
     * @return WriterIface
     * @throws DomainException If no writer can be found for the output format.
     */
    public function create($outputFormat) : WriterIface
    {
        $upperOutputFormat = strtoupper($outputFormat);

        switch ($upperOutputFormat) {
            case 'HTML5':
                return new HTML5(new XMLWriter);
            case 'JSON':
                return new JSON;
            case 'TEXT':
                return new Text;
            case 'XHTML':
                return new XHTML(new XMLWriter);
            case 'XML':
                return new XML(new XMLWriter);
            default:
                throw new DomainException('No writer for output format: ' . $outputFormat);
        }
    }
}
// EOF
