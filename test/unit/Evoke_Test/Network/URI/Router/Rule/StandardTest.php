<?php
/**
 * StandardTest
 *
 * @package   Evoke_Test\Network\URI\Router\Rule
 */
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Standard;

/**
 * @covers Evoke\Network\URI\Router\Rule\Standard
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetParams()
    {
        return [
            'Bad_URL'    => [
                'Expected' => [],
                'Path'     => '/Product',
                'URI'      => '/Product?Bad=1:80'
            ],
            'No_Params'  => [
                'Expected' => [],
                'Path'     => '/Product',
                'URI'      => '/Product'
            ],
            'One_Param'  => [
                'Expected' => ['Ball' => '6'],
                'Path'     => '/Product',
                'URI'      => '/Product?Ball=6'
            ],
            'Two_Param'  => [
                'Expected' => ['Ball' => '6', 'Colour' => 'Red'],
                'Path'     => '/Product',
                'URI'      => '/Product?Ball=6&Colour=Red'
            ],
            'Url_Decode' => [
                'Expected' => ['Spaced' => 'One Two', 'Plussed' => 'One+One'],
                'Path'     => '/Product',
                'URI'      => '/Product?Spaced=One+Two&Plussed=One%2BOne'
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'Empty_Match' => [
                'Expected' => true,
                'Path'     => '/Product',
                'URI'      => '/Product'
            ],
            'No_Match'    => [
                'Expected' => false,
                'Path'     => '/Product',
                'URI'      => '/'
            ],
            'Param_Match' => [
                'Expected' => true,
                'Path'     => '/Product',
                'URI'      => '/Product?Yes=1'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Standard('controller', 'path', true);
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Standard', $obj);
    }

    public function testGetController()
    {
        $obj = new Standard('controller', 'match', true);
        $obj->setURI('match');
        $this->assertSame('controller', $obj->getController());
    }

    /**
     * @dataProvider providerGetParams
     */
    public function testGetParams($expected, $path, $uri)
    {
        $obj = new Standard('controller', $path, true);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getParams());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($expected, $path, $uri)
    {
        $obj = new Standard('controller', $path, true);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch($uri));
    }
}
