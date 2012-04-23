<?php
namespace Evoke\Element\Form\Input;

class RequiredField extends \Evoke\Element\Base
{
	/** @property $translator
	 *  Translator \object
	 */
	protected $translator;

	public function __construct(Array $setup)
	{
		$setup += array('Translator' => NULL);

		if (!$translator instanceof \Evoke\Core\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}

		parent::__construct($setup);

		$this->translator = $translator;
	}

	public function set(Array $data)
	{
		return parent::set(
			array('div',
			      array('class' => 'Required_Field_Instructions'),
			      array(array('span',
			                  array('class' => 'Required_Field_Instructions_Text'),
			                  $this->translator->get(
				                  'Required_Field_Instructions')),
			            array('span',
			                  array('class' => 'Required'),
			                  '*'))));
			}
	}
	// EOF