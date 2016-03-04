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
                new Columnar(['F1'], ['I1', 'I2']),
                [
                    [
                        'F1' => 'r1',
                        'I1' => 1,
                        'I2' => 'a'
                    ],
                    [
                        'F1' => 'r2',
                        'I1' => 2,
                        'I2' => 'b'
                    ]
                ],
                [
                    '1_a' => ['F1' => 'r1'],
                    '2_b' => ['F1' => 'r2']
                ]
            ],
            'Simple'     => [
                new Columnar(['F1']),
                [
                    ['F1' => 'r1', 'ID' => 1],
                    ['F1' => 'r2', 'ID' => 2]
                ],
                [
                    1 => ['F1' => 'r1'],
                    2 => ['F1' => 'r2']
                ]
            ]
        ];
    }

    public function providerJointData()
    {
        $singleJoin = new Columnar(['F1']);
        $singleJoin->addJoin('J1', new Columnar(['F2'], ['J_ID']));

        $doubleJoin = new Columnar(['F1']);
        $doubleJoin->addJoin('J1', new Columnar(['F2'], ['J1_ID']));
        $doubleJoin->addJoin('J2', new Columnar(['F3'], ['J2_ID']));

        return [
            'Single_Join' => [
                $singleJoin,
                [
                    [
                        'ID'   => 1,
                        'F1'   => 'F1_1',
                        'J_ID' => 11,
                        'F2'   => 'F2_1'
                    ],
                    [
                        'ID'   => 2,
                        'F1'   => 'F1_2',
                        'J_ID' => null,
                        'F2'   => null
                    ]
                ],
                [
                    1 => [
                        'F1'         => 'F1_1',
                        'Joint_Data' => [
                            'J1' => [
                                11 => ['F2' => 'F2_1']
                            ]
                        ]
                    ],
                    2 => ['F1' => 'F1_2']
                ]
            ],
            'Double_Join' => [
                $doubleJoin,
                [
                    [
                        'ID'    => 1,
                        'F1'    => 'F1_1',
                        'F2'    => 'F2_1',
                        'F3'    => 'F3_1',
                        'J1_ID' => 'J1_1',
                        'J2_ID' => 'J2_1'
                    ],
                    [
                        'ID'    => 2,
                        'F1'    => 'F1_2',
                        'F2'    => 'F2_2',
                        'F3'    => null,
                        'J1_ID' => 'J1_2',
                        'J2_ID' => 'J2_2'
                    ]
                ],
                [
                    1 => [
                        'F1'         => 'F1_1',
                        'Joint_Data' => [
                            'J1' => [
                                'J1_1' => ['F2' => 'F2_1']
                            ],
                            'J2' => [
                                'J2_1' => ['F3' => 'F3_1']
                            ]
                        ]
                    ],
                    2 => [
                        'F1'         => 'F1_2',
                        'Joint_Data' => [
                            'J1' => [
                                'J1_2' => ['F2' => 'F2_2']
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
        $this->assertInstanceOf('Evoke\Model\Data\Join\Columnar', new Columnar(['F1']));
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
