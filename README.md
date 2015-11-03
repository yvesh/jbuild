# Moved to joomla-projects/jorobo

## This project has been moved to https://github.com/joomla-projects/jorobo






# JBuild (Robo.li tasks for Joomla! extensions) [![Build Status](http://test01.compojoom.com/api/badge/github.com/yvesh/jbuild/status.svg?branch=master)](http://test01.compojoom.com/github.com/yvesh/jbuild)

[![Latest Stable Version](https://poser.pugx.org/yvesh/jbuild/v/stable)](https://packagist.org/packages/yvesh/jbuild) [![Total Downloads](https://poser.pugx.org/yvesh/jbuild/downloads)](https://packagist.org/packages/yvesh/jbuild) [![Latest Unstable Version](https://poser.pugx.org/yvesh/jbuild/v/unstable)](https://packagist.org/packages/yvesh/jbuild) [![License](https://poser.pugx.org/yvesh/jbuild/license)](https://packagist.org/packages/yvesh/jbuild)

#### Warning: Currently in early stage!

Robo.li build scripts, tools and generators for compojoom.com Joomla! extensions.

## Installation (Standalone):

  * composer install
  * configure jbuild.ini
  * vendor/bin/robo
  

## Function overview:

  * vendor/bin/robo map destination - Symlinks an extension into an Joomla installation
  * vendor/bin/robo build - Builds your extension into an installable Joomla package including replacements
  * vendor/bin/robo generate [--mod_xy --com_xy --plg_system_xy] (not integrated yet)
  
  
## How-to use in your own extension

Update your composer.json file and add yvesh/jbuild:dev and do a composer require yvesh/jbuild:dev

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

or

`$this->taskBuild($params)->run()`

Look at the RoboFile.php in the library root for a sample file


## Directory setup

In order to use JBuild you should use the following directory structure (it's like the normal joomla one)

####Components

```
source/administrator/components/com_name/
source/administrator/components/com_name/name.xml
source/administrator/components/com_name/script.php (Optional)
source/components/com_name/
source/administrator/language/en-GB/en-GB.com_name.ini
source/administrator/language/en-GB/en-GB.com_name.sys.ini
source/language/en-GB/en-GB.com_name.ini
source/media/com_name
```

#### Modules

```
source/modules/mod_something
source/media/mod_something
source/language/en-GB/en-GB.mod_something.ini
```

### Plugins

```
source/plugins/type/name
source/media/plg_type_name
source/administrator/language/en-GB/en-GB.plg_type_name.ini
```