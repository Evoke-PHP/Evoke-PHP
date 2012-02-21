<?php
namespace Evoke\Response;

abstract class Base
{
	protected $Factory;
	protected $InstanceManager;

	public function __construct(Array $setup)
	{
		$setup += array('Factory'         => NULL,
		                'Instance_Manager' => NULL);

		if (!$setup['Factory'] instanceof \Evoke\Core\Factory)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Factory');
		}
      
		if (!$setup['Instance_Manager'] instanceof
		    \Evoke\Core\Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}

		$this->Factory = $setup['Factory'];
		$this->InstanceManager = $setup['Instance_Manager'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Set the headers to show that the document should be cached. This must
	 *  come before any output is set in the document (otherwise the headers will
	 *  have already been sent).
	 *
	 *  @param days    \int The number of days to cache the document for.
	 *  @param hours   \int The number of hours to cache the document for.
	 *  @param minutes \int The number of minutes to cache the document for.
	 *  @param seconds \int The number of seconds to cache the document for.
	 */
	public function cache($days=0, $hours=0, $minutes=0, $seconds=0)
	{
		if (headers_sent())
		{
			throw new \RuntimeException(
				__METHOD__ . ' headers have already been sent.');
		}
      
		// Calculate the offset in seconds.
		$offset = ((((($days * 24) + $hours) * 60) + $minutes) * 60) + $seconds;

		header('Pragma: public');
		header('Cache-Control: must-revalidate maxage=' . $offset);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
	}

	abstract public function execute();
}
// EOF
