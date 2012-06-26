<?php
include('./InterfaceBootstrap.php');
include('./Bootstrap.php');

\Crowdstats\Bootstrap::getInstance()->init();
$systemMonitor = new \Crowdstats\System\Monitor();

$systemMonitor->update(); // store system information for later use...

$cpuStats = $systemMonitor->getCpuStats();

$systemMonitor->update(); // store system information for later use...

echo('---- system stats -----' . PHP_EOL);
printf('system cpu utilization is: %.2f%% - CPU cores = %d' . PHP_EOL, $cpuStats['pcpu'], $systemMonitor->getCpuCores());
printf('system uptime: %d sec. (%s)' . PHP_EOL, $systemMonitor->getUptime(), date('Y-m-d, H:i:s', time() - $systemMonitor->getUptime()));
printf('network bytes in:  %d' . PHP_EOL, $systemMonitor->getNetBytesIn());
printf('network bytes out: %d' . PHP_EOL, $systemMonitor->getNetBytesOut());

$systemMonitor->update(); // store system information for later use...

echo('disk usage:' . PHP_EOL);
$diskUsage = $systemMonitor->getDiskUsage();
print_r($diskUsage);

$systemMonitor->update(); // store system information for later use...

echo('monitor:' . PHP_EOL);
$systemMonitor->prStats();

//EOF