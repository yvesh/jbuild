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

use JBuild\Tasks\Build;
use JBuild\Tasks\Generate;
use JBuild\Tasks\Map;

trait loadTasks
{
	/**
	 * Map Task
	 *
	 * @param   String  $target  - The target directory
	 *
	 * @return  Map
	 */
	protected function taskMap($target)
	{
		return new Map($target);
	}

	/**
	 * The build task
	 *
	 * @param   array  $params  - Opt params
	 *
	 * @return  Build
	 */
	protected function taskBuild($params)
	{
		return new Build($params);
	}

	/**
	 * The generate task
	 *
	 * @param   array  $params  - Opt params
	 *
	 * @return  Build
	 */
	protected function taskGenerate($params)
	{
		return new Generate($params);
	}
}
