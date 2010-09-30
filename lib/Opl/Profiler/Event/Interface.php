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
 * The event interface for Open-Power-Libs profiler.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Opl_Profiler_Event_Interface
{
	/**
	 * Returns name of module.
	 * 
	 * @return string
	 */
	public function getName();

	/**
	 * Notifies event about action.
	 *
	 * @param mixed $paramName Param name or array of params and its values.
	 * @param mixed $paramValue optional Param value.
	 */
	public function notify($paramName, $paramValue = null);

	/**
	 * Returns array of given data.
	 *
	 * @return array
	 */
	public function getData();
} // end Opl_Profiler_Event_Interface;