<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 17:58
 * To change this template use File | Settings | File Templates.
 */
include('./Bootstrap.php');
$bootstrap = new \Crowdstats\Bootstrap();

$cpStats = new \Crowdstats\CPU\Info();

$data = $cpStats->getStats();

var_dump($data);

echo $cpStats->getOsType() . PHP_EOL;

var_dump($cpStats);