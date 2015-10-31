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
 * Map extension into an Joomla installation
 *
 * @package  JBuild\Tasks\Component
 *
 * @since    1.0.0
 */
class Fake extends JTask implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	/**
	 * @var   null|String  $target  - The target folder
	 */
	protected $target = null;

	/**
	 * Initialize Map Task
	 *
	 * @param   String  $target  The target directory
	 */
	public function __construct($target)
	{
		parent::__construct();

		$this->target = $target;
	}

	/**
	 * Maps all parts of an extension into a Joomla! installation
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Task ' . __CLASS__  . " was called with " . $this->target);

		return true;
	}
}
