<?php
namespace Evoke\Persistance;

interface FilesystemIface
{
	/**
	 * chmod a file or directory.
	 *
	 * @param string The filename or directory to chmod.
	 * @param int Octal integer to set the permissions to.
	 */
	public function chmod($filename, $mode=0777);
   
	/**
	 * Copy file(s) recursively from source to destination.
	 *
	 * @param string The source of the file(s) to copy.
	 * @param string The desitination to copy the file(s) into.
	 * @param int    Octal integer for the directory permissions.
	 * @param @int   Octal integer for the file permisions.
	 */
	public function copy($from, $to, $dirMode=0770, $fileMode=0660);

	/**
	 * Close an open file handle.
	 *
	 * @param mixed The file handle to be closed.
	 */
	public function fclose($handle);

	/**
	 * Whether the file exists.
	 *
	 * @param string The filename to check for existence.
	 *
	 * @return bool Whether the file exists.
	 */
	public function file_exists($filename);

	/**
	 * Lock or unlock the file.
	 * 
	 * @param mixed The handle to the file.
	 * @param int   The lock to aquire or release.
	 */
	public function flock($handle, $lockType);
   
	/**
	 * Open a file or url.
	 *
	 * @param string The location of the file or url.
	 * @param string The mode to open the file (read write etc.)
	 * @param bool   Whether to look in the include path.
	 *
	 * @return mixed The file handle of the opened file.
	 */
	public function fopen($filename, $mode, $useIncludePath=false);

	/**
	 * Write to a file.
	 *
	 * @param mixed    The handle to the file.
	 * @param string   The data to write.
	 * @param int|null A maximum length of data to write.
	 *
	 * @return int The length of data written to the file.
	 */
	public function fwrite($handle, $string, $length=NULL);
   
	/**
	 * Whether the filename is a directory. No exceptions thrown, it is a query.
	 *
	 * @param string The filename to check.
	 *
	 * @return bool Whether the filename is a directory.
	 */
	public function is_dir($filename);

	/**
	 * Make the director(y,ies) with the specified permissions.
	 *
	 * @param string The directory path to create.
	 * @param int    The permissions to create the path as.
	 * @param bool   Whether to create nested directories.
	 */
	public function mkdir($dir, $mode=0777, $recursive=false);

	/**
	 * Rename a file or directory.
	 *
	 * @param string The original name.
	 * @param string The new name.
	 */
	public function rename($from, $to);

	/**
	 * Delete a file or directory(and all of its file).
	 *
	 * @param string The path to the file or directory.
	 */
	public function unlink($filename);
}
// EOF