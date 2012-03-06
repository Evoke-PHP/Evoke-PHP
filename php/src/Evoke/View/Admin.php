<?php
namespace Evoke\View;

class Admin extends Base
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

		if (!$this->tableInfo instanceof \Evoke\Core\DB\Table_Info)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Info');
		}

		if (!is_string($this->tableName))
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
      
		if (!($state['Editing_Record'] && $this->editSeparately))
		{
			$this->writeRecordList($data['Records']);
		}
	}
   
	/// Write the failures.
	protected function writeFailures($failures)
	{
		if (!empty($failures))
		{
			$this->Writer->write(
				$this->InstanceManager->create(
					'Element_Failures', array('Data' => $failures)));
		}
	}

   
	/// Write the Notifications.
	protected function writeNotifications($notifications)
	{
		if (!empty($notifications))
		{
			$this->Writer->write(
				$this->InstanceManager->create(
					'Element_Notifications', array('Data' => $notifications)));
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
			                            'value' => $this->Translator->get('Add')));
		}
		else
		{
			$buttonsArr[] = array('input',
			                      array('class' => 'Modify Button Info',
			                            'name'  => 'Modify',
			                            'type'  => 'submit',
			                            'value' => $this->Translator->get('Modify')));
		}
      
		$buttonsArr[] = array('input',
		                      array('class' => 'Cancel Button Bad',
		                            'name'  => 'Cancel',
		                            'type'  => 'submit',
		                            'value' => $this->Translator->get('Cancel')));
      
		return $buttonsArr;
	}

	/// Write a Create New button.
	protected function writeCreateNew()
	{
		$this->Writer->write(
			array('form',
			      array('class'  => 'Create_New',
			            'action' => '',
			            'method' => 'post'),
			      array('Children' => array(
				            array('input',
				                  array('class' => 'Create_New Button Good',
				                        'name'  => 'Create_New',
				                        'type'  => 'submit',
				                        'value' => $this->Translator->get(
					                        'Create_New')))))));
	}

	/// Write an entry form for the current record.
	protected function writeCurrentRecord($record, $isNew)
	{
		$this->Writer->write(
			$this->InstanceManager->create(
				'Element_Form_Entry',
				array('Field_Values'   => $record,
				      'Submit_Buttons' => $this->getSubmitButtons($isNew),
				      'Table_Info'     => $this->tableInfo,
				      'Table_Name'     => $this->tableName,
				      'Translator'     => $this->Translator)));
	}

	protected function writeDeleteRequest($request)
	{
		$this->Writer->write(array('div', array('class' => 'mask')));

		$rowButtons = array(
			array('input',
			      array('class' => 'Dialog_Submit Button Bad Small',
			            'name'  => 'Delete_Confirm',
			            'type'  => 'submit',
			            'value' => $this->Translator->get('Confirm'))),
			array('input',
			      array('class' => 'Dialog_Cancel Button Info Small',
			            'name'  => 'Delete_Cancel',
			            'type'  => 'submit',
			            'value' => $this->Translator->get('Cancel'))));
      
		$recordToDelete = $this->InstanceManager->create(
			'Element_Record_List_Table',
			array_merge(array('Attribs'        => array(
				                  'class' => 'Delete_Request Record_List'),
			                  'Data'           => $request,
			                  'Heading_Setup'  => array('Inline' => true),
			                  'Row_Buttons'    => $rowButtons,
			                  'Table_Info'     => $this->tableInfo,
			                  'Table_Name'     => $this->tableName,
			                  'Translator'     => $this->Translator)));      
			   
		$this->Writer->write(
			$this->InstanceManager->create(
				'Element_Form_Dialog',
				array('Attribs'          => array('class'  => 'Dialog Bad',
				                                  'action' => '',
				                                  'method' => 'post'),
				      'Heading_Text'     => $this->Translator-> get(
					      'Confirm_Delete_Heading'),
				      'Message_Elements' => array($recordToDelete),
				      'Message_Text'     => $this->Translator->get('Confirm_Delete_Text'))));
	}
   
	/// Write a header.
	protected function writeHeader($header)
	{
		$this->Writer->write(
			$this->InstanceManager->create(
				'Element_Admin_Header',
				array('Languages'  => $header['Languages'],
				      'Translator' => $this->Translator)));

		if (isset($this->pageName))
		{
			$this->Writer->write(
				array('h1',
				      array('class' => 'Admin_Heading'),
				      array('Text' => $this->Translator->get(
					            $this->pageName))));
		}
	}

	/// Write a list of records.
	protected function writeRecordList($data)
	{
		$this->Writer->write(
			$this->InstanceManager->create(
				'Element_Record_List_Table',
				array_merge(
					array('Data'           => $data,
					      'Row_Buttons'    => array(
						      array(
							      'input',
							      array('class' => 'Dialog_Submit Button Info Small',
							            'name'  => 'Edit',
							            'type'  => 'submit',
							            'value' => $this->Translator->get('Edit'))),
						      array(
							      'input',
							      array('class' => 'Dialog_Request Button Bad Small',
							            'name'  => 'Delete_Request',
							            'type'  => 'submit',
							            'value' => $this->Translator->get('Delete')))),
					      'Row_Buttons_As_Form' => true,
					      'Table_Info'          => $this->tableInfo,
					      'Table_Name'          => $this->tableName,
					      'Translator'          => $this->Translator),
					$this->recordListSetup)));
	}
}
// EOF