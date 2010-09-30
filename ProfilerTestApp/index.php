<?php
require '../lib/Opl/Loader.php';
$loader = new Opl_Loader('_');
$loader->addLibrary('Opl', '../lib/');
$loader->addLibrary('Test', './');
$loader->register();

$profiler = new Opl_Profiler();
$class = new Test_Class();
$class->setProfiler($profiler->getModule('Test'));
$class->doSomething();
$anotherInstance = new Test_Class();
$anotherInstance->setProfiler($profiler->getModule('AnotherInstance'));
$anotherInstance->doAnything();

// Iterate through all modules.
foreach($profiler as $module)
{
	// Show module name.
	echo $module->getName().PHP_EOL;
	// Iterate through all events in module.
	foreach($module as $event)
	{
		// Show event name.
		echo $event->getName().PHP_EOL;
		// Iterate through all data in event.
		foreach($event as $key => $data)
		{
			echo $key.PHP_EOL;
			print_r($data);
			echo PHP_EOL;
		}
	}
}
// Returns all modules in array
$modules = $profiler->getModules();
// Returns all events in array
$events = $profiler->getModule('Test')->getEvents();
// Return all event data in array
$eventData = $profiler->getModule('Test')->getEvent('doAnything')->getData();