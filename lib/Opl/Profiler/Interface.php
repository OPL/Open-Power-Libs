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
 * The interface needed to implement for profiler.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Opl_Profiler_Interface
{
	/**
	 * Returns registered module.
	 * If module doesn't exists creates new using current module classname.
	 *
	 * @param string $name Module name.
	 * @return Opl_Profiler_Module_Interface
	 */
	public function getModule($name);

	/**
	 * Adds module to profiler.
	 *
	 * @param Opl_Profiler_Module_Interface $module Module.
	 */
	public function addModule(Opl_Profiler_Module_Interface $module);

	/**
	 * Returns array of registered modules.
	 *
	 * @return array
	 */
	public function getModules();
} // end Opl_Profiler_Interface;