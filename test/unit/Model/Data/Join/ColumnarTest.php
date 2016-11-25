<?php
namespace Evoke_Test\Model\Data\Join;

use Evoke\Model\Data\Join\Columnar;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Join\Columnar
 * @covers Evoke\Model\Data\Join\Join
 */
class ColumnarTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerFlatData()
    {
        return [
            'multi_keys' => [
                new Columnar(['f1'], ['i1', 'i2']),
                [
                    [
                        'f1' => 'r1',
                        'i1' => 1,
                        'i2' => 'a'
                    ],
                    [
                        'f1' => 'r2',
                        'i1' => 2,
                        'i2' => 'b'
                    ]
                ],
                [
                    '1_a' => ['f1' => 'r1'],
                    '2_b' => ['f1' => 'r2']
                ]
            ],
            'simple'     => [
                new Columnar(['f1']),
                [
                    ['f1' => 'r1', 'id' => 1],
                    ['f1' => 'r2', 'id' => 2]
                ],
                [
                    1 => ['f1' => 'r1'],
                    2 => ['f1' => 'r2']
                ]
            ]
        ];
    }

    public function providerJointData()
    {
        $singleJoin = new Columnar(['f1']);
        $singleJoin->addJoin('j1', new Columnar(['f2'], ['j_id']));

        $doubleJoin = new Columnar(['f1']);
        $doubleJoin->addJoin('j1', new Columnar(['f2'], ['j1_id']));
        $doubleJoin->addJoin('j2', new Columnar(['f3'], ['j2_id']));

        return [
            'single_join' => [
                $singleJoin,
                [
                    [
                        'id'   => 1,
                        'f1'   => 'f1_1',
                        'j_id' => 11,
                        'f2'   => 'f2_1'
                    ],
                    [
                        'id'   => 2,
                        'f1'   => 'f1_2',
                        'j_id' => null,
                        'f2'   => null
                    ]
                ],
                [
                    1 => [
                        'f1'         => 'f1_1',
                        'joint_data' => [
                            'j1' => [
                                11 => ['f2' => 'f2_1']
                            ]
                        ]
                    ],
                    2 => ['f1' => 'f1_2']
                ]
            ],
            'double_join' => [
                $doubleJoin,
                [
                    [
                        'id'    => 1,
                        'f1'    => 'f1_1',
                        'f2'    => 'f2_1',
                        'f3'    => 'f3_1',
                        'j1_id' => 'j1_1',
                        'j2_id' => 'j2_1'
                    ],
                    [
                        'id'    => 2,
                        'f1'    => 'f1_2',
                        'f2'    => 'f2_2',
                        'f3'    => null,
                        'j1_id' => 'j1_2',
                        'j2_id' => 'j2_2'
                    ]
                ],
                [
                    1 => [
                        'f1'         => 'f1_1',
                        'joint_data' => [
                            'j1' => [
                                'j1_1' => ['f2' => 'f2_1']
                            ],
                            'j2' => [
                                'j2_1' => ['f3' => 'f3_1']
                            ]
                        ]
                    ],
                    2 => [
                        'f1'         => 'f1_2',
                        'joint_data' => [
                            'j1' => [
                                'j1_2' => ['f2' => 'f2_2']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function providerMultiLevelJoins()
    {
        // A -> B --> C
        //        `-> D
        $a = new Columnar(['a'], ['ai']);
        $b = new Columnar(['b'], ['bi']);
        $c = new Columnar(['c'], ['ci']);
        $d = new Columnar(['d'], ['di']);
        $b->addJoin('cj', $c);
        $b->addJoin('dj', $d);
        $a->addJoin('bj', $b);

        $data = [
            [
                'a'  => 'a1',
                'ai' => 1,
                'b'  => 'b1',
                'bi' => 1,
                'c'  => 'c1',
                'ci' => 1,
                'd'  => 'd1',
                'di' => 1,
            ],
            [
                'a'  => 'a2',
                'ai' => 2,
                'b'  => 'b1',
                'bi' => 1,
                'c'  => 'c1',
                'ci' => 1,
                'd'  => null,
                'di' => null
            ],
            [
                'a'  => 'a2',
                'ai' => 2,
                'b'  => 'b2',
                'bi' => 2,
                'c'  => 'c2',
                'ci' => 2,
                'd'  => 'd2',
                'di' => 2
            ],
            [
                'a'  => 'a2',
                'ai' => 2,
                'b'  => 'b2',
                'bi' => 2,
                'c'  => 'c3',
                'ci' => 3
            ]
        ];

        $expected = [
            1 => [
                'a'          => 'a1',
                'joint_data' => [
                    'bj' => [
                        1 => [
                            'b'          => 'b1',
                            'joint_data' => [
                                'cj' => [
                                    1 => [
                                        'c' => 'c1'
                                    ]
                                ],
                                'dj' => [
                                    1 => [
                                        'd' => 'd1'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            2 => [
                'a'          => 'a2',
                'joint_data' => [
                    'bj' => [
                        1 => [
                            'b'          => 'b1',
                            'joint_data' => [
                                'cj' => [
                                    1 => [
                                        'c' => 'c1'
                                    ]
                                ]
                            ]
                        ],
                        2 => [
                            'b'          => 'b2',
                            'joint_data' => [
                                'cj' => [
                                    2 => [
                                        'c' => 'c2'
                                    ],
                                    3 => [
                                        'c' => 'c3'
                                    ]
                                ],
                                'dj' => [
                                    2 => [
                                        'd' => 'd2'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return [
            'abc' => [
                'obj'      => $a,
                'data'     => $data,
                'expected' => $expected
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testConstruct()
    {
        $this->assertInstanceOf('Evoke\Model\Data\Join\Columnar', new Columnar(['f1']));
    }

    /**
     * @dataProvider providerFlatData
     */
    public function testFlatData(Columnar $obj, Array $data, Array $expected)
    {
        $this->assertSame($expected, $obj->arrangeFlatData($data));
    }

    /**
     * @dataProvider providerJointData
     */
    public function testJointData(Columnar $obj, Array $data, Array $expected)
    {
        $this->assertSame($expected, $obj->arrangeFlatData($data));
    }

    /**
     * @dataProvider providerMultiLevelJoins
     */
    public function testMultiLevelJoins(Columnar $obj, Array $data, Array $expected)
    {
        $this->assertEquals($expected, $obj->arrangeFlatData($data));
    }
}
// EOF
