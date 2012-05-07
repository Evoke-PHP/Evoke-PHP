<?php
namespace Evoke\Model\DB;

use Evoke\Iface;

/** Model_DB_Joint_Admin_Linked provides a CRUD interface to a joint set of data
 *  with linked information stored in files referenced from the database.
 */
class JointAdminLinked extends JointAdmin
{
	/** @property $dirMode
	 *  @int (octal) The directory mode to create directories at.
	 */
	protected $dirMode;

	/** @property $fileMode
	 *  @int (octal) The file mode to create linked files with.
	 */
	protected $fileMode;
	
	/** @property $filesystem
	 *  @object Filesystem
	 */
	protected $filesystem;

	/** @property $imageManip
	 *  @object Image manipulation
	 */
	protected $imageManip;

	/** @property $links
	 *  @array links
	 */
	protected $links;
	
	/** Construct an Administration Model of a joint set of database tables with
	 *  linked information in the filesystem.
	 *  @param sql            @object SQL object.   
	 *  @param tableName      @string The table name where joins start from.
	 *  @param joins          @object Joins object.
	 *  @param sessionManager @object SessionManager object.
	 *  @param tableListID    @object DB List ID Table object.
	 *  @param failures       @object Failure messages object.
	 *  @param notifications  @object Notification messages object.
	 *  @param eventManager   @object EventManager object.
	 *  @param select         @array  Select statement settings.
	 *  @param dataPrefix     @array  Any prefix to offset the data with.
	 *  @param validate       @bool   Whether to validate the data.
	 */
	public function __construct(Iface\DB\SQL          $sql,
	                            /* String */          $tableName,
	                            Iface\DB\Table\Joins  $joins,
	                            Iface\SessionManager  $sessionManager,
	                            Iface\DB\Table\ListID $tableListID,
	                            Iface\MessageTree     $failures,
	                            Iface\MessageTree     $notifications,
	                            Iface\EventManager    $eventManager,
	                            Icore\Links           $TODO_FIX_ME, ///< \todo FIXME
	                            Icore\Filesystem      $filesystem,
	                            Icore\ImageManip      $imageManip,
	                            /* Integer (Octal) */ $dirMode  = 0770,
	                            /* Integer (Octal) */ $fileMode = 0660,
	                            Array                 $select   = array(),
	                            /* Bool */            $validate = true)
	{
		/// \todo Fix to new coding standard.
		throw new \RuntimeException(
			__METHOD__ . ' needs implementation to new standard.');
		
		foreach ($links as $alias => &$link)
		{
			if (!is_array($link))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' invalid link for alias: ' . $alias .
					' link: ' . var_export($link, true));
			}
	 
			$link += array('ID_Fields' => array('ID'),
			               'Incoming'  => NULL,
			               'Storage'   => NULL);

			if (!isset($link['Incoming'], $link['Storage']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' link: ' . $alias .
					' requires a defined Incoming and Storage directory');
			}
		}

		parent::__construct($setup);

		$this->dirMode    = $dirMode;
		$this->fileMode   = $fileMode;
		$this->filesystem = $filesystem;
		$this->imageManip = $imageManip;
		$this->links      = $links;
	}

	public function cancel()
	{
		$data = array($this->sessionManager->get('Current_Record'));
		$this->recurse(array('Depth_First_Data' => array($this, 'cancelEntries')),
		               $data,
		               $this->joins);
      
		parent::cancel();
	}
   
	public function edit($record)
	{
		parent::edit($record);

		$data = array($this->sessionManager->get('Edited_Record'));
		$this->recurse(array('Breadth_First_Data' => array($this, 'editEntries')),
		               $data,
		               $this->joins);
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
			$dir .= $link['Session_Manager']->getID() . '/' .
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

			$addedData = $this->sQL->select(
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
		if (isset($this->links[$tableAlias]))
		{
			return $this->links[$tableAlias];
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
			if (!$this->filesystem->is_dir($dir))
			{
				$this->filesystem->mkdir($dir,
				                                   $this->dirMode,
				                                   true);
			}

			$this->filesystem->rename($file['Tmp_Name'],
			                                    $newName);
			$this->filesystem->chmod($newName,
			                                   $this->fileMode);
		}
		catch (\Exception $ex)
		{
			$msg = 'Could not move temp file: ' . $file['Tmp_Name']  . ' to: ' .
				$newName . ' due to exception: ' .
				$ex->getMessage();
	 
			$this->eventManager->notify(
				'Log',
				array('Level'   => LOG_ERR,
				      'Message' => $msg,
				      'Method'  => __METHOD__));
			throw $ex;
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
		$destBase = $link['Incoming'] . $this->sessionManager->getID() . '/';
      
		if (!$this->filesystem->is_dir($destBase))
		{
			$this->filesystem->mkdir(
				$destBase, $this->dirMode, true);
		}
      
		foreach ($data as $record)
		{
			$srcSpecific = $srcBase;
	 
			foreach ($link['ID_Fields'] as $field)
			{
				$srcSpecific .= '/' . $record[$field];
			}

			$this->filesystem->copy($srcSpecific,
			                                  $destBase . $fileNum++ . '/',
			                                  $this->dirMode,
			                                  $this->fileMode);
		}
	}

	/// Delete the incoming directory and reset the session.
	private function deleteIncoming($link)
	{
		$dir = $link['Incoming'] . $this->sessionManager->getID();
      
		if ($this->filesystem->is_dir($dir))
		{
			$this->filesystem->unlink($dir);
		}
	}

	private function deleteStorage($link, $data, $ref)
	{
		$firstRecord = reset($data);
		$dir = $link['Storage'] . $firstRecord[$ref->getChildField()];
		$this->filesystem->unlink($dir);
	}

	/// Move the incoming files to storage.
	private function moveIncomingToStorage($link, $data, $ref)
	{
		$srcBase = $link['Incoming'] . $this->sessionManager->getID() . '/';
		$destBase = $link['Storage'];

		foreach ($data as $num => $record)
		{
			$destSpecific = $destBase . $record[$ref->getChildField()] . '/';
	 
			foreach ($link['ID_Fields'] as $field)
			{
				$destSpecific .= $record[$field] . '/';
			}

			$this->filesystem->mkdir(
				$destSpecific, $this->dirMode, true);
			$this->filesystem->rename($srcBase . $num, $destSpecific);
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
			$setup = array_merge($size, array('Dir_Input'  => $dir,
			                                  'Dir_Output' => $dir));

			$this->imageManip->setSettings($setup);

			try
			{
				$this->imageManip->scaleImage($filename);
			}
			catch (\Exception $e)
			{
				$msg = 'Could not scale Image to ' . $format .
					' due to exception: ' . $e->getMessage();

				$this->eventManager->notify(
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