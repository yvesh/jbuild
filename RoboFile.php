<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       20.09.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', __DIR__);
}

// PSR-4 Autoload by composer
require_once JPATH_BASE . '/vendor/autoload.php';

/**
 * Standalone RoboFile for JBuild
 *
 * @since  5.3
 */
class RoboFile extends \Robo\Tasks
{
	/**
	 * Initialize Robo
	 */
	public function __construct()
	{
		$this->stopOnFail(true);
	}

	/**
	 * Map into Joomla installation.
	 *
	 * @param   String   $target    The target joomla instance
	 *
	 * @return  void
	 */
	public function map($target)
	{
		(new \JBuild\Tasks\Map($target))->run();
	}

	/**
	 * Build the joomla extension package
	 *
	 * @param   array  $params  Additional params
	 *
	 * @return  void
	 */
	public function build($params = ['dev' => false])
	{
		(new \JBuild\Tasks\Build($params))->run();
	}

	/**
	 * Generate an extension skeleton - not implemented yet
	 *
	 * @param   array  $extensions  Extensions to build (com_xy, mod_xy)
	 *
	 * @return  void
	 */
	public function generate($extensions)
	{
		(new \JBuild\Tasks\Generate($extensions))->run();
	}
}
