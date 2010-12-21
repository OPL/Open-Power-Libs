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
 */

/**
 * The base class for the Open Power Libs profilers. It provides the base
 * event storage, as well as iteration over the collection of profiler
 * events.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Opl_Profiler_Abstract implements SeekableIterator, Countable
{
	protected
		/**
		 * Current iterator position.
		 * @var integer
		 */
		$_position = 0,
		/**
		 * List of possible positions.
		 * @var array
		 */
		$_positions = array(),
		/**
		 * Contains events data.
		 * @var array
		*/
		$_events = array();

	/**
	 * Returns the number of profiler events.
	 *
	 * @return integer
	 */
	public function count()
	{
		return count($this->_events);
	} // end count();

	/**
	 * Rewinds the iterator position to the beginning.
	 */
	public function rewind()
	{
		$this->_position = 0;
	} // end rewind();

	/**
	 * Returns the current event.
	 *
	 * @return Opl_Profiler_Event_Interface
	 */
	public function current()
	{
		return $this->_events[$this->_positions[$this->_position]];
	} // end current();

	/**
	 * Returns the index of the current event.
	 *
	 * @return string
	 */
	public function key()
	{
		return $this->_positions[$this->_position];
	} // end key();

	/**
	 * Updates the internal iterator to the next events.
	 */
	public function next()
	{
		++$this->_position;
	} // end next();

	/**
	 * Checks if the internal iterator is in a valid position.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		if(isset($this->_positions[$this->_position]))
		{
			return isset($this->_events[$this->_positions[$this->_position]]);
		}
		return false;
	} // end valid();

	/**
	 * Moves the internal iterator to the specified position.
	 *
	 * @throws OutOfBoundsException
	 * @param integer $position The new iterator position.
	 */
	public function seek($position)
	{
		$this->_position = (int)$position;
		if(!$this->valid())
		{
			throw new OutOfBoundsException('Invalid seek position ('.$position.')');
		}
	} // end seek();
} // end Opl_Profiler_Abstract;
