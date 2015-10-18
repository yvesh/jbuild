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
 * Deploy project as Zip
 *
 * @since  1.0.2
 */
class Zip extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $target = null;

	private $zip = null;

	/**
	 * Initialize Build Task
	 */
	public function __construct()
	{
		parent::__construct();

		$this->target = JPATH_BASE . "/dist/" . $this->_ext() . "-" . $this->getConfig()->version . ".zip";

		$this->zip = new \ZipArchive($this->target, \ZipArchive::CREATE);
	}

	/**
	 * Build the package
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Zipping ' . $this->getConfig()->extension . " " . $this->getConfig()->version);

		// Instantiate the zip archive
		$this->zip->open($this->target, \ZipArchive::CREATE);

		// Process the files to zip
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->_dest()), \RecursiveIteratorIterator::SELF_FIRST) as $subfolder)
		{
			if ($subfolder->isFile())
			{
				// Set all separators to forward slashes for comparison
				$usefolder = str_replace('\\', '/', $subfolder->getPath());

				// Drop the folder part as we don't want them added to archive
				$addpath = str_ireplace($this->_dest(), '', $usefolder);

				// Remove preceding slash
				$findfirst = strpos($addpath, '/');

				if ($findfirst == 0 && $findfirst !== false)
				{
					$addpath = substr($addpath, 1);
				}

				if (strlen($addpath) > 0 || empty($addpath))
				{
					$addpath .= '/';
				}

				$options = array('add_path' => $addpath, 'remove_all_path' => true);
				$this->zip->addGlob($usefolder . '/*.*', GLOB_BRACE, $options);
			}
		}

		// Close the zip archive
		$this->zip->close();

		return true;
	}
}
