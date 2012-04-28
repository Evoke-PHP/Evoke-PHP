<?php
namespace Evoke\View;

use Evoke\Iface;

class Admin extends Base
{
	/** Construct an Admin view.
	 *  @param info            @object $info
	 *  @param tableName       @string $tableName
	 *  @param pageName        @string $pageName
	 *  @param recordListSetup @array  $recordListSetup
	 *  @param start           @array  $start
	 *  @param startBase       @array  $startBase
	 *  @param editSeparately  @bool   $editSeparately
	 */
	public function __construct(
		Iface\DB\Table\Info $info,
		/* String */ 		$tableName,
		/* String */ 		$pageName,
		Array        		$recordListSetup,
		Array        		$start,
		Array        		$startBase      = array(
			'CSS' => array(
				'/csslib/global.css',
				'/csslib/common.css',
				'/csslib/admin/admin.css',
				'/csslib/element/language.css',
				'/csslib/element/admin_header.css',
				'/csslib/element/error.css')),
		/* Bool */          $editSeparately = false)
	{
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}

		if (!is_string($pageName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires pageName as string');
		}

		if (!is_bool($editSeparately))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires editSeparately as bool');
		}

		$this->info            = $info;
		$this->tableName       = $tableName;
		$this->pageName        = $pageName;
		$this->recordListSetup = $recordListSetup;
		$this->start           = $start;
		$this->startBase       = $startBase;
		$this->editSeparately  = $editSeparately;
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
			$this->writer->write(
				$this->instanceManager->create(
					'Element_Failures', array('Data' => $failures)));
		}
	}

   
	/// Write the Notifications.
	protected function writeNotifications($notifications)
	{
		if (!empty($notifications))
		{
			$this->writer->write(
				$this->instanceManager->create(
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
			                            'value' => $this->translator->get('Add')));
		}
		else
		{
			$buttonsArr[] = array('input',
			                      array('class' => 'Modify Button Info',
			                            'name'  => 'Modify',
			                            'type'  => 'submit',
			                            'value' => $this->translator->get('Modify')));
		}
      
		$buttonsArr[] = array('input',
		                      array('class' => 'Cancel Button Bad',
		                            'name'  => 'Cancel',
		                            'type'  => 'submit',
		                            'value' => $this->translator->get('Cancel')));
      
		return $buttonsArr;
	}

	/// Write a Create New button.
	protected function writeCreateNew()
	{
		$this->writer->write(
			array('form',
			      array('class'  => 'Create_New',
			            'action' => '',
			            'method' => 'post'),
			      array(array('input',
			                  array('class' => 'Create_New Button Good',
			                        'name'  => 'Create_New',
			                        'type'  => 'submit',
			                        'value' => $this->translator->get(
				                        'Create_New')))))));
	}

	/// Write an entry form for the current record.
	protected function writeCurrentRecord($record, $isNew)
	{
		$this->writer->write(
			$this->instanceManager->create(
				'Element_Form_Entry',
				array('Field_Values'   => $record,
				      'Submit_Buttons' => $this->getSubmitButtons($isNew),
				      'Table_Info'     => $this->tableInfo,
				      'Table_Name'     => $this->tableName,
				      'Translator'     => $this->translator)));
	}

	protected function writeDeleteRequest($request)
	{
		$this->writer->write(array('div', array('class' => 'mask')));

		$rowButtons = array(
			array('input',
			      array('class' => 'Dialog_Submit Button Bad Small',
			            'name'  => 'Delete_Confirm',
			            'type'  => 'submit',
			            'value' => $this->translator->get('Confirm'))),
			array('input',
			      array('class' => 'Dialog_Cancel Button Info Small',
			            'name'  => 'Delete_Cancel',
			            'type'  => 'submit',
			            'value' => $this->translator->get('Cancel'))));
      
		$recordToDelete = $this->instanceManager->create(
			'Element_Record_List_Table',
			array_merge(array('Attribs'        => array(
				                  'class' => 'Delete_Request Record_List'),
			                  'Data'           => $request,
			                  'Heading_Setup'  => array('Inline' => true),
			                  'Row_Buttons'    => $rowButtons,
			                  'Table_Info'     => $this->tableInfo,
			                  'Table_Name'     => $this->tableName,
			                  'Translator'     => $this->translator)));      
			   
		$this->writer->write(
			$this->instanceManager->create(
				'Element_Form_Dialog',
				array('Attribs'          => array('class'  => 'Dialog Bad',
				                                  'action' => '',
				                                  'method' => 'post'),
				      'Heading_Text'     => $this->translator-> get(
					      'Confirm_Delete_Heading'),
				      'Message_Elements' => array($recordToDelete),
				      'Message_Text'     => $this->translator->get('Confirm_Delete_Text'))));
	}
   
	/// Write a header.
	protected function writeHeader($header)
	{
		$this->writer->write(
			$this->instanceManager->create(
				'Element_Admin_Header',
				array('Languages'  => $header['Languages'],
				      'Translator' => $this->translator)));

		if (isset($this->pageName))
		{
			$this->writer->write(
				array('h1',
				      array('class' => 'Admin_Heading'),
				      $this->translator->get($this->pageName)));
		}
	}

	/// Write a list of records.
	protected function writeRecordList($data)
	{
		$this->writer->write(
			$this->instanceManager->create(
				'Element_Record_List_Table',
				array_merge(
					array('Data'           => $data,
					      'Row_Buttons'    => array(
						      array(
							      'input',
							      array('class' => 'Dialog_Submit Button Info Small',
							            'name'  => 'Edit',
							            'type'  => 'submit',
							            'value' => $this->translator->get('Edit'))),
						      array(
							      'input',
							      array('class' => 'Dialog_Request Button Bad Small',
							            'name'  => 'Delete_Request',
							            'type'  => 'submit',
							            'value' => $this->translator->get('Delete')))),
					      'Row_Buttons_As_Form' => true,
					      'Table_Info'          => $this->tableInfo,
					      'Table_Name'          => $this->tableName,
					      'Translator'          => $this->translator),
					$this->recordListSetup)));
	}
}
// EOF