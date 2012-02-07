<?php
use \Evoke\Core\URI as URI;

class MapperRegexTest extends PHPUnit_Framework_TestCase
{ 
	/** @covers \Evoke\Core\URI\MapperRegex::__construct
	 */
	public function test__construct()
	{
		$testMethod = 'Evoke\Core\URI\MapperRegex::__construct';
		$requirements = array('Match'    => ' requires Match as string',
		                      'Params'   => ' requires Params as array',
		                      'Response' => ' requires Response as array');
      
		$tests = array(
			'Empty Setup' => array(
				'Exception_Expected' => true,
				'Exception_Message'  =>  $testMethod . $requirements['Match'],
				'Setup'              => array()),
			'Bad Match' => array(
				'Exception_Expected' => true,
				'Exception_Message'  => $testMethod . $requirements['Match'],
				'Setup'              => array('Match' => 129)),
			'Bad Params' => array(
				'Exception_Expected' => true,
				'Exception_Message'  => $testMethod . $requirements['Params'],
				'Setup'              => array('Match'    => '/.*/',
				                              'Params'   => 'bad',
				                              'Response' => '')),
			'Bad Response' => array(
				'Exception_Expected' => true,
				'Exception_Message'  => $testMethod . $requirements['Response'],
				'Setup'              => array('Match'    => '/good/',
				                              'Params'   => array('good'),
				                              'Response' => 'bad')),
			'Good' => array(
				'Exception_Expected' => false,
				'Setup'              => array('Authoritative' => true,
				                              'Match'         => '/good/',
				                              'Params'        => array('good'),
				                              'Response'      => array('good'))));

		foreach ($tests as $name => $test)
		{
			if ($test['Exception_Expected'])
			{
				try
				{
					$obj = new URI\MapperRegex($test['Setup']);
				}
				catch (Exception $e)
				{
					$this->assertEquals($test['Exception_Message'],
					                    $e->getMessage(),
					                    $name . 'No exception as expected.');
				}
			}
			else
			{
				$obj = new URI\MapperRegex($test['Setup']);
				$this->assertTrue($obj instanceof URI\MapperRegex,
				                  $name . 'Object created as MapperRegex.');
			}
		}
	}


	/** @covers \Evoke\Core\URI\MapperRegex::matches
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
			$obj = new URI\MapperRegex(
				array_merge(array('Authoritative' => true),
				            $test['Setup']));

			$this->assertEquals(
				$test['Expected_Return'],
				$obj->matches($test['URI']),
				$name . ' does not return expected boolean for presence of match.');
		}	 
	}

	/** @covers \Evoke\Core\URI\MapperRegex::getParams
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
			$obj = new URI\MapperRegex(array_merge(array('Authoritative' => true),
			                                       $test['Setup']));
			try
			{
				$params = $obj->getParams($test['URI']);
	    
				$this->assertSame($test['Expected_Return'],
				                  $params,
				                  $name . ' does not return params as expected.');
			}
			catch (Exception $e)
			{
				$this->assertTrue($test['Throws_Exception'],
				                  $name . ' should not throw exception.');
			}
		}
	}

	/** Test getResponse and the private method getMappedValue.
	 *  
	 *  @covers \Evoke\Core\URI\MapperRegex::getResponse
	 *  @covers \Evoke\Core\URI\MapperRegex::getMappedValue
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
			$obj = new URI\MapperRegex(array_merge(array('Authoritative' => true),
			                                       $test['Setup']));

			try
			{
				$response = $obj->getResponse($test['URI']);
				
				$this->assertEquals($test['Expected_Return'],
				                    $response,
				                    $name . ' does not return Response as expected.');
			}
			catch (Exception $e)
			{
				$this->assertTrue($test['Throws_Exception'],
				                  $name . ' should not throw exception.');
			}
		}		
	}
}
// EOF