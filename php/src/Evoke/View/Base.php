<?php
namespace Evoke\View;

use \Evoke\Core\Iface;

abstract class Base implements Iface\View
{
	protected $Translator;

	public function __construct(Array $setup)
	{
		$setup += array('Translator' => NULL);

		if (!$setup['Translator'] instanceof Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}
						  
		$this->Translator = $setup['Translator'];
	}
}
// EOF