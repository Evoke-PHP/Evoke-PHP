<?php
namespace Evoke_Test\Model\Mapper;

use Evoke\Model\Mapper\Session,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Mapper\Session
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerRead()
	{
		return [
			'Empty_No_Offset' =>
			['Session_Data' => [],
			 'Offset'       => [],
			 'Read_Data'    => []],
			'Empty_Offset'    =>
			['Session_Data' => ['Offset' => []],
			 'Offset'       => ['Offset'],
			 'Read_Data'    => []],
			'Full'            =>
			['Session_Data' => ['One', 1, ['Three' => '1 + 1 + 1']],
			 'Offset'       => [],
			 'Read_Data'    => ['One', 1, ['Three' => '1 + 1 + 1']]],
			'From_Offset'     =>
			['Session_Data' => ['One', 'Two' => [1 => 'A', 2 => 'B'], 'Three'],
			 'Offset'       => ['Two'],
			 'Read_Data'    => [1 => 'A', 2 => 'B']],
			'Deep_Offset'     =>
			['Session_Data' => ['A' => [2 => ['C' => [0, 1, 2, 3]]]],
			 'Offset'       => ['A', 2, 'C'],
			 'Read_Data'    => [0, 1, 2, 3]],
			'Unset_Offset'    =>
			['Session_Data' => ['A' => 1],
			 'Offset'       => ['B'],
			 'Read_Data'    => NULL]];
	}

	public function providerUpdateSuccess()
	{
		return ['Simple'  =>
		        ['Old' => ['A', 2],
		         'New' => ['Now', 3]],
		        'Complex' =>
		        ['Old' => ['A' => ['B' => ['C' => 123]]],
		         'New' => ['B' => ['A' => ['C' => '456']]]]];		        
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @uses Evoke\Model\Persistence\SessionIface
	 */
	public function testConstruct()
	{
		$obj = new Session($this->getMock(
			                   'Evoke\Model\Persistence\SessionIface'));
		$this->assertInstanceOf('Evoke\Model\Mapper\Session', $obj);
	}

	public function testCreate()
	{
		$data = ['Any' => 'old', 2 => 'Data', []];
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('setData')
			->with($data);
		
		$obj = new Session($mockSession);
		$obj->create($data);
	}

	public function testCreateNoParamGiven()
	{
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('setData')
			->with([]);
		
		$obj = new Session($mockSession);
		$obj->create();
	}

	public function testDelete()
	{
		$offset = ['Any', 'Offset'];
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('deleteAtOffset')
			->with($offset);
		
		$obj = new Session($mockSession);
		$obj->delete($offset);
	}

	public function testDeleteNoParamGiven()
	{
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('deleteAtOffset')
			->with([]);
		
		$obj = new Session($mockSession);
		$obj->delete();
	}
	
	/**
	 * @dataProvider providerRead
	 */
	public function testRead($sessionData, $offset, $readData)
	{
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('getCopy')
			->with()
			->will($this->returnValue($sessionData));
		
		$obj = new Session($mockSession);
		$this->assertSame($readData, $obj->read($offset));			       
	}

	public function testReadNoParams()
	{
		$sessionData = [[1], '2' => 3];
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('getCopy')
			->with()
			->will($this->returnValue($sessionData));

		$obj = new Session($mockSession);
		$this->assertSame($sessionData, $obj->read());
	}

	/**
	 * @expectedException        RuntimeException
	 * @expectedExceptionMessage Session update data has already been modified.
	 */
	public function testUpdateFailure()
	{
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->once())
			->method('getCopy')
			->with()
			->will($this->returnValue(['ALREADY_MODIFIED_OLD_VALUE']));

		$obj = new Session($mockSession);
		$obj->update(['OLD_VALUE'], ['NEW_VALUE']);
	}
	
	/**
	 * @dataProvider providerUpdateSuccess
	 */
	public function testUpdateSuccess($old, $new)
	{
		$mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
		$mockSession
			->expects($this->at(0))
			->method('getCopy')
			->with()
			->will($this->returnValue($old));
		$mockSession
			->expects($this->at(1))
			->method('setData')
			->with($new);

		$obj = new Session($mockSession);
		$obj->update($old, $new);			
	}
}
// EOF