<?php
namespace Evoke_Test\Model\Persistence;

use Evoke\Model\Persistence\Session,
    PHPUnit_Framework_TestCase;

class SessionTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/**
	 * Provider for deleteAtOffset.
	 */
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
					'Dos' => 'Tres'],
				'Delete_Offset' => ['One', 'Two'],
				'Domain'        => ['Any', 'Subdomain', 'Youlike'],
				'Expected'      => [
					'Any' => [
						'Subdomain' => [
							'Youlike' => [
								'One' => ['Two' => []],
								'Dos' => 'Tres']]]]
				]
			];
	}
	
	/**
	 * Provider for a domain.
	 */
	public function providerDomain()
	{
		$fiftyLevels = [];

		for ($level = 0; $level < 50; $level++)
		{
			$fiftyLevels[] = 'L' . $level;
		}
		
		return [
			'Empty'       => ['Domain' => []],
			'One_Level'   => ['Domain' => ['L1']],
			'Two_Level'   => ['Domain' => ['This is level one', 'Now Two']],
			'Five_Level'  => ['Domain' => ['L1', 'L2', 'L3', 'L4', 'L5']],
			'Fifty_Level' => ['Domain' => $fiftyLevels]];
	}
	
	/**
	 * Provider for a domain and value.
	 */
	public function providerDomainValue()
	{
		return [
			'Domain_Empty_Value_NULL'     => [
				'Domain' => [],
				'Value'  => NULL],
			'Domain_Empty_Value_Array'    => [
				'Domain' => [],
				'Value'  => ['Arr_Val']],
			'Domain_Empty_Value_String'   => [
				'Domain' => [],
				'Value'  => 'Str_Val'],
			'Domain_Leveled_Value_String' => [
				'Domain' => ['One', 'Two'],
				'Value'  => 'Str_Val'],
			'Domain_Leveled_Value_Array'  => [
				'Domain' => ['One', 'Two'],
				'Value'  => 'Str_Val']
			];
			
	}

	/**
	 * Provider for getAtOffset.
	 */
	public function providerGetAtOffset()
	{
		return [
			'All_Empty' => [
				'Data'     => [],
				'Domain'   => [],
				'Expected' => [],
				'Offset'   => []
				],
			'Partial'   => [
				'Data'     => ['One' => ['Two' => ['Tres' => 'Cuatro']]],
				'Domain'   => ['No', 'Care'],
				'Expected' => ['Tres' => 'Cuatro'],
				'Offset'   => ['One', 'Two']
				],
			'Non_Exist' => [
				'Data'     => ['One' => ['Two' => ['Tres' => []]]],
				'Domain'   => ['A'],
				'Expected' => NULL,
				'Offset'   => ['One', 'Fifty']
				]
			];		
	}

	public function providerIsEqual()
	{
		// $session, $domain, $key, $value, $equality
		return [
			'Empty' => [
				'Session'  => [],
				'Domain'   => [],
				'Key'      => '',
				'Value'    => '',
				'Equality' => FALSE],
			'Root' =>  [
				'Session'  => ['K' => 'Val'],
				'Domain'   => [],
				'Key'      => 'K',
				'Value'    => 'Val',
				'Equality' => TRUE]
			];
	}
	
	/***********/
	/* Fixture */
	/***********/

	/**
	 * Check that the runkit extension is available.
	 *
	 * @return bool Whether the runkit extension is available.
	 */
	protected function hasRunkit()
	{
		return function_exists('runkit_function_rename') &&
			function_exists('runkit_function_add');
	}
	
	/**
	 * Install session function using runkit.
	 */
	protected function installRunkit($functionName, $code)
	{
		runkit_function_rename($functionName, 'TEST_SAVED_' . $functionName);
		runkit_function_add($functionName, '', $code);
	}

	/**
	 * Restore session function using runkit.
	 */
	protected function restoreRunkit($functionName)
	{		
		runkit_function_remove($functionName);
		runkit_function_rename('TEST_SAVED_' . $functionName, $functionName);
	}
	
	/**
	 * Test tear down.
	 */
	public function tearDown()
	{
		unset($_SESSION);
	}

	/*********/
	/* Tests */
	/*********/
	
    /**
     * Ensure that before a session is created that $_SESSION does
     * not exist.
     *
     * @covers Evoke\Model\Persistence\Session::__construct
     */   
    public function test__constructGood()
    {
	    
	    $this->assertTrue(
		    !isset($_SESSION),
		    'Ensure we are starting from a clean state.');

	    $object = new Session;

	    $this->assertTrue(isset($_SESSION), 'Session is created.');
	    $this->assertInstanceOf('Evoke\Model\Persistence\Session', $object);
    }

    /**
     * Values can be added to the session.
     *
     * @covers Evoke\Model\Persistence\Session::addValue
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
     * @covers Evoke\Model\Persistence\Session::deleteAtOffset
     * @covers Evoke\Model\Persistence\Session::setData
     * @dataProvider providerDeleteAtOffset
     */
    public function testDeleteAtOffset(Array $data, Array $deleteOffset,
                                       Array $domain, Array $expected)
    {
	    $object = new Session($domain);
	    $object->setData($data);
	    $object->deleteAtOffset($deleteOffset);
	    
	    $this->assertSame($expected, $_SESSION);
    }
    
    /**
     * Ensure that a session domain is created for an empty session.
     *
     * @covers Evoke\Model\Persistence\Session::ensure
     */
    public function testEnsureDomainCreatedFromEmpty()
    {
	    $domain = ['L1', 'L2', 'L3'];

	    $object = new Session($domain);
	    $this->assertSame(['L1' => ['L2' => ['L3' => []]]],
	                        $_SESSION,
	                        'Ensure domain created from empty.');
    }

    /**
     * Ensure that a session can be augmented with a domain.
     *
     * @covers Evoke\Model\Persistence\Session::ensure
     */
    public function testEnsureSessionAugmentedWithDomain()
    {
	    $domain = ['L1', 'L2', 'L3'];
	    $_SESSION = ['A1' => ['A2' => 'A2 Val']];
	    
	    $object = new Session($domain);
	    $this->assertSame(
		    ['A1' => ['A2' => 'A2 Val'],
		     'L1' => ['L2' => ['L3' => []]]],
		    $_SESSION,
		    'Ensure Session augmented with domain.');
    }

    /**
     * @covers                   Evoke\Model\Persistence\Session::ensure
     * @expectedException        RuntimeException
     * @expectedExceptionMessage session_start failed
     */
    public function testEnsureFailedSessionStart()
    {
	    if (!$this->hasRunkit())
	    {
		    $this->markTestIncomplete(
			    'PHP runkit extension is required for this test.');
		    return;
	    }

	    $this->installRunkit('php_sapi_name', 'return "TEST_VALUE";');
	    $this->installRunkit('headers_sent',  'return false;');
	    $this->installRunkit('session_start', 'return false;');
	    
	    try
	    {
		    $domain = ['L1', 'L2', 'L3'];
		    $object = new Session($domain, false);
	    }
	    catch (\Exception $e)
	    {
		    $this->restoreRunkit('php_sapi_name');
		    $this->restoreRunkit('headers_sent');
		    $this->restoreRunkit('session_start');
		    throw $e;
	    }
    }

    /**
     * @covers Evoke\Model\Persistence\Session::ensure
     */
    public function testEnsureGoodSessionStart()
    {
	    if (!$this->hasRunkit())
	    {
		    $this->markTestIncomplete(
			    'PHP runkit extension is required for this test.');
		    return;
	    }

	    $this->installRunkit('php_sapi_name', 'return "TEST_VALUE";');
	    $this->installRunkit('headers_sent',  'return false;');
	    $this->installRunkit('session_start', '$_SESSION = []; return true;');

	    $domain = ['L1', 'L2', 'L3'];
	    $object = new Session($domain, false);
	    $this->assertSame(['L1' => ['L2' => ['L3' => []]]],
	                      $_SESSION);

	    $this->restoreRunkit('php_sapi_name');
	    $this->restoreRunkit('headers_sent');
	    $this->restoreRunkit('session_start');
    }

    /**
     * @covers                   Evoke\Model\Persistence\Session::ensure
     * @expectedException        RuntimeException
     * @expectedExceptionMessage session started after headers sent.
     */
    public function testEnsureHeadersAlreadySentException()
    {
	    if (!$this->hasRunkit())
	    {
		    $this->markTestIncomplete(
			    'PHP runkit extension is required for this test.');
		    return;
	    }

	    $this->installRunkit('php_sapi_name', 'return "NON_CLI";');
	    $this->installRunkit('headers_sent',  'return true;');
	    
	    try
	    {
		    $domain = ['L1', 'L2', 'L3'];
		    $object = new Session($domain, false);
	    }
	    catch (\Exception $e)
	    {
		    $this->restoreRunkit('php_sapi_name');
		    $this->restoreRunkit('headers_sent');
		    throw $e;
	    }
    }
    
    /**
     * Ensure that data can be retrieved at an offset.
     *
     * @covers       Evoke\Model\Persistence\Session::getAtOffset
     * @covers       Evoke\Model\Persistence\Session::setData
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
     * @covers       Evoke\Model\Persistence\Session::getCopy
     * @covers       Evoke\Model\Persistence\Session::setData
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
     *
     * @covers Evoke\Model\Persistence\Session::getFlatDomain
     */
    public function testGetFlatDomain()
    {
	    $domain = ['Lev_1', 'Lev_2', 'Lev_3', 'Lev_4'];
	    $object = new Session($domain);

	    $this->assertSame($domain, $object->getFlatDomain());
    }

    /**
     * Get the session ID.
     *
     * @covers Evoke\Model\Persistence\Session::getID
     */
    public function testGetID()
    {
	    $object = new Session;
	    $this->assertSame('CLI_SESSION', $object->getID());
    }

    /**
     * Values in the session domain can be incremented.
     *
     * @covers Evoke\Model\Persistence\Session::getAccess
     * @covers Evoke\Model\Persistence\Session::increment
     */
    public function testIncrement()
    {
	    $object = new Session(['A', 'B']);
	    $object->setData(['C' => 1]);
	    
	    $object->increment('C');
	    $this->assertSame(2, $_SESSION['A']['B']['C']);

	    $object->increment('C', 3);
	    $this->assertSame(5, $_SESSION['A']['B']['C']);

	    $object->increment('C', -1);
	    $this->assertSame(4, $_SESSION['A']['B']['C']);
    }

    /**
     * We can determine if the session domain is empty.
     *
     * @covers Evoke\Model\Persistence\Session::isEmpty
     */
    public function testIsEmpty()
    {
	    $object = new Session(['A', 'B']);

	    $this->assertTrue($object->isEmpty(), 'Domain should be empty.');

	    $object->addValue(5);
	    $this->assertFalse($object->isEmpty(), 'Domain should not be empty.');
    }

    /**
     * We can determine if a key is equal to a value.
     *
     * @covers       Evoke\Model\Persistence\Session::isEqual
     * @dataProvider providerIsEqual
     */
    public function  testIsEqual($session, $domain, $key, $value, $equality)
    {
	    $_SESSION =  $session;
	    $object = new Session($domain);
	    $this->assertSame($equality, $object->isEqual($key, $value));
    }

    /**
     * We can determine if a key is set.
     *
     * @covers Evoke\Model\Persistence\Session::issetKey
     */
    public function testIssetKey()
    {
	    $_SESSION = ['a' => ['b' => 'c', 'd' => 'e']];
	    $object = new Session(['a']);
	    $this->assertTrue($object->issetKey('b'));
	    $this->assertFalse($object->issetKey('e'));
    }

    /**
     * Can count the number of keys in the session.
     *
     * @covers Evoke\Model\Persistence\Session::keyCount
     */
    public function testKeyCount()
    {
	    $_SESSION = ['d' => ['a', 'b', 'c', 'd', 'e']];
	    $object = new Session(['d']);
	    $this->assertSame(5, $object->keyCount());
    }
    
    /**
     * Ensure that keys can be set and retrieved.
     *
     * @covers Evoke\Model\Persistence\Session::get
     * @covers Evoke\Model\Persistence\Session::set
     */
    public function testKeyGetSet()
    {
	    $object = new Session;
	    $object->set('Test_Key', 'Test_Value');
	    
	    $this->assertSame('Test_Value', $object->get('Test_Key'));
    }

    /**
     * Check that a session can be removed, but leaves the hierarchy above the
     * session domain intact.
     *
     * @covers       Evoke\Model\Persistence\Session::getCopy
     * @covers       Evoke\Model\Persistence\Session::remove
     * @dataProvider providerDomain
     */
    public function testRemove(Array $domain)
    {
	    $object = new Session($domain);

	    // Build the expected value.
	    $expectedFlat = $domain;
	    unset($expectedFlat[count($domain) - 1]);
	    $expected = [];
	    $expectedPtr =& $expected;
	    
	    foreach ($expectedFlat as $subdomain)
	    {
		    $expectedPtr[$subdomain] = [];
		    $expectedPtr =& $expectedPtr[$subdomain];
	    }
	    	    
	    $object->remove();
	    $this->assertSame($expected, $_SESSION);
    }

    /**
     * The session can be reset.
     *
     * @covers Evoke\Model\Persistence\Session::reset
     */
    public function testReset()
    {
	    $_SESSION = ['a' => ['b' => 'c']];
	    $object = new Session(['a']);
	    $object->reset();

	    $this->assertSame(['a' => []], $_SESSION);
    }

    /**
     * A specific key can be unset.
     *
     * @covers Evoke\Model\Persistence\Session::unsetKey
     */
    public function testUnsetKey()
    {
	    $_SESSION = ['a' => ['b' => 'c', 'd' => 'e', 'f' => 'g']];
	    $object = new Session(['a']);
	    $object->unsetKey('d');
	    
	    $this->assertSame(['a' => ['b' => 'c', 'f' => 'g']], $_SESSION);
    }
}
// EOF