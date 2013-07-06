<?php
namespace Evoke_Test\Network\HTTP;

use Evoke\Network\HTTP\Response,
	PHPUnit_Framework_TestCase;

class ResponseNormalProcessTest extends PHPUnit_Framework_TestCase
{
	/**
	 * We can't send if the headers have already been sent.
	 *
	 * @covers            Evoke\Network\HTTP\Response::send
	 * @expectedException LogicException
	 */
	public function testCantSendAfterHeadersSent()
	{
		$object = new Response;
		$object->setStatus(200);

		// The PHPUnit output has already sent output, so this shoudl throw.
		$object->send();
	}
}
// EOF