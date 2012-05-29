<?php
namespace Evoke\Model\Composite;

use Evoke\Model\DataIface,
	Evoke\Model\MapperIface;

/** An administrative model using mappers from the model layer to acheive the
 *  processing required for typical administration.
 */
class Admin
{
	/** @property currentData
	 *  @object Current Data
	 */
	protected $currentData;

	/** @property mapper
	 *  @object Mapper
	 */
	protected $mapper;

	/** Construct a Admin object.
	 *  @param currentData @object CurrentData.
	 *  @param mapper      @object Mapper.
	 */
	public function __construct(DataIface   $currentData,
	                            MapperIface $mapper)
	{
		$this->currentData = $currentData;
		$this->mapper      = $mapper;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function create($requestData)
	{
		var_export($requestData);
	}

	public function delete($requestData)
	{
		var_export($requestData);
	}

	public function getState()
	{
		return array('Failures'      => 'NONE',
		             'Notifications' => 'ALL');
	}
	
	public function update($requestData)
	{
		var_export($requestData);
	}
}
// EOF
