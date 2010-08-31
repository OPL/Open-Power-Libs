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
 * The translation interface for OPL libraries.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Opl_Translation_Interface
{
	/**
	 * This method is supposed to return the specified localized message.
	 *
	 * @param string $group The message group
	 * @param string $id The message identifier
	 * @return string
	 */
	public function _($group, $id);

	/**
	 * Assigns the external data to the message body. The operation of
	 * concatenating the message and the data is left for the programmer.
	 * The method should save it in the internal buffer and use in the
	 * next first call of _() method.
	 *
	 * @param string $group The message group
	 * @param string $id The message identifier
	 * @param ... Custom arguments for the specified text.
	 */
	public function assign($group, $id);
} // end Opl_Translation_Interface;