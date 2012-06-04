<?php
use Evoke\HTTP\URI\Rule\Regex;

/**
 *  @covers Evoke\HTTP\URI\Rule\Regex
 */
class RegexTest extends PHPUnit_Framework_TestCase
{
	/** Test that invalid arguments to the constructor raise IAE.
	 *  @covers            Evoke\HTTP\URI\Rule\Regex::__construct
	 *  @expectedException InvalidArgumentException
	 *  @dataProvider      provider__constructInvalidArguments
	 */
	public function test__constructInvalidArguments(
		$match, $replacement, Array $params = array(), $authoritative = false)
	{
		// Ensure that the first two parameters are strings.  They should throw
		// an InvalidArgumentException if they aren't supplied correctly.
		new Evoke\HTTP\URI\Rule\Regex(
			$match, $replacement, $params, $authoritative);

		// $reflectionClass = new ReflectionClass('Evoke\HTTP\URI\Rule\Regex');
		// $reflectionClass->newInstanceArgs($args);
				
		
		/*
		foreach ($invalidArgumentsTests as $args)
		{
			try
			{
				$obj = new Evoke\HTTP\URI\Rule\Regex($args[0], $args[1]);
				$this->fail('InvalidArgumentException should be raised.');
			}
			catch (InvalidArgumentException $e)
			{
				continue;
			}
			catch (Exception $e)
			{
				$this->fail(
					'Invalid argument should have been raised for arguments: ' .
					var_export($args, true) . ' received exception: ' .
					$e->getMessage());
			}
		}
		*/
	}

	public function test__constructInvalidParamSpec()
	{
		// Ensure that passed parameters match the param spec:
		// array('Key' => 'xxx', 'Value' => 'yyy')
		$invalidParamSpecArgs = array(
			array('One1', 'One2', array(array())),
			array('Two1', 'Two2', array(array('Key'   => 'Good',
			                                  'Value' => false))),
			array('Tri1', 'Tri2', array(array('Key'   => false,
			                                  'Value' => 'Good'))));
		$reflectionClass = new ReflectionClass('Evoke\HTTP\URI\Rule\Regex');

		foreach ($invalidParamSpecArgs as $args)
		{
			try
			{
				$reflectionClass->newInstanceArgs($args);
				$this->fail('InvalidArgumentException should be raised.');
			}
			catch (InvalidArgumentException $e)
			{
				continue;
			}
			catch (Exception $e)
			{
				$this->fail(
					'Invalid argument should have been raised for bad param ' .
					' spec in arguments: ' . var_export($args, true) .
					' received exception: ' . $e->getMessage());
			}
		}
		
		$this->assertTrue(
			true, 'Invalid arguments result in an InvalidArgumentException');
	}

	/** Test that the constructor builds the expected object.
	 *  @covers \Evoke\HTTP\URI\Rule\Regex::__construct
	 */
	public function test__constructGood()
	{
		$obj = new Evoke\HTTP\URI\Rule\Regex('str', 'str', array(), true);
		$this->assertInstanceOf('Evoke\HTTP\URI\Rule\Regex', $obj);
		
	}
		
	/** Test the matches for the regex.
	 *  @depends test__constructGood
	 *  @covers  Evoke\HTTP\URI\Rule\Regex::matches
	 */
	public function testMatches()
	{
		$tests = array(
			'Empty_URI_No_Match'  => array(
				'Expected_Return' => false,
				'Setup'           => array('Match'    => '/bad/',
				                           'Params'   => array(),
				                           'Response' => array()),
				'URI'             => ''),
			'Empty_URI_Match'     => array(
				'Expected_Return' => true,
				'Setup'           => array('Match'    => '//',
				                           'Params'   => array(),
				                           'Response' => array()),
				'URI'             => ''),
			'Filled_URI_No_Match' => array(
				'Expected_Return' => false,
				'Setup'           => array('Match'    => '/Prod.*bad$/',
				                           'Params'   => array(),
				                           'Response' => array()),
				'URI'             => 'Products/HotSella'),
			'Filled_URI_Match'    => array(
				'Expected_Return' => true,
				'Setup'           => array('Match' => '/^Prod.*Sella$/',
				                           'Params' => array(),
				                           'Response' => array()),
				'URI'             => 'Products/HotSella'));

		foreach ($tests as $name => $test)
		{
			$obj = new Regex(
				array_merge(array('Authoritative' => true),
				            $test['Setup']));

			$this->assertEquals(
				$test['Expected_Return'],
				$obj->matches($test['URI']),
				$name . ' does not return expected boolean for presence of ' .
				'match.');
		}
	}

	/**
	 *  @depends test__constructGood	   
	 *  @covers Evoke\HTTP\URI\Rule\Regex::getParams
	 */
	public function testGetParams()
	{
		$tests = array(
			'Domain_Exception_For_Bad_Second_Level'      => array(
				'Expected_Return'  => array(),
				'Setup'            => array(
					'Match'    => '/(.*)/',
					'Params'   => array(array('BAD_SECOND_LEVEL')),
					'Response' => array()),
				'Throws_Exception' => true,
				'URI'              => ''),
			'Match_All_Named_Params_Separator_By_Equals' => array(
				'Expected_Return'  => array('Product' => 'Banana',
				                            'Size'    => 'Big'),
				'Setup'            => array(
					'Match'    => '/^\/Product\/(Product)=(.*)&(Size=.*)$/',
					'Params'   => array(
						array('Name'     => array(
							      'Pattern'         => '//',
							      'Replacement'     => '',
							      'URI_Replacement' => '\1'),
						      'Required' => false,
						      'Value'    => array(
							      'Pattern'         => '//',
							      'Replacement'     => '',
							      'URI_Replacement' => '\2')),
						array('Name'     => array(
							      'Pattern'         => '/(.*)=(.*)$/',
							      'Replacement'     => '\1',
							      'URI_Replacement' => '\3'),
						      'Required' => false,
						      'Value'    => array(
							      'Pattern'         => '/(.*)=(.*)$/',
							      'Replacement'     => '\2',
							      'URI_Replacement' => '\3'))),
					'Response' => array()),
				'Throws_Exception' => false,
				'URI'              => '/Product/Product=Banana&Size=Big'),
			'Missing_Required_Param'                     => array(
				'Expected_Return'  => NULL,
				'Setup'            => array(
					'Match'    => '/^\/(Product)=(.*)$/',
					'Params'   => array(
						array('Name'     => array(
							      'Pattern'         => '/NO_MATCH/',
							      'Replacement'     => '',
							      'URI_Replacement' => '\1'),
						      'Required' => true,
						      'Value'    => array(
							      'Pattern'         => '//',
							      'Replacement'     => '',
							      'URI_Replacement' => '\2'))),
					'Response' => array()),
				'Throws_Exception' => true,
				'URI'              => '/Product='));

		foreach ($tests as $name => $test)
		{
			$obj = new Regex(array_merge(array('Authoritative' => true),
			                                       $test['Setup']));
			try
			{
				$params = $obj->getParams($test['URI']);

				$this->assertSame($test['Expected_Return'],
				                  $params,
				                  $name . ' does not return params as ' .
				                  'expected.');
			}
			catch (Exception $e)
			{
				$this->assertTrue($test['Throws_Exception'],
				                  $name . ' should not throw exception.');
			}
		}
	}

	/** Test getResponse and the private method getMappedValue.
	 *  @depends test__constructGood
	 *  @covers  Evoke\HTTP\URI\Rule\Regex::getResponse
	 *  @covers  Evoke\HTTP\URI\Rule\Regex::getMappedValue
	 */
	public function testGetResponse()
	{
		$tests = array(
			'No_Match'              => array(
				'Expected_Return'  => NULL,
				'Setup'            => array(
					'Match'    => '/NO_MATCH/',
					'Params'   => array(),
					'Response' => array('Pattern'         => '//',
					                    'Replacement'     => '',
					                    'URI_Replacement' => '')),
				'Throws_Exception' => true,
				'URI'              => '/This_URI_IS_UNEXPECTED'),
			'Domain_Exception'      => array(
				'Expected_Return'  => NULL,
				'Setup'            => array(
					'Match'    => '/MATCHES/',
					'Params'   => array(),
					'Response' => array()),
				'Throws_Exception' => true,
				'URI'              => 'MATCHES'),
			'Basic_Check'           => array(
				'Expected_Return'  => 'MappedURI',
				'Setup'            => array(
					'Match'    => '/IGN(.*)ORE/',
					'Params'   => array(),
					'Response' => array('Pattern'         => '/_/',
					                    'Replacement'     => '',
					                    'URI_Replacement' => '\1')),
				'Throws_Exception' => false,
				'URI'              => 'IGN_Map_ped_URI_ORE'),
			'Second_Level_No_Match' => array(
				'Expected_Return'  => NULL,
				'Setup'            => array(
					'Match'         => '/.*/',
					'Params'        => array(),
					'Response'      => array(
						'Pattern'         => '/SECOND_LEVEL_BAD/',
						'Replacement'     => 'blah',
						'URI_Replacement' => '\1')),
				'Throws_Exception'      => true,
				'URI' => 'NO_ME_CARE_BUT_NO_SECOND_LEVEL_MATCH'));

		foreach ($tests as $name => $test)
		{
			$obj = new Regex(array_merge(array('Authoritative' => true),
			                                       $test['Setup']));

			try
			{
				$response = $obj->getResponse($test['URI']);

				$this->assertEquals($test['Expected_Return'],
				                    $response,
				                    $name . ' does not return Response as ' .
				                    'expected.');
			}
			catch (Exception $e)
			{
				$this->assertTrue($test['Throws_Exception'],
				                  $name . ' should not throw exception.');
			}
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	public function provider__constructInvalidArguments()
	{
		return array(
			array(array('Both Bad'), 12),
			array('Only 1 Good', new stdClass()),
			array('Good', array('Bad')),
			array(NULL, 'Second one good.'),
			array(true, 'First was bad.'));
	}
}
// EOF