<?php
/**
 * The tests for Opl_Loader.
 *
 * WARNING: Requires PHPUnit 3.4!
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @author Jacek "eXtreme" Jędrzejewski
 * @copyright Copyright (c) 2009-2010 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once 'vfsStream/vfsStream.php';

/**
 * @covers Opl_Loader
 * @runTestsInSeparateProcesses
 */
class Package_LoaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Checks if the loader tests have been started.
	 * @var boolean
	 */
	private $_started = false;
	
	/**
	 * Opl_Loader object
	 * @var Opl_Loader
	 */
	private $loader = false; 
	
	/**
	 * Generates a file content for the virtual file system.
	 * @param string $file The file name
	 * @param string $content The content for the file.
	 */
	protected function _createFile($file, $content)
	{
		return vfsStream::newFile($file)->withContent('<'."?php\necho \"".$content.'\\n";');
	} // end _filePrint();

	/**
	 * Prepare a virtual filesystem for testing.
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		if(!$this->_started)
		{
			$this->loader = new Opl_Loader();
			$this->loader->register();

			$this->_started = true;
		}

		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot($root = new vfsStreamDirectory('libs'));

		$root->addChild(new vfsStreamDirectory('Foo'));
		$root->addChild(new vfsStreamDirectory('Bar'));
		$root->addChild(new vfsStreamDirectory('Joe'));
		$root->addChild(new vfsStreamDirectory('NewLib'));
		$root->addChild(new vfsStreamDirectory('vendor'));
		
		$root->addChild($this->_createFile('Bar.php', 'BAR.PHP'));

		// Contents of NewLib/
		$root->getChild('NewLib')->addChild($this->_createFile('Class.php', 'NEWLIB/CLASS.PHP'));
		$root->getChild('NewLib')->addChild($this->_createFile('Foo.php', 'NEWLIB/FOO.PHP'));

		// Contents of Joe/
		$root->getChild('Joe')->addChild($this->_createFile('Class.php', 'JOE/CLASS.PHP'));
		$root->getChild('Joe')->addChild($this->_createFile('Foo.php', 'JOE/FOO.PHP'));
		$root->getChild('Joe')->addChild($this->_createFile('Bar.php', 'JOE/BAR.PHP'));
		$root->getChild('Joe')->addChild($this->_createFile('Exception.php', 'JOE/EXCEPTION.PHP'));
		$root->getChild('Joe')->addChild(new vfsStreamDirectory('Foo'));
		$root->getChild('Joe/Foo')->addChild($this->_createFile('Exception.php', 'JOE/FOO/EXCEPTION.PHP'));

		// Contents of Bar/
		$root->getChild('Bar')->addChild($this->_createFile('Class.php', 'BAR/CLASS.PHP'));

		// Contents of Foo/
		$root->getChild('Foo')->addChild($this->_createFile('Bar.php', 'FOO/BAR.PHP'));
		$root->getChild('Foo')->addChild($this->_createFile('Class.php', 'FOO/CLASS.PHP'));
		
		// Contents of vendor/
		$root->getChild('vendor')->addChild(new vfsStreamDirectory('Baz'));
		$root->getChild('vendor')->getChild('Baz')->addChild($this->_createFile('Class.php', 'BAZ/CLASS.PHP'));
		// @codeCoverageIgnoreStop
	} // end setUp();

	/**
	 * Clean-up.
	 */
	protected function tearDown()
	{
		// @codeCoverageIgnoreStart

		// @codeCoverageIgnoreStop
	} // end tearDown();

	/**
	 * @cover Opl_Loader::setDefaultPath
	 * @cover Opl_Loader::getDefaultPath
	 */
	public function testGettingDirectory()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');
		$this->assertEquals('vfs://', $this->loader->getDefaultPath());
		
		$this->loader->setDefaultPath('vfs://test/');
		$this->assertEquals('vfs://test/', $this->loader->getDefaultPath());
		
		$this->loader->setDefaultPath('vfs://test');
		$this->assertEquals('vfs://test/', $this->loader->getDefaultPath());
	} // end testGettingDirectory();

	/**
	 * @cover Opl_Loader::setNamespaceSeparator
	 * @cover Opl_Loader::getNamespaceSeparator
	 */
	public function testNamespaceSeparator()
	{
		$this->loader->setNamespaceSeparator('test');
		$this->assertEquals('test', $this->loader->getNamespaceSeparator());
	} // end testNamespaceSeparator();
	
	/**
	 * @cover Opl_Loader::addLibrary
	 * @cover Opl_Loader::hasLibrary
	 */
	public function testAddingLibrary()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');
		
		$this->loader->addLibrary('Joe');
		
		$this->assertEquals(true, $this->loader->hasLibrary('Joe'));
		
		$this->loader->removeLibrary('Joe');
		
		$this->assertEquals(false, $this->loader->hasLibrary('Joe'));
	} // end testAddingLibrary();
	
	/**
	 * @cover Opl_Loader::removeLibrary
	 * @expectedException RuntimeException
	 */
	public function testRemovingLibrary()
	{
		$this->loader->removeLibrary('Joe');
	}
	
	/**
	 * @cover Opl_Loader::addLibrary
	 * @expectedException RuntimeException
	 */
	public function testDuplicateLibrary()
	{
		$this->loader->addLibrary('Joe');
		$this->loader->addLibrary('Joe');
	} // end testDuplicateLibrary();

	/**
	 * @cover Opl_Loader::loadClass
	 */
	public function testSimpleClassLoading()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');
		
		$this->loader->addLibrary('Bar', null);

		ob_start();
		$this->loader->loadClass('Bar_Class');

		$this->assertEquals(ob_get_clean(), "BAR/CLASS.PHP\n");
		return true;
	} // end testSimpleClassLoading();
	
	/**
	 * @cover Opl_Loader::loadClass
	 */
	public function testSimpleClassLoading2()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');
		
		$this->loader->addLibrary('Baz', 'vfs://vendor/');

		ob_start();
		$this->loader->loadClass('Baz_Class');

		$this->assertEquals(ob_get_clean(), "BAZ/CLASS.PHP\n");
		return true;
	} // end testSimpleClassLoading2();

	/**
	 * @cover Opl_Loader::setNamespaceSeparator
	 * @cover Opl_Loader::loadClass
	 */
	public function testLoadNamespaceClass()
	{
		$this->loader->setNamespaceSeparator('\\');
		$this->loader->setDefaultPath('vfs://');
		
		$this->loader->addLibrary('Foo');
		
		ob_start();
		$this->loader->loadClass('Foo\Bar');

		$this->assertEquals(ob_get_clean(), "FOO/BAR.PHP\n");
		return true;
	} // end testLoadNamespaceClass();

	/**
	 * @cover Opl_Loader::loadClass
	 */
	public function testLoadMoreClasses()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');
		
		$this->loader->addLibrary('Joe');

		ob_start();
		$this->loader->loadClass('Joe_Foo');
		$this->loader->loadClass('Joe_Bar');

		$this->assertEquals(ob_get_clean(), "JOE/FOO.PHP\nJOE/BAR.PHP\n");
		return true;
	} // end testLoadMoreClasses();
	
	/**
	 * @cover Opl_Loader::loadClass
	 */
	public function testFailedLoad()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');

		$this->assertEquals(false, $this->loader->loadClass('Foo_Bar'));
		return true;
	} // end testFailedLoad();
	
	/**
	 * @cover Opl_Loader::loadClass
	 */
	public function testFailedLoad2()
	{
		$this->loader->setNamespaceSeparator('_');
		$this->loader->setDefaultPath('vfs://');

		$this->loader->addLibrary('Bar');
		
		$this->assertEquals(false, $this->loader->loadClass('Bar'));
		return true;
	} // end testFailedLoad2();
} // end Package_LoaderTest;