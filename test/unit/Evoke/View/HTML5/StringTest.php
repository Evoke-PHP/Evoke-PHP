<?php
namespace Evoke_Test\View\HTML5;

use DOMDocumentFragment,
    Evoke\View\HTML5\String,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\String
 */
class StringTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerString()
    {
        return [
            'Commented'      => [
                'Expected' => ['One', ['div', [], 'Two'], 'Three'],
                'String'   => 'One<!-- C1 --><div>Two</div><!-- C2 -->Three'],
            'Multi_Nested'   => [
                'Expected' =>
                [['div',
                  ['class' => 'First'],
                  [['div', [], 'A'],
                   ['div', ['class' => 'Number'], '1']]],
                 ['div',
                  ['class' => 'Mid'],
                  [['div', [], 'M'],
                   ['div', ['class' => 'Number'], '5']]],
                 ['div',
                  ['class' => 'Last'],
                  [['div', [], 'Z'],
                   ['div', ['class' => 'Number'], '9']]]],
                'String' =>
                '<div class="First">' .
                '<div>A</div><div class="Number">1</div></div>' .
                '<div class="Mid">' .
                '<div>M</div><div class="Number">5</div></div>' .
                '<div class="Last">' .
                '<div>Z</div><div class="Number">9</div></div>'],
            'Single_Nested'  => [
                'Expected' => [['div',
                                [],
                                [['span', [], 'SP THIS'],
                                 ['div',
                                  ['class' => 'Other'],
                                  [['div', [], 'Alt']]]]]],
                'String'   => '<div><span>SP THIS</span><div class="Other">' .
                '<div>Alt</div></div></div>'],
            'Single_String'  => [
                'Expected' => 'str',
                'String'   => 'str'],
            'Single_CDATA'   => [
                'Expected' => 'this <div> can appear > CDATA &! all.',
                'String'   =>
                '<![CDATA[this <div> can appear > CDATA &! all.]]>'],
            'Single_Element' => [
                'Expected' => [['span', [], 'SP THIS']],
                'String'   => '<span>SP THIS</span>'],
            ];
    }
    
    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerString
     */
    public function testString($expected, $string)
    {
        $obj = new String;
        $obj->setHTML5($string);
        
        $this->assertSame($expected, $obj->get());
    }
}
// EOF