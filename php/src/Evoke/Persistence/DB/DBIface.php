<?php
namespace Evoke\Persistence\DB;

/**
 * DBIface
 *
 * The interface for a DB is virtually the same as the interface to PDO as it
 * is very good for interaction with many different databases.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
interface DBIface
{
	/**
	 * Begin a transaction in the database.
	 *
	 * @throw Evoke\Message\Exception\DB If we are already in a transaction.
	 */
	public function beginTransaction();

	/**
	 * Commit the current transaction to the database.
	 */
	public function commit();
	
	/**
	 * Get the error code from the database.
	 */
	public function errorCode();

	/**
	 * Get the error information associated with the last DB operation.
	 */
	public function errorInfo();
	
	/**
	 * Execute an SQL statement.
	 *
	 * @return int Number of rows affected.
	 */
	public function exec($statement);

	/**
	 * Get a database attribute.
	 *
	 * @return mixed The attribute.
	 */
	public function getAttribute($attribute);

	/* This would be forced to be static by PDO's implementation
	 * so we don't define this as part of the interface. 
	 */
	// public static function getAvailableDrivers();

	/**
	 * Whether we are in a transaction.
	 *
	 * @return bool Whether we are in a transaction.
	 */
	public function inTransaction();
	
	/**
	 * Get the ID of the last inserted row or sequence value.
	 *
	 * @param string|null The name of the sequence object.
	 *
	 * @return string
	 */
	public function lastInsertId($name=NULL);

	/**
	 * Prepares a statement for execution and returns a statement object.
	 *
	 * @return mixed Return the PDO statement object.
	 */
	public function prepare($statement, $driverOptions=array());

	/* The signature for the query function cannot be matched due to the nature
	 * of its implementation in the PHP engine. We omit it from the required
	 * interface but assume that it is provided.
	 */
	//public function query($statement);
   
	/**
	 * Quotes the input string (if required) and escapes special characters.
	 *
	 * @param string The string to quote.
	 * @param int    The type to quote it as.
	 */
	public function quote($string, $parameterType=\PDO::PARAM_STR);

	/**
	 * Rolls back the current transaction avoiding any change to the database.
	 */
	public function rollBack();

	/**
	 * Set an attribute on the database
	 *
	 * @param int   The attribute to set.
	 * @param mixed The value to set it to.
	 */
	public function setAttribute($attribute, $value);
}
// EOF