<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Tree;
use Evoke\Model\Data\TreeBuilder;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Tree
 */
class TreeTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerArrayGet()
    {
        $obj = new Tree;
        $obj->set(['A1' => 3, 'A2' => 7]);

        return [
            'Get_First'  =>
                [
                    'Expected' => 3,
                    'Obj'      => $obj,
                    'Offset'   => 'A1'
                ],
            'Get_Second' =>
                [
                    'Expected' => 7,
                    'Obj'      => $obj,
                    'Offset'   => 'A2'
                ]
        ];

    }

    public function providerArrayGetFail()
    {
        $objUnsetIndex = new Tree;
        $objUnsetIndex->set(['A1' => 3, 'A2' => 7]);

        $objNonArray = new Tree;
        $objNonArray->set(67);

        return [
            'Non_Array'  =>
                [
                    'Obj'      => $objNonArray,
                    'Offset'   => 'B1'
                ],
            'Unset_Index' =>
                [
                    'Obj'      => $objUnsetIndex,
                    'Offset'   => 'B1'
                ]
        ];
    }

    public function providerArrayIsset()
    {
        $issetObj = new Tree;
        $issetObj->set(['A1' => 3]);

        $unsetObj = clone($issetObj);

        return [
            'Is_Set'  =>
                [
                    'Expected' => true,
                    'Obj'      => $issetObj,
                    'Offset'   => 'A1'
                ],
            'Not_Set' =>
                [
                    'Expected' => false,
                    'Obj'      => $unsetObj,
                    'Offset'   => 'A2'
                ]
        ];
    }

    public function providerArraySet()
    {
        $obj = new Tree;
        $obj->set(['A1' => 3]);

        return [
            'Existing_Index' =>
                [
                    'Expected'  => 7,
                    'Obj'       => $obj,
                    'Offset'    => 'A1',
                    'Set_Value' => 7
                ],
            'New_Index'      =>
                [
                    'Expected'  => '63e',
                    'Obj'       => $obj,
                    'Offset'    => 'A2',
                    'Set_Value' => '63e'
                ]
        ];
    }

    public function providerArrayUnset()
    {
        $obj = new Tree;
        $obj->set(['A1' => 1, 'A2' => 2, 'A3' => 3]);

        return [
            'Middle' =>
                [
                    'Expected' => ['A1' => 1, 'A3' => 3],
                    'Index'    => 'A2',
                    'Obj'      => $obj
                ]
        ];
    }

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

    public function testArrayAccessToGrandchildValues()
    {
        $structure =
            [
                'Alpha' =>
                    [
                        1,
                        2,
                        3
                    ],
                'Berta' =>
                    [
                        ['V1' => 'Beta', 'V2' => '1'],
                        ['V1' => 'Beta', 'V2' => '2']
                    ]

            ];

        $obj = new Tree;
        $obj->set('Root');

        foreach ($structure as $levelOne => $levelTwo) {
            $levelOneTree = new Tree;
            $levelOneTree->set($levelOne);

            foreach ($levelTwo as $value) {
                $leafNode = new Tree;
                $leafNode->set($value);
                $levelOneTree->add($leafNode);
            }

            $obj->add($levelOneTree);
        }

        $obj->next();
        $children = $obj->getChildren();
        $grandchild = $children->getChildren();

        $this->assertSame('Beta', $grandchild['V1']);
        $this->assertSame('1', $grandchild['V2']);
    }

    /**
     * @dataProvider providerArrayGet
     * @param mixed  $expected
     * @param Tree   $obj
     * @param string $offset
     */
    public function testArrayGet($expected, Tree $obj, $offset)
    {
        $this->assertSame($expected, $obj[$offset]);
    }

    /**
     * @dataProvider providerArrayGetFail
     * @expectedException \DomainException
     * @param Tree   $obj
     * @param string $offset
     */
    public function testArrayGetFail(Tree $obj, $offset)
    {
        $obj[$offset];
    }

    /**
     * @dataProvider providerArrayIsset
     * @param bool   $expected
     * @param Tree   $obj
     * @param string $offset
     */
    public function testArrayIsset($expected, Tree $obj, $offset)
    {
        $this->assertSame($expected, isset($obj[$offset]));
    }

    /**
     * @dataProvider providerArraySet
     * @param mixed  $expected
     * @param Tree   $obj
     * @param string $offset
     * @param mixed  $setValue
     */
    public function testArraySet($expected, Tree $obj, $offset, $setValue)
    {
        $obj[$offset] = $setValue;
        $this->assertSame($expected, $obj[$offset]);
    }

    /**
     * @expectedException \DomainException
     */
    public function testArraySetFail()
    {
        $obj = new Tree;
        $obj->set(65);
        $obj['A1'] = 'bad';
    }

    /**
     * @dataProvider providerArrayUnset
     * @param mixed  $expected
     * @param string $index
     * @param Tree   $obj
     */
    public function testArrayUnset($expected, $index, Tree $obj)
    {
        unset($obj[$index]);
        $this->assertSame($expected, $obj->get());
    }

    /**
     * @expectedException \DomainException
     */
    public function testArrayUnsetFail()
    {
        $obj = new Tree;
        $obj->set(65);
        unset($obj['A1']);
    }

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
