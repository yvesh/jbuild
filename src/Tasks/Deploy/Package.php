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
 * Deploy project as Package file
 *
 * @since  1.0.2
 */
class Package extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	protected $target = null;

	private $hasComponent = true;

	private $hasModules = true;

	private $hasPlugins = true;

	private $hasLibraries = true;

	private $hasCBPlugins = true;

	/**
	 * Initialize Build Task
	 */
	public function __construct()
	{
		parent::__construct();

		$this->target = JPATH_BASE . "/dist/pkg-" . $this->_ext() . "-" . $this->getConfig()->version . ".zip";

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
		// TODO improve DRY!
		$this->say('Creating package ' . $this->getConfig()->extension . " " . $this->getConfig()->version);

		// Start getting single archives
		if (file_exists(JPATH_BASE . '/dist/tmp'))
		{
			$this->_deleteDir(JPATH_BASE . '/dist/tmp');
		}

		$this->_mkdir(JPATH_BASE . '/dist/tmp/zips');

		$this->analyze();

		if ($this->hasComponent)
		{
			$comZip = new \ZipArchive(JPATH_BASE . "/dist/tmp", \ZipArchive::CREATE);

			$comZip->open(JPATH_BASE . '/dist/tmp/zips/com_' . $this->_ext() . '.zip', \ZipArchive::CREATE);

			// Process the files to zip
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/components"), \RecursiveIteratorIterator::SELF_FIRST)
			         as $subfolder)
			{
				$this->addFiles($subfolder, $comZip);
			}

			// Admin component
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/administrator/components"), \RecursiveIteratorIterator::SELF_FIRST)
			         as $subfolder)
			{
				$this->addFiles($subfolder, $comZip);
			}

			// Admin language
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/administrator/language"), \RecursiveIteratorIterator::SELF_FIRST)
			         as $subfolder)
			{
				$this->addFiles($subfolder, $comZip);
			}

			// Language
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->current . "/language"), \RecursiveIteratorIterator::SELF_FIRST)
			         as $subfolder)
			{
				$this->addFiles($subfolder, $comZip);
			}

			$comZip->addFile($this->current . "/" . $this->_ext() . ".xml", $this->_ext() . ".xml");

			// Close the zip archive
			$comZip->close();
		}

		if ($this->hasModules)
		{
			$path = $this->current . "/modules";

			// Get every module
			$hdl = opendir($path);

			while ($entry = readdir($hdl))
			{
				// Only folders
				$p = $path . "/" . $entry;

				if (substr($entry, 0, 1) == '.')
				{
					continue;
				}

				if (!is_file($p))
				{
					$this->say("Packaging Module " . $entry);

					// Package file
					$zip = new \ZipArchive(JPATH_BASE . "/dist/tmp", \ZipArchive::CREATE);

					$zip->open(JPATH_BASE . '/dist/tmp/zips/' . $entry . '.zip', \ZipArchive::CREATE);

					$this->say("Module " . $p);

					// Process the files to zip
					foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($p), \RecursiveIteratorIterator::SELF_FIRST)
					         as $subfolder)
					{
						$this->addFiles($subfolder, $zip, $p);
					}

					// Close the zip archive
					$zip->close();
				}
			}

			closedir($hdl);
		}

		if ($this->hasPlugins)
		{
			$path = $this->current . "/plugins";

			// Get every plugin
			$hdl = opendir($path);

			while ($entry = readdir($hdl))
			{
				// Only folders
				$p = $path . "/" . $entry;

				if (substr($entry, 0, 1) == '.')
				{
					continue;
				}

				if (!is_file($p))
				{
					// Plugin type folder
					$type = $entry;

					$hdl2 = opendir($p);

					while ($plugin = readdir($hdl2))
					{
						if (substr($plugin, 0, 1) == '.')
						{
							continue;
						}

						// Only folders
						$p2 = $path . "/" . $type . "/" . $plugin;

						$this->say("P " . $p2);

						if (!is_file($p2))
						{
							$plg = "plg_" . $type . "_" . $plugin;

							$this->say("Packaging Plugin " . $plg);

							// Package file
							$zip = new \ZipArchive(JPATH_BASE . "/dist/tmp", \ZipArchive::CREATE);

							$zip->open(JPATH_BASE . '/dist/tmp/zips/' . $plg . '.zip', \ZipArchive::CREATE);

							$this->say("Plugin " . $p2);

							// Process the files to zip
							foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($p2), \RecursiveIteratorIterator::SELF_FIRST)
							         as $subfolder)
							{
								$this->addFiles($subfolder, $zip);
							}

							// Close the zip archive
							$zip->close();
						}
					}

					closedir($hdl2);
				}
			}

			closedir($hdl);
		}

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

		$this->zip->addFile($this->_source() . "/pkg_" . $this->_ext() . ".xml",  "pkg_" . $this->_ext() . ".xml");

		// Close the zip archive
		$this->zip->close();

		$this->_symlink($this->target, JPATH_BASE . "/dist/pkg-" . $this->_ext() . "-current.zip");

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

	/**
	 * Add files
	 *
	 * @param    string       $subfolder  The subfolder
	 * @param    \ZipArchive  $zip        The zip object
	 * @param    string       $path       Optional path
	 *
	 * @return  void
	 */
	private function addFiles($subfolder, $zip, $path = null)
	{
		if (!$path)
		{
			$path = $this->current;
		}

		if ($subfolder->isFile())
		{
			// Set all separators to forward slashes for comparison
			$usefolder = str_replace('\\', '/', $subfolder->getPath());

			// Drop the folder part as we don't want them added to archive
			$addpath = str_ireplace($path, '', $usefolder);

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
