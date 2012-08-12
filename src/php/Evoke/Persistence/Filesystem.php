<?php
namespace Evoke\Persistence;

use FilesystemIterator,
	RecursiveDirectoryIterator,
	RecursiveIteratorIterator,
	RuntimeException;

/** File_System Wrapper class to enable exceptions on filesystem actions.
 *  File stream contexts are not dealt with by this wrapper.
 */
class Filesystem implements FilesystemIface
{   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * chmod a file or directory.
	 *
	 * @param string The filename or directory to chmod.
	 * @param int Octal integer to set the permissions to.
	 */
	public function chmod($filename, $mode=0777)
	{
		if (!chmod($filename, $mode))
		{
			throw new RuntimeException(
				'Unable to chmod: ' . var_export($filename, true));
		}
	}
   
	/**
	 * Copy file(s) recursively from source to destination.
	 *
	 * @param string The source of the file(s) to copy.
	 * @param string The desitination to copy the file(s) into.
	 * @param int    Octal integer for the directory permissions.
	 * @param @int   Octal integer for the file permisions.
	 */
	public function copy($from, $to, $dirMode=0770, $fileMode=0660)
	{  
		if (is_dir($from))
		{
			// Recursively copy the directory.
			$rDir = new RecursiveDirectoryIterator(
				$from, FilesystemIterator::SKIP_DOTS);
			$rIt = new RecursiveIteratorIterator(
				$rDir, RecursiveIteratorIterator::SELF_FIRST);

			// Make the directory - recursively creating the path required.
			if (!mkdir($to, $dirMode, true))
			{
				throw new RuntimeException(
					__METHOD__ . ' Unable to make destination directory: ' .
					var_export($to, true));
			}
	    
			foreach ($rIt as $file)
			{
				$src = $file->getPathname();
				$dest = $to . $rIt->getInnerIterator()->getSubPathname();
	    
				if (is_dir($src))
				{
					if (!mkdir($dest, $dirMode))
					{
						throw new RuntimeException(
							__METHOD__ . ' From: ' . $from . ' To: ' . $to .
							' Copying subdirectory from:' . $src . ' to: ' .
							$dest);
					}

					chmod($dest, $dirMode);
				}
				else
				{
					if (!copy($src, $dest))
					{
						throw new RuntimeException(
							__METHOD__ . ' From: ' . $from . ' To: ' . $to .
							' Copying file from: ' . $src . ' to: ' . $dest);
					}

					chmod($dest, $fileMode);
				}
			}
		}
		else
		{
			if (!copy($from, $to))
			{
				throw new RuntimeException(
					__METHOD__ . ' Copying single file from: ' . $from .
					' to: ' . $to);
			}

			chmod($to, $fileMode);
		}
	}

	/**
	 * Close an open file handle.
	 *
	 * @param mixed The file handle to be closed.
	 */
	public function fclose($handle)
	{
		if (!fclose($handle))
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to close file handle.');
		}
	}

	/**
	 * Whether the file exists.
	 *
	 * @param string The filename to check for existence.
	 *
	 * @return bool Whether the file exists.
	 */
	public function file_exists($filename)
	{
		return file_exists($filename);
	}

	/**
	 * Lock or unlock the file.
	 * 
	 * @param mixed The handle to the file.
	 * @param int   The lock to aquire or release.
	 */
	public function flock($handle, $lockType)
	{
		if (!flock($handle, $lockType))
		{
			if ($lockType === LOCK_SH)
			{
				throw new RuntimeException(
					__METHOD__ . ' Unable to lock file for reading.');
			}
			elseif ($lockType === LOCK_EX)
			{
				throw new RuntimeException(
					__METHOD__ . ' Unable to lock file for writing.');
			}
			else
			{
				throw new RuntimeException(
					__METHOD__ . ' Unable to unlock file.');
			}
		}
	}
   
	/**
	 * Open a file or url.
	 *
	 * @param string The location of the file or url.
	 * @param string The mode to open the file (read write etc.)
	 * @param bool   Whether to look in the include path.
	 *
	 * @return mixed The file handle of the opened file.
	 */
	public function fopen($filename, $mode, $useIncludePath=false)
	{
		$handle = fopen($filename, $mode, $useIncludePath);
      
		if ($handle === false)
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to open file: ' . $filename .
				' with mode: ' . $mode . ' use include path: ' .
				var_export($useIncludePath, true));
		}
	 
		return $handle;
	}

	/**
	 * Write to a file.
	 *
	 * @param mixed    The handle to the file.
	 * @param string   The data to write.
	 * @param int|null A maximum length of data to write.
	 *
	 * @return int The length of data written to the file.
	 */
	public function fwrite($handle, $string, $length=NULL)
	{
		if (isset($length))
		{
			$result = fwrite($handle, $string, $length);
		}
		else
		{
			$result = fwrite($handle, $string);
		}

		if ($result === false)
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to write to file with data: ' . $string);
		}

		return $result;
	}
   
	/**
	 * Whether the filename is a directory. No exceptions thrown, it is a query.
	 *
	 * @param string The filename to check.
	 *
	 * @return bool Whether the filename is a directory.
	 */
	public function is_dir($filename)
	{
		return is_dir($filename);
	}

	/**
	 * Make the director(y,ies) with the specified permissions.
	 *
	 * @param string The directory path to create.
	 * @param int    The permissions to create the path as.
	 * @param bool   Whether to create nested directories.
	 */
	public function mkdir($dir, $mode=0777, $recursive=false)
	{
		if (!mkdir($dir, $mode, $recursive))
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to make directory: ' . $dir);
		}
	}

	/**
	 * Rename a file or directory.
	 *
	 * @param string The original name.
	 * @param string The new name.
	 */
	public function rename($from, $to)
	{
		if (!rename($from, $to))
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to rename file from: ' . $from .
				' to: ' . $to);
		}
	}

	/**
	 * Delete a file or directory(and all of its file).
	 *
	 * @param string The path to the file or directory.
	 */
	public function unlink($filename)
	{
		if (is_dir($filename))
		{
			// Recursively unlink the directory.
			$rDir = new RecursiveDirectoryIterator(
				$filename, FilesystemIterator::SKIP_DOTS);
			$rIt = new RecursiveIteratorIterator(
				$rDir, RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($rIt as $file)
			{
				$f = $file->getPathname();
	    
				if ($file->isDir())
				{
					if (!rmdir($f))
					{
						throw new RuntimeException(
							__METHOD__ . ' Unlink: ' . $filename .
							' Unable to delete subdirectory: ' . $f);
					}
				}
				elseif (!unlink($f))
				{
					throw new RuntimeException(
						__METHOD__ . ' Unlink: ' . $filename .
						' Unable to delete file: ' . $f);
				}
			}

			if (!rmdir($filename))
			{
				throw new RuntimeException(
					__METHOD__ . ' Unable to remove directory: ' . $filename);
			}
		}
		elseif (!unlink($filename))
		{
			throw new RuntimeException(
				__METHOD__ . ' Unable to delete file: ' . $filename);
		}
	}
}
// EOF