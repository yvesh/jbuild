<?php
/**
 * @package    JBuild
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.10.15
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
 * Metrics class for extensions
 *
 * @package  JBuild\Tasks
 */
class Metrics extends JTask implements TaskInterface
{
    /**
     * Compute or check for metrics
     *
     * @return  bool
     */
    public function run($bail = false)
    {
        $this->say('Not implemented yet');
        $this->say(print_r($this->getConfig()->params, true));

        $this->codestyle($bail);
        $this->messdetect($bail);
    }

    public function codestyle($bail = false)
    {
        if ((bool) $bail)
        {
            $this->say('Checking code style according to ' . $this->getConfig()->params['standard'] . ' standard');
        }
        else
        {
            $this->say('Generating code style report according to ' . $this->getConfig()->params['standard'] . ' standard');
        }
    }

    public function messdetect($bail = false)
    {
        if ((bool) $bail)
        {
            $this->say('Checking for mess');
        }
        else
        {
            $this->say('Generating mess report');
        }
    }
}
