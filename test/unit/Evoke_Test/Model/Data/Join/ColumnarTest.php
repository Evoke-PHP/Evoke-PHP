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
            'Multi_Keys' => [
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
            'Simple'     => [
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
        $singleJoin->addJoin('J1', new Columnar(['f2'], ['J_id']));

        $doubleJoin = new Columnar(['f1']);
        $doubleJoin->addJoin('J1', new Columnar(['f2'], ['J1_id']));
        $doubleJoin->addJoin('J2', new Columnar(['f3'], ['J2_id']));

        return [
            'Single_Join' => [
                $singleJoin,
                [
                    [
                        'id'   => 1,
                        'f1'   => 'f1_1',
                        'J_id' => 11,
                        'f2'   => 'f2_1'
                    ],
                    [
                        'id'   => 2,
                        'f1'   => 'f1_2',
                        'J_id' => null,
                        'f2'   => null
                    ]
                ],
                [
                    1 => [
                        'f1'         => 'f1_1',
                        'Joint_Data' => [
                            'J1' => [
                                11 => ['f2' => 'f2_1']
                            ]
                        ]
                    ],
                    2 => ['f1' => 'f1_2']
                ]
            ],
            'Double_Join' => [
                $doubleJoin,
                [
                    [
                        'id'    => 1,
                        'f1'    => 'f1_1',
                        'f2'    => 'f2_1',
                        'f3'    => 'f3_1',
                        'J1_id' => 'J1_1',
                        'J2_id' => 'J2_1'
                    ],
                    [
                        'id'    => 2,
                        'f1'    => 'f1_2',
                        'f2'    => 'f2_2',
                        'f3'    => null,
                        'J1_id' => 'J1_2',
                        'J2_id' => 'J2_2'
                    ]
                ],
                [
                    1 => [
                        'f1'         => 'f1_1',
                        'Joint_Data' => [
                            'J1' => [
                                'J1_1' => ['f2' => 'f2_1']
                            ],
                            'J2' => [
                                'J2_1' => ['f3' => 'f3_1']
                            ]
                        ]
                    ],
                    2 => [
                        'f1'         => 'f1_2',
                        'Joint_Data' => [
                            'J1' => [
                                'J1_2' => ['f2' => 'f2_2']
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
        $a = new Columnar(['A'], ['AI']);
        $b = new Columnar(['B'], ['BI']);
        $c = new Columnar(['C'], ['CI']);
        $d = new Columnar(['D'], ['DI']);
        $b->addJoin('CJ', $c);
        $b->addJoin('DJ', $d);
        $a->addJoin('BJ', $b);

        $data = [
            [
                'A'  => 'A1',
                'AI' => 1,
                'B'  => 'B1',
                'BI' => 1,
                'C'  => 'C1',
                'CI' => 1,
                'D'  => 'D1',
                'DI' => 1,
            ],
            [
                'A'  => 'A2',
                'AI' => 2,
                'B'  => 'B1',
                'BI' => 1,
                'C'  => 'C1',
                'CI' => 1,
                'D'  => null,
                'DI' => null
            ],
            [
                'A'  => 'A2',
                'AI' => 2,
                'B'  => 'B2',
                'BI' => 2,
                'C'  => 'C2',
                'CI' => 2,
                'D'  => 'D2',
                'DI' => 2
            ],
            [
                'A'  => 'A2',
                'AI' => 2,
                'B'  => 'B2',
                'BI' => 2,
                'C'  => 'C3',
                'CI' => 3
            ]
        ];

        $expected = [
            1 => [
                'A'          => 'A1',
                'Joint_Data' => [
                    'BJ' => [
                        1 => [
                            'B'          => 'B1',
                            'Joint_Data' => [
                                'CJ' => [
                                    1 => [
                                        'C' => 'C1'
                                    ]
                                ],
                                'DJ' => [
                                    1 => [
                                        'D' => 'D1'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            2 => [
                'A'          => 'A2',
                'Joint_Data' => [
                    'BJ' => [
                        1 => [
                            'B'          => 'B1',
                            'Joint_Data' => [
                                'CJ' => [
                                    1 => [
                                        'C' => 'C1'
                                    ]
                                ]
                            ]
                        ],
                        2 => [
                            'B'          => 'B2',
                            'Joint_Data' => [
                                'CJ' => [
                                    2 => [
                                        'C' => 'C2'
                                    ],
                                    3 => [
                                        'C' => 'C3'
                                    ]
                                ],
                                'DJ' => [
                                    2 => [
                                        'D' => 'D2'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return [
            'ABC' => [
                'Obj'      => $a,
                'Data'     => $data,
                'Expected' => $expected
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
