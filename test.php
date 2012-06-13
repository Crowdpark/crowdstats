<?php
include('./InterfaceBootstrap');
include('./Bootstrap.php');

\Crowdstats\Bootstrap::getInstance()->init(); //Autoloader Init

$systemInfo = new \Crowdstats\System\Info(); // Class Init

$netStats = $systemInfo->getNetStats(); // Network stats

$cpuStats = $systemInfo->getCpuStats(); // current CPU utilization

//EOF