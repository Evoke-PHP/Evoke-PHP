<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Persistence\DB\SQLIface,
	Evoke\Persistence\FilesystemIface,
	Exception,
	InvalidArgumentException;

/**
 * @todo Investigate whether this class is now obsolete.
 */

/**
 * TableAdminTranslation
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class TableAdminTranslation extends TableAdmin
{
	protected $languageTable;
	protected $translatorFile;
	
	protected $filesystem;
	
	/** Construct an Administration model for the translation table.
	 *  @param sql            @object SQL object.
	 *  @param filesystem     @object Filesystem object.
	 *  @param translatorFile @string Translations file.
	 *  @param dataPrefix     @array  Data prefix to offset the data to.
	 *  @param languageTable  @string Table name for the languages table.
	 */
	public function __construct(SQLIface        $sql,
	                            FilesystemIface $filesystem,
	                            /* String */    $translatorFile,
	                            Array           $dataPrefix    = array(),
	                            /* String */    $languageTable = 'Language')
	{
		if (!is_string($translatorFile))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires translatorFile as string');
		}

		parent::__construct($sql, $dataPrefix);

		$this->filesystem     = $filesystem;
		$this->languageTable  = $languageTable;
		$this->translatorFile = $translatorFile;
	}

	/******************/
	/* Public Methods */
	/******************/

	// Add a translation.
	public function add(Array $record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
	 
			if (parent::add($record))
			{
				$this->updateTranslations();
				$this->sql->commit();
			}
			else
			{
				$this->sql->rollBack();
			}
		}
		catch (Exception $e)
		{
			trigger_error(
				'Failure adding translation to database due to exception:  ' .
				$e->getMessage(), E_USER_WARNING);	 
			$this->failures->add('Failure Adding Language', 'Sys_Admin_Notified');
			$this->sql->rollBack();
		}
	}

	/// Execute a delete that has been confirmed.
	public function delete(Array $record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
	 
			if (parent::delete($record))
			{
				$this->updateTranslations();
				$this->sql->commit();
			}
			else
			{
				$this->sql->rollBack();
			}
		}
		catch (Exception $e)
		{
			trigger_error(
				'Failure deleting translation from database due to ' .
				'exception: ' . $e->getMessage(), E_USER_WARNING);
			$this->failures->add(
				'Failure Deleting Language',
				'System Administrator has been notified.');
			$this->sql->rollBack();
		}
	}

	public function modify(Array $oldRecord, Array $newRecord)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sql->beginTransaction();
	 
			if (parent::modify($record))
			{
				$this->updateTranslations();
				$this->sql->commit();
			}
			else
			{
				$this->sql->rollBack();
			}
		}
		catch (Exception $e)
		{
			trigger_error(
				'Failure modifying translation from database due to ' .
				'exception: ' . $e->getMessage(), E_USER_WARNING);
			$this->failures->add(
				'Failure Modifying Language',
				'System Administrator has been notified.');
			$this->sql->rollBack();
		}
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	/** Write the translations file.
	 *  We make the following assumptions for the use of this translation file:
	 *      We know what language we want.
	 *      We know what page we are on.
	 *      We know the string_id of the string that we want.
	 *  The translation array is stored in the following format:
	 *     array('Lang' => array('page' => (array('string_id' => 'string'))));
	 */
	protected function updateTranslations()
	{
		$languages = $this->sql->select($this->languageTable, '*');
      
		foreach($languages as $language)
		{
			$lang = $language['Language'];
	 
			unset($language['ID']);
			unset($language['Language']);
	 
			// Store the language record as an array.
			$langArr[$lang] = $language;
		}
      
		$translations = $this->sql->select($this->tableName, '*');
      
		foreach($translations as $translation)
		{
			// Get the general information.
			$name = $translation['Name'];
			$page = $translation['Page'];
	 
			// Record each unique keys used for translating.
			$pageKeyArr[$page] = true;
			$transKeyArr[$name] = true;
	 
			// Remove the fields that are not translations.
			unset($translation['ID']);
			unset($translation['Name']);
			unset($translation['Page']);
	 
			// Set the translation information.
			foreach ($translation as $trLang => $value)
			{
				$transArr[$trLang][$page][$name] = $value;
			}
		}
      
		$translationArr = array('Languages'        => $langArr,
		                        'Page_Keys'        => $pageKeyArr,
		                        'Translation_Keys' => $transKeyArr,
		                        'Translations'     => $transArr);
      
		// Write the translation file.
		$file = $this->filesystem->fopen($this->translatorFile, 'w');
		fwrite($file, 	 '<?php' . "\n" .
		       '      $translationArr =' . "\n" .
		       var_export($translationArr, true) . ";\n" .
		       '// EOF');
		fclose($file);
	}
}
// EOF