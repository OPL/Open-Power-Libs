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
 * The standard profiler for Open-Power-Libs.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Profiler extends Opl_Profiler_Abstract implements Opl_Profiler_Interface
{
	protected
		/**
		 * Currently used class of modules.
		 * @var string
		 */
		$_moduleClassName = 'Opl_Profiler_Module';

	/**
	 * Returns registered module.
	 * If module doesn't exists creates new using current module classname.
	 * 
	 * @param string $name Module name.
	 * @return Opl_Profiler_Module_Interface
	 */
	public function getModule($name)
	{
		if(!isset($this->_modules[(string)$name]))
		{
			$this->_modules[(string)$name] = new $this->_moduleClassName((string)$name);
			$this->_positions[] = (string)$name;
		}
		return $this->_modules[(string)$name];
	} // end getModule();

	/**
	 * Adds module to profiler.
	 * 
	 * @param string $name Module name.
	 * @param Opl_Profiler_Module_Interface $module Module.
	 */
	public function addModule(Opl_Profiler_Module_Interface $module)
	{
		$this->_modules[$module->getName()] = $module;
		$this->_positions[] = $module->getName();
	} // end addModule();

	/**
	 * Sets default class name for new modules.
	 *
	 * @param string $classname Module class name.
	 */
	public function setModuleClassName($classname)
	{
		$this->_moduleClassName = (string)$classname;
	} // end setModuleClassName();

	/**
	 * Returns default class name for new modules.
	 * 
	 * @return string
	 */
	public function getModuleClassName()
	{
		return $this->_moduleClassName;
	} // end getModuleClassName();

	/**
	 * Returns array of registered modules.
	 *
	 * @return array
	 */
	public function getModules()
	{
		return $this->_modules;
	} // end getModules();
} // end Opl_Profiler;