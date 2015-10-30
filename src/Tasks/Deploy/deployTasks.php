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

use JBuild\Tasks\Build\Component;
use JBuild\Tasks\Build\Media;

trait deployTasks
{
	/**
	 * Build extension
	 *
	 * @return  Zip
	 */
	protected function deployZip()
	{
		return new Zip;
	}

	/**
	 * Build extension
	 *
	 * @return  Package
	 */
	protected function deployPackage()
	{
		return new Package();
	}
}
