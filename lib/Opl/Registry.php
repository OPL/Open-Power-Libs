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
 * The Registry provides a safe registry of the main system objects and
 * certain values. Contrary to singleton implementations, the registry
 * can be erased. Intentionally, the programmer should use different buffers
 * for scalar values and for objects.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Registry
{
	/**
	 * The list of stored objects.
	 * @static
	 * @var array
	 */
	static private $_objects = array();

	/**
	 * The list of stored values.
	 * @static
	 * @var array
	 */
	static private $_values = array();

	/**
	 * Registers a new object in the registry.
	 *
	 * @static
	 * @param string $name The object key
	 * @param object $object The registered object
	 */
	static public function set($name, $object)
	{
		self::$_objects[$name] = $object;
	} // end set();

	/**
	 * Returns the previously registered object. If the object does not
	 * exist, it throws an exception.
	 *
	 * @static
	 * @throws Opl_Registry_Exception
	 * @param string $name The registered object key.
	 * @return object The object stored under the specified key.
	 */
	static public function get($name)
	{
		if(!isset(self::$_objects[$name]))
		{
			throw new Opl_Registry_Exception('The specified registry object: '.$name.' does not exist');
		}
		return self::$_objects[$name];
	} // end get();

	/**
	 * Check whether there is an object registered under a specified key.
	 *
	 * @static
	 * @param string $name The object key
	 * @return boolean A boolean value indicating whether the object exists or not.
	 */
	static public function exists($name)
	{
		return !empty(self::$_objects[$name]);
	} // end exists();

	/**
	 * Sets the state variable in the registry.
	 *
	 * @static
	 * @param string $name The variable name
	 * @param mixed $value The variable value
	 */
	static public function setValue($name, $value)
	{
		self::$_values[$name] = $value;
	} // end setValue();

	/**
	 * Returns the state variable from the registry. If the
	 * variable does not exist, it returns NULL.
	 *
	 * @static
	 * @param string $name The variable name
	 * @return mixed
	 */
	static public function getValue($name)
	{
		if(!isset(self::$_values[$name]))
		{
			return NULL;
		}
		return self::$_values[$name];
	} // end getValue();
} // end Opl_Registry;