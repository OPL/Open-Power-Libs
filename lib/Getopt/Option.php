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
 * $Id: Base.php 331 2010-04-14 07:23:03Z zyxist $
 */

/**
 * Represents a single option for the OPL Getopt.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Getopt_Option
{
	/**
	 * The option name which should be unique. It is used for identifying the option
	 * in the script.
	 * @var string
	 */
	private $_name;

	/**
	 * The short flag symbol.
	 * @var char
	 */
	private $_shortFlag;

	/**
	 * The long flag name.
	 * @var string
	 */
	private $_longFlag;

	/**
	 * The help message
	 * @var string
	 */
	private $_help;

	/**
	 * Is the option found?
	 * @var boolean
	 */
	private $_found;

	/**
	 * The option argument.
	 * @var mixed
	 */
	private $_argument;

	/**
	 * Creates a new Getopt option. In the constructor, we can specify the
	 * basic settings. '$name' is the unique name which will be used to
	 * identify the option after parsing. The '$shortFlag' specifies the
	 * short flag which may appear after a single '-' symbol in the input.
	 * It must be a single letter or number. Short flags can be grouped under
	 * a single switch, i.e. '-afx'.
	 * 
	 * The '$longFlag' specifies the long flag which may appear after '--'
	 * sequence. Long flags do not have upper limits over their length, although
	 * they may contain numbers, letters, pauses and underscores only.
	 *
	 * The option may contain equivalent short and long forms, only one of them
	 * or neither of them. In the last case, the option is interpreted as a
	 * list of remaining script arguments that are not preceded by '-' and '--'.
	 * There might be only one such option registered in each Getopt parser instance.
	 * If we do not want to specify one of the flags, we should set it to NULL.
	 *
	 * In the constructor, we may also define a help message which specifies
	 * the meaning of this option in the help list.
	 * 
	 * @throws Opl_Getopt_Exception
	 * @param string $name The unique option name
	 * @param char $shortFlag The short flag
	 * @param string $longFlag The long flag
	 * @param string $help The help message
	 */
	public function __construct($name, $shortFlag = null, $longFlag = null, $help = null)
	{
		$this->_name = (string)$name;

		if($shortFlag !== null)
		{
			if(strlen($shortFlag) != 1)
			{
				throw new Opl_Getopt_Exception('The short flag for option "'.$name.'" must be a single character.');
			}
			$this->_shortFlag = $shortFlag;
		}
		if($longFlag !== null)
		{
			if(!preg_match('/^[a-z0-9A-Z\-\_]{1,}$/', (string)$longFlag))
			{
				throw new Opl_Getopt_Exception('The long flag for option "'.$name.'" must contain letters, number, pause and underscore symbols only.');
			}
			$this->_longFlag = $longFlag;
		}
		$this->_help = (string)$help;
	} // end __construct();

	/**
	 * Returns the option name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Returns the short flag for this option. If the short flag is not set,
	 * it returns NULL
	 *
	 * @return char
	 */
	public function getShortFlag()
	{
		return $this->_shortFlag;
	} // end getShortFlag();

	/**
	 * Returns the long flag for this option. If the long flag is not set,
	 * it returns NULL.
	 *
	 * @return string
	 */
	public function getLongFlag()
	{
		return $this->_longFlag;
	} // end getLongFlag();

	/**
	 * Returns the information about the argument.
	 *
	 * @internal
	 * @return array
	 */
	public function getArgument()
	{
		return null;
	} // end getArgument();

	/**
	 * Sets the 'found' state which tells whether the option
	 * has been found during the last parsing or not. The
	 * programmer should not call it directly.
	 * 
	 * @internal
	 * @param boolean $state The new 'found' state.
	 */
	public function setFound($state)
	{
		$this->_found = (bool)$state;
	} // end setFound();

	/**
	 * Returns true, if the option has been found during the last parsing.
	 *
	 * @return boolean Is the option found?
	 */
	public function isFound()
	{
		return $this->_found;
	} // end isFound();

	/**
	 * Sets the option argument value.
	 *
	 * @internal
	 * @param mixed $argument The option argument value.
	 */
	public function setValue($argument)
	{
		$this->_argument = $argument;
	} // end setValue();

	/**
	 * Returns the option value. If the option have not had an argument,
	 * it returns NULL.
	 *
	 * @return mixed The argument value.
	 */
	public function getValue()
	{
		return $this->_argument;
	} // end getValue();
} // end Opl_Getopt_Option;