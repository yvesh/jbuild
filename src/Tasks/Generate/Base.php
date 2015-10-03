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

use JBuild\Tasks\JTask;

/**
 * Generate base class - contains methods / data used in multiple generateion tasks
 *
 * @package  JBuild\Generate\Base
 *
 * @since    1.0.1
 */
class Base extends JTask implements TaskInterface
{
	use \Robo\Common\TaskIO;

	/**
	 * Returns true - should never be called on this
	 *
	 * @return  bool
	 */
	public function run()
	{
		return true;
	}
}
