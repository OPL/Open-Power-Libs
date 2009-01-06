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
 * $Id: Debug.php 22 2008-12-03 11:32:29Z zyxist $
 */

	class Opl_Debug
	{

		static public function dump($variable, $desc = NULL)
		{
			if(!is_null($desc))
			{
				echo '<h3>'.$desc.'</h3>';
			}
			if(extension_loaded('xdebug'))
			{
				var_dump($variable);
				return;
			}
			
			echo '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
		} // end dump();
		
		static public function write($message)
		{
			echo '<div>'.$message.'</div>';
		} // end write();
		
		static public function backtrace()
		{
			echo '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		} // end backtrace();
		
		static public function writeErr($message)
		{
			$fp = fopen('php://stderr', 'w');
			fwrite($fp, $message."\r\n");
			fclose($fp);
		} // end writeErr();
		
		static public function printFlags($int, $console = false)
		{
			$i = 1;
			$j = 1;
			if($console)
			{
				echo "Set flags:\r\n";
			}
			else
			{
				echo '<div>Set flags: <br/>';
			}
			for($j = 0; $j < 32; $j++)
			{
				$i = $i << 2;
				if($int & $i)
				{
					if($console)
					{
						echo $i."\r\n"; 
					}
					else
					{
						echo $i.'<br/>';
					}
				}
			}
			if(!$console)
			{
				echo '</div>';
			}
		} // end printFlags();
	} // end Opl_Debug;
