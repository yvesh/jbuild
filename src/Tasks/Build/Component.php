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
 * Class Component
 *
 * @package  JBuild\Tasks\Build
 *
 * @since    1.0.1
 */
class Component extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;
	use buildTasks;

	protected $adminPath = null;

	protected $frontPath = null;

	protected $hasAdmin = true;

	protected $hasFront = true;

	protected $hasCli = true;

	/**
	 * Initialize Build Task
	 *
	 * @param   String  $params  The target directory
	 */
	public function __construct($params)
	{
		parent::__construct();

		// Reset files - > new component
		$this->resetFiles();

		$this->adminPath = $this->getSourceFolder() . "/administrator/components/com_" . $this->_ext();
		$this->frontPath = $this->getSourceFolder() . "/components/com_" . $this->_ext();
	}

	/**
	 * Build the package
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Building component');

		// Analyize extension structure
		$this->analyze();

		// Prepare directories
		$this->prepareDirectories();

		if ($this->hasAdmin)
		{
			$adminFiles = $this->copyTarget($this->adminPath, $this->_dest() . "/administrator/components/com_" . $this->_ext());

			$this->addFiles('backend', $adminFiles);
		}

		if ($this->hasFront)
		{
			$frontendFiles = $this->copyTarget($this->frontPath, $this->_dest() . "/components/com_" . $this->_ext());

			$this->addFiles('frontend', $frontendFiles);
		}

		// Build media (relative path)
		$media = $this->buildMedia("media/com_" . $this->_ext(), 'com_' . $this->_ext());
		$media->run();

		$this->addFiles('media', $media->getResultFiles());

		// Build language files for the component
		$language = $this->buildLanguage("com_" . $this->_ext());
		$language->run();

		// Cli
		if ($this->hasCli)
		{
			$this->buildCli()->run();
		}

		// Update XML and script.php
		$this->createInstaller();

		// Copy XML and script.php to root
		$adminFolder = $this->_dest() . "/administrator/components/com_" . $this->_ext();
		$xmlFile     = $adminFolder . "/" . $this->_ext() . ".xml";
		$scriptFile  = $adminFolder . "/script.php";

		$this->_copy($xmlFile, $this->_dest() . "/" . $this->_ext() . ".xml");
		$this->_copy($scriptFile, $this->_dest() . "/script.php");

		if (file_exists($scriptFile))
		{
			$this->_copy($scriptFile, $this->_dest() . "/script.php");
		}

		// Copy Readme
		if (JPATH_BASE . "/docs/README.md")
		{
			$this->_copy(JPATH_BASE . "/docs/README.md", $this->_dest() . "/README");
		}

		return true;
	}

	/**
	 * Analyze the component structure
	 *
	 * @return  void
	 */
	private function analyze()
	{
		if (!file_exists($this->adminPath))
		{
			$this->hasAdmin = false;
		}

		if (!file_exists($this->frontPath))
		{
			$this->hasFront = false;
		}

		if (!file_exists($this->sourceFolder . "/cli"))
		{
			$this->hasCli = false;
		}
	}

	/**
	 * Prepare the directory structure
	 *
	 * @return  void
	 */
	private function prepareDirectories()
	{
		if ($this->hasAdmin)
		{
			$this->_mkdir($this->_dest() . "/administrator/components/com_" . $this->_ext());
		}

		if ($this->hasFront)
		{
			$this->_mkdir($this->_dest() . "/components/com_" . $this->_ext());
		}
	}

	/**
	 * Generate the installer xml file for the component
	 *
	 * @return  void
	 */
	private function createInstaller()
	{
		$this->say("Creating component installer");

		$adminFolder = $this->_dest() . "/administrator/components/com_" . $this->_ext();
		$xmlFile     = $adminFolder . "/" . $this->_ext() . ".xml";
		$scriptFile  = $adminFolder . "/script.php";
		$helperFile  = $adminFolder . "/helpers/defines.php";

		// Version & Date Replace
		$this->taskReplaceInFile($xmlFile)
			->from(array('##DATE##', '##YEAR##', '##VERSION##'))
			->to(array($this->getDate(), date('Y'), $this->getConfig()->version))
			->run();

		if (file_exists($scriptFile))
		{
			$this->taskReplaceInFile($scriptFile)
				->from(array('##DATE##', '##YEAR##', '##VERSION##'))
				->to(array($this->getDate(), date('Y'), $this->getConfig()->version))
				->run();
		}

		if (file_exists($helperFile))
		{
			$this->taskReplaceInFile($helperFile)
				->from(array('##DATE##', '##YEAR##', '##VERSION##'))
				->to(array($this->getDate(), date('Y'), $this->getConfig()->version))
				->run();
		}

		// Files and folders
		if ($this->hasAdmin)
		{
			$f = $this->generateFileList($this->getFiles('backend'));

			$this->taskReplaceInFile($xmlFile)
				->from('##BACKEND_COMPONENT_FILES##')
				->to($f)
				->run();

			// Language files
			$f = $this->generateLanguageFileList($this->getFiles('backendLanguage'));

			$this->taskReplaceInFile($xmlFile)
				->from('##BACKEND_LANGUAGE_FILES##')
				->to($f)
				->run();
		}

		if ($this->hasFront)
		{
			$f = $this->generateFileList($this->getFiles('frontend'));

			$this->taskReplaceInFile($xmlFile)
				->from('##FRONTEND_COMPONENT_FILES##')
				->to($f)
				->run();

			// Language files
			$f = $this->generateLanguageFileList($this->getFiles('frontendLanguage'));

			$this->taskReplaceInFile($xmlFile)
				->from('##FRONTEND_LANGUAGE_FILES##')
				->to($f)
				->run();
		}

		// Media files
		$f = $this->generateFileList($this->getFiles('media'));

		$this->taskReplaceInFile($xmlFile)
			->from('##MEDIA_FILES##')
			->to($f)
			->run();
	}
}
