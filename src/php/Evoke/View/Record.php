<?php
namespace Evoke\View;

class Record implements ViewIface
{
	/**
	 * Fields
	 * @var string[]
	 */
	protected $fields;

	/**
	 * Construct a Record object.
	 *
	 * @param string[] Fields.
	 */
	public function __construct(Array $fields)
	{
		$this->fields = $fields;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get a view of the record.
	 */
	public function get()
	{
		$recordElems = array();
		$params = array_merge(array('Data'     => array(),
		                            'Row'      => 0,
		                            'Selected' => false),
		                      $params);

		foreach ($this->fields as $field)
		{
			$fieldValue = isset($params['Data'][$field]) ?
				$params['Data'][$field] : '';
			
			$recordElems[] = array('div',
			                       array('class' => 'Field ' . $field),
			                       $fieldValue);
		}

		$oddEven = $params['Row'] % 2 ? 'Odd' : 'Even';
		
		return array(
			'div',
			array('class' => 'Record ' . $oddEven),
			$recordElems);
	}
}
// EOF
