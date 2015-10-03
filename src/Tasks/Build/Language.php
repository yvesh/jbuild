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
 * Class Language
 *
 * @package  JBuild\Tasks\Component
 *
 * @since    1.0.1
 */
class Language extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $ext = null;

	protected $target = null;

	protected $adminLangPath = null;

	protected $frontLangPath = null;

	protected $hasAdminLang = true;

	protected $hasFrontLang = true;

	/**
	 * Initialize Build Task
	 *
	 * @param   String  $extension  The extension (component, module etc.)
	 */
	public function __construct($extension)
	{
		parent::__construct();

		$this->adminLangPath = $this->getCodeBase() . "/administrator/language";
		$this->frontLangPath = $this->getCodeBase() . "/language";

		$this->ext = $extension;
	}

	/**
	 * Returns true
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say("Building language for " . $this->ext);

		$this->analyze();

		// Make sure we have the language folders in our target
		$this->prepareDirectories();

		if ($this->hasAdminLang)
		{
			$map = $this->copyLanguage("administrator/language");
		}

		if ($this->hasAdminLang)
		{
			$map = $this->copyLanguage("language");
		}

		$this->say("Done copying language files");

		// Can't use this - frontend and backend
		// $this->setResultFiles($map);

		return true;
	}

	/**
	 * Analyze the component structure
	 *
	 * @return  void
	 */
	private function analyze()
	{
		// We just check for english here
		if (!file_exists($this->adminLangPath . "/en-GB/en-GB." . $this->ext . ".ini"))
		{
			$this->say($this->adminLangPath . "/en-GB/en-GB." . $this->ext . ".ini");
			$this->hasAdminLang = false;
		}

		if (!file_exists($this->frontLangPath . "/en-GB/en-GB." . $this->ext . ".ini"))
		{
			$this->hasFrontLang = false;
		}
	}

	/**
	 * Prepare the directory structure
	 *
	 * @return  void
	 */
	private function prepareDirectories()
	{
		if ($this->hasAdminLang)
		{
			$this->_mkdir($this->_dest() . "/administrator/language");
		}

		if ($this->hasFrontLang)
		{
			$this->_mkdir($this->_dest() . "/language");
		}
	}

	/**
	 * Copy language files
	 *
	 * @param   string  $dir  - The directory (administrator/language or language)
	 *
	 * @return   array
	 */
	public function copyLanguage($dir)
	{
		// Equals administrator/language or language
		$path = $this->getCodeBase() . "/" . $dir;
		$files = array();

		$hdl = opendir($path);

		while ($entry = readdir($hdl))
		{
			$p = $path . "/" . $entry;

			// Which languages do we have
			// Ignore hidden files
			if (substr($entry, 0, 1) != '.')
			{
				// Language folders
				if (!is_file($p))
				{
					// Make folder at destination
					$this->_mkdir($this->_dest() . "/" . $dir . "/" . $entry);

					$fileHdl = opendir($p);

					while ($file = readdir($fileHdl))
					{
						// Only copy language files for this extension
						if (substr($file, 0, 1) != '.' && strpos($file, $this->ext))
						{
							$files[] = array($entry => $file);

							// Copy file
							$this->_copy($p . "/" . $file, $this->_dest() . "/" . $dir . "/" . $entry . "/" . $file);
						}
					}

					closedir($fileHdl);
				}
			}
		}

		closedir($hdl);

		return $files;
	}
}
