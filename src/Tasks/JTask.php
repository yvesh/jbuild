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
 * Class JTask - Base class for our tasks
 *
 * @package  JBuild\Tasks
 * @since    0.9.0
 */
abstract class JTask extends \Robo\Tasks implements TaskInterface
{
	/**
	 * The config object
	 *
	 * @var    array|null
	 */
	protected static $config = null;

	/**
	 * Operating sytem
	 *
	 * @var    string
	 */
	protected $os = '';

	/**
	 * The file extension (OS Support) - should be renamed
	 *
	 * @var    string
	 */
	protected $extension = '';

	/**
	 * The source folder
	 *
	 * @var    string
	 */
	protected $sourceFolder = '';


	/**
	 * Construct
	 *
	 * @param   Array  $params  Opt params
	 */
	public function __construct($params = array())
	{
		// Load config only once
		if (self::$config == null)
		{
			// Load config as object
			self::$config = json_decode(json_encode(parse_ini_file(JPATH_BASE . '/jbuild.ini')), false);

			if (!self::$config)
			{
				$this->say('Error: Config file jbuild.ini not available');

				return false;
			}

			// Are we building a git / dev release?
			if ($params['dev'])
			{
				$res = $this->_exec('git rev-parse --short HEAD');

				$version = trim($res->getMessage());

				if ($version)
				{
					$this->say("Changing version to development version " . $version);
					self::getConfig()->version = $version;
				}
			}

			$target = "/dist/" . $this->getConfig()->extension . "_" . $this->getConfig()->version;
			$target = str_replace(".", "-", $target);

			self::getConfig()->buildFolder = JPATH_BASE . $target;

			self::getConfig()->params = $params;

			// Date set
			date_default_timezone_set('UTC');
		}

		// Detect operating system
		$this->os = strtoupper(substr(PHP_OS, 0, 3));

		if ($this->os === 'WIN')
		{
			$this->extension = '.exe';
		}

		// Source folder
		$this->sourceFolder = JPATH_BASE . "/" . $this->getConfig()->source;

		if (!is_dir($this->sourceFolder))
		{
			$this->say('Warning - Directory: ' . $this->sourceFolder . ' is not available');
		}
	}

	/**
	 * Function to check if folders are existing / writable (Code Base etc.)
	 *
	 * @return bool
	 */
	public function checkFolders()
	{
		$dirHandle = opendir($this->getCodeBase());

		if ($dirHandle === false)
		{
			$this->printTaskError('Can not open ' . $this->getCodeBase() . ' for parsing');

			return false;
		}

		return true;
	}

	/**
	 * Get the operating system
	 *
	 * @return string
	 */
	public function getOs()
	{
		return $this->os;
	}

	/**
	 * Get the extensions for which we run this script
	 *
	 * @return  string
	 */
	public function getRunextension()
	{
		return $this->extension;
	}

	/**
	 * Get the build config
	 *
	 * @return  object
	 */
	public function getConfig()
	{
		return self::$config;
	}

	/**
	 * Get the source folder path
	 *
	 * @deprecated  Use getSourceFolder instead
	 *
	 * @return  string  absolute path
	 */
	public function getCodeBase()
	{
		return $this->getSourceFolder();
	}

	/**
	 * Get the source folder path
	 *
	 * @return  string  absolute path
	 */
	public function getSourceFolder()
	{
		return $this->sourceFolder;
	}

	/**
	 * Get the extension name
	 *
	 * @return   string
	 */
	public function _ext()
	{
		return strtolower($this->getConfig()->extension);
	}

	/**
	 * Get the destination / build folder
	 *
	 * @return   string
	 */
	public function _dest()
	{
		return $this->getConfig()->buildFolder;
	}

	/**
	 * Get the Source folder
	 *
	 * @return   string
	 */
	public function _source()
	{
		return $this->getSourceFolder();
	}
}
