<?php
/**
 * HTML5
 *
 * @package   Evoke\Writer
 */
namespace Evoke\Writer;

/**
 * HTML5
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Writer
 */
class HTML5 extends XHTML
{
    /**
     * Write the start of the document.
     */
    public function writeStart()
    {
        $this->xmlWriter->startDtd('html');
        $this->xmlWriter->endDtd();
        $this->xmlWriter->startElement('html');
        $this->xmlWriter->writeAttribute('class', 'no-js');
        $this->xmlWriter->writeAttribute('lang', $this->language);
    }
}
// EOF
