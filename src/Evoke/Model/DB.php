<?php
namespace Evoke;
/// Model_DB provides the basic implementation for a database model.
abstract class Model_DB extends Model
{ 
   protected $sql;

   public function __construct(Array $setup)
   {
      $setup += array('SQL' => NULL);
      parent::__construct($setup);

      if (!$this->setup['SQL'] instanceof DB\SQL)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' requires SQL');
      }
 
      $this->sql =& $this->setup['SQL'];
   }
}
// EOF