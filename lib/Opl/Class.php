<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

if(version_compare(PHP_VERSION, '5.3.0', '<'))
{
	die('Open Power Libs requires PHP 5.3.0 or newer. Your version: '.PHP_VERSION);
}

/**
 * The base class for the other OPL libraries. It provides the configuration
 * and plugin handling.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Class
{
	// Plugin support
	public $pluginDir = NULL;
	public $pluginDataDir = NULL;
	public $pluginAutoload = true;

	/**
	 * The optional configuration options.
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Returns the specified configuration property value.
	 *
	 * @param string $name The property name
	 * @return mixed The property value.
	 */
	public function __get($name)
	{
		if($name[0] == '_')
		{
			return NULL;
		}
		if(!isset($this->_config[$name]))
		{
			throw new Opl_Class_Exception('The specified property: '.$name.' does not exist in an object of '.get_class($this));
		}
		return $this->_config[$name];
	} // __get();

	/**
	 * Sets the custom configuration property value.
	 *
	 * @param string $name The property name
	 * @param mixed $value The property value
	 */
	public function __set($name, $value)
	{
		if($name[0] == '_')
		{
			return NULL;
		}

		$this->_config[$name] = $value;
	} // end __set();

	/**
	 * Loads the configuration from external array or INI file.
	 *
	 * @param string|array $config The configuration option values or the INI filename.
	 * @return boolean True on success
	 */
	public function loadConfig($config)
	{
		if(is_string($config))
		{
			$config = @parse_ini_file($config);
		}

		if(!is_array($config))
		{
			return false;
		}
		foreach($config as $name => $value)
		{
			if($name[0] == '_')
			{
				continue;
			}
			if(property_exists($this, $name))
			{
				$this->$name = $value;
			}
			else
			{
				$this->_config[$name] = $value;
			}
		}
		return true;
	} // end loadConfig();

	/**
	 * Returns the configuration as an array.
	 *
	 * @return array The configuration properties
	 */
	public function getConfig()
	{
		$vars = $this->_config;
		$internal = get_object_vars($this);
		foreach($internal as $id=>$var)
		{
			if($id[0] != '_')
			{
				$vars[$id] = $var;
			}
		}
		return $vars;
	} // end getConfig();

	/**
	 * Loads the plugins from the directories specified in the class configuration.
	 *
	 * @throws Opl_Filesystem_Exception If one of plugin directories does not exist or is not accessible.
	 */
	public function loadPlugins()
	{
		if(is_string($this->pluginDir))
		{
			$dirs[] = &$this->pluginDir;
		}
		elseif(is_array($this->pluginDir))
		{
			$dirs = &$this->pluginDir;
		}
		else
		{
			throw new DomainException(get_class($this).'::pluginDir requires pluginDir property to be either string or array.');
		}

		$dataFile = $this->pluginDataDir.get_class($this).'_Plugins.php';
		$cplTime = @filemtime($dataFile);
		$rebuild = false;
		if($this->pluginAutoload)
		{
			if($cplTime !== false)
			{
				// The plugin data file exists, but we have to check
				// whether there are some new plugins or not.
				$mode = 0;
				foreach($dirs as &$dir)
				{
					if($mode == 0)
					{
						$dirTime = @filemtime($dir);
						if($dirTime === false)
						{
							throw new Opl_Filesystem_Exception('The directory '.$dir.' does not exist.');
						}

						// Some new plugins have been added to this directory
						if($dirTime > $cplTime)
						{
							$rebuild = true;
							$mode = 1;
						}
					}
					// Now, we know that one of the dirs has a new plugin
					// We just have to check if all the directories exist
					elseif(!is_dir($dir))
					{
						throw new Opl_Filesystem_Exception('The directory '.$dir.' does not exist.');
					}
				}
			}
		}
		if($cplTime === false)
		{
			// No plugin data file,
			foreach($dirs as &$dir)
			{
				if(!is_dir($dir))
				{
					throw new Opl_Filesystem_Exception('The directory '.$dir.' does not exist.');
				}
			}
			$rebuild = true;
		}
		// We have to rebuild the file
		if($rebuild)
		{
			$src = '<'.'?php ';
			foreach($dirs as &$dir)
			{
				$this->_securePath($dir);
				foreach(new DirectoryIterator($dir) as $file)
				{
					if($file->isFile())
					{
						$src .= $this->_pluginLoader($dir, $file);
					}
				}
			}
			if(is_writeable($this->pluginDataDir))
			{
				file_put_contents($dataFile, $src);
			}
			else
			{
				throw new Opl_Filesystem_Exception('The directory '.$this->pluginDataDir.' is not writeable by PHP.');
			}
		}

		require($dataFile);
	} // end loadPlugins();

	/**
	 * The method allows to define the specific plugin loading settings for the
	 * library. Because the results are cached in order not to exhaust the server
	 * resources, the method must return a PHP code that loads the specified plugin.
	 *
	 * @internal
	 * @param string $directory The plugin location
	 * @param SplFileInfo $file The plugin file information
	 * @return string
	 */
	protected function _pluginLoader($directory, SplFileInfo $file)
	{
		return '';
	} // end _pluginLoader();

	/**
	 * The method allows to secure the path by adding an ending slash, if
	 * it is not specified.
	 *
	 * @internal
	 * @param string &$path The path to secure.
	 */
	public function _securePath(&$path)
	{
		if($path[strlen($path)-1] != '/')
		{
			$path .= '/';
		}
	} // end _securePath();
} // end Opl_Class;
