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
 * $Id$
 */

/**
 * A port that handler Opl_Exception exceptions.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_ErrorHandler_Port_Opl implements Opl_ErrorHandler_Port_Interface
{
	/**
	 * Matches the exception to the port.
	 *
	 * @param Exception $exception The exception to match
	 * @return boolean Does the port handle the exception?
	 */
	public function match(Exception $exception)
	{
		return ($exception instanceof Opl_Exception);
	} // end match();

	/**
	 * Returns the port name to display in the error message header.
	 *
	 * @return string The port name
	 */
	public function getName()
	{
		return 'Open Power Libs';
	} // end getName();

	/**
	 * Returns the generic context information for OPL errors.
	 *
	 * @param Exception $exception The exception
	 * @return array|Null The context information
	 */
	public function getContext(Exception $exception)
	{
		return array(
			'backtrace' => array(),
		);
	} // end getContext();
} // end Opl_ErrorHandler_Port_Opl;