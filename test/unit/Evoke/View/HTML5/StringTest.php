<?php
namespace Evoke_Test\View\HTML5;

use DOMDocument,
    Evoke\View\HTML5\String,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\String
 * @uses DomDocument
 */
class StringTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerSimple()
    {
        return [
            /*
            'Nested' => [
                'Expected' => ['div',
                               [],
                               [['span', [], 'SP THIS'],
                                ['div',
                                 ['class' => 'Other'],
                                 [['div', [], 'Alt']]]]],
                'String'   => '<span>SP THIS</span><div class="Other">' .
                '<div>Alt</div></div>',
                'Tag'      => 'div'],
            */
            'Span'   => [
                'Expected' => ['div', [], [['span', [], 'SP THIS']]],
                'String'   => '<span>SP THIS</span>',
                'Tag'      => 'div'],
            ];
    }
    
    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerSimple
     */
    public function testSimple($expected, $string, $tag)
    {
        $obj = new String(new DOMDocument, $tag);
        $obj->setHTML5($string);
        
        $this->assertSame($expected, $obj->get());
    }
}
// EOF