**System Information in PHP**
=============================

Some things which we really wanna know.

***FEATURES***
--------------

* Config free runtime
* Automatic OS detection
* Autoloader
* [PHP Profiling via xhprof](http://php.net/manual/en/intro.xhprof.php)

***Provides***
--------------

* CPU utilization
* Memory usage
* Network utilization and information
* Disk usage

***Dependencies***

[xhprof @ pecl](http://pecl.php.net/package/xhprof)

Installation:

    $ pecl config-set preferred_state beta
    $ pecl install xhprof
    $ pecl config-set preferred_state stable

The config switch might not be necessary. Just in case pecl refuses to install xhprof.

***Usage example***
-------------------

Look at [test.php](https://github.com/Crowdpark/crowdstats/blob/master/test.php) for general examples

Look at [profiling.php](https://github.com/Crowdpark/crowdstats/blob/master/profiling.php) for profiling.

***Classes***
-------------

Crowdstats\System\Info
----------------------

Basic functionality/access to all system information methods (no profiling).

Crowdstats\System\Monitor
-------------------------

Adding monitoring features to gather system usage over time (includes profiling informtaion if enabled)

Crowdstats\System\Profiling
---------------------------

Like the basic Info-Class. This is the fastest way to gather PHP profiling information.


***TODO list***
---------------

* iostats integration for disk io stats...
* data sampling and aggregating
* tcp connection usage / monitoring

***Things which will not be implemented***
------------------------------------------

* Database based logging / you should use your own already existing stack to store the gathered data into what ever you have in mind.
* Any other OS support.

***Warranty/Copyright (c) 2011 by Crowdpark GmbH***
---------------------------------------------------

Neither ... nor, nothing. You use this stuff at your own risk!