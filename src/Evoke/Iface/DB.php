<?php
namespace Evoke\Iface;

interface DB
{
   public function beginTransaction();
   public function commit();
   public function errorCode();
   public function errorInfo();
   public function exec($statement);
   public function getAttribute($attribute);

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