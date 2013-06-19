<?php
/**
 * Settings Loader
 *
 * @package Service
 */
namespace Evoke\Service;

/**
 * Settings Loader
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service
 */
class SettingsLoader
{
	/** 
	 * Settings object.
	 * @var Evoke\Service\SettingsIface
	 */
	protected $settings;

	/**
	 * Construct the settings loader.
	 *
	 * @param Evoke\Service\SettingsIface Settings object for loading the
	 *                                    settings into.
	 */
	public function __construct(SettingsIface $settings)
	{
		$this->settings = $settings;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Load all of the Evoke system settings.
	 */
	public function load()
	{
		/**
		 * Initialize constant values for the evoke system.
		 * <pre>
		 *  Default_Language    - Default Language for the site.
		 *  Development_Servers - List of Development servers (for logging etc.)
		 * </pre>
		 */
		$this->settings->set(
			'Constant',
			array('Default_Language'             => 'EN',
			      'Development_Servers'          => array(),
			      'Max_Length_Exception_Message' => 6000,
			      'SQL_Date_Time_Format'         => 'Y-m-d H:i:s'));

		/**
		 * Initialize the DB connections for the evoke system. This should be an
		 * array of connection settings.  Each item in the array should be of
		 * the form:       
		 * <pre>
		 *  Data_Source_Name - Data Source Name.
		 *  Options          - Options for passing to PDO.
		 *  Password         - Password.
		 *  Username         - Username.
		 * </pre>
		 *
		 * No database connections by default.
		 */
		$this->settings->set('DB', array());

		/**
		 * Initialize the Dir (directory) constants for the evoke system:
		 * <pre>
		 *  DB_Incoming   - Incoming DB files.
		 *  DB_Storage    - Stored DB files.
		 *  Site_Includes - Site include files.
		 *  Web_Root      - Website root.
		 * </pre>
		 */
		$this->settings->set(
			'Dir',
			array('DB_Incoming'   => '/srv/db/incoming',
			      'DB_Storage'    => '/srv/db/storage',
			      'Site_Includes' => '/srv/site_lib',
			      'Web_Root'      => '/srv/web'));

		/**
		 * Initialize the Email constants for the evoke system:
		 * <pre>
		 *  Administrator - Administrator Email
		 * </pre>
		*/
		$this->settings->set(
			'Email',
			array('Administrator' => ''));

		/**
		 * Initialize the file constants for the evoke system.
		 * <pre>
		 *  Log         - Log file.
		 *  Translation - Translations File.
		 * </pre>
		 */
		$this->settings->set(
			'File',
			array('Log'         => '/srv/log/log.txt',
			      'Translation' => '/srv/site_lib/evoke/translations.php'));

		/**
		 * Initialize the web locations for the evoke system.
		 * <pre>
		 *  DB_Incoming    - Incoming DB files.
		 *  DB_Storage     - Stored DB files.
		 *  Lang_Image_Dir - Languages images directory.
		 *  No_Photo       - No Photo Image File.
		 * </pre>
		 */
		$this->settings->set(
			'Web',
			array('DB_Incoming'     => '/db/incoming/',
			      'DB_Storage'     => '/db/storage/',
			      'Lang_Image_Dir' => 'images/languages/',
			      'No_Photo'       => 'images/No_Photo/No_Photo_%LANG%.png'));
	}
}
// EOF