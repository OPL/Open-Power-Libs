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
class Opl_Profiler_Module extends Opl_Profiler_Module_Abstract implements Opl_Profiler_Module_Interface
{
	protected
		$_eventClassName = 'Opl_Profiler_Event',
		$_name;

	/**
	 * Creates object. Sets name.
	 * 
	 * @param string $name Name of module.
	 */
	public function __construct($name)
	{
		$this->_name = (string)$name;
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
	 * Adds an event to module.
	 *
	 * @param Opl_Profiler_Event_Interface $event Event object.
	 */
	public function addEvent(Opl_Profiler_Event_Interface $event)
	{
		$this->_events[$event->getName()] = $event;
		$this->_positions[] = $event->getName();
	} // end addEvent();

	/**
	 * Returns event object.
	 *
	 * @param string $eventName Event name.
	 */
	public function getEvent($eventName)
	{
		$eventName = (string)$eventName;
		if(!isset($this->_events[$eventName]))
		{
			$this->_events[$eventName] = new $this->_eventClassName($eventName);
			if(!$this->_events[$eventName] instanceof Opl_Profiler_Event_Interface)
			{
				throw new Opl_Profiler_Module_Exception('Event "'.$eventName.'" is not an instance of Opl_Profiler_Event_Interface!');
			}
			$this->_positions[] = $eventName;
		}
		return $this->_events[$eventName];
	} // end getEvent();

	/**
	 * Creates an event with specified name.
	 *
	 * @param string $eventName Event name.
	 */
	public function createEvent($eventName)
	{
		$eventName = (string)$eventName;
		$this->_events[$eventName] = new $this->_eventClassName($eventName);
		if(!$this->_events[$eventName] instanceof Opl_Profiler_Event_Interface)
		{
			throw new Opl_Profiler_Module_Exception('Event "'.$eventName.'" is not an instance of Opl_Profiler_Event_Interface!');
		}
		$this->_positions[] = $eventName;
	} // end createEvent();

	/**
	 * Notifies event about action.
	 * 
	 * @param string $eventName Event name.
	 * @param mixed $paramName Param name or array of params and its values.
	 * @param mixed $paramValue optional Param value.
	 */
	public function notify($eventName, $paramName, $paramValue = null)
	{
		if(!isset($this->_events[$eventName]))
		{
			throw new Opl_Profiler_Module_Exception('There\'s no event with name "'.$eventName.'"!');
		}
		$this->_events[$eventName]->notify($paramName, $paramValue);
	} // end notify();

	/**
	 * Sets default class name for new events.
	 *
	 * @param string $classname Module class name.
	 */
	public function setEventClassName($classname)
	{
		$this->_eventClassName = (string)$classname;
	} // end setEventClassName();

	/**
	 * Returns default class name for new events.
	 *
	 * @return string
	 */
	public function getEventClassName()
	{
		return $this->_eventClassName;
	} // end getEventClassName();

	/**
	 * Returns array of registered events.
	 * 
	 * @return array
	 */
	public function getEvents()
	{
		return $this->_events;
	} // end getEvents();
} // end Opl_Profiler_Module;