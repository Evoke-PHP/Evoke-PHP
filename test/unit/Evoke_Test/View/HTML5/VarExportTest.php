<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\VarExport;
use PHPUnit_Framework_TestCase;

class VarExportTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerVar()
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
     * We can set a variable and the view exports it.
     *
     * @covers       Evoke\View\HTML5\VarExport::get
     * @covers       Evoke\View\HTML5\VarExport::set
     * @dataProvider providerVar
     */
    public function testVarExport($value)
    {
        $object = new VarExport;
        $object->set($value);
        $this->assertSame(['div', ['class' => 'var_export'], var_export($value, true)], $object->get());
    }
}
// EOF
