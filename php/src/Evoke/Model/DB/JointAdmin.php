<?php
namespace Evoke\Model\DB;
/// Model_DB_Joint_Admin provides a CRUD interface to a joint set of data.
class JointAdmin extends Joint implements \Evoke\Core\Iface\Model\Admin
{
   protected $failures;
   protected $notifications;
   protected $sessionManager;

   public function __construct(Array $setup)
   {
      $setup += array('AJAX_Data'      => array(),
		      'Failures'       => NULL,
		      'Notifications'  => NULL,
		      'SessionManager' => NULL,
		      'TableListID'    => NULL,
		      'Validate'       => true);

      parent::__construct($setup);

      if (!$this->setup['Failures'] instanceof Evoke\Core\MessageArray)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Failures as Evoke\Core\MessageArray');
      }

      if (!$this->setup['Notifications'] instanceof Evoke\Core\MessageArray)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Notifications as Evoke\Core\MessageArray');
      }
      
      if (!$this->setup['SessionManager'] instanceof \Evoke\Core\SessionManager)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires SessionManager');
      }
      
      if (!$this->setup['TableListID'] instanceof \Evoke\Core\DB\TableListID)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires TableListID');
      }

      $this->failures =& $this->setup['Failures'];
      $this->notifications =& $this->setup['Notifications'];
      $this->sessionManager =& $this->setup['SessionManager'];
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /** Add the joint record to the database.
    *  @param record \array Any new data for the record to be added.
    */
   public function add($record)
   {
      $this->failures->reset();

      $this->updateCurrentRecord($record);
      $record = $this->sessionManager->get('Current_Record');
      $data = array($record);
      
      if ($this->setup['Validate'] &&
	  !$this->validate($data, $this->setup['Table_References']))
      {
	 return false;
      }
            
      ////////////////////
      // DB Transaction //
      ////////////////////
      try
      {
	 $this->sql->beginTransaction();

	 $this->recurse(
	    array('Depth_First_Data'   => array($this, 'addEntries'),
		  'Depth_First_Parent' => array($this, 'feedbackListID')),
	    $data,
	    $this->setup['Table_References']);
	 
	 $this->sql->commit();
	 $this->sessionManager->reset();
 	 $this->notifications->add('Add', 'Successful');
      }
      catch (\Exception $e)
      {
	 $this->em->notify('Log', array('Level'   => LOG_ERR,
					'Method'  => __METHOD__,
					'Message' => $e->getMessage()));
	 $this->failures->add('Add_Failed', 'Sys_Admin_Notified');	 
	 $this->sql->rollBack();
      }
   }

   /// Cancel the editing of the record.
   public function cancel()
   {
      $this->sessionManager->reset();
   }

   /// Begin creating a new record.
   public function createNew()
   {
      $currentRecord = $this->setup['Table_References']->getEmpty();
      $this->sessionManager->set('New_Record', true);
      $this->sessionManager->set('Current_Record', $currentRecord);
      $this->sessionManager->set('Editing_Record', true);
      $this->sessionManager->set('Edited_Record', array());
   }
   
   /// Cancel the currently requested deletion.
   public function deleteCancel()
   {
      $this->sessionManager->reset();
   }

   /// Delete the specified record.
   public function deleteConfirm(Array $record)
   {
      $this->failures->reset();

      ////////////////////
      // DB Transaction //
      ////////////////////
      try
      {
	 $this->sql->beginTransaction();
	 $deleteRecord = $this->sessionManager->get('Delete_Record');
	 
	 $this->recurse(
	    array('Breadth_First_Data' => array($this, 'deleteEntries')),
	    $deleteRecord,
	    $this->setup['Table_References']);
	 
	 $this->sessionManager->reset();
	 $this->sql->commit();
	 $this->notifications->add('Delete', 'Successful');
      }
      catch (\Exception $e)
      {
	 $this->em->notify('Log', array('Level'   => LOG_ERR,
					'Method'  => __METHOD__,
					'Message' => $e->getMessage()));
	 $this->failures->add('Delete_Failed', 'Sys_Admin_Notified');
	 $this->sql->rollBack();
      }
   }

   /** Request that a record should be deleted, but only after confirmation
    *  from the user.
    */
   public function deleteRequest($record)
   {
      $conditions = array();
      
      // The fields to select the record are from the record in the parent.
      foreach ($record as $field => $value)
      {
	 $conditions[$this->setup['Table_Name'] . '.' . $field] = $value;
      }

      $data = $this->getData(array('Conditions' => $conditions));
      
      // Get the offset in the data where the record is found.
      $baseData = $this->getAtPrefix($data, $this->setup['Data_Prefix']);
      $record = $baseData['Records'];
      
      $this->sessionManager->set('Delete_Request', true);
      $this->sessionManager->set('Delete_Record', $record);
   }

   /** Edit a record.
    *  @param record The post data.
    */
   public function edit($record)
   {
      $conditions = array();
      
      // The fields to select the record are from the record in the parent.
      foreach ($record as $field => $value)
      {
	 $conditions[$this->setup['Table_Name'] . '.' . $field] = $value;
      }
      
      $results = parent::getData(array('Conditions' => $conditions));
      $data = $this->getAtPrefix($results, $this->setup['Data_Prefix']);
      
      // We edit a single record at a time.
      if (count($data) !== 1)
      {
	 $this->em->notify(
	    'Log',
	    array('Level'   => LOG_ERR,
		  'Method'  => __METHOD__,
		  'Message' => 'multiple records received.'));
	 
	 $this->failures->add('Edit_Failed', 'Sys_Admin_Notified');

	 return;
      }

      $record = reset($data);
      
      // Set the session to show that the record is being edited.
      $this->sessionManager->set('New_Record', false);
      $this->sessionManager->set('Current_Record', $record);
      $this->sessionManager->set('Editing_Record', true);
      $this->sessionManager->set('Edited_Record', $record);
   }
   
   /// Get the data for the model.
   public function getData($selectSetup=array())
   {
      return $this->offsetData(
	 array('AJAX_Data' => $this->setup['AJAX_Data'],
	       'Records'   => parent::getData($selectSetup),
	       'State'     => array_merge(
		  array('Failures'      => $this->failures->get(),
			'Notifications' => $this->notifications->get()),
		  $this->sessionManager->getAccess())));
   }

   /// Modify a record in the database.
   public function modify($updates)
   {
      $this->updateCurrentRecord($updates);
      $addRecord = $this->sessionManager->get('Current_Record');
      $addData = array($addRecord);
      
      if ($this->setup['Validate'])
      {
	 if (!$this->validate($addData, $this->setup['Table_References']))
	 {
	    return false;
	 }
      }

      $deleteData = $this->sessionManager->get('Edited_Record');
      
      ////////////////////
      // DB Transaction //
      ////////////////////
      try
      {
	 $this->sql->beginTransaction();

	 /// \todo Implement modify.
	 throw new \Exception('Modify action not yet implemented.');

	 // Recurse and process the modify.

	 $this->sql->commit();
	 $this->sessionManager->reset();
	 $this->notifications->add('Modify', 'Successful');
      }
      catch (\Exception $e)
      {
	 $this->em->notify('Log', array('Level'   => LOG_ERR,
					'Method'  => __METHOD__,
					'Message' => $e->getMessage()));
	 $this->failures->add('Modify_Failed', 'Sys_Admin_Notified');
	 $this->sql->rollBack();
      }
   }

   /** Update the current record in the sesssion.
    *  @param record \array New information to be added to the current record.
    */
   public function updateCurrentRecord($updateRecord)
   {
      if (!$this->sessionManager->issetKey('Current_Record'))
      {
	 throw new \RuntimeException(
	    __METHOD__ . ' Current_Record is not set for update.');
      }

      $data = $this->setup['Table_References']->arrangeResults(
	 array($updateRecord));
      $updateRecord = reset($data); 

      if (!empty($updateRecord))
      {
	 $currentRecord = $this->sessionManager->get('Current_Record');
	 $jointKey = $this->setup['Table_References']->getJointKey();
	 
	 // First merge any joint data.
	 if (isset($updateRecord[$jointKey]))
	 {
	    $updateJoint =& $updateRecord[$jointKey];
	    
	    if (!isset($currentRecord[$jointKey]))
	    {
	       $currentRecord[$jointKey] = array();
	    }
	    
	    $currentJoint =& $currentRecord[$jointKey];
	    
	    foreach ($updateJoint as $key => $val)
	    {
	       if (isset($currentJoint[$key]))
	       {
		  $currentJoint[$key] = array_merge($currentJoint[$key], $val);
	       }
	       else
	       {
		  $currentJoint[$key] = $val;
	       }
	    }
	 }
	 
	 // Now merge any non-joint data.
	 unset($updateRecord[$jointKey]);
	 $currentRecord = array_merge($currentRecord, $updateRecord);
	 
	 $this->sessionManager->set('Current_Record', $currentRecord);
      }

      $this->setup['AJAX_Data'] = array('Success' => true);
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/
   
   protected function addEntries(&$data, $ref)
   {
      if (!$ref->isAdminManaged() || empty($data))
      {
	 return;
      }

      $tableName = $ref->getTableName();
      $childField = $ref->getChildField();
      
      // Work out whether we need a List_ID to be calculated.
      if (isset($childField))
      {
	 $listID = $this->setup['Table_List_ID']->getNew(
	    $tableName, $childField);
	 
	 foreach ($data as &$record)
	 {
	    $record[$childField] = $listID;
	 }
      }

      // We only need to add the records for the current table so we should
      // remove the joint data on a copy of the data.
      $addData = $data;
      
      foreach ($addData as &$addRecord)
      {
	 unset($addRecord[$ref->getJointKey()]);
      }

      $this->sql->insert($tableName, array_keys(reset($addData)), $addData);
      
      // If the listID was null then this is a one to one relationship with an
      // ID that we can obtain with lastInsertID.  We set it so that it can be
      // used in any feedback of data to the parent records.
      if (!isset($listID))
      {
	 // If it is not a 1 to 1 relationship then we are in trouble.
	 if (count($addData) !== 1)
	 {
	    throw new \RuntimeException(
	       __METHOD__ . ' child field not set for Child Field: ' .
	       var_export($childField, true) . ' in table: ' .
	       var_export($tableName, true));
	  }
	 
	 foreach ($data as &$childRecord)
	 {
	    $childRecord[$childField] = $this->sql->lastInsertID($childField);
	 }  
      }
       
      // Return the data which has been altered with any new list ids.
      return $data;
   }
   
   /** Delete the records for the current table.
    *  @param data \array The records to be deleted.
    *  @param ref \obj The Table_References object.
    */
   protected function deleteEntries($data, $ref)
   {
      foreach ($data as $record)
      {
	 unset($record[$ref->getJointKey()]);
	 $this->sql->delete($ref->getTableName(), $record);
      }
   }   

   /** Recurse the data and table references calling the appropriate callbacks.
    *  This recursion is used to call the callback functions in varying breadth
    *  first or depth first manner.
    *
    *  @param callbacks \array Callbacks to be called recursively on the data of
    *  the format:
    *  \verbatim
       // Supply callbacks as values in this array.
       // The comments show what the callback will receive.
       array('Breadth_First_Data'   => NULL, // Data,          Table Reference.
             'Breadth_First_Record' => NULL, // Record,        Table Reference.
	     'Breadth_First_Parent' => NULL, // Parent Record, Child Reference.
	     'Depth_First_Data'     => NULL, // Parent Record, Child Reference.
	     'Depth_First_Record'   => NULL, // Record,        Table Reference.
	     'Depth_First_Parent'   => NULL) // Data,          Table Reference.
       \endverbatim
    *  For any one bit of data these functions are called in the following order:
    *  \verbatim
       1 Breadth_First_Data
       2 Breadth_First_Record
       3 Breadth_First_Parent
       4 Depth_First_parent
       5 Depth_First_Record
       6 Depth_First_Data
       \endverbatim
    *  Order is important when callback functions alter the data they receive.
    *
    *  @param data \array The data to traverse.
    *  @param tRef \obj The Table_References object to traverse the data with.
    *  \return The data after possibly being modified by the callbacks.
    */
   protected function recurse(Array $callbacks, Array &$data, $tRef)
   {
      $jointKey = $tRef->getJointKey();
      $childReferences = $tRef->getReferences();
      
      $this->call($callbacks, 'Breadth_First_Data', &$data, $tRef);
      
      // Loop through the data record by record, recursing downwards through the
      // data referenced by the table references.
      foreach ($data as &$record)
      {
	 $this->call($callbacks, 'Breadth_First_Record', &$record, $tRef);
      
	 foreach ($childReferences as $ref)
	 {	    
	    $parentField = $ref->getParentField();
	    
	    if (isset($record[$jointKey][$parentField]))
	    {
	       $this->call($callbacks, 'Breadth_First_Parent', &$record, $ref);
	       
	       $record[$jointKey][$parentField] = $this->recurse(
		  $callbacks, $record[$jointKey][$parentField], $ref);
	       
	       $this->call($callbacks, 'Depth_First_Parent', &$record, $ref);
	    }
	 }
	 
	 $this->call($callbacks, 'Depth_First_Record', &$record, $tRef);
      }
      
      $this->call($callbacks, 'Depth_First_Data', &$data, $tRef);
      
      return $data;
   }
   
   /** Feeback the List_ID from the referenced child records into the parent
    *  record.
    *  @param parentRecord \array The parent record.
    *  @param ref \object The reference to the child record. 
    */
   protected function feedbackListID(&$parentRecord, $ref)
   {
      $jointKey    = $ref->getJointKey();
      $childField  = $ref->getChildField();
      $parentField = $ref->getParentField();

      if (!isset($childField))
      {
	 return;
      }
      
      if (isset($parentRecord[$jointKey][$parentField]))
      {
	 $firstChildRecord = reset($parentRecord[$jointKey][$parentField]);

	 if (is_array($firstChildRecord) &&
	     isset($firstChildRecord[$childField]))
	 {
	    $parentRecord[$parentField] = $firstChildRecord[$childField];
	 }
      }
   }
   
   /// Get the event map used for connecting events.
   protected function getProcessingEventMap()
   {
      return array_merge(
	 parent::getProcessingEventMap(),
	 array('Add'           	       => 'add',
	       'Cancel'        	       => 'cancel',
	       'Create_New'    	       => 'createNew',
	       'Delete_Cancel' 	       => 'deleteCancel',
	       'Delete_Confirm'	       => 'deleteConfirm',
	       'Delete_Request'	       => 'deleteRequest',
	       'Edit'          	       => 'edit',
	       'Modify'        	       => 'modify',
	       'Update_Current_Record' => 'updateCurrentRecord'));
   }
   
   /** Validate all of the data specified with the table references.
    *  @param data \array The data to validate.
    *  @param ref \obj The Table References object to validate the data with.
    *  @return \bool Whether the data is valid or not.
    */
   protected function validate($data, $ref)
   {
      $this->recurse(array('Depth_First_Data' => array($this, 'validateEntries')),
		     $data,
		     $ref);
      
      return $this->failures->isEmpty();
   }

   /** Validate the data for the referenced table.
    */
   protected function validateEntries($data, $ref)
   {
      if (!$ref->isAdminManaged() || empty($data))
      {
	 return;
      }

      foreach ($data as $record)
      {
	 // We validate, ignoring the joint key field and any child and parent
	 // fields which are set automatically.
	 $ignoredFields = array($ref->getJointKey(), $ref->getChildField());
	 $refs = $ref->getReferences();
	 
	 foreach ($refs as $r)
	 {
	    $ignoredFields[] = $r->getParentField();
	 }

	 if (!$ref->isValid($record, $ignoredFields))
	 {
	    $this->failures->append($ref->getFailures());
	 }
      }
   }

   /*******************/
   /* Private Methods */
   /*******************/

   /** Helper function to call a callback.
    *  @param callbacks \array The array of callbacks that we are calling from.
    *  @param cb \string The index for the callback that we want to call.
    *  @param data \array The data to pass to the callback by reference.
    *  @param ref \obj The Table_References object to pass to the callback.
    */
   private function call(Array $callbacks, $cb, Array &$data, $ref)
   {
      if (isset($callbacks[$cb]))
      {
	 call_user_func($callbacks[$cb], &$data, $ref);
      }
   }
}
// EOF