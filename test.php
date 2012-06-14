<?php
include('./InterfaceBootstrap.php');
include('./Bootstrap.php');

\Crowdstats\Bootstrap::getInstance()->init(); //Autoloader Init

$systemInfo = new \Crowdstats\System\Info(); // Class Init

$netStats = $systemInfo->getNetStats(); // Network stats

var_dump($netStats);

$cpuStats = $systemInfo->getCpuStats(); // current CPU utilization

var_dump($cpuStats);

$sysUptime = $systemInfo->getUptime(); // system uptime in seconds since last boot...


//EOF