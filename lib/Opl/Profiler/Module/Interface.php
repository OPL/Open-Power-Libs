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
 * The interface for Open-Power-Libs compatible module profiler.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Opl_Profiler_Module_Interface
{
	/**
	 * Returns name of module.
	 * 
	 * @return string
	 */
	public function getName();

	/**
	 * Adds an event to module.
	 *
	 * @param Opl_Profiler_Event_Interface $event Event object.
	 */
	public function addEvent(Opl_Profiler_Event_Interface $event);

	/**
	 * Returns event object.
	 *
	 * @param string $eventName Event name.
	 */
	public function getEvent($eventName);

	/**
	 * Notifies event about action.
	 *
	 * @param string $eventName Event name.
	 * @param mixed $paramName Param name or array of params and its values.
	 * @param mixed $paramValue optional Param value.
	 */
	public function notify($eventName, $paramName, $paramValue = null);

	/**
	 * Returns array of registered events.
	 *
	 * @return array
	 */
	public function getEvents();
} // end Opl_Profiler_Module_Interface;