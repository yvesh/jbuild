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
class Package extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $target = null;

	/**
	 * Initialize Build Task
	 */
	public function __construct()
	{
		parent::__construct();

		$this->target = JPATH_BASE . "/dist/package-" . $this->_ext() . "-" . $this->getConfig()->version . ".zip";

		$this->current = JPATH_BASE . "/dist/current";

		$this->zip = new \ZipArchive($this->target, \ZipArchive::CREATE);
	}

	/**
	 * Build the package
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Creating package ' . $this->getConfig()->extension . " " . $this->getConfig()->version);

		// Start getting single archives
		$comZip = new \ZipArchive(JPATH_BASE . "/dist/tmp", \ZipArchive::CREATE);

		if (file_exists(JPATH_BASE . '/dist/tmp'))
		{
			$this->_deleteDir(JPATH_BASE . '/dist/tmp');
		}

		$this->_mkdir(JPATH_BASE . '/dist/tmp/zips');

		$comZip->open(JPATH_BASE . '/dist/tmp/zips/com_weblinks.zip', \ZipArchive::CREATE);

		// Process the files to zip
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/components"), \RecursiveIteratorIterator::SELF_FIRST)
		         as $subfolder)
		{
			$this->addFiles($subfolder, $comZip);
		}

		// Process the files to zip
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/administrator/components"), \RecursiveIteratorIterator::SELF_FIRST)
		         as $subfolder)
		{
			$this->addFiles($subfolder, $comZip);
		}

		$comZip->addFile($this->current . "/weblinks.xml", "weblinks.xml");


		// Close the zip archive
		$comZip->close();

		// Instantiate the zip archive
		$this->zip->open($this->target, \ZipArchive::CREATE);

		// Process the files to zip
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(JPATH_BASE . '/dist/tmp/zips'), \RecursiveIteratorIterator::SELF_FIRST) as $subfolder)
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

	/**
	 * Analyze the extension structure
	 *
	 * @return  void
	 */
	private function analyze()
	{
		// Check if we have component, module, plugin etc.
		if (!file_exists($this->current . "/administrator/components/com_" . $this->_ext())
			&& !file_exists($this->current . "/components/com_" . $this->_ext())
		)
		{
			$this->say("Extension has no component");
			$this->hasComponent = false;
		}

		if (!file_exists($this->current . "/modules"))
		{
			$this->hasModules = false;
		}

		if (!file_exists($this->current . "/plugins"))
		{
			$this->hasPlugins = false;
		}

		if (!file_exists($this->current . "/libraries"))
		{
			$this->hasLibraries = false;
		}

		if (!file_exists($this->current . "/components/com_comprofiler"))
		{
			$this->hasCBPlugins = false;
		}
	}

	private function addFiles($subfolder, $zip)
	{
		if ($subfolder->isFile())
		{
			// Set all separators to forward slashes for comparison
			$usefolder = str_replace('\\', '/', $subfolder->getPath());

			// Drop the folder part as we don't want them added to archive
			$addpath = str_ireplace($this->current, '', $usefolder);

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
			$zip->addGlob($usefolder . '/*.*', GLOB_BRACE, $options);
		}
	}
}
