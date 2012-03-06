<?php
namespace Evoke\View;

use \Evoke\Core\Iface;

abstract class Base implements Iface\View
{
	/** @property $Translator
	 *  Translator \object
	 */
	protected $Translator;

	/** @property $Writer
	 *  Writer \object
	 */
	protected $Writer;

	public function __construct(Array $setup)
	{
		$setup += array('Translator' => NULL,
		                'Writer'     => NULL);

		if (!$setup['Translator'] instanceof Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		if (!$setup['Writer'] instanceof Iface\Writer)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Writer');
		}
						  
		$this->Translator = $setup['Translator'];
		$this->Writer     = $setup['Writer'];
	}
}
// EOF