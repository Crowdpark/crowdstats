<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */
namespace crowdstats\CPU;
class Info extends \crowdstats\BaseInfo implements \crowdstats\InterfaceInfo
{
    /**
     *
     */
    public function __construct()
    {
        $this->_osType = PHP_OS;

    }

    /**
     * @return int|mixed
     */
    public function getStats()
    {
        die($this->_osType);

        $cpuStats = array(
            'pcpu' => 0
        );

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("file", "/dev/null", "a"),
        );

        $cwd = '/tmp';

        $proc = proc_open(
            'ps -C -eo pid,pcpu,comm', $descriptorspec, $pipes, $cwd, null
        );


        foreach (preg_split('/\n/', stream_get_contents($pipes[1])) as $line) {
            if (stristr($line, 'PID')) continue;

            preg_match('/^(.{5})\s(.{5})\s(.*)$/', $line, $stats);

            $pid  = isset($stats[1]) ? (int)$stats[1] : null;
            $pcpu = isset($stats[2]) ? (float)$stats[2] : null;
            $prog = isset($stats[3]) ? (string)$stats[3] : null;

            $cpuStats['pcpu'] += $pcpu;
        }

        $procResult = proc_close($proc);

        if ($procResult == 0) {
            return $cpuStats;
        }

        die($procResult);

        return array();
    }

    private function _getCpuNumber()
    {

    }
}
