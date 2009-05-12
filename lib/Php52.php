<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ===========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) 2008 Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id$
 */

 /*
  * This is a compatibility file that contains some implementations
  * provided by PHP 5.3 and not available in the previous versions.
  * 
  * Currently, the file implements the basic functionality of the
  * following classes:
  *  - SplQueue
  *  - SplStack
  */

	class SplDoublyLinkedList implements Countable, Iterator
	{
		const IT_MODE_LIFO = 1;
		const IT_MODE_FIFO = 2;
		const IT_MODE_DELETE = 4;
		const IT_MODE_KEEP = 8;
		
		protected $_iteratorMode = 10;
		protected $_data = array();
		protected $_count = 0;
		protected $_i = 0;
		
		/*
		 * Data access methods
		 */
		
		public function push($value)
		{
			array_push($this->_data, $value);
			$this->_count++;
		} // end push();

		public function pop()
		{
			if($this->_count > 0)
			{
				$this->_count--;
				return array_pop($this->_data);
			}
			throw new RuntimeException('Can\'t pop from an empty datastructure');
		} // end pop();

		public function top()
		{
			return end($this->_data);
		} // end top();
		
		public function unshift($data)
		{
			$this->_count++;
			array_unshift($this->_data, $data);
		} // end enqueue();

		public function shift()
		{
			if($this->_count > 0)
			{
				$this->_count--;
				return array_shift($this->_data);
			}
			return NULL;			
		} // end shift();
		
		/*
		 * Iterator mode
		 */
		
		public function setIteratorMode($mode)
		{
			$this->_iteratorMode = $mode;
		} // end setIteratorMode();
		
		public function getIteratorMode()
		{
			return $this->_iteratorMode;
		} // end getIteratorMode();
		
		/*
		 * Countable
		 */
		
		public function count()
		{
			return $this->_count;
		} // end count();
		
		/*
		 * Iterator
		 */
		
		public function rewind()
		{
			if($this->_iteratorMode & self::IT_MODE_FIFO)
			{
				$this->_i = 0;
			}
			else
			{
				$this->_i = $this->_count-1;
			}
		} // end rewind();
		
		public function next()
		{
			if($this->_iteratorMode & self::IT_MODE_FIFO)
			{
				$this->_i++;
			}
			else
			{
				$this->_i--;
			}
		} // end next();
		
		public function current()
		{
			return $this->_data[$this->_i];
		} // end current();
		
		public function valid()
		{
			if($this->_iteratorMode & self::IT_MODE_FIFO)
			{
				return $this->_i < $this->_count;
			}
			else
			{
				return $this->_i >= 0;
			}
		} // end valid();
		
		public function key()
		{
			return $this->_i;
		} // end key();
		
	} // end SplDoublyLinkedList;

	class SplQueue extends SplDoublyLinkedList
	{
		public function enqueue($data)
		{
			array_push($this->_data, $data);
			$this->_count++;
		} // end enqueue();

		public function dequeue()
		{
			if($this->_count > 0)
			{
				$this->_count--;
				return array_shift($this->_data);
			}
			throw new RuntimeException('Can\'t shift from an empty datastructure');
		} // end dequeue();

		public function count()
		{
			return $this->_count;
		} // end count();
	} // end SplQueue;
	
	class SplStack extends SplDoublyLinkedList
	{
		protected $_iteratorMode = 9;	
		

	} // end SplStack;
