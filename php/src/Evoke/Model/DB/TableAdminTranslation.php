<?php
namespace Evoke\Model\DB;

class TableAdminTranslation extends TableAdmin
{
	protected $languageTable;
	protected $translatorFile;
	
	protected $filesystem;
	
	public function __construct(Array $setup)
	{
		$setup += array('Filesystem'      => NULL,
		                'Language_Table'  => 'Language',
		                'Translator_File' => NULL);
      
		if (!$filesystem instanceof Filesystem)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires File_System');
		}

		if (!is_string($translatorFile))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator_File as string');
		}

		parent::__construct($setup);
		
		$this->languageTable  = $languageTable;
		$this->translatorFile = $translatorFile;

		$this->filesystem = $filesystem;
	}

	/******************/
	/* Public Methods */
	/******************/

	// Add a translation.
	public function add($record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();
	 
			if (parent::add($record))
			{
				$this->updateTranslations();
				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure adding translation to database due to exception:  ' .
				$e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add('Failure Adding Language', 'Sys_Admin_Notified');
			$this->sQL->rollBack();
		}
	}

	/// Execute a delete that has been confirmed.
	public function delete($record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();
	 
			if (parent::delete($record))
			{
				$this->updateTranslations();
				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure deleting translation from database due to ' .
				'exception: ' . $e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add(
				'Failure Deleting Language',
				'System Administrator has been notified.');

			$this->sQL->rollBack();
		}
	}

	public function modify($record)
	{
		////////////////////
		// DB Transaction //
		////////////////////
		try
		{
			$this->sQL->beginTransaction();
	 
			if (parent::modify($record))
			{
				$this->updateTranslations();
				$this->sQL->commit();
			}
			else
			{
				$this->sQL->rollBack();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Failure modifying translation from database due to ' .
				'exception: ' . $e->getMessage();
	 
			$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
			                                         'Message' => $msg,
			                                         'Method'  => __METHOD__));

			$this->failures->add(
				'Failure Modifying Language',
				'System Administrator has been notified.');

			$this->sQL->rollBack();
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
		$languages = $this->sQL->select($this->languageTable, '*');
      
		foreach($languages as $language)
		{
			$lang = $language['Language'];
	 
			unset($language['ID']);
			unset($language['Language']);
	 
			// Store the language record as an array.
			$langArr[$lang] = $language;
		}
      
		$translations = $this->sQL->select($this->tableName, '*');
      
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