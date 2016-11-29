<?php
namespace Evoke_Test\Model\Persistence;

use Evoke\Model\Persistence\Session;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Persistence\Session
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerDeleteAtOffset()
    {
        return [
            'All_Empty'       => [
                'Data'          => [],
                'Delete_Offset' => [],
                'Domain'        => [],
                'Expected'      => []
            ],
            'Already_Deleted' => [
                'Data'          => ['Data'],
                'Delete_Offset' => ['Already_Deleted'],
                'Domain'        => ['Okay'],
                'Expected'      => ['Okay' => ['Data']]
            ],
            'Partial_Delete'  => [
                'Data'          => [
                    'One' => ['Two' => ['Three' => 3]],
                    'Dos' => 'Tres'
                ],
                'Delete_Offset' => ['One', 'Two'],
                'Domain'        => ['Any', 'Subdomain', 'Youlike'],
                'Expected'      => [
                    'Any' => [
                        'Subdomain' => [
                            'Youlike' => [
                                'One' => ['Two' => []],
                                'Dos' => 'Tres'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function providerDomain()
    {
        $fiftyLevels = [];

        for ($level = 0; $level < 50; $level++) {
            $fiftyLevels[] = 'l' . $level;
        }

        return [
            'empty'       => ['domain' => []],
            'one_level'   => ['domain' => ['l1']],
            'two_level'   => ['domain' => ['This is level one', 'Now Two']],
            'five_level'  => ['domain' => ['l1', 'l2', 'l3', 'l4', 'l5']],
            'fifty_level' => ['domain' => $fiftyLevels]
        ];
    }

    public function providerDomainValue()
    {
        return [
            'domain_empty_value_null'     => [
                'domain' => [],
                'value'  => null
            ],
            'domain_empty_value_array'    => [
                'domain' => [],
                'value'  => ['arr_val']
            ],
            'domain_empty_value_string'   => [
                'domain' => [],
                'value'  => 'str_val'
            ],
            'domain_leveled_value_string' => [
                'domain' => ['one', 'two'],
                'value'  => 'str_val'
            ],
            'domain_leveled_value_array'  => [
                'domain' => ['one', 'two'],
                'value'  => 'str_val'
            ]
        ];

    }

    public function providerGetAtOffset()
    {
        return [
            'all_empty' => [
                'data'     => [],
                'domain'   => [],
                'expected' => [],
                'offset'   => []
            ],
            'partial'   => [
                'data'     => ['one' => ['two' => ['three' => 'four']]],
                'domain'   => ['no', 'care'],
                'expected' => ['three' => 'four'],
                'offset'   => ['one', 'two']
            ],
            'non_exist' => [
                'data'     => ['one' => ['two' => ['three' => []]]],
                'domain'   => ['a'],
                'expected' => null,
                'offset'   => ['one', 'fifty']
            ]
        ];
    }

    public function providerIsEqual()
    {
        return [
            'empty' => [
                'session'  => [],
                'domain'   => [],
                'key'      => '',
                'value'    => '',
                'equality' => false
            ],
            'root'  => [
                'session'  => ['k' => 'val'],
                'domain'   => [],
                'key'      => 'k',
                'value'    => 'val',
                'equality' => true
            ]
        ];
    }

    public function providerSetDataAtOffset()
    {
        return [
            'all_empty'       => [
                'domain'       => [],
                'expected'     => [],
                'initial_data' => [],
                'offset'       => [],
                'set_data'     => []
            ],
            'existing_offset' => [
                'domain'       => ['a'],
                'expected'     => ['a' => ['b' => ['e' => 'f']]],
                'initial_data' => ['b' => ['c' => 'd']],
                'offset'       => ['b'],
                'set_data'     => ['e' => 'f']
            ],
            'no_offset'       => [
                'domain'       => ['d'],
                'expected'     => ['d' => ['set_data']],
                'initial_data' => ['initial_data'],
                'offset'       => [],
                'set_data'     => ['set_data']
            ],
            'unset_offset'    => [
                'domain'       => ['a'],
                'expected'     => [
                    'a' =>
                        [
                            'o' => 'other',
                            'b' => ['c' => ['d' => ['e' => 'f']]]
                        ]
                ],
                'initial_data' => ['o' => 'other'],
                'offset'       => ['b', 'c'],
                'set_data'     => ['d' => ['e' => 'f']]
            ],
        ];
    }

    /***********/
    /* Fixture */
    /***********/

    /**
     * Test tear down.
     */
    public function tearDown()
    {
        unset($_SESSION);
    }

    /**
     * Ensure that before a session is created that $_SESSION does
     * not exist.
     */
    public function testConstructGood()
    {

        $this->assertTrue(!isset($_SESSION), 'Ensure we are starting from a clean state.');

        $object = new Session;

        $this->assertTrue(isset($_SESSION), 'Session is created.');
        $this->assertInstanceOf('Evoke\Model\Persistence\Session', $object);
    }

    /**
     * Values can be added to the session.
     */
    public function testAddValue()
    {
        $object = new Session;
        $object->addValue(1);
        $object->addValue(2);
        $object->addValue(77);

        $this->assertSame([1, 2, 77], $_SESSION);
    }

    /**
     * Check that data can be set for a session subdomain and part of it can be
     * deleted.
     *
     * @dataProvider providerDeleteAtOffset
     */
    public function testDeleteAtOffset(Array $data, Array $deleteOffset, Array $domain, Array $expected)
    {
        $object = new Session($domain);
        $object->setData($data);
        $object->deleteAtOffset($deleteOffset);

        $this->assertSame($expected, $_SESSION);
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Ensure that a session domain is created for an empty session.
     */
    public function testEnsureDomainCreatedFromEmpty()
    {
        $domain = ['l1', 'l2', 'l3'];

        $object = new Session($domain);
        $this->assertSame(['l1' => ['l2' => ['l3' => []]]], $_SESSION, 'Ensure domain created from empty.');
    }

    /**
     * Ensure that a session can be augmented with a domain.
     */
    public function testEnsureSessionAugmentedWithDomain()
    {
        $domain   = ['l1', 'l2', 'l3'];
        $_SESSION = ['a1' => ['a2' => 'A2 Val']];

        $object = new Session($domain);
        $this->assertSame(
            [
                'a1' => ['a2' => 'A2 Val'],
                'l1' => ['l2' => ['l3' => []]]
            ],
            $_SESSION,
            'Ensure Session augmented with domain.'
        );
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage session_start failed
     */
    public function testEnsureFailedSessionStart()
    {
        uopz_set_return('php_sapi_name', 'TEST_VALUE');
        uopz_set_return('headers_sent', false);
        uopz_set_return('session_start', false);

        try {
            $domain = ['l1', 'l2', 'l3'];
            $object = new Session($domain, false);
        } catch (\Exception $e) {
            uopz_unset_return('php_sapi_name');
            uopz_unset_return('headers_sent');
            uopz_unset_return('session_start');
            throw $e;
        }
    }

    public function testEnsureGoodSessionStart()
    {
        uopz_set_return('php_sapi_name', 'TEST_VALUE');
        uopz_set_return('headers_sent', false);
        uopz_set_return('session_start', function () { $_SESSION = []; return true; }, true);

        $domain = ['l1', 'l2', 'l3'];
        $object = new Session($domain, false);
        $this->assertSame(['l1' => ['l2' => ['l3' => []]]], $_SESSION);

        uopz_unset_return('php_sapi_name');
        uopz_unset_return('headers_sent');
        uopz_unset_return('session_start');
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage session started after headers sent.
     */
    public function testEnsureHeadersAlreadySentException()
    {
        uopz_set_return('php_sapi_name', 'NON_CLI');
        uopz_set_return('headers_sent', true);

        try {
            $domain = ['l1', 'l2', 'l3'];
            $object = new Session($domain, false);
        } catch (\Exception $e) {
            uopz_unset_return('php_sapi_name');
            uopz_unset_return('headers_sent');
            throw $e;
        }
    }

    /**
     * Ensure that data can be retrieved at an offset.
     *
     * @dataProvider providerGetAtOffset
     */
    public function testGetAtOffset($data, $domain, $expected, $offset)
    {
        $object = new Session($domain);
        $object->setData($data);

        $this->assertSame($expected, $object->getAtOffset($offset));
    }

    /**
     * Ensure that data can be set on a subdomain and can then be retrieved.
     *
     * @dataProvider providerDomainValue
     */
    public function testGetCopySetData(Array $domain, $value)
    {
        $object = new Session($domain);
        $object->setData($value);

        $this->assertSame($value, $object->getCopy());
    }

    /**
     * Check that the correct flat domain is returned.
     */
    public function testGetFlatDomain()
    {
        $domain = ['lev_1', 'lev_2', 'lev_3', 'lev_4'];
        $object = new Session($domain);

        $this->assertSame($domain, $object->getFlatDomain());
    }

    /**
     * Get the session ID.
     */
    public function testGetID()
    {
        $object = new Session;
        $this->assertSame('CLI_SESSION', $object->getID());
    }

    /**
     * Values in the session domain can be incremented.
     */
    public function testIncrement()
    {
        $object = new Session(['a', 'b']);
        $object->setData(['c' => 1]);

        $object->increment('c');
        $this->assertSame(2, $_SESSION['a']['b']['c']);

        $object->increment('c', 3);
        $this->assertSame(5, $_SESSION['a']['b']['c']);

        $object->increment('c', -1);
        $this->assertSame(4, $_SESSION['a']['b']['c']);
    }

    /**
     * We can determine if the session domain is empty.
     */
    public function testIsEmpty()
    {
        $object = new Session(['a', 'b']);

        $this->assertTrue($object->isEmpty(), 'Domain should be empty.');

        $object->addValue(5);
        $this->assertFalse($object->isEmpty(), 'Domain should not be empty.');
    }

    /**
     * We can determine if a key is equal to a value.
     *
     * @dataProvider providerIsEqual
     */
    public function testIsEqual($session, $domain, $key, $value, $equality)
    {
        $_SESSION = $session;
        $object   = new Session($domain);
        $this->assertSame($equality, $object->isEqual($key, $value));
    }

    /**
     * We can determine if a key is set.
     */
    public function testIssetKey()
    {
        $_SESSION = ['a' => ['b' => 'c', 'd' => 'e']];
        $object   = new Session(['a']);
        $this->assertTrue($object->issetKey('b'));
        $this->assertFalse($object->issetKey('e'));
    }

    /**
     * Can count the number of keys in the session.
     */
    public function testKeyCount()
    {
        $_SESSION = ['d' => ['a', 'b', 'c', 'd', 'e']];
        $object   = new Session(['d']);
        $this->assertSame(5, $object->keyCount());
    }

    /**
     * Ensure that keys can be set and retrieved.
     */
    public function testKeyGetSet()
    {
        $object = new Session;
        $object->set('test_key', 'test_value');

        $this->assertSame('test_value', $object->get('test_key'));
    }

    /**
     * Check that a session can be removed, but leaves the hierarchy above the
     * session domain intact.
     *
     * @dataProvider providerDomain
     */
    public function testRemove(Array $domain)
    {
        $object = new Session($domain);

        // Build the expected value.
        $expectedFlat = $domain;
        unset($expectedFlat[count($domain) - 1]);
        $expected    = [];
        $expectedPtr =& $expected;

        foreach ($expectedFlat as $subdomain) {
            $expectedPtr[$subdomain] = [];
            $expectedPtr             =& $expectedPtr[$subdomain];
        }

        $object->remove();
        $this->assertSame($expected, $_SESSION);
    }

    /**
     * The session can be reset.
     */
    public function testReset()
    {
        $_SESSION = ['a' => ['b' => 'c']];
        $object   = new Session(['a']);
        $object->reset();

        $this->assertSame(['a' => []], $_SESSION);
    }

    /**
     * Ensure that data can be retrieved at an offset.
     *
     * @dataProvider providerSetDataAtOffset
     */
    public function testSetDataAtOffset($domain, $expected, $initialData, $offset, $setData)
    {
        $object = new Session($domain);
        $object->setData($initialData);
        $object->setDataAtOffset($setData, $offset);

        $this->assertSame($expected, $_SESSION);
    }

    /**
     * A specific key can be unset.
     */
    public function testUnsetKey()
    {
        $_SESSION = ['a' => ['b' => 'c', 'd' => 'e', 'f' => 'g']];
        $object   = new Session(['a']);
        $object->unsetKey('d');

        $this->assertSame(['a' => ['b' => 'c', 'f' => 'g']], $_SESSION);
    }
}
// EOF
