<?php
	require_once('PHPUnit/Framework.php');

	if(!defined('GROUPED'))
	{
		$config = parse_ini_file('../paths.ini', true);
		require($config['libraries']['Opl'].'Base.php');
		// Load the compatibility layer.
	}

	class autoloadTest extends PHPUnit_Framework_TestCase
	{
		public function testLoadCompatibilityLayer()
		{
			if(version_compare(phpversion(), '5.3.0-dev', '<'))
			{
				ob_start();
				Opl_Loader::setDirectory('./autoload/');
				Opl_Loader::load('Opl_Test');

				$this->assertEquals(ob_get_clean(), "OPL/PHP52.PHP\n");
			}
			return true;
		} // end testLoadCompatibilityLayer();

		public function testSimpleClassLoading()
		{
			Opl_Loader::setDirectory('./autoload/');

			ob_start();
			Opl_Loader::load('Bar_Class');

			$this->assertEquals(ob_get_clean(), "BAR/CLASS.PHP\n");
			return true;
		} // end testSimpleClassLoading();

		public function testLoadMainClass()
		{
			Opl_Loader::setDirectory('./autoload/');

			ob_start();
			Opl_Loader::load('Foo_Bar');

			$this->assertEquals(ob_get_clean(), "FOO/CLASS.PHP\nFOO/BAR.PHP\n");
			return true;
		} // end testLoadMainClass();

		public function testLoadMoreClasses()
		{
			Opl_Loader::setDirectory('./autoload/');

			ob_start();
			Opl_Loader::load('Joe_Foo');
			Opl_Loader::load('Joe_Bar');

			$this->assertEquals(ob_get_clean(), "JOE/CLASS.PHP\nJOE/FOO.PHP\nJOE/BAR.PHP\n");
			return true;
		} // end testLoadMoreClasses();

		public function testExceptionLoading()
		{
			Opl_Loader::setDirectory('./autoload/');

			ob_start();
			Opl_Loader::load('Joe_Foo_Exception');

			$this->assertEquals(ob_get_clean(), "JOE/EXCEPTION.PHP\n");
			return true;
		} // end testExceptionLoading();

		public function testCustomLibrary()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::addLibrary('NewLib', array('handler' => null));
			ob_start();
			Opl_Loader::load('NewLib_Foo');

			$this->assertEquals(ob_get_clean(), "NEWLIB/FOO.PHP\n");
			return true;
		} // end testCustomLibrary();

		public function testCustomLibraryButNoHandlerDefined()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::addLibrary('NewLib', array('directory' => './autoload/NewLib/'));
			ob_start();
			Opl_Loader::load('NewLib_Foo');

			$this->assertEquals(ob_get_clean(), "NEWLIB/CLASS.PHP\nNEWLIB/FOO.PHP\n");
			return true;
		} // end testCustomLibraryButNoHandlerDefined();

		public function testAlternativePath()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::addLibrary('NewLib', array('directory' => './autoload/Joe/'));
			ob_start();
			Opl_Loader::load('NewLib_Foo');

			$this->assertEquals(ob_get_clean(), "JOE/FOO.PHP\n");
			return true;
		} // end testAlternativePath();

		public function testAlternativePathWithoutSlash()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::addLibrary('NewLib', array('directory' => './autoload/Joe'));
			ob_start();
			Opl_Loader::load('NewLib_Foo');

			$this->assertEquals(ob_get_clean(), "JOE/FOO.PHP\n");
			return true;
		} // end testAlternativePathWithoutSlash();

		public function testDisableHandler()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::setDefaultHandler(null);
			ob_start();
			Opl_Loader::load('Joe_Foo_Exception');

			$this->assertEquals(ob_get_clean(), "JOE/FOO/EXCEPTION.PHP\n");
			return true;
		} // end testDisableHandler();

		public function testManualFileMappingWithAutodetection()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::map('Joe_Testmapping', 'Bar.php');
			ob_start();
			Opl_Loader::load('Joe_Testmapping');

			$this->assertEquals(ob_get_clean(), "JOE/BAR.PHP\n");
			return true;
		} // end testManualFileMappingWithAutodetection();

		public function testManualFileMappingWithoutAutodetection()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::map('Joe_Testmapping', 'Bar.php', 'Foo');
			ob_start();
			Opl_Loader::load('Joe_Testmapping');

			$this->assertEquals(ob_get_clean(), "FOO/BAR.PHP\n");
			return true;
		} // end testManualFileMappingWithoutAutodetection();

		public function testAbsoluteMapping()
		{
			Opl_Loader::setDirectory('./autoload/');
			Opl_Loader::mapAbsolute('Foo_Abc', './autoload/Joe/Bar.php');
			ob_start();
			Opl_Loader::load('Foo_Abc');

			$this->assertEquals(ob_get_clean(), "JOE/BAR.PHP\n");
			return true;
		} // end testAbsoluteMapping();
	} // end autoloadTest;