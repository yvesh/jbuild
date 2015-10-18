<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.10.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace JBuild\Tasks\Deploy;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

use JBuild\Tasks\JTask;

/**
 * Deployment base - contains methods / data used in multiple build tasks
 *
 * @since  1.0.2
 */
class Base extends JTask implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	/**
	 * Returns true
	 *
	 * @return  bool
	 */
	public function run()
	{
		return true;
	}
}
