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
 * $Id: ErrorHandler.php 19 2008-11-20 16:09:45Z zyxist $
 */

	class Opl_ErrorHandler
	{
		protected $_library = 'Open Power Libs';
		protected $_context = array(
			'Opl_Debug_Exception' => array(
				'BasicConfiguration' => array(),
				'Backtrace' => array()
			),
			'__UNKNOWN__' => array(
				'BasicConfiguration' => array()
			),
		);

		/**
		 * Displays an exception information using the default OPL graphics
		 * style.
		 *
		 * @param Opl_Exception $exception The exception to be displayed.
		 */	
		public function display(Opl_Exception $exception)
		{
echo <<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  	<title>{$this->_library} error</title>
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

li { margin-top: 2px; margin-bottom: 2px; padding: 0; }
li:hover{ background: #ffdddd; }
li.value { font-weight: bold; }
li span{ float: right; margin-right: 6px; font-variant: small-caps; }
li.value span.good{ color: #009900; }
li.value span.maybe{ color: #777700; }
li.value span.bad{ color: #770000; }

code{ font-family: Courier, Courier New; background: #ffdddd;  }
  	</style>  
  </head>
  <body>
  
  	<div id="frame">
  	
  		<h1>{$this->_library} error</h1>
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

	if(Opl_Registry::getState('opl_extended_errors'))
	{
		echo "			<div class=\"object\"><div>\r\n";
		$this->_resolveContextInfo($exception);
		echo "  		</div></div>\r\n";
	}
	echo <<<EOF
  	</div>
  </body>
 </html>
EOF;
		} // end display();
	
		protected function _resolveContextInfo($exception)
		{
			$use = get_class($exception);
			if(!isset($this->_context[$use]))
			{
				$use = '__UNKNOWN__';
			}
			foreach($this->_context[$use] as $name => $config)
			{
				if(!method_exists($this, '_print'.$name))
				{
					$this->_printErrorInfo($exception, 'Error message filter "'.$name.'" not found.');
				}
				else
				{			
					call_user_func_array(array($this, '_print'.$name), array_merge(array(0 => $exception), $config));
				}
			}
		} // end _resolveContextInfo();	
		
		protected function _printErrorInfo($exception, $text)
		{
			echo '  			<p><strong>Exception information:</strong> '.$text."</p>\r\n";
		} // end _printErrorInfo();

		protected function _printStackInfo($exception, $title)
		{
			echo '		<p class="directive">'.$title.":</p>\r\n";
			$data = $exception->getData();
			$i = 1;
			while(sizeof($data) > 0)
			{
				$item = array_shift($data);
				if(sizeof($data) == 0)
				{
					echo "		<p class=\"directive\">".$i.". <span class=\"bad\">".$item."</span></p>\r\n";
				}
				else
				{
					echo "		<p class=\"directive\">".$i.". <span>".$item."</span></p>\r\n";
				}
				$i++;
			}
		} // end _printStackInfo();

		protected function _printBasicConfiguration($exception)
		{
			/* null */
		} // end _printBasicConfiguration();

		protected function _printBacktrace($exception)
		{
			echo "		<p class=\"directive\"><strong>Debug backtrace:</strong></p>\r\n";
			$data = array_reverse($exception->getTrace());
			$data[] = array(
				'function' => 'Opl_Debug_Exception',
				'file' => $exception->getFile(),
				'line' => $exception->getLine()
			);
			while(sizeof($data) > 0)
			{
				$item = array_shift($data);

				$name = (isset($item['class']) ? $item['class'].'::' : '').$item['function'];

				if(sizeof($data) == 0)
				{
					echo "		<p class=\"directive\">".$name."() <span class=\"bad\">".basename($item['file']).':'.$item['line']."</span></p>\r\n";
				}
				else
				{
					echo "		<p class=\"directive\">".$name."() <span>".basename($item['file']).':'.$item['line']."</span></p>\r\n";
				}
			}
		} // end _printBacktrace();
	} // end Opl_ErrorHandler;