<?php
namespace Evoke\Response;

abstract class Base
{
	/** @property $Factory
	 *  Factory \object
	 */
	protected $Factory;

	/** @property $InstanceManager
	 *  InstanceManager \object
	 */
	protected $InstanceManager;

	/** @property $Request
	 *  Request \object
	 */
	protected $Request;
	
	public function __construct(Array $setup)
	{
		$setup += array('Factory'         => NULL,
		                'InstanceManager' => NULL,
		                'Request'         => NULL);

		if (!$setup['Factory'] instanceof \Evoke\Core\Factory)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Factory');
		}
      
		if (!$setup['InstanceManager'] instanceof
		    \Evoke\Core\Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}

		if (!$setup['Request'] instanceof \Evoke\Core\Iface\URI\Request)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Request');
		}


		$this->Factory         = $setup['Factory'];
		$this->InstanceManager = $setup['InstanceManager'];
		$this->Request         = $setup['Request'];
	}
   
	/******************/
	/* Public Methods */
	/******************/
	
	abstract public function execute();

	/** Set the headers to show that the document should be cached. This must
	 *  come before any output is set in the document (otherwise the headers will
	 *  have already been sent).
	 *
	 *  @param days    \int The number of days to cache the document for.
	 *  @param hours   \int The number of hours to cache the document for.
	 *  @param minutes \int The number of minutes to cache the document for.
	 *  @param seconds \int The number of seconds to cache the document for.
	 */
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0)
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

	/** Set the content type for the document.
	 */
	public function setContentType($contentType)
	{
		if (headers_sent())
		{
			throw new \RuntimeException(
				__METHOD__ . ' headers have already been sent.');
		}

		header('Content-Type: ' . $contentType);
	}
}
// EOF
