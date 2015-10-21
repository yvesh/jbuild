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
 * Build Library
 *
 * @package  JBuild\Tasks\Build
 *
 * @since    1.0.2
 */
class Library extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;
	use buildTasks;

	protected $source = null;

	protected $target = null;

	protected $libName = null;

	/**
	 * Initialize Build Task
	 *
	 * @param   String  $libName  Name of the library to build
	 * @param   String  $params   Optional params
	 */
	public function __construct($libName, $params)
	{
		parent::__construct();

		// Reset files - > new lib
		$this->resetFiles();

		$this->libName = $libName;

		$this->source = $this->_source() . "/libraries/" . $libName;
		$this->target = $this->_dest() . "/libraries/" . $libName;
	}

	/**
	 * Runs the library build tasks, just copying files currently
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say("Building library folder " . $this->libName);

		if (!file_exists($this->source))
		{
			$this->say("Folder " . $this->source . " does not exist!");

			return true;
		}

		$this->prepareDirectory();

		// Libaries are problematic.. we have libraries/name/libraries/name in the end for the build script
		$files = $this->copyTarget($this->source, $this->target . "/libraries/" . $this->libName);

		$lib = $this->libName;

		// Workaround for libraries without lib_
		if (substr($this->libName, 0, 3) != "lib")
		{
			$lib = 'lib_' . $this->libName;
		}

		// Build media (relative path)
		$media = $this->buildMedia("media/" . $lib, $lib);
		$media->run();

		$this->addFiles('media', $media->getResultFiles());

		// Build language files for the component
		$language = $this->buildLanguage($lib);
		$language->run();

		// Copy XML
		$this->createInstaller($files);

		$xmlFile = $this->target . "/libraries/" . $this->libName . "/" . $this->libName . ".xml";

		// Copy XML to library root
		$this->_copy($xmlFile, $this->target . "/" . $this->libName . ".xml");

		return true;
	}

	/**
	 * Prepare the directory structure
	 *
	 * @return  void
	 */
	private function prepareDirectory()
	{
		$this->_mkdir($this->target);
	}

	/**
	 * Generate the installer xml file for the library
	 *
	 * @param   array  $files  The library files
	 *
	 * @return  void
	 */
	private function createInstaller($files)
	{
		$this->say("Creating library installer");

		$xmlFile = $this->target . "/libraries/" . $this->libName . "/" . $this->libName . ".xml";

		// Version & Date Replace
		$this->taskReplaceInFile($xmlFile)
			->from(array('@@DATE@@', '##YEAR##', '##VERSION##'))
			->to(array($this->getDate(), date('Y'), $this->getConfig()->version))
			->run();

		// Files and folders
		$f = $this->generateFileList($files);

		$this->taskReplaceInFile($xmlFile)
			->from('##LIBRARYFILES##')
			->to($f)
			->run();

		// Language files
		$f = $this->generateLanguageFileList($this->getFiles('frontendLanguage'));

		$this->taskReplaceInFile($xmlFile)
			->from('##FRONTENDLANGUAGEFILES##')
			->to($f)
			->run();

		// Media files
		$f = $this->generateFileList($this->getFiles('media'));

		$this->taskReplaceInFile($xmlFile)
			->from('##MEDIAPACKAGEFILES##')
			->to($f)
			->run();
	}
}
