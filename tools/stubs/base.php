Opl_Loader::setDirectory('');
Opl_Loader::setDefaultHandler(array('Opl_Loader', 'pharHandler'));
Opl_Loader::addLibrary('%%library%%', array('directory' => 'phar://'.__FILE__));
Opl_Loader::register();

if(version_compare(phpversion(), '5.3.0-dev', '<'))
{
	require(Opl_Loader::getLibraryPath('Opl').'Php52.php');
}

__HALT_COMPILER();
?>