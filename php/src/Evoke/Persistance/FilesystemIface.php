<?php
namespace Evoke\Persistance;

interface FilesystemIface
{
	/** chmod a file or directory.
	 *  @param filename @string The filename or directory to chmod.
	 *  @mode @int Octal integer to set the permissions to.
	 */
	public function chmod($filename, $mode=0777);
   
	/** Copy file(s) recursively from source to destination.
	 *  @param from @string The source of the file(s) to copy.
	 *  @param to @string The desitination to copy the file(s) into.
	 *  @param dirMode @int Octal integer for the directory permissions.
	 *  @param fileMode @int Octal integer for the file permisions.
	 */
	public function copy($from, $to, $dirMode=0770, $fileMode=0660);

	/** Close an open file handle.
	 *  @param handle The file handle to be closed.
	 */
	public function fclose($handle);

	/** Whether the file exists.
	 *  @param filename @string The filename to check for existence.
	 *  @return @bool Whether the file exists.
	 */
	public function file_exists($filename);

	/** Lock or unlock the file.
	 *  @param handle The handle to the file.
	 *  @param lockType The lock to aquire or release.
	 */
	public function flock($handle, $lockType);
   
	/** Open a file or url.
	 *  @param filename The location of the file or url.
	 *  @param mode The mode to open the file (read write etc.)
	 *  @param useIncludePath Whether to look in the include path.
	 *  @returns The file handle of the opened file.
	 */
	public function fopen($filename, $mode, $useIncludePath=false);

	/** Write to a file.
	 *  @param handle The handle to the file.
	 *  @param string The data to write.
	 *  @param length A maximum length of data to write.
	 *  @returns The length of data written to the file.
	 */
	public function fwrite($handle, $string, $length=NULL);
   
	/** Whether the filename is a directory. No exceptions thrown, it is a query.
	 *  @param filename @string The filename to check.
	 *  @returns @bool Whether the filename is a directory.
	 */
	public function is_dir($filename);

	/** Make the director(y,ies) with the specified permissions.
	 *  @param dir @string The directory path to create.
	 *  @param mode @int The permissions to create the path as.
	 *  @param recursive @bool Whether to create nested directories.
	 */
	public function mkdir($dir, $mode=0777, $recursive=false);

	/** Rename a file or directory.
	 *  @param from @string The original name.
	 *  @param to @string The new name.
	 */
	public function rename($from, $to);

	/** Delete a file or directory(and all of its file).
	 *  @param filename @string The path to the file or directory.
	 */
	public function unlink($filename);
}
// EOF