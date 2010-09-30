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
 * The standard profiler module for Open-Power-Libs.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Profiler_Event extends Opl_Profiler_Event_Abstract implements Opl_Profiler_Event_Interface
{
	protected
		/**
		 * Event name.
		 * @var string
		 */
		$_name,
		/**
		 * Start time.
		 * @var float
		 */
		$_start,
		/**
		 * End time.
		 * @var float
		 */
		$_end,
		/**
		 * Event data.
		 * @var array
		 */
		$_data = array();

	/**
	 * Creates new event.
	 *
	 * @param string $name Event name.
	 */
	public function __construct($name)
	{
		$this->_name = (string)$name;
		$this->_start = microtime(true);
	} // end __construct();

	/**
	 * Returns name of module.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Notifies event about action.
	 *
	 * @param mixed $paramName Param name or array of params and its values.
	 * @param mixed $paramValue optional Param value.
	 */
	public function notify($paramName, $paramValue = null)
	{
		if(is_array($paramName))
		{
			foreach($paramName as $name => $value)
			{
				if($name == 'end')
				{
					$this->_end = $value;
				}
				else
				{
					$this->set($name, $value);
				}
			}
		}
		else
		{
			if($paramValue === null)
			{
				throw new Opl_Profiler_Event_Exception('No value for parameter "'.$paramName.'" given.');
				return;
			}
			if($paramName == 'end')
			{
				$this->_end = $paramValue;
			}
			else
			{
				$this->set($paramName, $paramValue);
			}
		}
	} // end notify();

	public function set($name, $value)
	{
		$name = explode('.', $name);
		if(!in_array($name[0], $this->_positions))
		{
			$this->_positions[] = $name[0];
		}
		$holder = &$this->_data;
		foreach($name as $n)
		{
			if(!isset($holder[$n]))
			{
				$holder[$n] = array();
			}
			$holder = &$holder[$n];
		}
		if(empty($holder))
		{
			$holder = $value;
		}
		else
		{
			$holder = array_merge_recursive($holder, array($value));
		}
	} // end set();

	public function get($name)
	{
		$name = explode('.', $name);
		$holder = &$this->_data;
		foreach($name as $n)
		{
			if(!isset($holder[$n]))
			{
				break;
			}
			$holder = &$holder[$n];
		}
		return $holder;
	} // end get();

	/**
	 * Returns array of given data.
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	} // end getData();

// Magical methods

	public function __get($name)
	{
		return $this->get($name);
	} // end __get();

	public function __set($name, $value)
	{
		$this->set($name, $value);
	} // end __set();
} // end Opl_Profiler_Event;