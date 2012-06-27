<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 27.06.12
 * Time: 12:16
 * To change this template use File | Settings | File Templates.
 */

include('./InterfaceBootstrap.php');
include('./Bootstrap.php');

\Crowdstats\Bootstrap::getInstance()->init();
$profiling = new \Crowdstats\System\Profiling(); // use $do_not_start_profiling_now = true param if you don't want to start profiling immediately!
$profiling->start();

sleep(3); // do some more serous stuff here... ;-)
for ($n = 0; $n <= 10000; $n++) {
    $test[] = date('Y-m-d, H:i:s', time());
}

$profiling->stop();

$profiling->prData();