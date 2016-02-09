<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Any;
use PHPUnit_Framework_TestCase;

class AnyTest extends PHPUnit_Framework_TestCase
{
    public function providerAlwaysMatches()
    {
        return [
            'Any1' => [['blah']],
            'Any2' => [[]],
            'Any3' => [[1 => 3, 2 => new \StdClass]]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @covers       Evoke\Network\HTTP\MediaType\Rule\Any::__construct
     * @covers       Evoke\Network\HTTP\MediaType\Rule\Any::isMatch
     * @dataProvider providerAlwaysMatches
     */
    public function testAlwaysMatches(Array $mediaType)
    {
        $obj = new Any('DC');
        $this->assertTrue($obj->isMatch());
    }
}
// EOF
