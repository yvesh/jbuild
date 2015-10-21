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

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

use JBuild\Tasks\JTask;

/**
 * The supervisor
 *
 * @package  JBuild\Tasks\Build
 *
 * @since    1.0.1
 */
class Extension extends Base implements TaskInterface
{
	use \Robo\Task\Development\loadTasks;
	use \Robo\Common\TaskIO;
	use buildTasks;

	protected $params = null;

	private $hasComponent = true;

	private $hasModules = true;

	private $hasPlugins = true;

	private $hasLibraries = true;

	private $hasCBPlugins = true;

	private $modules = array();

	private $plugins = array();

	private $libraries = array();

	/**
	 * Community Builder plugins
	 *
	 * @var array
	 */
	private $cbplugins = array();

	/**
	 * Initialize Build Task
	 *
	 * @param   String  $params  The target directory
	 */
	public function __construct($params)
	{
		parent::__construct();
	}

	/**
	 * Build the package
	 *
	 * @return  bool
	 */
	public function run()
	{
		$this->say('Building Extension package');

		$this->analyze();

		// Build component
		if ($this->hasComponent)
		{
			$this->buildComponent($this->params)->run();
		}

		// Modules
		if ($this->hasModules)
		{
			$path = $this->getSourceFolder() . "/modules";

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
					// Module folder
					$this->modules[] = $entry;
					$this->buildModule($entry, $this->params)->run();
				}
			}

			closedir($hdl);
		}

		// Plugins
		if ($this->hasPlugins)
		{
			$path = $this->getSourceFolder() . "/plugins";

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
						// Only folders
						$p2 = $path . "/" . $entry;

						if (substr($plugin, 0, 1) == '.')
						{
							continue;
						}

						if (!is_file($p2))
						{
							$this->plugins[] = "plg_" . $type . "_" . $plugin;
							$this->buildPlugin($type, $plugin, $this->params)->run();
						}
					}

					closedir($hdl2);
				}
			}

			closedir($hdl);
		}

		if ($this->hasLibraries)
		{
			$path = $this->getSourceFolder() . "/libraries";

			// Get every library
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
					// Library folder
					$this->libraries[] = $entry;
					$this->buildLibrary($entry, $this->params, $this->hasComponent)->run();
				}
			}

			closedir($hdl);
		}

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
		if (!file_exists($this->getSourceFolder() . "/administrator/components/com_" . $this->_ext())
			&& !file_exists($this->getSourceFolder() . "/components/com_" . $this->_ext()))
		{
			$this->say("Extension has no component");
			$this->hasComponent = false;
		}

		if (!file_exists($this->getSourceFolder() . "/modules"))
		{
			$this->hasModules = false;
		}

		if (!file_exists($this->getSourceFolder() . "/plugins"))
		{
			$this->hasPlugins = false;
		}

		if (!file_exists($this->getSourceFolder() . "/libraries"))
		{
			$this->hasLibraries = false;
		}
	}
}
