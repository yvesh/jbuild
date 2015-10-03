<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.10.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace JBuild\Tasks\Build;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

use JBuild\Tasks\JTask;

/**
 * Build base - contains methods / data used in multiple build tasks
 *
 * @package  JBuild\Tasks\Build
 *
 * @since    1.0.1
 */
class Base extends JTask implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected static $mediaFiles = array();

	protected static $frontFiles = array();

	protected static $backendFiles = array();

	protected $resultFiles = array();

	/**
	 * Returns true
	 *
	 * @return  bool
	 */
	public function run()
	{
		return true;
	}

	/**
	 * Add files to array
	 *
	 * @param   string  $type       - Type (media, component etc.)
	 * @param   array   $fileArray  - File array
	 *
	 * @return bool
	 */
	public function addFiles($type, $fileArray)
	{
		$method = 'add' . ucfirst($type) . "Files";

		if (method_exists($this, $method))
		{
			$this->$method($fileArray);
		}
		else
		{
			$this->say('Missing method: ' . $method);
		}

		return true;
	}

	/**
	 * Adds Files / Folders to media array
	 *
	 * @param   array  $fileArray  Array of files / folders
	 *
	 * @return  void
	 */
	public function addMediaFiles($fileArray)
	{
		self::$mediaFiles = array_merge(self::$mediaFiles, $fileArray);
	}

	/**
	 * Adds Files / Folders to media array
	 *
	 * @param   array  $fileArray  Array of files / folders
	 *
	 * @return  void
	 */
	public function addFrontendFiles($fileArray)
	{
		self::$frontFiles = array_merge(self::$frontFiles, $fileArray);
	}

	/**
	 * Adds Files / Folders to media array
	 *
	 * @param   array  $fileArray  Array of files / folders
	 *
	 * @return  void
	 */
	public function addBackendFiles($fileArray)
	{
		self::$backendFiles = array_merge(self::$backendFiles, $fileArray);
	}


	/**
	 * Copies the files and maps them into an array
	 *
	 * @param   string  $path  - Folder path
	 * @param   string  $tar   - Target path
	 *
	 * @return array
	 */
	protected function copyTarget($path, $tar)
	{
		$map = array();
		$hdl = opendir($path);

		while ($entry = readdir($hdl))
		{
			$p = $path . "/" . $entry;

			// Ignore hidden files
			if (substr($entry, 0, 1) != '.')
			{
				if (is_file($p))
				{
					$map[] = array("file" => $entry);
					$this->_copy($p, $tar . "/" . $entry);
				}
				else
				{
					$map[] = array("folder" => $entry);
					$this->_copyDir($p, $tar . "/" . $entry);
				}
			}
		}

		closedir($hdl);

		return $map;
	}

	/**
	 * Get the result files
	 *
	 * @return  array
	 */
	public function getResultFiles()
	{
		return $this->resultFiles;
	}

	/**
	 * Set the result files
	 *
	 * @param   array  $resultFiles  - The result of the copying
	 *
	 * @return  void
	 */
	public function setResultFiles($resultFiles)
	{
		$this->resultFiles = $resultFiles;
	}
}
