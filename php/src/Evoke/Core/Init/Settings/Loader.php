<?php
namespace Evoke\Core\Init\Settings;

class Loader
{
	/** @property $Settings
	 *  Settings \object
	 */
	protected $Settings;

	public function __construct(Array $setup)
	{
		$setup += array('Settings' => NULL);

		if (!$setup['Settings'] instanceof \Evoke\Core\Settings)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Settings');
		}

		$this->Settings = $setup['Settings'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Load all of the Evoke system settings.
	public function load()
	{
		/** Initialize constant values for the evoke system.
		    \verbatim
		    Default_Language    - Default Language for the site.
		    Development_Servers - List of Development servers (for logging etc.)
		    \endverbatim
		*/
		$this->Settings->set(
			'Constant',
			array('Default_Language'             => 'EN',
			      'Development_Servers'          => array(),
			      'Max_Length_Exception_Message' => 6000,
			      'SQL_Date_Time_Format'         => 'Y-m-d H:i:s'));

		/** Initialize the DB connections for the evoke system. This should be an
		 *  array of connection settings.  Each item in the array should be of the
		 *  form:       
		 \verbatim
		 DSN      - Data Source Name.
		 Options  - Options for passing to PDO.
		 Password - Password.
		 Username - Username.
		 \endverbatim
		 * No database connections by default.
		 */
		$this->Settings->set('DB', array());

		/** Initialize the Dir (directory) constants for the evoke system:
		    \verbatim
		    DB_Incoming   - Incoming DB files.
		    DB_Storage    - Stored DB files.
		    Site_Includes - Site include files.
		    Web_Root      - Website root.
		    \endverbatim
		*/
		$this->Settings->set(
			'Dir',
			array('DB_Incoming'   => '/srv/db/incoming',
			      'DB_Storage'    => '/srv/db/storage',
			      'Site_Includes' => '/srv/site_lib',
			      'Web_Root'      => '/srv/web'));

		/** Initialize the Email constants for the evoke system:
		    \verbatim
		    Administrator - Administrator Email
		    \endverbatim
		*/
		$this->Settings->set(
			'Email',
			array('Administrator' => ''));

		/** Initialize the file constants for the evoke system.
		    \verbatim
		    Log         - Log file.
		    Translation - Translations File.
		    \endverbatim
		*/
		$this->Settings->set(
			'File',
			array('Log'         => '/srv/log/log.txt',
			      'Translation' => '/srv/site_lib/evoke/translations.php'));

		/** Initialize the web locations for the evoke system.
		    \verbatim
		    DB_Incoming    - Incoming DB files.
		    DB_Storage     - Stored DB files.
		    Lang_Image_Dir - Languages images directory.
		    No_Photo       - No Photo Image File.
		    \endverbatim
		*/
		$this->Settings->set(
			'Web',
			array('DB_Incoming'     => '/db/incoming/',
			      'DB_Storage'     => '/db/storage/',
			      'Lang_Image_Dir' => 'images/languages/',
			      'No_Photo'       => 'images/No_Photo/No_Photo_%LANG%.png'));
	}
}
// EOF