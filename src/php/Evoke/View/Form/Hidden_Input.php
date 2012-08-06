<?php
namespace Evoke\View\Form;

/**
 * Hidden Input
 *
 * @todo Check whether this class is obsolete.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class HiddenInput extends Form
{
	protected $record;
	protected $primaryKeys;
   
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		$setup += array('App'            => NULL,
		                'Data'           => NULL,
		                'Encasing'       => false,
		                'Ignored_Fields' => array(),
		                'Primary_Keys'   => NULL,
		                'Submit_Buttons' => NULL,
		                'Translator'     => NULL);
      
		$app->needs(
			array('Instance' => array('Translator' => $translator),
			      'Set'      => array(
				      'Data'           => $data,
				      'Primary_Keys'   => $primaryKeys,
				      'Submit_Buttons' => $submitButtons)));
      
		$this->record = $data;
		$this->primaryKeys = $primaryKeys;
		parent::__construct($setup);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/// Build the non button elements of the form (the hidden inputs).
	protected function buildFormElements()
	{
		foreach ($this->primaryKeys as $key)
		{
			if (!in_array($key, $this->ignoredFields))
			{
				$this->addElement(
					array('input',
					      array('type'  => 'hidden',
					            'name'  => $key,
					            'value' => $this->record[$key])));
			}
		}
	}
}
// EOF