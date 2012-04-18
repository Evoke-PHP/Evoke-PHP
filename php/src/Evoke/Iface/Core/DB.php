<?php
namespace Evoke\Iface\Core;

/** The interface for a DB is virtually the same as the interface to PDO as it
 *  is very good for interaction with many different databases.
 */
interface DB
{
	public function beginTransaction();
	public function commit();
	public function errorCode();
	public function errorInfo();
	public function exec($statement);
	public function getAttribute($attribute);

	/** This would be forced to be static by PDO's implementation (u suck PDO)
	 *  so we don't define this as part of the interface.
	 *  - public static function getAvailableDrivers();
	 */

	public function inTransaction();
	public function lastInsertId($name=NULL);
	public function prepare($statement, $driverOptions=array());

	// The signature for the query function cannot be matched due to the nature
	// of its implementation in the PHP engine. We omit it from the required
	// interface but assume that it is provided.
	//public function query($statement);
   
	public function quote($string, $parameterType=\PDO::PARAM_STR);
	public function rollBack();
	public function setAttribute($attribute, $value);
}
// EOF