<?php
/*
 *  OPEN POWER LIBS <http://libs.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Base.php 24 2008-12-07 10:57:26Z extremo $
 */

	/*
	 * Interface definitions
	 */

	interface Opl_Translation_Interface
	{
		public function _($group, $id);
		public function assign($group, $id);
	} // end Opt_Translation_Interface;

	/*
	 * Class definitions
	 */
	
	class Opl_Loader
	{
		static private $_mappedFiles = array();
		static private $_mappedLibs = array();
		static private $_initialized = array();
		static private $_directory = '';
		static private $_loaded = false;

		/**
		 * Specifies a directory path to the OPL libraries.
		 *
		 * @param string $name The directory name where the OPL libraries are kept.
		 */
		static public function setDirectory($name)
		{
			if($name != '')
			{
				if($name[strlen($name)-1] != '/')
				{
					$name .= '/';
				}
			}
			// Prevention against current directory changes in Apache
			// which affects destructors. We avoid it by switching to the
			// absolute path.
			
			if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false)
			{
				$name = realpath($name).'/';
			}
			self::$_directory = $name;
		} // end setDirectory();

		/**
		 * Registers the autoloader.
		 */
		static public function register()
		{
			spl_autoload_register(array('Opl_Loader', 'autoload'));
		} // end register();

		/**
		 * Returns the OPL libraries directory.
		 *
		 * @return string The OPL libraries directory.
		 */
		static public function getDirectory()
		{
			return self::_directory;
		} // end getDirectory();

		/**
		 * Allows to load the path list for the libraries either from an
		 * array or from an INI file.
		 *
		 * @param string|array $config The path list to the OPL libraries.
		 */
		static public function loadPaths($config)
		{
			if(!is_array($config))
			{
				if(!file_exists($config))
				{
					throw new Opl_FileNotExists_Exception('file', $config);
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
					self::mapLibrary($lib, $path);
				}
			}
			if(isset($config['classes']) && is_array($config['classes']))
			{
				foreach($config['classes'] as $class => $path)
				{
					if(strpos($path, ':') !== false)
					{
						$data = explode(':', $path);
						self::map($class, $data[1], $data[0]);
					}
					else
					{
						self::map($class, $path);
					}
				}
			}
		} // end loadPaths();

		/**
		 * Allows to specify a directory for a single OPL library.
		 *
		 * @param string $libraryName The three-letter library code.
		 * @param string $directory The directory, where the library is located.
		 */
		static public function mapLibrary($libraryName, $directory)
		{
			if($directory != '')
			{
				if($directory[strlen($directory)-1] != '/')
				{
					$directory .= '/';
				}
			}
			self::$_mappedLibs[$libraryName] = $directory;
		} // end mapLibrary();

		/**
		 * Allows to specify a path for one of the classes manually.
		 * However, the path must be located within the class library
		 * directory.
		 *
		 * @param string $className The class name
		 * @param string $directory The directory, where the library is located.
		 * @param string|null $library If not specified, the library name is taken from the class name.
		 */
		static public function map($className, $file, $library = NULL)
		{
			if(is_null($library))
			{
				// Determine the library name according to the class name.
				$name = substr($className, 0, 3);
				if(strpos($name, 'Op') !== 0)
				{
					throw new Opl_InvalidClass_Exception($className);
				}
				$library = $name;
			}

			if(isset(self::$_mappedLibs[$library]))
			{
				self::$_mappedFiles[$className] = self::$_mappedLibs[$library].$file;
			}
			else
			{
				self::$_mappedFiles[$className] = self::$_directory.$file;
			}
		} // end map();

		/**
		 * Allows to specify an absolute path to the class.
		 *
		 * @param string $className The class name.
		 * @param string $file The absolute path to the class file.
		 */
		static public function mapAbsolute($className, $file)
		{
			self::$_mappedFiles[$className] = $file;
		} // end mapAbsolute();

		/**
		 * Loads the class.
		 *
		 * @param string $name The class name.
		 * @return boolean True, if the class was successfully loaded.
		 */
		static public function load($name)
		{
			return self::autoload($name);
		} // end load();

		/**
		 * An autoloader method.
		 *
		 * @param string $className The class name.
		 * @return boolean True, if the class was successfully loaded.
		 */
		static public function autoload($className)
		{
			// Backward compatibility to PHP 5.2
			// This allows to load the compatibility classes even if some parts of OPT are
			// loaded by different autoloader.
			if(!self::$_loaded)
			{
				if(version_compare(phpversion(), '5.3.0-dev', '<'))
				{
					require((isset(self::$_mappedLibs['Opl']) ? self::$_mappedLibs['Opl'] : self::$_directory.'Opl/').'Php52.php');
				}
				self::$_loaded = true;
				if(class_exists($className, false) || interface_exists($className, false))
				{
					return true;
				}
			}

			// Later, only the OPT classes go.
			if(strpos($className, 'Op') !== 0)
			{
				return false;
			}
			$items = explode('_', $className);
			if(strlen($items[0]) != 3)
			{
				return false;
			}
			// Determine the library path
			if(isset(self::$_mappedLibs[$items[0]]))
			{
				$base = self::$_mappedLibs[$items[0]];
			}
			else
			{
				$base = self::$_directory.$items[0].'/';
			}
			
			// Load the base library file, if not loaded yet.
			if(!isset(self::$_initialized[$items[0]]))
			{
				if(file_exists($base.'Class.php'))
				{
					require($base.'Class.php');
				}
				self::$_initialized[$items[0]] = true;
				if(class_exists($className, false) || interface_exists($className, false))
				{
					return true;
				}
			}

			// Manually mapped files support
			if(isset(self::$_mappedFiles[$className]))
			{
				if(file_exists(self::$_mappedFiles[$className]))
				{
					require(self::$_mappedFiles[$className]);
					return true;
				}
				return false;
			}

			// Automated mapping
			if(end($items) == 'Exception')
			{
				require($base.'Exception.php');
				return true;
			}
			else
			{
				unset($items[0]);
				$base .= implode($items, DIRECTORY_SEPARATOR).'.php';
				if(file_exists($base))
				{
					require($base);
					return true;
				}
			}
			return false;
		} // end autoload();
	} // end Opl_Loader;
	
	class Opl_Registry
	{
		static private $_objects = array();
		static private $_states = array();		

		static public function register($name, $object)
		{
			self::$_objects[$name] = $object;
		} // end register();
		
		static public function get($name)
		{
			if(!isset(self::$_objects[$name]))
			{
				throw new Opl_Debug_ItemNotExists_Exception('object', $name);
			}
			return self::$_objects[$name];
		} // end get();
		
		static public function exists($name)
		{
			return isset(self::$_objects[$name]);
		} // end exists();
		
		static public function setState($name, $value)
		{
			self::$_states[$name] = $value;
		} // end setState();
		
		static public function getState($name)
		{
			if(!isset(self::$_states[$name]))
			{
				return NULL;
			}
			return self::$_states[$name];
		} // end getState();
	} // end Opl_Registry;

	class Opl_Class
	{
		// Plugin support
		public $pluginDir = NULL;
		public $pluginDataDir = NULL;
		public $pluginAutoload = true;
	
		// The rest of the configuration
		protected $_config = array();

		public function __get($name)
		{
			if($name[0] == '_')
			{
				return NULL;
			}
			if(!isset($this->_config[$name]))
			{
				throw new Opl_OptionNotExists_Exception($name, get_class($this));
			}
			return $this->_config[$name];
		} // __get();

		public function __set($name, $value)
		{
			if($name[0] == '_')
			{
				return NULL;
			}
			
			$this->_config[$name] = $value;
		} // end __set();

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
				throw new Opl_InvalidType_Exception(get_class($this).'::pluginDir', 'string or array');
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
								throw new Opl_FileNotExists_Exception('directory', $dir);
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
							throw new Opl_FileNotExists_Exception('directory', $dir);
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
						throw new Opl_FileNotExists_Exception('directory', $dir);
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
					throw new Opl_NotWriteable_Exception('directory', $this->pluginDataDir);
				}
			}
			
			require($dataFile);
		} // end loadPlugins();
		
		protected function _pluginLoader($directory, SplFileInfo $file)
		{
			return '';
		} // end _pluginLoader();
		
		protected function _securePath(&$path)
		{
			if($path[strlen($path)-1] != '/')
			{
				$path .= '/';
			}
		} // end _securePath();
	} // end Opl_Class;
	
	class Opl_Goto_Exception extends Exception
	{
	} // end Opl_Goto_Exception;
