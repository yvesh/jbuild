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

use JBuild\Tasks\Build\Component;
use JBuild\Tasks\Build\Media;

trait buildTasks
{
	/**
	 * Build extension
	 *
	 * @param   array  $params  - Opt params
	 *
	 * @return  Extension
	 */
	protected function buildExtension($params)
	{
		return new Extension($params);
	}

	/**
	 * Build component
	 *
	 * @param   array  $params  - Opt params
	 *
	 * @return  Component
	 */
	protected function buildComponent($params)
	{
		return new Component($params);
	}

	/**
	 * Build media folder
	 *
	 * @param   array  $source  - The media folder (an extension could have multiple)
	 *
	 * @return  Media
	 */
	protected function buildMedia($source)
	{
		return new Media($source);
	}

	/**
	 * Build media folder
	 *
	 * @param   string  $extension  - The extension (not the whoe, but mod_xy or plg_)
	 *
	 * @return  Language
	 */
	protected  function buildLanguage($extension)
	{
		return new Language($extension);
	}

	/**
	 * Build library folder
	 *
	 * @return  Library
	 */
	protected function buildLibrary()
	{
		return new Library;
	}

	/**
	 * Build cli folder
	 *
	 * @return  Cli
	 */
	protected function buildCli()
	{
		return new Cli;
	}

	/**
	 * Build Module
	 *
	 * @param   String  $modName  Name of the module
	 * @param   array   $params   Opt params
	 *
	 * @return  Module
	 */
	protected function buildModule($modName, $params)
	{
		return new Module($modName, $params);
	}

	/**
	 * Build Plugin
	 *
	 * @param   String  $type    Type of the plugin
	 * @param   String  $name    Name of the plugin
	 * @param   array   $params  Opt params
	 *
	 * @return  Plugin
	 */
	protected function buildPlugin($type, $name, $params)
	{
		return new Plugin($type, $name, $params);
	}
}
