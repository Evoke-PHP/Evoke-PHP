<?php


class Model_Session_Admin extends Model_Session implements Iface_Model_Admin
{ 
   public function __construct()
   {
      $setup += array('AJAX_Data'       => array(),
		      'State_Manager'   => NULL,
		      'Validate'        => true);

      parent::__construct($setup);

      $this->app->needs(
	 array('Instance' => array(
		  'Session_Manager' => $this->setup['State_Manager'])));

      $this->stateManager =& $this->setup['State_Manager'];

   }

   /******************/
   /* Public Methods */
   /******************/

   /// Add a record.
   public function add($record)
   {

   }

   /// Cancel any currently edited record.
   public function cancel()
   {
      $this->stateManager->reset();

      $this->setup['AJAX_Data'] = array('Success' => true);
   }

   /// Begin creating a new record.
   public function createNew()
   {
      $this->stateManager->set('Current_Record' => array(),
			       'New_Record'     => true);

      $this->setup['AJAX_Data'] = array('Success' => true);
   }

   /// Cancel the currently requested deletion.
   public function deleteCancel()
   {

   }
   
   /// Delete a record (normally after confirmation from the user).
   public function deleteConfirm(Array $record)
   {

   }

   /** Request that a record should be deleted, but only after confirmation
    *  from the user.
    */
   public function deleteRequest($record)
   {

   }   
   
   /// Set a record for editing.
   public function edit($record)
   {

   }

   /// Modify a record.
   public function modify($record)
   {

   }
}

// EOF