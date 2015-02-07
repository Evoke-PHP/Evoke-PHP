<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Tree;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Tree
 */
class TreeTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetChildren()
    {
        return [
            'One'  => [[$this->getMock('Evoke\Model\Data\TreeIface')]],
            'More' => [
                [
                    $this->getMock('Evoke\Model\Data\TreeIface'),
                    $this->getMock('Evoke\Model\Data\TreeIface'),
                    $this->getMock('Evoke\Model\Data\TreeIface')
                ]
            ]
        ];
    }

    public function providerHasChildren()
    {
        return [
            'None' => [[], false],
            'One'  => [
                [$this->getMock('Evoke\Model\Data\TreeIface')],
                true
            ],
            'More' => [
                [
                    $this->getMock('Evoke\Model\Data\TreeIface'),
                    $this->getMock('Evoke\Model\Data\TreeIface'),
                    $this->getMock('Evoke\Model\Data\TreeIface')
                ],
                true
            ]
        ];
    }

    public function providerUseValue()
    {
        return [
            'Array'  => [[1, '2', new \StdClass]],
            'Int'    => [1],
            'String' => ['blah'],
            'Object' => [new \StdClass]
        ];
    }

    public function providerValidNoNext()
    {
        $oneTree = new Tree;
        $oneTree->add(new Tree);

        return [
            'Empty' => [new Tree, false],
            'One'   => [$oneTree, true]
        ];
    }

    public function providerValidOneNext()
    {
        $oneTree = new Tree;
        $oneTree->add(new Tree);

        $twoTree = new Tree;
        $twoTree->add(new Tree);
        $twoTree->add(new Tree);

        return [
            'Empty' => [new Tree, false],
            'One'   => [$oneTree, false],
            'Two'   => [$twoTree, true]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCurrent()
    {
        $obj      = new Tree;
        $expected = new Tree;

        $obj->add(new Tree);
        $obj->add($expected);
        $obj->add(new Tree);
        $obj->next();

        $this->assertSame($expected, $obj->current());
    }

    /**
     * @dataProvider providerGetChildren
     */
    public function testGetChildren(Array $children)
    {
        $obj = new Tree;

        foreach ($children as $child) {
            $obj->add($child);
        }

        $this->assertSame(reset($children), $obj->getChildren());
    }

    /**
     * @dataProvider providerHasChildren
     */
    public function testHasChildren(Array $children, $expected)
    {
        $obj = new Tree;

        foreach ($children as $child) {
            $obj->add($child);
        }

        $this->assertSame($expected, $obj->hasChildren());
    }

    public function testKey()
    {
        $obj = new Tree;
        $obj->add(new Tree);
        $obj->add(new Tree);
        $obj->add(new Tree);
        $obj->next();

        $this->assertSame(1, $obj->key());
    }

    public function testRewind()
    {
        $obj      = new Tree;
        $expected = new Tree;

        $obj->add($expected);
        $obj->add(new Tree);
        $obj->add(new Tree);

        $obj->next();
        $obj->rewind();

        $this->assertSame($expected, $obj->current());
    }

    /**
     * @dataProvider providerValidNoNext
     */
    public function testValidNoNext($obj, $expected)
    {
        $this->assertSame($expected, $obj->valid());
    }

    /**
     * @dataProvider providerValidOneNext
     */
    public function testValidOneNext($obj, $expected)
    {
        $obj->next();

        $this->assertSame($expected, $obj->valid());
    }

    /**
     * @dataProvider providerUseValue
     */
    public function testUseValue($value)
    {
        $obj = new Tree;
        $obj->set($value);

        $this->assertSame($value, $obj->get());
    }
}
// EOF
