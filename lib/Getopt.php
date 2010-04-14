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
 * Provides platform-indepentend getopt functionality for the
 * command-line applications.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opl_Getopt implements IteratorAggregate
{
	const ALLOW_SHORT_ARGS = 1;
	const ALLOW_LONG_ARGS = 2;
	const ALLOW_INCREMENTING = 4;
	const AUTO_HELP = 8;

	/**
	 * The list of available options.
	 * @var array
	 */
	private $_availableOpts = array();

	/**
	 * The list of options found during the last parsing.
	 * @var array
	 */
	private $_foundOpts = array();

	/**
	 * The list of single-symbol flags
	 * @var array
	 */
	private $_shortFlags = array();

	/**
	 * The list of long flags
	 * @var array
	 */
	private $_longFlags = array();

	/**
	 * The data option.
	 * @var Opl_Getopt_Option
	 */
	private $_data = null;
	
	/**
	 * The parser flags
	 * @var integer
	 */
	private $_flags;

	/**
	 * Creates the Getopt parser object, passing some flags.
	 *
	 * @param integer $flags The parser flags.
	 */
	public function __construct($flags = 0)
	{
		$this->_flags = (int)$flags;
	} // end __construct();

	/**
	 * Registers a new option in the Getopt parser. If the option is
	 * already registered under the specified name (which is hold
	 * by the $option object), the method throws an exception.
	 *
	 * @throws Opl_Getopt_Exception
	 * @param Opl_Getopt_Option $option The option to register.
	 */
	public function addOption(Opl_Getopt_Option $option)
	{
		$name = (string)$option->getName();
		if(isset($this->_availableOpts[$name]))
		{
			throw new Opl_Getopt_Exception('The option '.$name.' is already registered in getopt.');
		}

		$this->_availableOpts[$name] = $option;

		if(($flag = $option->getShortFlag()) !== null)
		{
			$this->_shortFlags[$flag] = $option;
		}
		if(($flag = $option->getLongFlag()) !== null)
		{
			$this->_longFlags[$flag] = $option;
		}
	} // end addOption();

	/**
	 * Returns true, if there is an option registered under the specified name.
	 *
	 * @param string $name The option name used while registering.
	 * @return boolean
	 */
	public function hasOption($name)
	{
		return isset($this->_availableOpts[(string)$name]);
	} // end hasOption();

	/**
	 * Returns an option registered under the specified name. If the option does
	 * not exist, the method throws an exception.
	 *
	 * @throws Opl_Getopt_Exception
	 * @param string $name The option name
	 * @return Opl_Getopt_Option The option registered in Getopt.
	 */
	public function getOption($name)
	{
		if(!isset($this->_availableOpts[(string)$name]))
		{
			throw new Opl_Getopt_Exception('The option '.$name.' is not registered in getopt.');
		}
		return $this->_availableOpts[(string)$name];
	} // end getOption();


	/**
	 * Parses the specified command-line input against the registered options and
	 * flags. If an error occurs during parsing, the method throws an exception.
	 *
	 * The found options are marked as found. They can be retrieved through either
	 * getOption() method or by iterating through Getopt parser object.
	 *
	 * By default, the method returns true. If the AUTO_HELP flag is set in the
	 * parser, the method may return false to signal the need of displaying help.
	 *
	 * @throws Opl_Getopt_Exception
	 * @param array $input The array with arguments to parse.
	 * @return boolean
	 */
	public function parse(array $input)
	{
		// Reset the flags
		$this->_foundOpts = array();
		foreach($this->_availableOpts as $opt)
		{
			$opt->setFound(false);
		}

		// Parse everything
		$cnt = sizeof($input);
		for($i = 0; $i < $cnt; $i++)
		{
			// Long option
			if(strpos($input[$i], '--') == 0)
			{
				if($this->_flags & self::AUTO_HELP)
				{
					if($input[$i] == '--help')
					{
						return false;
					}
				}

				if(($id = strpos($input[$i], '=')) !== false)
				{
					if(! $this->_flags & self::ALLOW_LONG_ARGS)
					{
						throw new Opl_Getopt_Exception('Long flag arguments are not allowed.');
					}
					$optionName = ltrim(substr($input[$i], 0, $id), '-');
					$optionArgs = substr($input[$i], $id, strlen($input[$i]) - $id);
				}
				else
				{
					$optionName = ltrim($input[$i], '-');
					$optionArgs = null;
				}

				// Locate the option.
				if(!isset($this->_longFlags[$optionName]))
				{
					throw new Opl_Getopt_Exception('Unknown option: --'.$optionName);
				}
				$option = $this->_longFlags[$optionName];
				$argument = $option->getArgument();

				// Test the arguments
				if($argument === null && $optionArgs !== null)
				{
					throw new Opl_Getopt_Exception('The option --'.$optionName.' does not take any arguments.');
				}
				elseif($argument[0] === Opl_Getopt_Option::REQUIRED && $optionArgs === null)
				{
					throw new Opl_Getopt_Exception('The option --'.$optionName.' requires an argument.');
				}

				if($optionArgs !== null)
				{
					$option->setArgument($this->_validateArgument('--'.$optionName, $optionArgs, $argument[1]));
				}
				$option->setFound(true);
			}
			// Short option
			elseif(strpos($input[$i], '-') == 0)
			{

			}
			// Text arguments
			else
			{
				// Test if we can have them
				if($this->_data === null)
				{
					throw new Opl_Getopt_Exception('Plain arguments are not allowed.');
				}

				// Take the rest of the input as the plain text arguments
				$list = array();
				for(; $i < $cnt; $i++)
				{
					$list[] = $input[$i];
				}
				$this->_data->setValue($list);
				$this->_data->setFound(true);
			}
		}
	} // end parse();

	/**
	 * Generates a help from the available options.
	 *
	 * @param Opl_Console_Stream $stdout The output stream used for rendering.
	 */
	public function printHelp(Opl_Console_Stream $stdout)
	{

	} // end printHelp();

	/**
	 * An iterator for iterating through recognized options.
	 *
	 * @internal
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_foundOpts);
	} // end getIterator();

	/**
	 * Validates the option argument.
	 *
	 * @internal
	 * @throws Opl_Getopt_Exception
	 * @param string $option The option name for debug purposes
	 * @param string $argument The argument value to test
	 * @param integer $type The argument type
	 * @return mixed The filtered argument
	 */
	private function _validateArgument($option, $argument, $type)
	{
		switch($type)
		{
			case Opl_Getopt_Option::INTEGER:
				if(!ctype_digit($argument))
				{
					throw new Opl_Getopt_Exception('The argument of --'.$option.' must be integer.');
				}
				return (int)$argument;
			case Opl_Getopt_Option::BOOLEAN:
				if($argument != 'true' && $argument != 'yes' && $argument != 'no' && $argument != 'false')
				{
					throw new Opl_Getopt_Exception('The argument of --'.$option.' must be a boolean value: true/false/yes/no.');
				}
				if($argument == 'true' || $argument == 'yes')
				{
					return true;
				}
				return false;
			case Opl_Getopt_Option::ENABLED:
				if($argument != 'enabled' && $argument != 'disabled')
				{
					throw new Opl_Getopt_Exception('The argument of --'.$option.' must be either \'enabled\' or \'disabled\'.');
				}
				if($argument == 'enabled')
				{
					return true;
				}
				return false;
			case Opl_Getopt_Option::STRING:
				if(empty($argument))
				{
					throw new Opl_Getopt_Exception('The argument of --'.$option.' must not be empty.');
				}
				return $argument;
		}
		return $argument;
	} // end _validateArgument();
} // end Opl_Console_Getopt;