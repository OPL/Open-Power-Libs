<?php
require '../lib/Opl/Loader.php';
$loader = new Opl_Loader('_');
$loader->addLibrary('Opl', '../lib/');
$loader->addLibrary('Test', './');
$loader->register();

$test_profiler = new Opl_Profiler('TestClass');
$class = new Test_Class();
$class->setProfiler($test_profiler);
$class->doSomething();
$another_profiler = new Opl_Profiler('AnotherInstance');
$anotherInstance = new Test_Class();
$anotherInstance->setProfiler($another_profiler);
$anotherInstance->doAnything();

// Iterate through all modules.
foreach(array($test_profiler, $another_profiler) as $module)
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
// Returns all events in array
$events = $test_profiler->getEvents();
// Return all event data in array
$eventData = $test_profiler->getEvent('doAnything')->getData();