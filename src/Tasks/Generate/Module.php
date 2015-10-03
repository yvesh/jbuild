<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.10.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JBuild\Tasks\Generate;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

/**
 * Generate a module skeleton
 *
 * @package  JBuild\Tasks\Generate
 *
 * @since    0.9.0
 */
class Module extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $adminPath = null;

	protected $frontPath = null;

	protected $hasAdmin = true;

	protected $hasFront = true;

	/**
	 * Initialize Build Task
	 *
	 * @param   String  $params  The target directory
	 */
	public function __construct($params)
	{
		parent::__construct();

		$this->adminPath = $this->getCodeBase() . "/administrator/components/com_" . $this->_ext();
		$this->frontPath = $this->getCodeBase() . "/components/com_" . $this->_ext();
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
		$media = $this->buildMedia("media/com_" . $this->_ext());
		$media->run();

		$this->addFiles('media', $media->getResultFiles());

		$language = $this->buildLanguage("com_matukio");
		$language->run();

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
}
