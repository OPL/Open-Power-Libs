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
 * $Id: Base.php 331 2010-04-14 07:23:03Z zyxist $
 */

/**
 * The class represents the script standard output.
 *
 * @abstract
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Stream_Console_Output extends Opl_Stream_Output
{
	/**
	 * The assumed display width.
	 * 
	 * @var integer 
	 */
	private $_width = 80;

	/**
	 * Constructs the standard output stream.
	 */
	public function __construct()
	{
		$this->_stream = fopen(STDOUT, 'w');
	} // end __construct();

	/**
	 * Sets the assumed display width which will be used for
	 * centering the text.
	 *
	 * @param integer The new display width.
	 */
	public function setDisplayWidth($width)
	{
		$this->_width = (integer)$width;
	} // end setDisplayWidth();

	/**
	 * Returns the current assumed display width.
	 *
	 * @return integer
	 */
	public function getDisplayWidth()
	{
		return $this->_width;
	} // end getDisplayWidth();

	/**
	 * Writes the specified text to the output, ending it with
	 * a newline symbol.
	 *
	 * @throws Opl_Stream_Exception
	 * @param string $text The text to write.
	 */
	public function writeln($text)
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Output stream is not opened.');
		}
		fwrite($this->_stream, (string)$text.PHP_EOL);
	} // end writeln();
	
	/**
	 * Attempts to center the text on the screen, using the
	 * assumed display width.
	 *
	 * @throws Opl_Stream_Exception
	 * @param string $text The text to display.
	 */
	public function writeCentered($text)
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Output stream is not opened.');
		}

		if(strlen($text) > $this->_width)
		{
			fwrite($this->_stream, $text.PHP_EOL);
		}
		else
		{
			fwrite($this->_stream, str_repeat(' ', floor($this->_width/2) - floor(strlen($text)/2)).$text.PHP_EOL);
		}
	} // end writeCentered();

	/**
	 * Closes the standard output stream.
	 */
	public function close()
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Output stream is not opened.');
		}
		fclose($this->_stream);
		$this->_stream = null;
	} // end close();

	/**
	 * Flushes the data to the standard output.
	 *
	 * @return boolean
	 */
	public function flush()
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Output stream is not opened.');
		}
		return fflush($this->_stream);
	} // end flush();
} // end Opl_Stream_Console_Output;
