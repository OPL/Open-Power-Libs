<?php
/**
 * The tests for Opl_Getopt.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers Opl_Getopt
 */
class Package_GetoptTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testAutomaticHelpRecognition()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::AUTO_HELP);
		$this->assertFalse($getopt->parse(array('--help')));
	} // end testAutomaticHelpRecognition();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testAutomaticHelpRecognitionWhenDisabled()
	{
		$getopt = new Opl_Getopt();
		$getopt->parse(array('--help'));
	} // end testAutomaticHelpRecognitionWhenDisabled();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongOptionsPassedByIteration()
	{
		$getopt = new Opl_Getopt();
		$getopt->addOption(new Opl_Getopt_Option('foo', null, 'foo'));
		$getopt->addOption(new Opl_Getopt_Option('bar', null, 'bar'));
		$getopt->addOption(new Opl_Getopt_Option('joe', null, 'joe'));
		$this->assertTrue($getopt->parse(array('--foo', '--bar')));

		$recognized = 0;
		$opts = array('foo', 'bar');
		foreach($getopt as $option)
		{
			$this->assertTrue($option instanceof Opl_Getopt_Option);
			if(in_array($option->getName(), $opts))
			{
				$recognized++;
			}
		}
		if($recognized != sizeof($opts))
		{
			$this->fail('Getopt did not recognize 2 options, but '.$recognized);
		}
		return true;
	} // end testLongOptionsPassedByIteration();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongOptionsWithUnknown()
	{
		$getopt = new Opl_Getopt();
		$getopt->addOption(new Opl_Getopt_Option('foo', null, 'foo'));
		$getopt->addOption(new Opl_Getopt_Option('bar', null, 'bar'));
		$getopt->addOption(new Opl_Getopt_Option('joe', null, 'joe'));
		$getopt->parse(array('--foo', '--goo'));
	} // end testLongOptionsWithUnknown();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongOptionsDoNotTakeArguments()
	{
		$getopt = new Opl_Getopt();
		$getopt->addOption(new Opl_Getopt_Option('foo', null, 'foo'));
		$getopt->addOption(new Opl_Getopt_Option('bar', null, 'bar'));
		$getopt->addOption(new Opl_Getopt_Option('joe', null, 'joe'));
		$getopt->parse(array('--foo', '--bar=joe'));
	} // end testLongOptionsDoNotTakeArguments();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongOptionalAttributeNotSet()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_LONG_ARGS);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setArgument(Opl_Getopt_Option::OPTIONAL, Opl_Getopt_Option::ANYTHING);
		$this->assertTrue($getopt->parse(array('--bar')));

		$this->assertTrue($option->isFound());
		$this->assertSame(null, $option->getValue());
	} // end testLongOptionalAttributeNotSet();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongOptionalAttributeSet()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_LONG_ARGS);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setArgument(Opl_Getopt_Option::OPTIONAL, Opl_Getopt_Option::ANYTHING);
		$this->assertTrue($getopt->parse(array('--bar=foo')));

		$this->assertTrue($option->isFound());
		$this->assertEquals('foo', $option->getValue());
	} // end testLongOptionalAttributeSet();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongRequiredAttributeNotSet()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_LONG_ARGS);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setArgument(Opl_Getopt_Option::REQUIRED, Opl_Getopt_Option::ANYTHING);
		$this->assertTrue($getopt->parse(array('--bar')));
	} // end testRequiredAttributeNotSet();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongRequiredAttributeSet()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_LONG_ARGS);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setArgument(Opl_Getopt_Option::REQUIRED, Opl_Getopt_Option::ANYTHING);
		$this->assertTrue($getopt->parse(array('--bar=foo')));

		$this->assertTrue($option->isFound());
		$this->assertEquals('foo', $option->getValue());
	} // end testLongRequiredAttributeSet();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongAttributeUsedTwiceWithoutIncrementation()
	{
		$getopt = new Opl_Getopt();
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setMaxOccurences(2);
		$getopt->parse(array('--bar', '--bar'));
	} // end testLongAttributeUsedTwiceWithoutIncrementation();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongAttributeUsedTwiceWithIncrementation()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_INCREMENTING);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setMaxOccurences(2);
		$this->assertTrue($getopt->parse(array('--bar', '--bar')));
		
		$this->assertEquals(2, $option->getOccurences());
	} // end testLongAttributeUsedTwiceWithIncrementation();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongAttributeUsedMoreThanPossible()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_INCREMENTING);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setMaxOccurences(2);
		$getopt->parse(array('--bar', '--bar', '--bar'));
	} // end testLongAttributeUsedMoreThanPossible();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 * @expectedException Opl_Getopt_Exception
	 */
	public function testLongAttributeUsedFewerThanPossible()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_INCREMENTING);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setMinOccurences(2);
		$option->setMaxOccurences(5);
		$getopt->parse(array('--bar'));
	} // end testLongAttributeUsedFewerThanPossible();

	/**
	 * @covers Opl_Getopt::__construct
	 * @covers Opl_Getopt::parse
	 */
	public function testLongAttributeUsedTwiceWithArguments()
	{
		$getopt = new Opl_Getopt(Opl_Getopt::ALLOW_INCREMENTING);
		$getopt->addOption($option = new Opl_Getopt_Option('bar', null, 'bar'));
		$option->setArgument(Opl_Getopt_Option::REQUIRED, Opl_Getopt_Option::ANYTHING);
		$option->setMaxOccurences(2);
		$this->assertTrue($getopt->parse(array('--bar=foo', '--bar=bar')));

		$this->assertEquals(2, $option->getOccurences());
		$this->assertTrue(is_array($option->getValue()));
		$this->assertContains('foo', $option->getValue());
		$this->assertContains('bar', $option->getValue());
	} // end testLongAttributeUsedTwiceWithArguments();
} // end Package_GetoptTest;