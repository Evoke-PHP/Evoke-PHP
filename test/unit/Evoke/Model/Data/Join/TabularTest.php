<?php
namespace Evoke_Test\Model\Data\Join;

use Evoke\Model\Data\Join\JoinIface,
    Evoke\Model\Data\Join\Tabular,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Join\Tabular
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

        $obj = new Tabular('T');
        $obj->addJoin('Single', $joinMock);
        $obj->addJoin('Two_Word', $joinMock);
        $obj->addJoin('Three_Word_Join', $joinMock);
        $obj->addJoin('lowerTwo', $joinMock);
        $obj->addJoin('lowerCamelJoin', $joinMock);

        // We expect to receive Upper_Pascal (which can also be got by using
        // lowerCamel) unless the join was added with lowerCamel where it must
        // be retrieved with the exact lowerCamel join ID.
        return [
            'Single_Camel'     => [
                'Obj'      => $obj,
                'Expected' => 'Single',
                'Join'     => 'single'],
            'Single_Pascal'    => [
                'Obj'      => $obj,
                'Expected' => 'Single',
                'Join'     => 'Single'],
            'Two_Word_Pascal'  => [
                'Obj'      => $obj,
                'Expected' => 'Two_Word',
                'Join'     => 'Two_Word'],
            'Three_Word_Camel' => [
                'Obj'      => $obj,
                'Expected' => 'Three_Word_Join',
                'Join'     => 'threeWordJoin'],
            'Lower_Two_Camel'  => [
                'Obj'      => $obj,
                'Expected' => 'lowerTwo',
                'Join'     => 'lowerTwo'],
            'Lower_Camel_Join' => [
                'Obj'      => $obj,
                'Expected' => 'lowerCamelJoin',
                'Join'     => 'lowerCamelJoin'],
            'Alpha_Num_Extra'  => [
                'Obj'      => $obj,
                'Expected' => 'lowerCamelJoin',
                'Join'     => 'Lower_Camel_j#O#i#N'],
            'Alpha_Num_Less'   => [
                'Obj'      => $obj,
                'Expected' => 'Three_Word_Join',
                'Join'     => 'threewordjoin']];
    }

    public function getJoins()
    {

        $joinMock = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();
        $anotherJoinMock = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $one = new Tabular('One');
        $oneJoin = ['Single' => $joinMock];
        $one->addJoin('Single', $joinMock);

        $many = new Tabular('Many');
        $manyJoins = ['One'   => $joinMock,
                      'Two'   => $anotherJoinMock,
                      'Three' => $joinMock];

        foreach ($manyJoins as $joinID => $join)
        {
            $many->addJoin($joinID, $join);
        }

        return [
            'No_Joins'   => [new Tabular('None'), []],
            'One_Join'   => [$one, $oneJoin],
            'Many_Joins' => [$many, $manyJoins]];

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

        $extraChars = '$*()&#@!~`\'"\\/<>,.=-+_';
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

        return ['Camel_Over_Exact'  => [$aCOE, 'addCamelOverExact'],
                'Camel_Over_Camel'  => [$aCOC, 'addCamelOverCamel'],
                'Exact_Over_Exact'  => [$aEOE, 'Add_Exact_Over_Exact'],
                'Exact_Over_Camel'  => [$aEOC, 'Add_Exact_Over_Camel'],
                'Extra_Over_Less'   => [$aExtra, $extraJoinID],
                'Less_Over_Extra'   => [$aLess, $lessJoinID],
                'Empty_Over_Empty'  => [$aEmpty, ''],
                'Extra_Over_Number' => [$aNumber, '1#2#3#'],
                'Less_Over_Extra'   => [$aLess, $lessJoinID],
                'Symbol_Over_Empty' => [$aEmpty, '%$^*&#@!']];
    }

    public function providerAddJoinGetJoinID()
    {
        return [
            'Exact_Join'            => [
                new Tabular('A'), 'Exact_Join', 'Exact_Join'],
            'Lower_Camel'           => [
                new Tabular('B'), 'lowerCamel', 'Lower_Camel'],
            'Canonical_Lower_Camel' => [
                new Tabular('C'), 'lowerCamel', 'lowerCamel']];
    }

    public function providerGetJoinIDNotFound()
    {
        $join = $this
            ->getMockBuilder('Evoke\Model\Data\Join\JoinIface')
            ->disableOriginalConstructor()
            ->getMock();

        $objStandard = new Tabular('Standard');
        $objStandard->addJoin('Two_Word', $join);

        $objEmpty = new Tabular('Empty');

        $objExact = new Tabular('Exact',
                                array('ID'),
                                'Joint_Data',
                                true,
                                '_T_',
                                false); // Don't use alphaNum match
        $objExact->addJoin('Exact_Match', $join);

        return [
            'Empty_Given_Empty'        => [$objEmpty, ''],
            'Empty_Given_Null'         => [$objEmpty, NULL],
            'Empty_Given_Something'    => [$objEmpty, 'something'],
            'Exact_Given_Lower_Camel'  => [$objExact, 'exactMatch'],
            'Exact_Given_Lower_Pascal' => [$objExact, 'exact_match'],
            'Exact_Given_Upper_Camel'  => [$objExact, 'ExactMatch'],
            'Exact_Given_Upper_Case'   => [$objExact, 'EXACT_MATCH'],
            'Standard_Missing'         => [$objStandard, 'Wrong_Word'],
            'Standard_Extra_Num'       => [$objStandard, 'two2WoRD']];
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
        $object = new Tabular('T1st');
        $object->addJoin('List', new Tabular('T2nd'));

        $data = [
            ['T1st_T_ID' => 1,
             'T1st_T_A'  => 1,
             'T1st_T_B'  => 'B 1',
             'T2nd_T_ID' => 1,
             'T2nd_T_X'  => 1,
             'T2nd_T_Y'  => 'Y 1'],
            ['T1st_T_ID' => 1,
             'T1st_T_A'  => 1,
             'T1st_T_B'  => 'B 1',
             'T2nd_T_ID' => 2,
             'T2nd_T_X'  => 2,
             'T2nd_T_Y'  => 'Y 2'],
            ['T1st_T_ID' => 2,
             'T1st_T_A'  => 2,
             'T1st_T_B'  => 'B 2',
             'T2nd_T_ID' => NULL,
             'T2nd_T_X'  => NULL,
             'T2nd_T_Y'  => NULL]];

        $expectedData = [
            1 => ['A'        => 1,
                  'B'        => 'B 1',
                  'Joint_Data' => [
                      'List' => [
                          1 => ['X'     => 1,
                                'Y'     => 'Y 1'],
                          2 => ['X'     => 2,
                                'Y'     => 'Y 2']]]],
            2 => ['A'     => 2,
                  'B'     => 'B 2',
                  'Joint_Data' => [
                      'List' => []]]];

        $this->assertSame($expectedData, $object->arrangeFlatData($data));
    }

    public function testArrangeFlatDataMultipleKeys()
    {
        $obj = new Tabular('T1', ['K1', 'K2']);
        $obj->addJoin('L', new Tabular('T2', ['K3', 'K4']));

        $data = [
            ['T1_T_K1' => 5,
             'T1_T_K2' => 6,
             'T1_T_VA' => 7,
             'T2_T_K3' => 8,
             'T2_T_K4' => 9,
             'T2_T_VB' => 10],
            ['T1_T_K1' => 5,
             'T1_T_K2' => 6,
             'T1_T_VA' => 7,
             'T2_T_K3' => 8,
             'T2_T_K4' => 19,
             'T2_T_VB' => 100],
            ['T1_T_K1' => 555,
             'T1_T_K2' => 666,
             'T1_T_VA' => 777,
             'T2_T_K3' => NULL,
             'T2_T_K4' => NULL,
             'T2_T_VB' => NULL]];

        $expected = [
            '5_6'     =>
            ['VA'         => 7,
             'Joint_Data' => [
                 'L' => ['8_9'  => ['VB' => 10],
                         '8_19' => ['VB' => 100]]]],
            '555_666' =>
            ['VA'         => 777,
             'Joint_Data' => [
                 'L' => []]]];

        $this->assertSame($expected, $obj->arrangeFlatData($data));
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Missing Key: ID for table: Standard
     */
    public function testArrangeFlatDataMissingKeys()
    {
        $obj = new Tabular('Standard');
        $obj->arrangeFlatData([['Standard_T_Missing_ID' => 1]]);
    }

    public function testArrangeFlatDataWithOptionalNonTabularFields()
    {
        $obj = new Tabular('Opt',
                           ['ID'],
                           'Joint_Data',
                           false); // Optional Non Tabular Fields.

        $data = [
            ['Opt_T_ID'    => 1,
             'Opt_T_V1'    => 'One has V1 1',
             'Non_Tabular' => 3],
            ['Opt_T_ID'    => 2,
             'Opt_T_V1'    => 'Two has V1 2',
             'Non_Tabular' => 4]];

        $this->assertSame([1 => ['V1' => 'One has V1 1'],
                           2 => ['V1' => 'Two has V1 2']],
                          $obj->arrangeFlatData($data));
    }

    /**
     * @expectedException DomainException
     */
    public function testArrangeFlatDataWithRequiredTabularFields()
    {
        $obj = new Tabular('Rqd');

        $data = [
            ['Rqd_T_ID'    => 1,
             'Rqd_T_V1'    => 'One has V1 1',
             'Non_Tabular' => 3],
            ['Rqd_T_ID'    => 2,
             'Rqd_T_V1'    => 'Two has V1 2',
             'Non_Tabular' => 4]];

        $obj->arrangeFlatData($data);
    }

    public function testArrangeFlatDataWithoutKeys()
    {
        $obj = new Tabular('NK', []);

        $data = [
            ['NK_T_V1' => 1,
             'NK_T_V2' => 2],
            ['NK_T_V1' => 3,
             'NK_T_V2' => 4]];

        $this->assertSame(
            [0 => ['V1' => 1, 'V2' => 2],
             1 => ['V1' => 3, 'V2' => 4]],
            $obj->arrangeFlatData($data));
    }

    public function testArrangeFlatDataWithoutKeysMultipleValues()
    {
        $obj = new Tabular('NK', []);
        $obj->addJoin('JT', new Tabular('JR', []));

        $data = [
            ['NK_T_V1' => 1,
             'NK_T_V2' => 2,
             'JR_T_VA' => 'First'],
            ['NK_T_V1' => 1,
             'NK_T_V2' => 2,
             'JR_T_VA' => 'Second'],
            ['NK_T_V1' => 3,
             'NK_T_V2' => 4]];

        $this->assertSame(
            [0 => ['V1'         => 1,
                   'V2'         => 2,
                   'Joint_Data' => [
                       'JT' => [0 => ['VA' => 'First'],
                                1 => ['VA' => 'Second']]]],
             1 => ['V1'         => 3,
                   'V2'         => 4,
                   'Joint_Data' => [
                       'JT' => []]]],
            $obj->arrangeFlatData($data));
    }

    public function testCreateStandard()
    {
        $this->assertInstanceOf('Evoke\Model\Data\Join\Tabular',
                                new Tabular('Standard'));
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