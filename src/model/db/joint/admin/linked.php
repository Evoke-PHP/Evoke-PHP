<?php
// Copyright (c) 2009 - 2011 Skyshops Marketing. All Rights Reserved.

/** Model_DB_Joint_Admin_Linked provides a CRUD interface to a joint set of data
 *  with linked information stored in files referenced from the database.
 */
class Model_DB_Joint_Admin_Linked extends Model_DB_Joint_Admin
{
   public function __construct(Array $setup)
   {
      $setup += array('Dir_Mode'    => 0770,
		      'File_Mode'   => 0660,
		      'File_System' => NULL,
		      'Image_Manip' => NULL,
		      'Links'       => array());
      parent::__construct($setup);

      $this->app->needs(
	 array('Instance' => array(
		  'File_System' => $this->setup['File_System'],
		  'Image_Manip' => $this->setup['Image_Manip'])));
      
      foreach ($this->setup['Links'] as $alias => &$link)
      {
	 if (!is_array($link))
	 {
	    throw new InvalidArgumentException(
	       __METHOD__ . ' invalid link for alias: ' . $alias .
	       ' link: ' . var_export($link, true));
	 }
	 
	 $link += array('ID_Fields' => NULL,
			'Incoming'  => NULL,
			'Storage'   => NULL);
	 
	 $this->app->needs(
	    array('Set' => array('Incoming' => $link['Incoming'],
				 'Storage'  => $link['Storage'])));

	 if (!isset($link['ID_Fields']))
	 {
	    $link['ID_Fields'] = array('ID');
	 }
      }

      if ($this->setup['Connect_Events'])
      {
	 $this->connectEvents('File.', array('Input_File' => 'upload'));
      }
   }

   public function cancel()
   {
      $data = array($this->sessionManager->get('Current_Record'));
      $this->recurse(array('Depth_First_Data' => array($this, 'cancelEntries')),
		     $data,
		     $this->setup['Table_References']);
      
      parent::cancel();
   }
   
   public function edit($record)
   {
      parent::edit($record);

      $data = array($this->sessionManager->get('Edited_Record'));
      $this->recurse(array('Breadth_First_Data' => array($this, 'editEntries')),
		     $data,
		     $this->setup['Table_References']);
   }
   
   /** Upload a file.
    */
   public function upload($file)
   {
      $link = $this->getLink($file['Table_Alias']);

      if ($link === false)
      {
	 return;
      }
      
      // If we do not have a session then we just copy the file simply.
      $dir = $link['Incoming'];
      
      if (isset($link['Session_Manager']))
      {
	 $dir .= $link['Session_Manager']->id() . '/' .
	    $link['Session_Manager']->keyCount() . '/';
	 $this->addToSession($link, $file);
      }
      
      $this->addToIncoming($link, $dir, $file);
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /** Add the entries and linked data to the database.
    *  @param data \array The data to be added to the database.
    *  @param ref \obj The table references object for the data.
    */
   protected function addEntries(&$data, $ref)
   {
      parent::addEntries(&$data, $ref);
      
      $link = $this->getLink($ref->getTableAlias());

      if ($link !== false)
      {
	 // The data has been added to the database, but would not have been
	 // updated with the ID's that were added.  As this information is used
	 // when linking the data we should get the full data that was added
	 // now.
	 $childField = $ref->getChildField();
	 $firstRecord = reset($data);
	 $conditions = array($childField => $firstRecord[$childField]);

	 $addedData = $this->sql->select(
	    $ref->getTableName(), '*', $conditions);
	 
	 $this->moveIncomingToStorage($link, $addedData, $ref);
      }
   }
   
   protected function addToSession($link, $file)
   {
      if (isset($link['Image_Sizes']))
      {
	 $link['Session_Manager']->keyCount();
	 
	 // Default a single record to the default image.
	 $record = array(
	    'Image'      => $file['Name'],
	    'Is_Default' => ($link['Session_Manager']->keyCount() === 0));
      }
      else
      {
	 $record = array('Name' => $file['Name']);
      }

      $link['Session_Manager']->addValue($record);
   }
   
   /// Cancel the editing of the entries.
   protected function cancelEntries($data, $ref)
   {
      $link = $this->getLink($ref->getTableAlias());

      if ($link !== false)
      {
	 $this->deleteIncoming($link);
      }
   }

   /** Delete an entry,  The table reference can be used to calculate specific
    *  actions for deleting related data.
    */
   protected function deleteEntries($data, $ref)
   {
      if (empty($data))
      {
	 return;
      }
      
      parent::deleteEntries($data, $ref);
      $link = $this->getLink($ref->getTableAlias());

      if ($link !== false)
      {
	 $this->deleteStorage($link, $data, $ref);
      }
   }
   
   
   /** Edit the entry by moving the linked data for the editing of the entry.
    *  @param data \array The data for the current table that is being edited.
    *  @param ref \obj The table references object for the current table.
    */
   protected function editEntries($data, $ref)
   {
      if (empty($data))
      {
	 return;
      }

      $link = $this->getLink($ref->getTableAlias());
      
      if ($link !== false)
      {
	 $this->copyStorageToIncoming($link, $data, $ref);

	 if (isset($link['Session_Manager']))
	 {
	    $this->renumberSession($link['Session_Manager'], $data);
	 }
      }
   }

   /** Get the link entry for the specified table alias.
    *  @param tableAlias \string The table to get the link entry for.
    */
   protected function getLink($tableAlias)
   {
      if (isset($this->setup['Links'][$tableAlias]))
      {
	 return $this->setup['Links'][$tableAlias];
      }

      return false;
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Add a file to the directory for the specified link entry.
    *  @param link \array  The link entry.
    *  @param dir  \string The directory to add the file to.
    *  @param file \string The file to add.
    */
   private function addToIncoming(Array $link, $dir, $file)
   {
      $newName = $dir . $file['Name'];
      
      try
      {
	 if (!$this->setup['File_System']->is_dir($dir))
	 {
	    $this->setup['File_System']->mkdir($dir,
					       $this->setup['Dir_Mode'],
					       true);
	 }

	 $this->setup['File_System']->rename($file['Tmp_Name'],
					     $newName);
	 $this->setup['File_System']->chmod($newName,
					    $this->setup['File_Mode']);
      }
      catch (Exception $e)
      {
	 $msg = 'Could not move temp file: ' . $file['Tmp_Name']  . ' to: ' .
	    $newName . ' due to exception: ' .
	    $e->getMessage();
	 
	 $this->setup['Event_Manager']->notify(
	    'Log',
	    array('Level'   => LOG_ERR,
		  'Message' => $msg,
		  'Method'  => __METHOD__));
	 throw $e;
      }

      if (isset($link['Image_Sizes']))
      {
	 $this->resizeImage($link['Image_Sizes'], $dir, $file['Name']);
      }
   }

   /** Copy the linked files from storage to incoming and update the session.
    */   
   private function copyStorageToIncoming($link, $data, $ref)
   {
      $fileNum = 0;
      $firstRecord = reset($data);
      $srcBase = $link['Storage'] . $firstRecord[$ref->getChildField()];
      
      // Make sure the destination exists or is created.
      $destBase = $link['Incoming'] . $this->sessionManager->id() . '/';
      
      if (!$this->setup['File_System']->is_dir($destBase))
      {
	 $this->setup['File_System']->mkdir(
	    $destBase, $this->setup['Dir_Mode'], true);
      }
      
      foreach ($data as $record)
      {
	 $srcSpecific = $srcBase;
	 
	 foreach ($link['ID_Fields'] as $field)
	 {
	    $srcSpecific .= '/' . $record[$field];
	 }

	 $this->setup['File_System']->copy($srcSpecific,
					   $destBase . $fileNum++ . '/',
					   $this->setup['Dir_Mode'],
					   $this->setup['File_Mode']);
      }
   }

   /// Delete the incoming directory and reset the session.
   private function deleteIncoming($link)
   {
      $dir = $link['Incoming'] . $this->sessionManager->id();
      
      if ($this->setup['File_System']->is_dir($dir))
      {
	 $this->setup['File_System']->unlink($dir);
      }
   }

   private function deleteStorage($link, $data, $ref)
   {
      $firstRecord = reset($data);
      $dir = $link['Storage'] . $firstRecord[$ref->getChildField()];
      $this->setup['File_System']->unlink($dir);
   }

   /// Move the incoming files to storage.
   private function moveIncomingToStorage($link, $data, $ref)
   {
      $srcBase = $link['Incoming'] . $this->sessionManager->id() . '/';
      $destBase = $link['Storage'];

      foreach ($data as $num => $record)
      {
	 $destSpecific = $destBase . $record[$ref->getChildField()] . '/';
	 
	 foreach ($link['ID_Fields'] as $field)
	 {
	    $destSpecific .= $record[$field] . '/';
	 }

	 $this->setup['File_System']->mkdir(
	    $destSpecific, $this->setup['Dir_Mode'], true);
	 $this->setup['File_System']->rename($srcBase . $num, $destSpecific);
      }
   }
   
   private function renumberSession($session, $data)
   {
      $session->replaceWith(array_values($data));
   }

   // Create images scaled to our desired sizes.
   private function resizeImage($sizes, $dir, $filename)
   {
      unset($sizes['Original']);
      
      foreach ($sizes as $format => $size)
      {
	 $setup = array_merge($size, array('Dir_Input' => $dir,
					   'Dir_Output' => $dir));

	 $this->setup['Image_Manip']->setSettings($setup);

	 try
	 {
	    $this->setup['Image_Manip']->scaleImage($filename);
	 }
	 catch (Exception $e)
	 {
	    $msg = 'Could not scale Image to ' . $format .
	       ' due to exception: ' . $e->getMessage();

	    $this->setup['Event_Manager']->notify(
	       'Log',
	       array('Level'   => LOG_ERR,
		     'Message' => $msg,
		     'Method'  => __METHOD__));
	    throw $e;
	 }
      }
   }   
}

// EOF