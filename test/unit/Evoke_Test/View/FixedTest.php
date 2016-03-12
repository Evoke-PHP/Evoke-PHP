<?php
namespace Evoke_Test\View;

use Evoke\View\Fixed;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\Fixed
 */
class FixedTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerFixed()
    {
        return [
            'integer' => [125],
            'array'   => [['div', [], 'aiofw']],
            'string'  => ['str']
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new Fixed('blah');
        $this->assertInstanceOf('Evoke\View\Fixed', $object);
    }

    /**
     * The fixed view returns the fixed data sent to it.
     *
     * @dataProvider providerFixed
     */
    public function testGetView($value)
    {
        $object = new Fixed($value);
        $this->assertSame($value, $object->get());
    }
}
// EOF
