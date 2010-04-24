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
 * $Id$
 */

if(version_compare(PHP_VERSION, '5.3.0', '<'))
{
	die('Open Power Libs requires PHP 5.3.0 or newer. Your version: '.PHP_VERSION);
}

/**
 * The translation interface for OPL libraries.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Opl_Translation_Interface
{
	/**
	 * This method is supposed to return the specified localized message.
	 *
	 * @param string $group The message group
	 * @param string $id The message identifier
	 * @return string
	 */
	public function _($group, $id);

	/**
	 * Assigns the external data to the message body. The operation of
	 * concatenating the message and the data is left for the programmer.
	 * The method should save it in the internal buffer and use in the
	 * next first call of _() method.
	 *
	 * @param string $group The message group
	 * @param string $id The message identifier
	 * @param ... Custom arguments for the specified text.
	 */
	public function assign($group, $id);
} // end Opl_Translation_Interface;

/**
 * The generic class autoloader.
 *
 * @author Tomasz Jędrzejewski
 * @author Amadeusz Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Loader
{	
	/**
	 * The main directory used by autoloader
	 * @static
	 * @var string
	 */
	static private $_directory = '';
	/**
	 * The library-specific configuration.
	 * @static
	 * @var array
	 */
	static private $_libraries = array();

	/**
	 * The list of manually mapped files.
	 * @static
	 * @var array
	 */
	static private $_mappedFiles = array();

	/**
	 * Checking if the file exists status.
	 * @static
	 * @var boolean
	 */
	static private $_fileCheck = false;

	/**
	 * Checking if loader should handle not known libraries.
	 * @static
	 * @var boolean
	 */
	static private $_handleUnknownLibraries = true;

	/**
	 * Specifies a directory path to the OPL libraries.
	 *
	 * @static
	 * @param string $name The directory name where the OPL libraries are kept.
	 */
	static public function setDirectory($name)
	{
		if($name != '')
		{
			if($name[strlen($name)-1] != DIRECTORY_SEPARATOR)
			{
				$name .= DIRECTORY_SEPARATOR;
			}
		}
		// Prevention against current directory changes in Apache
		// which affects destructors. We avoid it by switching to the
		// absolute path.

		if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false)
		{
			$name = realpath($name).DIRECTORY_SEPARATOR;
		}
		self::$_directory = $name;
	} // end setDirectory();

	/**
	 * Sets state for handling unknown, not registered by addLibrary libraries.
	 *
	 * @static
	 * @param boolean $state State for handling unknown libraries
	 */
	static public function setHandleUnknownLibraries($state)
	{
		self::$_handleUnknownLibraries = (boolean)$state;
	} // end setHandleUnknownLibraries();

	/**
	 * Registers the autoloader.
	 *
	 * @static
	 */
	static public function register()
	{
		spl_autoload_register(array('Opl_Loader', 'load'));
	} // end register();

	/**
	 * Returns the OPL libraries directory.
	 *
	 * @return string The OPL libraries directory.
	 */
	static public function getDirectory()
	{
		return self::$_directory;
	} // end getDirectory();

	/**
	 * Allows to enable or disable the file existence checking by
	 * the autoloader. Note that for the performance reasons, the
	 * checking should be disabled in the production environment.
	 *
	 * @static
	 * @param boolean $status The new status
	 */
	static public function setCheckFileExists($status)
	{
		self::$_fileCheck = (bool)$status;
	} // end setCheckFileExists();

	/**
	 * Allows to load the path list for the libraries either from an
	 * array or from an INI file.
	 *
	 * @static
	 * @param string|array $config The path list to the OPL libraries.
	 */
	static public function loadPaths($config)
	{
		if(!is_array($config))
		{
			if(!file_exists($config))
			{
				throw new Opl_Filesystem_Exception('The file '.$config.' does not exist.');
			}
			$config = parse_ini_file($config, true);
		}

		if(isset($config['directory']))
		{
			self::setDirectory($config['directory']);
		}
		if(isset($config['libraries']) && is_array($config['libraries']))
		{
			foreach($config['libraries'] as $lib => $path)
			{
				self::addLibrary($lib, array('directory' => $path));
			}
		}
		if(isset($config['classes']) && is_array($config['classes']))
		{
			foreach($config['classes'] as $class => $path)
			{
				self::map($class, $path);
			}
		}
	} // end loadPaths();

	/**
	 * Configures the autoloader settings for the specific library.
	 *
	 * @static
	 * @param String $prefix The library prefix used by the classes.
	 * @param Array $config The library configuration
	 */
	static public function addLibrary($prefix, Array $config)
	{
		if(isset($config['directory']))
		{
			if($config['directory'] != '')
			{
				if($config['directory'][strlen($config['directory'])-1] != DIRECTORY_SEPARATOR)
				{
					$config['directory'] .= DIRECTORY_SEPARATOR;
				}
			}
		}
		if(isset($config['basePath']))
		{
			if($config['basePath'] != '')
			{
				if($config['basePath'][strlen($config['basePath'])-1] != DIRECTORY_SEPARATOR)
				{
					$config['basePath'] .= DIRECTORY_SEPARATOR;
				}
			}
		}
		self::$_libraries[$prefix] = $config;
	} // end addLibrary();

	/**
	 * Removes the library-specific settings for the library.
	 *
	 * @static
	 * @param String $prefix The library prefix used by the classes
	 */
	static public function removeLibrary($prefix)
	{
		if(isset(self::$_libraries[$prefix]))
		{
			unset(self::$_libraries[$prefix]);
		}
	} // end removeLibrary();

	/**
	 * Allows to specify an absolute path to the class.
	 *
	 * @static
	 * @param string $className The class name.
	 * @param string $file The absolute path to the class file.
	 */
	static public function map($className, $file)
	{
		self::$_mappedFiles[$className] = $file;
	} // end mapAbsolute();

	/**
	 * Loads the specified class
	 *
	 * @static
	 * @param string $className The class name.
	 * @return boolean True, if the class was successfully loaded.
	 */
	static public function load($className)
	{
		// Manually mapped files support
		if(isset(self::$_mappedFiles[$className]))
		{
			require(self::$_mappedFiles[$className]);
			return true;
		}

		$replacement = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $className);
		$id = strpos($replacement, DIRECTORY_SEPARATOR);
		$wholeName = false;
		// Handle the situation if there is no "_" in the class name
		if($id === false)
		{
			$id = strlen($replacement);
			$wholeName = true;
			$library = $className;
		}
		else
		{
			$library = substr($className, 0, $id);
		}
		// Check if autoloader have to handle not registered libraries
		if(!isset(self::$_libraries[$library]))
		{
			if(!self::$_handleUnknownLibraries)
			{
				return false;
			}
			$data = array('basePath' => self::$_directory);
		}
		else
		{
			$data = self::$_libraries[$library];
		}

		// Now load the file, depending on the path type set for the library.
		if(isset($data['basePath']))
		{
			$path = $data['basePath'].$replacement.'.php';
			if(self::$_fileCheck == true && !file_exists($path))
			{
				return true;
			}
			require($path);
			return true;
		}
		elseif(isset($data['directory']))
		{
			if($wholeName)
			{
				$path = $data['directory'].'..'.DIRECTORY_SEPARATOR.$replacement.'.php';
			}
			else
			{
				$path = $data['directory'].substr($replacement, $id+1, strlen($replacement) - $id - 1).'.php';
			}
			if(self::$_fileCheck == true && !file_exists($file))
			{
				return true;
			}
			require($path);
			return true;
		}
		return false;
	} // end autoload();

	/**
	 * Returns the path for the specified library.
	 *
	 * @param String $library The library
	 * @return String
	 */
	static public function getLibraryPath($library)
	{
		if(isset(self::$_libraries[$library]))
		{
			if(isset(self::$_libraries[$library]['directory']))
			{
				return self::$_libraries[$library]['directory'];
			}
			if(isset(self::$_libraries[$library]['basePath']))
			{
				return self::$_libraries[$library]['basePath'].$library.DIRECTORY_SEPARATOR;
			}
		}
		return self::$_directory.$library.DIRECTORY_SEPARATOR;
	} // end getLibraryPath();
} // end Opl_Loader;

/**
 * The Registry provides a safe registry of the main system objects and
 * certain values. Contrary to singleton implementations, the registry
 * can be erased. Intentionally, the programmer should use different buffers
 * for scalar values and for objects.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Registry
{
	/**
	 * The list of stored objects.
	 * @static
	 * @var array
	 */
	static private $_objects = array();

	/**
	 * The list of stored values.
	 * @static
	 * @var array
	 */
	static private $_values = array();

	/**
	 * Registers a new object in the registry.
	 *
	 * @static
	 * @param string $name The object key
	 * @param object $object The registered object
	 */
	static public function set($name, $object)
	{
		self::$_objects[$name] = $object;
	} // end set();

	/**
	 * Returns the previously registered object. If the object does not
	 * exist, it throws an exception.
	 *
	 * @static
	 * @throws Opl_Registry_Exception
	 * @param string $name The registered object key.
	 * @return object The object stored under the specified key.
	 */
	static public function get($name)
	{
		if(!isset(self::$_objects[$name]))
		{
			throw new Opl_Registry_Exception('The specified registry object: '.$name.' does not exist');
		}
		return self::$_objects[$name];
	} // end get();

	/**
	 * Check whether there is an object registered under a specified key.
	 *
	 * @static
	 * @param string $name The object key
	 * @return boolean A boolean value indicating whether the object exists or not.
	 */
	static public function exists($name)
	{
		return !empty(self::$_objects[$name]);
	} // end exists();

	/**
	 * Sets the state variable in the registry.
	 *
	 * @static
	 * @param string $name The variable name
	 * @param mixed $value The variable value
	 */
	static public function setValue($name, $value)
	{
		self::$_values[$name] = $value;
	} // end setValue();

	/**
	 * Returns the state variable from the registry. If the
	 * variable does not exist, it returns NULL.
	 *
	 * @static
	 * @param string $name The variable name
	 * @return mixed
	 */
	static public function getValue($name)
	{
		if(!isset(self::$_values[$name]))
		{
			return NULL;
		}
		return self::$_values[$name];
	} // end getValue();
} // end Opl_Registry;

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
