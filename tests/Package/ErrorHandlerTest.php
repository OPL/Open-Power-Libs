<?php
/**
 * The tests for Opl_ErrorHandler.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */


/**
 * @covers Opl_ErrorHandler
 */
class Package_ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
	public function testOutputCancellation()
	{
		ob_start();

		ob_start();

		echo 'XYZ';

		$eh = new Opl_ErrorHandler;
		$eh->display(new Opl_Exception('Foo'));

		$out = ob_get_clean();

		if(strpos($out, 'XYZ') !== false)
		{
			$this->fail('The earlier string XYZ was not cancelled by the exception handler.');
		}
		return true;
	} // end testOutputCancellation();
} // end Package_ErrorHandlerTest;