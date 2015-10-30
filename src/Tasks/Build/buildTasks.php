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
	 * @param   array   $source  The media folder (an extension could have multiple)
	 * @param   string  $type    The extension name (e.g. mod_xy)
	 *
	 * @return  Media
	 */
	protected function buildMedia($source, $extName)
	{
		return new Media($source, $extName);
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
	 * Build a library
	 *
	 * @param   String  $libName       Name of the module
	 * @param   array   $params        Opt params
	 * @param   bool    $hasComponent  has the extension a component (then we need to build differnet)
	 *
	 * @return  Library
	 */
	protected function buildLibrary($libName, $params, $hasComponent)
	{
		return new Library($libName, $params, $hasComponent);
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
	 * Build a Module
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
	 * Build a Plugin
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

	/**
	 * Build a CBPlugin
	 *
	 * @param   String  $type    Type of the plugin
	 * @param   String  $name    Name of the plugin
	 * @param   array   $params  Opt params
	 *
	 * @return  Plugin
	 */
	protected function buildCBPlugin($type, $name, $params)
	{
		return new CBPlugin($type, $name, $params);
	}

	/**
	 * Build a Template
	 *
	 * @param   String  $templateName  Name of the template
	 * @param   array   $params   Opt params
	 *
	 * @return  Module
	 */
	protected function buildTemplate($templateName, $params)
	{
		return new Template($templateName, $params);
	}
}
