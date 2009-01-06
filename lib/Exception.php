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
 * $Id: Exception.php 22 2008-12-03 11:32:29Z zyxist $
 */

	/*
	 * Function definitions
	 */

	function Opl_Error_Handler(Opl_Exception $exception)
	{
		// Show the error message
echo <<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  	<title>Open Power Libs error</title>
  	<style type="text/css">
  	
html, body{ font-family: Arial, Nimbus Sans, Verdana, Lucida, Helvetica, sans-serif; font-size: 10pt; background: #faffe6; }
div#frame{ width: 500px; margin-top: 150px; margin-left: auto; margin-right: auto; padding: 2px; }
div#frame h1{ font-size: 13px; text-align: center; padding: 3px; margin: 2px 0; background: #ffffff; border-top: 4px solid #e60066; }
div#frame div.object{ border: 1px solid #ffdecc; background: #ffeeee; padding: 0; }
div#frame div.object div{ border-left: 15px solid #e33a3a; padding: 3px; padding-left: 12px; margin: 0; }
p{ margin-top: 2px; margin-bottom: 2px; padding: 0; }
p.message { font-size: 14px; }
p:hover{ background: #ffdddd; }
p.code{ font-weight: bold; }
p span{ float: right; margin-right: 6px; font-variant: small-caps; }
p.call{ width: 100%; text-align: right; border-top: 1px solid #e33a3a; }
p.call span{ float: none; margin-right: 0; font-style: italic; }
p.directive span{ font-weight: bold; }
p.directive span.good{ color: #009900; }
p.directive span.maybe{ color: #777700; }
p.directive span.bad{ color: #770000; }
p.important{ font-weight: bold; text-align: center; width:100%; }
p.warning span{	float: left; margin-right: 12px; font-weight: bold; }

  	</style>  
  </head>
  <body>
  
  	<div id="frame">
  	
  		<h1>Open Power Libs error</h1>
  		<div class="object"><div>
 
EOF;
	echo '  			<p class="message">'.$exception->getMessage()."</p>\r\n";
	echo '  			<p class="code">Type: <span>'.get_class($exception)."</span></p>\r\n";
	if(Opl_Registry::getState('opl_extended_errors'))
	{
		echo '  			<p class="call">In <span>'.$exception->getFile().'</span> on line <span>'.$exception->getLine()."</span></p>\r\n";
	}
	else
	{
		echo "  			<p class=\"call\">Debug mode is disabled. No additional information provided.</p>\r\n";
	}
	echo "  		</div></div>\r\n";
	echo <<<EOF
  	</div>
  </body>
 </html>
EOF;
	} // end Opl_Error_Handler();

	/*
	 * Class definitions
	 */

	class Opl_Exception extends Exception
	{
		protected $_message = '%s';

		public function __construct()
		{
			$args = func_get_args();
			$this->message = vsprintf($this->_message, $args);
			$this->clean();
		} // end __construct();

		public function getLibrary()
		{
			$tokens = explode('_', get_class_name($this));
			return Opl_Base::get(strtolower($tokens[0]));
		} // end getLibrary();
		
		public function clean()
		{
			/* null */
		} // end clean();
	} // end Opl_Exception;
	
	class Opl_NoTranslationInterface_Exception extends Opl_Exception
	{
		protected $_message = '%s can\'t complete its job: no translation interface defined.';
	} // end Opl_NoTranslationInterface_Exception;
	
	class Opl_InvalidType_Exception extends Opl_Exception
	{
		protected $_message = 'Invalid type of %s: %s required.';
	} // end Opl_InvalidType_Exception;
	
	/*
	 * Filesystem exceptions
	 */
	
	class Opl_Filesystem_Exception extends Exception{}
	
	class Opl_NotReadable_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" is not readable by PHP.';
	} // end Opl_NotReadable_Exception;
	
	class Opl_NotWriteable_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" is not writeable by PHP.';
	} // end Opl_Not_Writeable_Exception;
	
	class Opl_FileNotExists_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = 'The %s "%s" does not exist in the filesystem.';
	} // end Opl_Not_Writeable_Exception;

	class Opl_InvalidClass_Exception extends Opl_Filesystem_Exception
	{
		protected $_message = '"%s" is not a valid OPL class name.';
	} // end Opl_Not_Writeable_Exception;
	
	/*
	 * Debugger exceptions
	 */
	
	class Opl_Debug_Exception extends Opl_Exception{}
	
	class Opl_OptionNotExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The option "%s" does not exist in %s.';
	} // end Opl_OptionNotExists_Exception;
	
	class Opl_Debug_ItemExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The %s: "%s" already exists.';
	} // end Opl_Debug_ItemExists_Exception;
	
	class Opl_Debug_ItemNotExists_Exception extends Opl_Debug_Exception
	{
		protected $_message = 'The %s: "%s" does not exist.';
	} // end Opl_Debug_ItemNotExists_Exception;
	
	class Opl_Debug_Generic_Exception extends Opl_Debug_Exception
	{
		public function __construct($message)
		{
			$this->message = $message;
		} // end __construct();
	} // end Opl_Debug_Generic_Exception;