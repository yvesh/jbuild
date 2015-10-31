<?php
/**
 * Project Robo File
 *
 * Extend this file according to your needs.
 */

if (!defined('JPATH_BASE'))
{
    define('JPATH_BASE', __DIR__);
}

// PSR-4 Autoload by composer
require_once JPATH_BASE . '/vendor/autoload.php';

/**
 * Class RoboFile
 */
class RoboFile extends \JBuild\RoboFile
{

}