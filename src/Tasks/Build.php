<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       20.09.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JBuild\Tasks;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

/**
 * Class Build
 *
 * @package  JBuild\Tasks
 *
 * @since    1.0.1
 */
class Build extends JTask implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;
	use Build\buildTasks;

	/**
	 * @var array|null
	 */
	protected $params = null;

	/**
	 * Initialize Build Task
	 *
	 * @param   array  $params  Additional params
	 */
	public function __construct($params)
	{
		parent::__construct();

		$this->params = $params;
	}

	/**
	 * Build the package
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Building ' . $this->getConfig()->extension . " " . $this->getConfig()->version);

		if (!$this->checkFolders())
		{
			return false;
		}

		// Create directory
		$this->prepareDistDirectory();

		// Build component
		$this->buildComponent($this->params)->run();
	}

	/**
	 * Cleanup the given directory
	 *
	 * @param   string  $dir  The dir
	 *
	 * @return  void
	 */
	private function cleanup($dir)
	{
		// Clean building directory
		$this->_cleanDir($dir);
	}

	/**
	 * Prepare the directories
	 *
	 * @return  void
	 */
	private function prepareDistDirectory()
	{
		$build = $this->getconfig()->buildfolder;

		if (!file_exists($build))
		{
			$this->_mkdir($build);
		}

		$this->cleanup($build);
	}
}
