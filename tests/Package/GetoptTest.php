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
} // end Package_GetoptTest;