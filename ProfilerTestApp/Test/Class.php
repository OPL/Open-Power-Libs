<?php
class Test_Class
{
	protected $_profiler;

	public function setProfiler($profiler)
	{
		$this->_profiler = $profiler;
	} // end setProfiler();

	public function doSomething()
	{
		$event = $this->_profiler->getEvent('doSomething');
		$event->notify('oplClass.start', microtime(true));
		$class = new Opl_Class();
		$this->_profiler->notify('doSomething', 'oplClass.end', microtime(true));
		$event->notify('oplClass', 'dupa');
		for($i = 0; $i<100000; $i++)
		{
			$i = $i + 5;
		}
		$event->notify('i', $i);
		$event->notify('end', microtime(true));
	} // end doSomething();

	public function doAnything()
	{
		$this->_profiler->createEvent('doAnything');
		for($i = 0; $i<100000; $i++)
		{
			$i = $i + 5;
		}
		$this->_profiler->notify('doAnything', 'template.name', 'name.tpl');
		$this->_profiler->notify('doAnything', 'end', microtime(true));
	} // end doAnything();
} // end Test_Class;