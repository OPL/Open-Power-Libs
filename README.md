Open Power Libs 2.0.5
=====================

Open Power Libraries is a project of specialized PHP5 libraries to 
support various frameworks and individual scripts. The design is 
based on our own experience with other libraries, where the lack of 
some general idea and vision was visible.

System Requirements
-------------------

The recommended version to run OPL is PHP 5.3, however - the library
does also work on PHP 5.2. 

Installation
------------

In order to install the library, copy the `/lib` directory into your
project directory tree. The initialization can be done by loading
`/lib/Opl/Base.php` file manually and configuring the autoloader:

    <?php
    require('./lib/Opl/Base.php');
    Opl_Loader::setDirectory('./lib/');
    Opl_Loader::register();
       
    // Now you can use OPL and OPT

If your webserver supports PHP 5.3, you may also download official PHAR-s
from [Invenzzia Website](http://www.invenzzia.org):

    <?php
    require('./opl.phar');
    require('./opt.phar');
       
    // Now you can use OPL and OPT

Please note that the PHAR-s do not contain any extra stuff, like
documentations, unit tests etc. - they are just plain libraries.

Usage
-----

The examples can be found in the `/examples` directory. We also
recommend to read the user manual and the tutorials published on
<http://www.invenzzia.org/>

Unit Tests
----------

In order to execute the unit tests, you must have the PHPUnit utility.
You can install the package using the PEAR installer. Execute the
following commands in the console:

    $ pear channel-discover pear.phpunit.de
    $ pear install phpunit/PHPUnit

Now you can switch into the `/tests` directory and execute some tests
with:

    $ phpunit testname

Before running test, make sure that the library paths in `tests/paths.ini`
file are set correctly.

Authors and License
-------------------

Open Power Libs  
Copyright (c) Invenzzia Group 2008-2010 <http://www.invenzzia.org>

The code was written by:

 - Tomasz "Zyx" Jedrzejewski - design, programming, documentation
 - Jacek "eXtreme" Jedrzejewski - testing, minor improvements, debug console
 - Amadeusz "megaweb" Starzykiewicz - additional programming.

We would like to thank everyone that send us new ideas and find bugs.

The libraries are available under the new BSD license that can be found
in `LICENSE` file. The documentation is available under the terms of
GNU Free Documentation License 1.2 that can be found in `LICENSE.DOCS`
file.