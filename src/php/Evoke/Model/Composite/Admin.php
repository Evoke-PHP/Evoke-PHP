<?php
namespace Evoke\Model\Composite;

use Evoke\Model\DataIface,
	Evoke\Model\MapperIface;

/**
 * Admin
 *
 * An administrative model using mappers from the model layer to acheive the
 * processing required for typical administration.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Admin
{
	/**
	 * Current Data
	 * @var Evoke\Model\DataIface
	 */
	protected $currentData;

	/**
	 * Mapper
	 * @var Evoke\Model\MapperIface
	 */
	protected $mapper;

	/**
	 * Construct an Admin Model.
	 *
	 * @param Evoke\Model\DataIface   CurrentData.
	 * @param Evoke\Model\MapperIface Mapper.
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

	/**
	 * Create an entry.
	 *
	 * @param mixed[] The entry to create.
	 */
	public function create(Array $requestData)
	{
		var_export($requestData);
	}

	/**
	 * Delete an entry.
	 *
	 * @param mixed[] The entry to delete.
	 */
	public function delete(Array $requestData)
	{
		var_export($requestData);
	}

	/**
	 * Get the state of the administration model.
	 *
	 * @return mixed[] The state of the administrative model.
	 */
	public function getState()
	{
		return array('Failures'      => 'NONE',
		             'Notifications' => 'ALL');
	}

	/**
	 * Update an entry.
	 *
	 * @param mixed[] The new entry value.
	 */
	public function update(Array $requestData)
	{
		var_export($requestData);
	}
}
// EOF
