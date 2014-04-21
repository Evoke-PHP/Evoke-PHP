<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Rule,
	PHPUnit_Framework_TestCase;

class Test_Rule_Extended extends Rule
{
	public function getMediaType()
	{
		return $this->mediaType;
	}
	
	public function isMatch()
	{
		return true;
	}   
}

class RuleTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\HTTP\MediaType\Rule\Rule::__construct
	 */
	public function testCreate()
	{
		$obj = new Test_Rule_Extended('Output_Format');
		$this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Rule', $obj);
	}

	/**
	 * @covers Evoke\Network\HTTP\MediaType\Rule\Rule::getOutputFormat
	 */
	public function testGetOutputFormat()
	{
		$obj = new Test_Rule_Extended('Output_Format');
		$this->assertSame('Output_Format', $obj->getOutputFormat());
	}

	/**
	 * @covers Evoke\Network\HTTP\MediaType\Rule\Rule::setMediaType
	 */
	public function testSetMediaType()
	{
		$obj = new Test_Rule_Extended('DC');
		$mediaType = ['Params'   => [],
		              'Q_Factor' => '1.0',
		              'Subtype'  => 'TV',
		              'Type'     => 'Screen'];
		$obj->setMediaType($mediaType);
		$this->assertSame($mediaType, $obj->getMediaType());
	}
			
}
// EOF