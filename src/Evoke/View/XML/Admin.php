<?php
namespace Evoke;

class View_XML_Admin extends View_XML
{
   public function __construct($setup=array())
   {
      $setup += array(
	 'Edit_Separately'   => false,
	 'Page_Name'         => NULL,
	 'Record_List_Setup' => array(),
	 'Start'             => array(),
	 'Start_Base'        => array(
	    'CSS' => array('/csslib/global.css',
			   '/csslib/common.css',
			   '/csslib/admin/admin.css',
			   '/csslib/element/language.css',
			   '/csslib/element/admin_header.css',
			   '/csslib/element/error.css')),
	 'Table_Info'        => NULL,
	 'Table_Name'        => NULL);

      parent::__construct($setup);

      if (!$this->setup['Table_Info'] instanceof DB\Table_Info)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Table_Info');
      }

      if (!is_string($this->setup['Table_Name']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Table_Name as string');
      }
   }

   /******************/
   /* Public Methods */
   /******************/

   public function writeContent($data)
   {
      $state = array_merge(array('Current_Record' => array(),
				 'Delete_Record'  => array(),
				 'Delete_Request' => false,
				 'Editing_Record' => false,
				 'Failures'       => array(),
				 'New_Record'     => false,
				 'Notifications'  => array()),
			   $data['State']);
      
      $this->writeFailures($state['Failures']);
      $this->writeNotifications($state['Notifications']);
            
      // Set and not false for the delete request is interpreted as requesting.
      if ($state['Delete_Request'] !== false)
      {
	 $this->writeDeleteRequest($state['Delete_Record']);
      }
	 
      if ($state['Editing_Record'])
      {
	 $this->writeCurrentRecord($state['Current_Record'],
				   $state['New_Record']);
      }
      else
      {
	 $this->writeCreateNew();
      }
      
      if (!($state['Editing_Record'] && $this->setup['Edit_Separately']))
      {
	 $this->writeRecordList($data['Records']);
      }
   }
   
   /// Write the failures.
   protected function writeFailures($failures)
   {
      if (!empty($failures))
      {
	 $this->xwr->write(
	    $this->app->getNew('Element_Failures', array('Data' => $failures)));
      }
   }

   
   /// Write the Notifications.
   protected function writeNotifications($notifications)
   {
      if (!empty($notifications))
      {
	 $this->xwr->write(
	    $this->app->getNew('Element_Notifications',
			       array('Data' => $notifications)));
      }
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   /// Get the submit buttons for the entry form.
   protected function getSubmitButtons($isNew)
   {
      $buttonsArr = array();
      
      if ($isNew)
      {
	 $buttonsArr[] = array('input',
			       array('class' => 'Add Button Good',
				     'name'  => 'Add',
				     'type'  => 'submit',
				     'value' => $this->tr->get('Add')));
      }
      else
      {
	 $buttonsArr[] = array('input',
			       array('class' => 'Modify Button Info',
				     'name'  => 'Modify',
				     'type'  => 'submit',
				     'value' => $this->tr->get('Modify')));
      }
      
      $buttonsArr[] = array('input',
			    array('class' => 'Cancel Button Bad',
				  'name'  => 'Cancel',
				  'type'  => 'submit',
				  'value' => $this->tr->get('Cancel')));
      
      return $buttonsArr;
   }

   /// Write a Create New button.
   protected function writeCreateNew()
   {
      $this->xwr->write(
	 array('form',
	       array('class'  => 'Create_New',
		     'action' => '',
		     'method' => 'post'),
	       array('Children' => array(
			array('input',
			      array('class' => 'Create_New Button Good',
				    'name'  => 'Create_New',
				    'type'  => 'submit',
				    'value' => $this->tr->get(
				       'Create_New')))))));
   }

   /// Write an entry form for the current record.
   protected function writeCurrentRecord($record, $isNew)
   {
      $this->xwr->write(
	 $this->app->getNew(
	    'Element_Form_Entry',
	    array('App'            => $this->app,
		  'Field_Values'   => $record,
		  'Submit_Buttons' => $this->getSubmitButtons($isNew),
		  'Table_Info'     => $this->setup['Table_Info'],
		  'Table_Name'     => $this->setup['Table_Name'],
		  'Translator'     => $this->tr)));
   }

   protected function writeDeleteRequest($request)
   {
      $this->xwr->write(array('div', array('class' => 'mask')));

      $rowButtons = array(
	 array('input',
	       array('class' => 'Dialog_Submit Button Bad Small',
		     'name'  => 'Delete_Confirm',
		     'type'  => 'submit',
		     'value' => $this->tr->get('Confirm'))),
	 array('input',
	       array('class' => 'Dialog_Cancel Button Info Small',
		     'name'  => 'Delete_Cancel',
		     'type'  => 'submit',
		     'value' => $this->tr->get('Cancel'))));
      
      $recordToDelete = $this->app->getNew(
	 'Element_Record_List_Table',
	 array_merge(array('App'            => $this->app,
			   'Attribs'        => array(
			      'class' => 'Delete_Request Record_List'),
			   'Data'           => $request,
			   'Heading_Setup'  => array('Inline' => true),
			   'Row_Buttons'    => $rowButtons,
			   'Table_Info'     => $this->setup['Table_Info'],
			   'Table_Name'     => $this->setup['Table_Name'],
			   'Translator'     => $this->tr)));      
			   
      $this->xwr->write(
	 $this->app->getNew(
	    'Element_Form_Dialog',
	    array('Attribs'          => array('class'  => 'Dialog Bad',
					      'action' => '',
					      'method' => 'post'),
		  'Heading_Text'     => $this->tr-> get(
		     'Confirm_Delete_Heading'),
		  'Message_Elements' => array($recordToDelete),
		  'Message_Text'     => $this->tr->get('Confirm_Delete_Text'))));
   }
   
   /// Write a header.
   protected function writeHeader($header)
   {
      $this->xwr->write(
	 $this->app->getNew('Element_Admin_Header',
			    array('App'        => $this->app,
				  'Languages'  => $header['Languages'],
				  'Translator' => $this->tr)));

      if (isset($this->setup['Page_Name']))
      {
	 $this->xwr->write(
	    array('h1',
		  array('class' => 'Admin_Heading'),
		  array('Text' => $this->tr->get(
			   $this->setup['Page_Name']))));
      }
   }

   /// Write a list of records.
   protected function writeRecordList($data)
   {
      $this->xwr->write(
	 $this->app->getNew(
	    'Element_Record_List_Table',
	    array_merge(
	       array('App'            => $this->app,
		     'Data'           => $data,
		     'Row_Buttons'    => array(
			array(
			   'input',
			   array('class' => 'Dialog_Submit Button Info Small',
				 'name'  => 'Edit',
				 'type'  => 'submit',
				 'value' => $this->tr->get('Edit'))),
			array(
			   'input',
			   array('class' => 'Dialog_Request Button Bad Small',
				 'name'  => 'Delete_Request',
				 'type'  => 'submit',
				 'value' => $this->tr->get('Delete')))),
		     'Row_Buttons_As_Form' => true,
		     'Table_Info'          => $this->setup['Table_Info'],
		     'Table_Name'          => $this->setup['Table_Name'],
		     'Translator'          => $this->tr),
	       $this->setup['Record_List_Setup'])));
   }
}
// EOF