<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       20.09.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JBuild\Tasks\Generate;

trait generateTasks
{
	/**
	 * Generate a component skeleton
	 *
	 * @title   string  $title   The component name (e.g. com_component)
	 * @param   array   $params  Opt params
	 *
	 * @return
	 */
	protected function generateComponent($title, $params = array())
	{
		return null;
	}

	/**
	 * Generate a module skeleton
	 *
	 * @title   string  $title   The component name (e.g. com_component)
	 * @param   array   $params  Opt params
	 *
	 * @return
	 */
	protected function generateModule($title, $params = array())
	{
		return null;
	}

	/**
	 * Generate a plugin skeleton
	 *
	 * @title   string  $title   The component name (e.g. com_component)
	 * @param   array   $params  Opt params
	 *
	 * @return
	 */
	protected  function generatePlugin($title, $params = array())
	{
		return null;
	}
}
