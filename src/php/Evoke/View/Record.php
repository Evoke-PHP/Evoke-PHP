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
	public function get(Array $params = array())
	{
		$recordElems = array();
		$params += array('Data'     => array(),
		                 'Row'      => 0,
		                 'Selected' => false);

		foreach ($this->fields as $field)
		{
			$fieldValue = isset($params['Data'][$field]) ?
				$params['Data'][$field] : '';
			
			$recordElems[] = array('div',
			                       array('class' => 'Field ' . $field),
			                       $fieldValue);
		}

		return array('div',
		             array('class' => 'Record'),
		             $recordElems);
	}
}
// EOF
