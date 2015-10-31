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
 * Build Cli
 *
 * @package  JBuild\Tasks\Build
 *
 * @since    1.0.2
 */
class Cli extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $source = null;

	protected $target = null;

	protected $fileMap = null;

	/**
	 * Initialize Build Task
	 */
	public function __construct()
	{
		parent::__construct();

		$this->source = $this->getSourceFolder() . "/cli";
		$this->target = $this->getBuildFolder() . "/cli";
	}

	/**
	 * Runs the cli build tasks, just copying files currently
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say("Copying CLI files " . $this->source);

		if (!file_exists($this->source))
		{
			$this->say("Folder " . $this->source . " does not exist!");

			return true;
		}

		$this->prepareDirectory();

		$map = $this->copyTarget($this->source, $this->target);

		$this->setResultFiles($map);

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
}
