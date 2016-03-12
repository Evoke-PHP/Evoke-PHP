<?php
namespace Evoke_Test\Model\Data\Join;

use Evoke\Model\Data\Join\Tabular;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Join\Tabular
 * @covers Evoke\Model\Data\Join\Join
 */
class TabularTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetJoinIDFromMultipleJoins()
    {
        $joinMock = $this->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $obj = new Tabular('t');
        $obj->addJoin('single', $joinMock);
        $obj->addJoin('two_word', $joinMock);
        $obj->addJoin('three_word_join', $joinMock);
        $obj->addJoin('lowerTwo', $joinMock);
        $obj->addJoin('lowerCamelJoin', $joinMock);

        // We expect to receive Upper_Pascal (which can also be got by using
        // lowerCamel) unless the join was added with lowerCamel where it must
        // be retrieved with the exact lowerCamel join ID.
        return [
            'single_camel'     => [
                'obj'      => $obj,
                'expected' => 'single',
                'join'     => 'single'
            ],
            'single_pascal'    => [
                'obj'      => $obj,
                'expected' => 'single',
                'join'     => 'Single'
            ],
            'two_word_pascal'  => [
                'obj'      => $obj,
                'expected' => 'two_word',
                'join'     => 'Two_Word'
            ],
            'three_word_camel' => [
                'obj'      => $obj,
                'expected' => 'three_word_join',
                'join'     => 'threeWordJoin'
            ],
            'lower_two_camel'  => [
                'obj'      => $obj,
                'expected' => 'lowerTwo',
                'join'     => 'lowerTwo'
            ],
            'lower_camel_join' => [
                'obj'      => $obj,
                'expected' => 'lowerCamelJoin',
                'join'     => 'lowerCamelJoin'
            ],
            'alpha_num_extra'  => [
                'obj'      => $obj,
                'expected' => 'lowerCamelJoin',
                'join'     => 'Lower_Camel_j#O#i#N'
            ],
            'alpha_num_less'   => [
                'obj'      => $obj,
                'expected' => 'three_word_join',
                'join'     => 'threewordjoin'
            ]
        ];
    }

    public function getJoins()
    {

        $joinMock        = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();
        $anotherJoinMock = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $one     = new Tabular('one');
        $oneJoin = ['single' => $joinMock];
        $one->addJoin('single', $joinMock);

        $many      = new Tabular('many');
        $manyJoins = [
            'one'   => $joinMock,
            'two'   => $anotherJoinMock,
            'three' => $joinMock
        ];

        foreach ($manyJoins as $joinID => $join) {
            $many->addJoin($joinID, $join);
        }

        return [
            'no_joins'   => [new Tabular('none'), []],
            'one_join'   => [$one, $oneJoin],
            'many_joins' => [$many, $manyJoins]
        ];

    }

    public function providerAddJoinAmbiguous()
    {
        $join = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $aCOE = new Tabular('ACOE');
        $aCOE->addJoin('Add_Camel_Over_Exact', $join);

        $aCOC = new Tabular('ACOC');
        $aCOC->addJoin('addCamelOverCamel', $join);

        $aEOE = new Tabular('AEOE');
        $aEOE->addJoin('Add_Exact_Over_Exact', $join);

        $aEOC = new Tabular('AEOC');
        $aEOC->addJoin('addExactOverCamel', $join);

        $extraChars  = '$*()&#@!~`\'"\\/<>,.=-+_';
        $extraJoinID = 'exact' . $extraChars . 'Join';
        $lessJoinID  = 'exactJoin';

        $aExtra = new Tabular('Add_Extra');
        $aExtra->addJoin($lessJoinID, $join);

        $aLess = new Tabular('Add_Less');
        $aLess->addJoin($extraJoinID, $join);

        $aEmpty = new Tabular('Add_Empty');
        $aEmpty->addJoin('', $join);

        $aNumber = new Tabular('Add_Number');
        $aNumber->addJoin('123', $join);

        return [
            'camel_over_exact'  => [$aCOE, 'addCamelOverExact'],
            'camel_over_camel'  => [$aCOC, 'addCamelOverCamel'],
            'exact_over_exact'  => [$aEOE, 'Add_Exact_Over_Exact'],
            'exact_over_camel'  => [$aEOC, 'Add_Exact_Over_Camel'],
            'extra_over_less'   => [$aExtra, $extraJoinID],
            'less_over_extra'   => [$aLess, $lessJoinID],
            'empty_over_empty'  => [$aEmpty, ''],
            'extra_over_number' => [$aNumber, '1#2#3#'],
            'less_over_extra'   => [$aLess, $lessJoinID],
            'symbol_over_empty' => [$aEmpty, '%$^*&#@!']
        ];
    }

    public function providerAddJoinGetJoinID()
    {
        return [
            'exact_join'            => [
                new Tabular('a'),
                'exact_join',
                'exact_join'
            ],
            'lower_camel'           => [
                new Tabular('b'),
                'lowerCamel',
                'Lower_Camel'
            ],
            'canonical_lower_camel' => [
                new Tabular('c'),
                'lowerCamel',
                'lowerCamel'
            ]
        ];
    }

    public function providerGetJoinIDNotFound()
    {
        $join = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $objStandard = new Tabular('standard');
        $objStandard->addJoin('two_word', $join);

        $objEmpty = new Tabular('empty');

        $objExact = new Tabular(
            'exact',
            ['id'],
            'joint_data',
            true,
            '_T_',
            false  // Don't use alphaNum match
        );
        $objExact->addJoin('exact_match', $join);

        return [
            'empty_given_empty'        => [$objEmpty, ''],
            'empty_given_null'         => [$objEmpty, null],
            'empty_given_something'    => [$objEmpty, 'something'],
            'exact_given_lower_camel'  => [$objExact, 'exactMatch'],
            'exact_given_upper_camel'  => [$objExact, 'ExactMatch'],
            'exact_given_upper_pascal' => [$objExact, 'Exact_Match'],
            'exact_given_upper_case'   => [$objExact, 'EXACT_MATCH'],
            'standard_missing'         => [$objStandard, 'Wrong_Word'],
            'standard_extra_num'       => [$objStandard, 'two2WoRD']
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider             providerAddJoinAmbiguous
     * @expectedException        LogicException
     * @expectedExceptionMessage Ambiguous join
     */
    public function testAddJoinAmbiguous(Tabular $obj, $joinID)
    {
        $join = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $obj->addJoin($joinID, $join);
    }

    /**
     * @dataProvider providerAddJoinGetJoinID
     */
    public function testAddJoinGetJoinID(Tabular $obj, $getName, $joinID)
    {
        $join = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $obj->addJoin($joinID, $join);
        $this->assertSame($joinID, $obj->getJoinID($getName));
    }

    public function testArrangeFlatData()
    {
        $object = new Tabular('t1st');
        $object->addJoin('list', new Tabular('t2nd'));

        $data = [
            [
                't1st_T_id' => 1,
                't1st_T_a'  => 1,
                't1st_T_b'  => 'B 1',
                't2nd_T_id' => 1,
                't2nd_T_x'  => 1,
                't2nd_T_y'  => 'Y 1'
            ],
            [
                't1st_T_id' => 1,
                't1st_T_a'  => 1,
                't1st_T_b'  => 'B 1',
                't2nd_T_id' => 2,
                't2nd_T_x'  => 2,
                't2nd_T_y'  => 'Y 2'
            ],
            [
                't1st_T_id' => 2,
                't1st_T_a'  => 2,
                't1st_T_b'  => 'B 2',
                't2nd_T_id' => null,
                't2nd_T_x'  => null,
                't2nd_T_y'  => null
            ]
        ];

        $expectedData = [
            1 => [
                'a'          => 1,
                'b'          => 'B 1',
                'joint_data' => [
                    'list' => [
                        1 => [
                            'x' => 1,
                            'y' => 'Y 1'
                        ],
                        2 => [
                            'x' => 2,
                            'y' => 'Y 2'
                        ]
                    ]
                ]
            ],
            2 => [
                'a'          => 2,
                'b'          => 'B 2',
                'joint_data' => [
                    'list' => []
                ]
            ]
        ];

        $this->assertSame($expectedData, $object->arrangeFlatData($data));
    }

    public function testArrangeFlatDataMultipleKeys()
    {
        $obj = new Tabular('t1', ['k1', 'k2']);
        $obj->addJoin('l', new Tabular('t2', ['k3', 'k4']));

        $data = [
            [
                't1_T_k1' => 5,
                't1_T_k2' => 6,
                't1_T_va' => 7,
                't2_T_k3' => 8,
                't2_T_k4' => 9,
                't2_T_vb' => 10
            ],
            [
                't1_T_k1' => 5,
                't1_T_k2' => 6,
                't1_T_va' => 7,
                't2_T_k3' => 8,
                't2_T_k4' => 19,
                't2_T_vb' => 100
            ],
            [
                't1_T_k1' => 555,
                't1_T_k2' => 666,
                't1_T_va' => 777,
                't2_T_k3' => null,
                't2_T_k4' => null,
                't2_T_vb' => null
            ]
        ];

        $expected = [
            '5_6'     =>
                [
                    'va'         => 7,
                    'joint_data' => [
                        'l' => [
                            '8_9'  => ['vb' => 10],
                            '8_19' => ['vb' => 100]
                        ]
                    ]
                ],
            '555_666' =>
                [
                    'va'         => 777,
                    'joint_data' => [
                        'l' => []
                    ]
                ]
        ];

        $this->assertSame($expected, $obj->arrangeFlatData($data));
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Missing Key: id for table: standard
     */
    public function testArrangeFlatDataMissingKeys()
    {
        $obj = new Tabular('standard');
        $obj->arrangeFlatData([['standard_T_missing_id' => 1]]);
    }

    public function testArrangeFlatDataWithOptionalNonTabularFields()
    {
        $obj = new Tabular(
            'opt',
            ['id'],
            'joint_data',
            false // Optional Non Tabular Fields.
        );

        $data = [
            [
                'opt_T_id'    => 1,
                'opt_T_v1'    => 'One has V1 1',
                'non_tabular' => 3
            ],
            [
                'opt_T_id'    => 2,
                'opt_T_v1'    => 'Two has V1 2',
                'non_tabular' => 4
            ]
        ];

        $this->assertSame(
            [
                1 => ['v1' => 'One has V1 1'],
                2 => ['v1' => 'Two has V1 2']
            ],
            $obj->arrangeFlatData($data)
        );
    }

    /**
     * @expectedException DomainException
     */
    public function testArrangeFlatDataWithRequiredTabularFields()
    {
        $obj = new Tabular('rqd');

        $data = [
            [
                'rqd_T_id'    => 1,
                'rqd_T_v1'    => 'One has V1 1',
                'non_tabular' => 3
            ],
            [
                'rqd_T_id'    => 2,
                'rqd_T_v1'    => 'Two has V1 2',
                'non_tabular' => 4
            ]
        ];

        $obj->arrangeFlatData($data);
    }

    public function testArrangeFlatDataWithoutKeys()
    {
        $obj = new Tabular('nk', []);

        $data = [
            [
                'nk_T_v1' => 1,
                'nk_T_v2' => 2
            ],
            [
                'nk_T_v1' => 3,
                'nk_T_v2' => 4
            ]
        ];

        $this->assertSame(
            [
                0 => ['v1' => 1, 'v2' => 2],
                1 => ['v1' => 3, 'v2' => 4]
            ],
            $obj->arrangeFlatData($data)
        );
    }

    public function testArrangeFlatDataWithoutKeysMultipleValues()
    {
        $obj = new Tabular('nk', []);
        $obj->addJoin('jt', new Tabular('jr', []));

        $data = [
            [
                'nk_T_v1' => 1,
                'nk_T_v2' => 2,
                'jr_T_va' => 'first'
            ],
            [
                'nk_T_v1' => 1,
                'nk_T_v2' => 2,
                'jr_T_va' => 'second'
            ],
            [
                'nk_T_v1' => 3,
                'nk_T_v2' => 4
            ]
        ];

        $this->assertSame(
            [
                0 => [
                    'v1'         => 1,
                    'v2'         => 2,
                    'joint_data' => [
                        'jt' => [
                            0 => ['va' => 'first'],
                            1 => ['va' => 'second']
                        ]
                    ]
                ],
                1 => [
                    'v1'         => 3,
                    'v2'         => 4,
                    'joint_data' => [
                        'jt' => []
                    ]
                ]
            ],
            $obj->arrangeFlatData($data)
        );
    }

    public function testCreateStandard()
    {
        $this->assertInstanceOf('Evoke\Model\Data\Join\Tabular', new Tabular('standard'));
    }

    /**
     * @dataProvider providerGetJoinIDFromMultipleJoins
     */
    public function testGetJoinID(Tabular $obj, $expected, $join)
    {
        $this->assertSame($expected, $obj->getJoinID($join));
    }

    /**
     * @dataProvider             providerGetJoinIDNotFound
     * @expectedException        DomainException
     * @expectedExceptionMessage Join not found
     */
    public function testGetJoinIDNotFound(Tabular $obj, $join)
    {
        $obj->getJoinID($join);
    }

    /**
     * @dataProvider getJoins
     */
    public function testGetJoins(Tabular $obj, $expected)
    {
        $this->assertSame($expected, $obj->getJoins());
    }
}
// EOF
