<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       20.09.15
 *
 * @copyright  Copyright (C) 2008 - 2015 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JBuild;

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
		(new Tasks\Map($target))->run();
	}

	/**
	 * Fake into Joomla installation.
	 *
	 * @param   String   $target    The target joomla instance
	 *
	 * @return  void
	 */
	public function fake($target)
	{
		(new Tasks\Fake($target))->run();
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
		(new Tasks\Build($params))->run();
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
		(new Tasks\Generate($extensions))->run();
	}
}
