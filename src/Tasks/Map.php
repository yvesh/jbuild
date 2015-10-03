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

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

/**
 * Map extension into an Joomla installation
 *
 * @package  JBuild\Tasks\Component
 *
 * @since    1.0.0
 */
class Map extends JTask implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;

	/**
	 * @var   null|String  $target  - The target folder
	 */
	protected $target = null;

	/**
	 * @var   array  $adminFolders  - Admin folders
	 */
	protected $adminFolders = array('components', 'language', 'modules');

	/**
	 * Initialize Map Task
	 *
	 * @param   String  $target  The target directory
	 */
	public function __construct($target)
	{
		parent::__construct();

		$this->target = $target;
	}

	/**
	 * Maps all parts of an extension into a Joomla! installation
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Mapping ' . $this->getConfig()->extension . " to " . $this->target);
		$this->say('OS: ' . $this->getOs() . " | Basedir: " . $this->getCodeBase());

		if (!$this->checkFolders())
		{
			return false;
		}

		$dirHandle = opendir($this->getCodeBase());

		// Get all main dirs
		while (false !== ($element = readdir($dirHandle)))
		{
			if ($element == "." || $element == "..")
			{
				continue;
			}

			$method = 'process' . ucfirst($element);

			if (method_exists($this, $method))
			{
				$this->$method($this->getCodeBase() . "/" . $element, $this->target);
			}
			else
			{
				$this->say('Missing method: ' . $method);
			}
		}

		closedir($dirHandle);

		// Get lib_compojoom (TODO move into separate file)
		$libDir = dirname(dirname($this->getCodeBase())) . "/lib_compojoom/source";

		$libHandle = opendir($libDir);

		if ($libHandle === false)
		{
			$this->printTaskError('Can not open ' . $libDir . ' for parsing');

			return false;
		}

		$this->say("Syncing library " . $libDir);

		while (false !== ($element = readdir($libHandle)))
		{
			if ($element == "." || $element == "..")
			{
				continue;
			}

			$method = 'process' . ucfirst($element);

			if (method_exists($this, $method))
			{
				$this->$method($libDir . "/" . $element, $this->target);
			}
			else
			{
				$this->say('Missing method: ' . $method);
			}
		}

		closedir($libHandle);

		$this->say("Finished symlinking into Joomla!");

		return true;
	}

	/**
	 * Process Administrator files
	 *
	 * @return  void
	 */
	private function processAdministrator()
	{
		$this->processComponents($this->getCodeBase() . '/administrator/components', $this->target . '/administrator');

		$this->processLanguage($this->getCodeBase() . '/administrator/language', $this->target . '/administrator');

		$this->processModules($this->getCodeBase() . '/administrator/modules', $this->target . '/administrator/modules');
	}


	/**
	 * Process components
	 *
	 * @param   String  $src  - The source
	 * @param   String  $to   - The target
	 *
	 * @return  void
	 */
	private function processComponents($src, $to)
	{
		// Component directory
		if (is_dir($src))
		{
			$dirHandle = opendir($src);

			while (false !== ($element = readdir($dirHandle)))
			{
				if (false !== strpos($element, 'com_'))
				{
					$this->symlink($src . '/' . $element, $to . '/components/' . $element);
				}
			}
		}
	}

	/**
	 * Process components
	 *
	 * @param   String  $toDir     - The target
	 *
	 * @return  void
	 */
	private function processLanguage($toDir)
	{
		if (is_dir($this->getCodeBase()))
		{
			$dirHandle = opendir($this->getCodeBase());

			while (false !== ($element = readdir($dirHandle)))
			{
				if ($element != "." && $element != "..")
				{
					if (is_dir($this->getCodeBase() . "/" . $element))
					{
						$langDirHandle = opendir($this->getCodeBase() . '/' . $element);

						while (false !== ($file = readdir($langDirHandle)))
						{
							if (is_file($this->getCodeBase() . '/' . $element . '/' . $file))
							{
								$this->say($file);
								$this->symlink($this->getCodeBase() . '/' . $element . '/' . $file, $toDir . '/language/' . $element . '/' . $file);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Process Libraries
	 *
	 * @param   String  $toDir  The target
	 *
	 * @return  void
	 */
	private function processLibraries($toDir)
	{
		$this->mapDir('libraries', $this->getCodeBase(), $toDir);
	}

	/**
	 * Process media
	 *
	 * @param   String  $toDir  The target
	 *
	 * @return  void
	 */
	private function processMedia($toDir)
	{
		$this->mapDir('media', $this->getCodeBase(), $toDir);
	}

	/**
	 * Process Cli
	 *
	 * @param   String  $toDir  - The target
	 *
	 * @return  void
	 */
	private function processCli($toDir)
	{
		$this->mapDir('cli', $this->getCodeBase(), $toDir);
	}

	/**
	 * Process Module
	 *
	 * @param   String  $toDir  - The target
	 *
	 * @return  void
	 */
	private function processModules($toDir)
	{
		$this->mapDir('modules', $this->getCodeBase(), $toDir);
	}

	/**
	 * Process Plugins
	 *
	 * @param   String  $toDir  - The target
	 *
	 * @return  void
	 */
	private function processPlugins($toDir)
	{
		if (is_dir($this->getCodeBase()))
		{
			$dirHandle = opendir($this->getCodeBase());

			// Plugin folder
			while (false !== ($element = readdir($dirHandle)))
			{
				if ($element != "." && $element != "..")
				{
					$plgDirHandle = opendir($this->getCodeBase() . "/" . $element);

					while (false !== ($plugin = readdir($plgDirHandle)))
					{
						if ($plugin != "." && $plugin != "..")
						{
							if (is_dir($this->getCodeBase() . "/" . $element . "/" . $plugin))
							{
								$this->symlink(
									$this->getCodeBase() . '/' . $element . "/" . $plugin,
									$toDir . '/plugins/' . $element . '/' . $plugin
								);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Process components
	 *
	 * @param   String  $type   - The type
	 * @param   String  $toDir  - The target
	 *
	 * @return  void
	 */
	private function mapDir($type, $toDir)
	{
		// Check if dir exists
		if (is_dir($this->getCodeBase()))
		{
			$dirHandle = opendir($this->getCodeBase());

			while (false !== ($element = readdir($dirHandle)))
			{
				if ($element != "." && $element != "..")
				{
					$this->symlink($this->getCodeBase() . '/' . $element, $toDir . '/' . $type . '/' . $element);
				}
			}
		}
	}

	/**
	 * Symlinks files / folders
	 *
	 * @param   String  $source  - The source
	 * @param   String  $target  - The target
	 *
	 * @return  void
	 */
	private function symlink($source, $target)
	{
		$this->say('Source: ' . $source);
		$this->say('Target: ' . $target);

		if (file_exists($target))
		{
			$this->say("DELETING TARGET: " . $target);
			$this->_deleteDir($target);
		}

		try
		{
			$this->taskFileSystemStack()
				->symlink($source, $target)
				->run();
		}
		catch (Exception $e)
		{
			$this->say('ERROR: ' . $e->message());
		}
	}
}
