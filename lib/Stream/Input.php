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
 * The input stream abstract primitive.
 *
 * @abstract
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Opl_Stream_Input implements Opl_Stream_Interface
{
	const NONBLOCKING = 0;
	const BLOCKING = 1;

	/**
	 * The stream.
	 *
	 * @var resource
	 */
	protected $_stream;

	/**
	 * The current blocking mode.
	 *
	 * @var integer
	 */
	private $_blocking = 1;

	/**
	 * Returns the estimate of the number of bytes that can be read from
	 * this stream without blocking it. Note that the concrete streams
	 * may not be able to estimate the buffer size. In this case they are
	 * expected to throw Opl_Stream_Exception.
	 *
	 * @throws Opl_Stream_Exception
	 * @return integer
	 */
	abstract function available();

	/**
	 * Sets the blocking mode: either NONBLOCKING or BLOCKING. Returns true,
	 * if the operation was successful.
	 *
	 * @throws DomainException
	 * @param integer $mode The blocking mode
	 * @return boolean The operation status.
	 */
	public function setBlocking($mode)
	{
		if($mode != 0 && $mode != 1)
		{
			throw new DomainException('Invalid blocking mode: either NONBLOCKING or BLOCKING expected');
		}

		if(is_resource($this->_stream))
		{
			if(stream_set_blocking($this->_stream, $mode))
			{
				$this->_blocking = $mode;
				return true;
			}
			return false;
		}
		$this->_blocking = $mode;
		return true;
	} // end setBlocking();

	/**
	 * Returns the current blocking mode for this stream.
	 *
	 * @return integer The current blocking mode.
	 */
	public function getBlocking()
	{
		return $this->_blocking;
	} // end getBlocking();

	/**
	 * Reads the specified number of bytes from a stream.
	 *
	 * @throws Opl_Stream_Exception
	 * @param integer $byteNum The number of bytes to read.
	 * @return string The returned string.
	 */
	public function read($byteNum = null)
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Input stream is not opened.');
		}

		$content = stream_get_line($this->_stream, (integer)$byteNum, '');

		if($content === false)
		{
			throw new Opl_Stream_Exception('Unable to read '.$byteNum.' bytes from an input stream.');
		}
		elseif($content === '' && $byteNum > 0)
		{
			throw new Opl_Stream_Exception('Host disconnected.');
		}
		return $content;
	} // end read();

	/**
	 * Resets the stream, setting the internal cursor to the beginning.
	 */
	abstract function reset();

	/**
	 * Skips the specified number of bytes in the input.
	 *
	 * @param int $byteNum The number of bytes to skip.
	 */
	public function skip($byteNum = 1)
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Input stream is not opened.');
		}
		stream_get_line($this->_stream, (integer)$byteNum, '');
	} // end skip();
	
	/**
	 * Returns true, if the stream is open.
	 *
	 * @return boolean Is the stream open?
	 */
	public function isOpen()
	{
		return is_resource($this->_stream);
	} // end isOpen();
} // end Opl_Stream_Input;