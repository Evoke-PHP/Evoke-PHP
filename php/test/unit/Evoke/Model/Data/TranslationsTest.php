<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Translations,
	Evoke\HTTP\RequestIface,
	PHPUnit_Framework_TestCase;

class TranslationsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the construction of a good object.
	 * @covers       Evoke\Model\Data\Translations::__construct
	 * @dataProvider provider__constructGood
	 */
	public function test__constructGood($request, $page, $data = NULL)
	{
		if (isset($data))
		{
			$obj = new Translations($request, $page, $data);
		}
		else
		{
			$obj = new Translations($request, $page);
		}
		
		$this->assertInstanceOf('Evoke\Model\Data\Translations', $obj);
	}

	/******************/
	/* Data Providers */
	/******************/

	public function provider__constructGood()
	{
		$request = $this->getMock('\Evoke\HTTP\RequestIface');
		
		return array(
			'Empty_Data' =>
			array('Request' => $request,
			      'Page'    => 'Page',
			      'Data'    => array(array('Name' => 'Translation_One',
			                               'EN'   => 'TOne',
			                               'ES'   => 'TUno'))),
			'Has_Data' =>
			array('Request' => $request,
			      'Page'    => 'Another page',
			      'Data'    => array(array('Name' => 'Nom',
			                               'EN'   => 'Nom',
			                               'ES'   => 'Nom'))));
	}
}
// EOF
