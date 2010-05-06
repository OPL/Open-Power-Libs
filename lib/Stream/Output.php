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
 * The output stream abstract primitive.
 *
 * @abstract
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Opl_Stream_Output implements Opl_Stream_Interface
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
	 * Flushes the remaining bytes and forces them to be saved in the stream.
	 */
	abstract function flush();

	/**
	 * Reads the specified number of bytes from a stream.
	 *
	 * @throws Opl_Stream_Exception
	 * @param string $bytes The data to write.
	 * @param int $offset The offset of the write buffer to start.
	 * @param int $length The data length.
	 */
	public function write($bytes, $offset = 0, $length = null)
	{
		if(!is_resource($this->_stream))
		{
			throw new Opl_Stream_Exception('Output stream is not opened.');
		}

		if($offset > 0)
		{
			if($length = null)
			{
				$length = strlen($bytes) - $offset;
			}
			fwrite($this->_stream, substr($bytes, $offset, $length), $length);
		}
		else
		{
			fwrite($this->_stream, $bytes, $length);
		}
	} // end write();

	/**
	 * Packs the data into a binary format and writes them to the stream.
	 * The format is identical with the pack() function format and its size
	 * must match the number of arguments.
	 *
	 * @throws DomainException
	 * @throws Opl_Stream_Exception
	 * @param string $format The binary format (same as for pack() function).
	 * @param array $arguments The arguments
	 */
	public function writePacked($format, $arguments)
	{
		if(!is_array($arguments) && !$arguments instanceof Traversable)
		{
			throw new DomainException('The specified arguments must be either an array or a data structure.');
		}
		
		if(strlen($format) != sizeof($arguments))
		{
			throw new DomainException('The format size must match the number of arguments.');
		}

		$i = 0;
		$stream = '';
		foreach($arguments as $arg)
		{
			$stream .= pack($format[$i], $arg);
			$i++;
		}

		$this->write($stream);
	} // end writePacked();

	/**
	 * Writes a compound PHP type to the stream. For scalar values, it works
	 * identically, as write(). For arrays and serializable objects, it serializes
	 * them and writes to the stream.
	 *
	 * @throws DomainException
	 * @throws Opl_Stream_Exception
	 * @param mixed $item The item to write.
	 */
	public function writeCompound($item)
	{
		if(is_scalar($item))
		{
			$this->write($item);
		}
		elseif(is_array($item) || (is_object($item) && method_exists($item, '__sleep')))
		{
			$this->write(serialize($item));
		}
		throw new DomainException('The specified argument is not serializable.');
	} // end writeCompound();

	/**
	 * Returns true, if the stream is open.
	 *
	 * @return boolean Is the stream open?
	 */
	public function isOpen()
	{
		return is_resource($this->_stream);
	} // end isOpen();
} // end Opl_Stream_Output;