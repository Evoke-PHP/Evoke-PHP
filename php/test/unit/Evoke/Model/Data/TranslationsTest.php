<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Translations,
	Evoke\HTTP\RequestIface,
	PHPUnit_Framework_TestCase;

/**
 * Translations Test.
 *
 * Test the Modelling of translations.
 */
class TranslationsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the construction of a good object.
	 *
	 * @covers Evoke\Model\Data\Translations::__construct
	 * @covers Evoke\Model\Data\TranslationsIface
	 */
	public function test__constructGood()
	{
		$this->assertInstanceOf(
			'Evoke\Model\Data\Translations',
			new Translations($this->getMock('\Evoke\HTTP\RequestIface')));
	}

	/**
	 * Test that the constructor raises an IAE for bad scalars.
	 *
	 * @covers            Evoke\Model\Data\Translations::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function test__constructInvalidArguments()
	{
		$object = new Translations($this->getMock('\Evoke\HTTP\RequestIface'),
		                           array(),
		                           array('Invalid(non-string)'));
	}

	/**
	 * Test that the languages that the translations are provided in can be
	 * retrieved.
	 *
	 * @covers       Evoke\Model\Data\Translations::getLanguages
	 * @dataProvider providerGetLanguages
	 * @depends      test__constructGood
	 */
	public function testGetLanguages($object, $expected)
	{
		$this->assertSame($expected, $object->getLanguages());
	}

	/**
	 * Test that the languages that the translations are in can be queried.
	 *
	 * @covers       Evoke\Model\Data\Translations::hasLanguage
	 * @dataProvider providerHasLanguage
	 * @depends      test__constructGood
	 */
	public function testHasLanguage($object, $language, $expected)
	{
		$this->assertSame($expected, $object->hasLanguage($language));
	}
	
	/**
	 * Test that the translator gets the highest priority language that we have
	 * in the first translation.
	 *
	 * @covers  	 Evoke\Model\Data\Translations::getLanguage
	 * @covers  	 Evoke\Model\Data\Translations::setLanguage
	 * @dataProvider providerLanguagePriority
	 */
	public function testLanguagePriority($object, $expected)
	{
		$this->assertSame($expected, $object->getLanguage());
	}	

	/**
	 * Test that the language can be set and retrieved using the translator.
	 *
	 * @covers Evoke\Model\Data\Translations::getLanguage
	 * @covers Evoke\Model\Data\Translations::setLanguage
	 */
	public function testSetLanguageExplicit()
	{
		$object = new Translations(
			$this->getMock('Evoke\HTTP\RequestIface'),
			array(array('Name' => 'First',
			            'Page' => '',
			            'FR'   => 'French is First',
			            'TG'   => 'Tagalog',
			            'DE'   => 'German is Third')));
		$object->setLanguage('TG');
		
		$this->assertSame('TG', $object->getLanguage());
	}

	/**
	 * Test the setting of a language that does not have a translation.
	 *
	 * @covers            Evoke\Model\Data\Translations::setLanguage
	 * @expectedException DomainException
	 */
	public function testSetLanguageBad()
	{
		$object = new Translations(
			$this->getMock('Evoke\HTTP\RequestIface'),
			array(array('Name' => 'First',
			            'Page' => '',
			            'FR'   => 'French is First',
			            'TG'   => 'Tagalog',
			            'DE'   => 'German is Third')));
		$object->setLanguage('ZZ');
	}
	
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetLanguages()
	{
		$tests = array();
		$request = $this->getMock('Evoke\HTTP\RequestIface');

		$tests['Empty_Data'] =
			array('Object'   => new Translations($request),
			      'Expected' => array());
		
		$twoLanguagesData = array(array('ID'     => '1',
		                                'Name'   => 'Who_Cares',
		                                'L1'     => 'Lang_1',
		                                'Lang_2' => 'Lang_2'));
		$objectTwoLanguages = new Translations($request, $twoLanguagesData);
		$tests['Two_Languages'] =
			array('Object'   => $objectTwoLanguages,
			      'Expected' => array('L1', 'Lang_2'));

		return $tests;
		
		$multipleRecordData = array(array('NON_LANG'  => true,
		                                  'Trans_ID'  => 'G103',
		                                  'Gibberish' => 'wun oh the re',
		                                  'Hex'       => 'G67'),
		                            array('NON_LANG'  => 'fsajk',
		                                  'Trans_ID'  => 'H21',
		                                  'DIFF_LANG' => '182',
		                                  'Hex'       => 'H15'));
		$firstRecordObject = new Translations($request,
		                                      $multipleRecordData,
		                                      array('NON_LANG', 'Trans_ID'));

		$tests['First_Multiple'] =
			array('Object'   => $secondRecordObject,
			      'Expected' => array('Gibberish', 'Hex'));

		$secondRecordObject = new Translations($request,
		                                       $multipleRecordData,
		                                       array('NON_LANG', 'Trans_ID'));
		$secondRecordObject->next();

		$tests['Second_Multiple'] =
			array('Object'   => $secondRecordObject,
			      'Expected' => array('Gibberish', 'Hex'));
			
		return $tests;	
	}

	public function providerHasLanguage()
	{
		$tests = array();
		$request = $this->getMock('Evoke\HTTP\RequestIface');
		$emptyObject = new Translations($request);
		
		$tests['Empty_Any'] =
			array('Object'   => $emptyObject,
			      'Language' => 'Any',
			      'Expected' => false);
		
		$tests['Empty_Empty'] =
			array('Object'   => $emptyObject,
			      'Language' => '',
			      'Expected' => false);
		
		return $tests;
	}
		
	/** 
	 * Provide the data for testing the language priority.  The order is:
	 *
	 * 1. The language that we have set manually.
	 * 2. URI Query parameter e.g ?l=EN
	 * 3. HTTP Request AcceptLanguage header.
	 * 4. The Default Language the Translator was constructed with.
	 * 5. The order of the languages as they appear in the first translation.
	 */
	public function providerLanguagePriority()
	{
		$tests = array();

		/*******************************************/
		/* Testing from least priority to highest. */
		/*******************************************/
		
		// Priority 5 - Order in first translation.
		$priority5Request = $this->getMock('Evoke\HTTP\RequestIface');
		$priority5Request
			->expects($this->at(0))
			->method('issetQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue(false));
		$priority5Request
			->expects($this->at(1))
			->method('parseAcceptLanguage')
			->will($this->returnValue(array()));
		
		$tests['Priority_5'] = array(
			'Object'   => new Translations(
				$priority5Request,
				array(array('Name'      => 'First_Translation',
				            'Page'      => '',
				            'Aardvark'  => 'Aar Aar Aar',
				            'Icelandic' => '#I@OU$(FDHOIVOIJ'),
				      array('Name'      => 'Ignored_Translation',
				            'Page'      => '',
				            'Ignore_Me' => 'OK'))),
			'Expected' => 'Aardvark');

		// Priority 4 - Default language from construction.
		$priority4Request = $this->getMock('Evoke\HTTP\RequestIface');
		$priority4Request
			->expects($this->at(0))
			->method('issetQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue(false));
		$priority4Request
			->expects($this->at(1))
			->method('parseAcceptLanguage')
			->will($this->returnValue(array()));
		
		$tests['Priority_4'] = array(
			'Object'   => new Translations(
				$priority4Request,
				array(array('Name' => 'First',
				            'Page' => '',
				            'FR'   => 'French is First',
				            'EN'   => 'English is Default',
				            'DE'   => 'German is Third'))),
			'Expected' => 'EN');
				
		// Priority 3 - Accept Language from Request.
		$priority3Request = $this->getMock('Evoke\HTTP\RequestIface');
		$priority3Request
			->expects($this->at(0))
			->method('issetQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue(false));
		$priority3Request
			->expects($this->at(1))
			->method('parseAcceptLanguage')
			->will($this->returnValue(array(array('Language' => 'SW'))));

		$tests['Priority_3'] = array(
			'Object'   => new Translations(
				$priority3Request,
				array(array('Name' => 'First',
				            'Page' => '',
				            'FR'   => 'French is First',
				            'SW'   => 'Swedish is Second',
				            'EN'   => 'English is Default',
				            'DE'   => 'German is Third'))),
			'Expected' => 'SW');

		// Priority 2 - URI Query parameter.
		$priority2Request = $this->getMock('Evoke\HTTP\RequestIface');
		$priority2Request
			->expects($this->at(0))
			->method('issetQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue(true));
		$priority2Request
			->expects($this->at(1))
			->method('getQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue('NO'));
		
		$tests['Priority_2'] = array(
			'Object'   => new Translations(
				$priority2Request,
				array(array('Name' => 'First',
				            'Page' => '',
				            'FR'   => 'French is First',
				            'SW'   => 'Swedish is Second',
				            'NO'   => 'Norweigan',
				            'EN'   => 'English is Default',
				            'DE'   => 'German is Third'))),
			'Expected' => 'NO');

		// Priority 1 - Set Manually cannot be tested here (with coverage).

		// Priorities 2-4 outside range, have to use priority 5.
		$outsideRangeRequest = $this->getMock('Evoke\HTTP\RequestIface');
		$outsideRangeRequest
			->expects($this->at(0))
			->method('issetQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue(true));
		$outsideRangeRequest
			->expects($this->at(1))
			->method('getQueryParam')
			->with($this->equalTo('l'))
			->will($this->returnValue('ZZ'));
		$outsideRangeRequest
			->expects($this->at(2))
			->method('parseAcceptLanguage')
			->will($this->returnValue(array(array('Language' => 'YY'))));
		
		$tests['Other_Priority_Languages_Outside_Range'] = array(
			'Object'   => new Translations(
				$outsideRangeRequest,
				array(array('Name' => 'First',
				            'Page' => '',
				            'FR'   => 'French is First',
				            'SW'   => 'Swedish is Second',
				            'DE'   => 'German is Third'))),
			'Expected' => 'FR');
		
		return $tests;
	}
}
// EOF
