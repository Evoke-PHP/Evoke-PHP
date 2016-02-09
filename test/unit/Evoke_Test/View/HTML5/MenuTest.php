<?php
namespace Evoke_Test\View\HTML5;

use Evoke\Model\Data\Tree;
use Evoke\View\HTML5\Menu;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\Menu
 */
class MenuTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Requires menu data but not given any.
     *
     * @expectedException        LogicException
     * @expectedExceptionMessage needs tree to be set.
     */
    public function testRequiresTreeNoneGiven()
    {
        $obj = new Menu;
        $obj->get();
    }

    /**
     * @expectedException        ErrorException
     */
    public function testRequiresTreeInvalidGiven()
    {
        $obj = new Menu;
        set_error_handler(function () {
            throw new \ErrorException("Expected");
        });

        try {
            $obj->set(34);
        } catch (\ErrorException $e) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();
    }

    /**
     * Empty Menu.
     */
    public function testGetEmptyMenu()
    {
        $tIndex   = 0;
        $mockTree = $this->getMock('Evoke\Model\Data\TreeIface');
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('get')
            ->with()
            ->will($this->returnValue('Name'));
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('hasChildren')
            ->with()
            ->will($this->returnValue(false));

        $obj = new Menu;
        $obj->set($mockTree);
        $this->assertSame(['ul', ['class' => 'Menu Name Empty'], []], $obj->get());
    }

    /**
     * Single Level Menu.
     */
    public function testGetSingleLevelMenu()
    {
        $cIndex        = 0;
        $mockChildTree = $this->getMock('Evoke\Model\Data\TreeIface');
        $mockChildTree
            ->expects($this->at($cIndex++))
            ->method('get')
            ->with()
            ->will($this->returnValue([
                'Href' => 'SL_Href',
                'Text' => 'SL_Text'
            ]));

        $tIndex   = 0;
        $mockTree = $this->getMock('Evoke\Model\Data\TreeIface');
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('get')
            ->with()
            ->will($this->returnValue('SL_Name'));
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('hasChildren')
            ->with()
            ->will($this->returnValue(true));
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('rewind');
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('valid')
            ->with()
            ->will($this->returnValue(true));

        // I don't know why RecursiveIteratorIterator has an extra next and
        // valid after the rewind / valid, but it does.
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('next');
        $mockTree
            ->expects($this->at($tIndex++))
            ->method('valid')
            ->with()
            ->will($this->returnValue(true));

        $mockTree
            ->expects($this->at($tIndex++))
            ->method('current')
            ->with()
            ->will($this->returnValue($mockChildTree));

        $obj = new Menu;
        $obj->set($mockTree);

        $this->assertSame(
            [
                'ul',
                ['class' => 'Menu SL_Name'],
                [
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => 'SL_Href'],
                                'SL_Text'
                            ]
                        ]
                    ]
                ]
            ],
            $obj->get()
        );
    }

    /**
     * @uses Evoke\Model\Data\Tree
     */
    public function testSingleLevelMenuRealTree()
    {
        $a = new Tree;
        $a->set(['Href' => '/a', 'Text' => 'a']);

        $b = new Tree;
        $b->set(['Href' => '/b', 'Text' => 'b']);

        $tree = new Tree;
        $tree->set('Main_Tree');
        $tree->add($a);
        $tree->add($b);

        $obj = new Menu;
        $obj->set($tree);

        $this->assertSame(
            [
                'ul',
                ['class' => 'Menu Main_Tree'],
                [
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/a'],
                                'a'
                            ]
                        ]
                    ],
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/b'],
                                'b'
                            ]
                        ]
                    ]
                ]
            ],
            $obj->get()
        );
    }

    /**
     * Multi Level Menu.
     *
     * @uses Evoke\Model\Data\Tree
     */
    public function testMultiLevelMenuRealTree()
    {
        $letters          = ['a', 'b', 'c', 'd'];
        $firstLevelItems  = [3, 2, 1, 0];
        $secondLevelItems = [[1, 2, 3], [2, 0], [1]];
        $treeElements     = [];
        $tree             = new Tree;
        $tree->set('Multi');

        // Use a real tree because mocking this is too complex.
        foreach ($letters as $index => $letter) {
            $treeElements[$letter] = new Tree;
            $treeElements[$letter]->set([
                'Href' => '/0/' . $letter,
                'Text' => '0 ' . $letter
            ]);

            for ($i = 0; $i < $firstLevelItems[$index]; $i++) {
                $firstLevelIndex                = $letter . $i;
                $treeElements[$firstLevelIndex] = new Tree;
                $treeElements[$firstLevelIndex]->set(
                    [
                        'Href' => '/1/' . $firstLevelIndex,
                        'Text' => '1 ' . $firstLevelIndex
                    ]
                );

                for ($j = 0; $j < $secondLevelItems[$index][$i]; $j++) {
                    $secondLevelIndex                = $letter . $i . $letters[$j];
                    $treeElements[$secondLevelIndex] = new Tree;
                    $treeElements[$secondLevelIndex]->set(
                        [
                            'Href' => '/2/' . $secondLevelIndex,
                            'Text' => '2 ' . $secondLevelIndex
                        ]
                    );

                    $treeElements[$firstLevelIndex]->add($treeElements[$secondLevelIndex]);
                }

                $treeElements[$letter]->add($treeElements[$firstLevelIndex]);
            }

            $tree->add($treeElements[$letter]);
        }

        $obj = new Menu;
        $obj->set($tree);

        $expected =
            [
                'ul',
                ['class' => 'Menu Multi'],
                [
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/0/a'],
                                '0 a'
                            ],
                            [
                                'ul',
                                [],
                                [
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/a0'],
                                                '1 a0'
                                            ],
                                            [
                                                'ul',
                                                [],
                                                [
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a0a'],
                                                                '2 a0a'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/a1'],
                                                '1 a1'
                                            ],
                                            [
                                                'ul',
                                                [],
                                                [
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a1a'],
                                                                '2 a1a'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a1b'],
                                                                '2 a1b'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/a2'],
                                                '1 a2'
                                            ],
                                            [
                                                'ul',
                                                [],
                                                [
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a2a'],
                                                                '2 a2a'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a2b'],
                                                                '2 a2b'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/a2c'],
                                                                '2 a2c'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/0/b'],
                                '0 b'
                            ],
                            [
                                'ul',
                                [],
                                [
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/b0'],
                                                '1 b0'
                                            ],
                                            [
                                                'ul',
                                                [],
                                                [
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/b0a'],
                                                                '2 b0a'
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/b0b'],
                                                                '2 b0b'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/b1'],
                                                '1 b1'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/0/c'],
                                '0 c'
                            ],
                            [
                                'ul',
                                [],
                                [
                                    [
                                        'li',
                                        ['class' => 'Menu_Item Level_1'],
                                        [
                                            [
                                                'a',
                                                ['href' => '/1/c0'],
                                                '1 c0'
                                            ],
                                            [
                                                'ul',
                                                [],
                                                [
                                                    [
                                                        'li',
                                                        ['class' => 'Menu_Item Level_2'],
                                                        [
                                                            [
                                                                'a',
                                                                ['href' => '/2/c0a'],
                                                                '2 c0a'
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'li',
                        ['class' => 'Menu_Item Level_0'],
                        [
                            [
                                'a',
                                ['href' => '/0/d'],
                                '0 d'
                            ]
                        ]
                    ]
                ]
            ];
        $actual   = $obj->get();

        $this->assertSame($expected, $actual);
    }
}
// EOF
