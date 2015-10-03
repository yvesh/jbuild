# JBuild (Robo.li tasks for Joomla! extensions)

Warning: Currently in early testing stage!

Robo.li Build scripts, tools and generators for compojoom.com Joomla! extensions.

## Installation (Standalone):

  * composer install
  * configure jbuild.ini
  * vendor/bin/robo

## Function overview:

  * vendor/bin/robo map destination - Symlinks an extension into an Joomla installation
  * vendor/bin/robo build - Builds your extension into an installable Joomla package including replacements
  * vendor/bin/robo generate [--mod_xy --com_xy --plg_system_xy]
  * vendor/bin/robo tests
  
## How-to use in your own extension

Update your composer.json file and add compojoom/jbuild:dev and do a composer require compojoom/jbuild:dev

Make sure your RoboFile.php loads the tasks:

```
<?php
require 'vendor/autoload.php';

class RoboFile extends \Robo\Tasks
{
	use \JBuild\Tasks\loadTasks;
	..
```

Then you can use it your own tasks for example:

`$this->taskMap($target)->run();`