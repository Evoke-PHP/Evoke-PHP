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
        $obj->set(['a1' => 3, 'a2' => 7]);

        return [
            'get_first'  =>
                [
                    'expected' => 3,
                    'obj'      => $obj,
                    'offset'   => 'a1'
                ],
            'get_second' =>
                [
                    'expected' => 7,
                    'obj'      => $obj,
                    'offset'   => 'a2'
                ]
        ];

    }

    public function providerArrayGetFail()
    {
        $objUnsetIndex = new Tree;
        $objUnsetIndex->set(['a1' => 3, 'a2' => 7]);

        $objNonArray = new Tree;
        $objNonArray->set(67);

        return [
            'non_array'  =>
                [
                    'obj'      => $objNonArray,
                    'offset'   => 'b1'
                ],
            'unset_index' =>
                [
                    'obj'      => $objUnsetIndex,
                    'offset'   => 'b1'
                ]
        ];
    }

    public function providerArrayIsset()
    {
        $issetObj = new Tree;
        $issetObj->set(['a1' => 3]);

        $unsetObj = clone($issetObj);

        return [
            'is_set'  =>
                [
                    'expected' => true,
                    'obj'      => $issetObj,
                    'offset'   => 'a1'
                ],
            'not_set' =>
                [
                    'expected' => false,
                    'obj'      => $unsetObj,
                    'offset'   => 'a2'
                ]
        ];
    }

    public function providerArraySet()
    {
        $obj = new Tree;
        $obj->set(['a1' => 3]);

        return [
            'existing_index' =>
                [
                    'expected'  => 7,
                    'obj'       => $obj,
                    'offset'    => 'a1',
                    'set_value' => 7
                ],
            'new_index'      =>
                [
                    'expected'  => '63e',
                    'obj'       => $obj,
                    'offset'    => 'a2',
                    'set_value' => '63e'
                ]
        ];
    }

    public function providerArrayUnset()
    {
        $obj = new Tree;
        $obj->set(['a1' => 1, 'a2' => 2, 'a3' => 3]);

        return [
            'middle' =>
                [
                    'expected' => ['a1' => 1, 'a3' => 3],
                    'index'    => 'a2',
                    'obj'      => $obj
                ]
        ];
    }

    public function providerGetChildren()
    {
        return [
            'one'  => [[$this->getMock('Evoke\Model\Data\TreeIface')]],
            'more' => [
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
            'none' => [[], false],
            'one'  => [
                [$this->getMock('Evoke\Model\Data\TreeIface')],
                true
            ],
            'more' => [
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
            'array'  => [[1, '2', new \stdClass]],
            'int'    => [1],
            'string' => ['blah'],
            'object' => [new \stdClass]
        ];
    }

    public function providerValidNoNext()
    {
        $oneTree = new Tree;
        $oneTree->add(new Tree);

        return [
            'empty' => [new Tree, false],
            'one'   => [$oneTree, true]
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
            'empty' => [new Tree, false],
            'one'   => [$oneTree, false],
            'two'   => [$twoTree, true]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testArrayAccessToGrandchildValues()
    {
        $structure =
            [
                'alpha' =>
                    [
                        1,
                        2,
                        3
                    ],
                'berta' =>
                    [
                        ['v1' => 'beta', 'v2' => '1'],
                        ['v1' => 'beta', 'v2' => '2']
                    ]

            ];

        $obj = new Tree;
        $obj->set('root');

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

        $this->assertSame('beta', $grandchild['v1']);
        $this->assertSame('1', $grandchild['v2']);
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
        $obj['a1'] = 'bad';
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
        unset($obj['a1']);
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
