<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\System {
    class Info extends \Crowdstats\BaseInfo implements \Crowdstats\InterfaceInfo
    {
        /**
         * @return int|mixed
         */
        public function getCpuStats()
        {
            $cpuStats = array(
                'pcpu' => 0
            );

            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("file", "/dev/null", "a"),
            );

            $cwd = '/tmp';

            /**
             * There is mostly no difference in ps output between OSX (Darwin) and Linux.
             * That's why we don't have low level routines in SystemSupport for this.
             */
            $proc = proc_open(
                'ps -eo pid,pcpu,command', $descriptorspec, $pipes, $cwd, null
            );

            foreach (preg_split('/\n/', stream_get_contents($pipes[1])) as $line) {
                if (stristr($line, 'PID')) continue;

                preg_match('/^(.{5})\s+([0-9\.]*)?\s(.*)$/', $line, $stats);

                $pid  = isset($stats[1]) ? (int)$stats[1] : null;
                $pcpu = isset($stats[2]) ? (float)$stats[2] : null;
                $prog = isset($stats[3]) ? (string)$stats[3] : null;

                if ($this->_osType == 'Darwin') {
                    $pcpu = $pcpu / $this->_cpuCores;
                }

                $cpuStats['pcpu'] += $pcpu;

                if ($pcpu >= 1) {
                    // anything below 1% cpu utilization is not of interest...
                    if (isset($cpuStats['prog'][$prog])) {
                        $cpuStats['prog'][$prog] += $pcpu;
                    } else {
                        $cpuStats['prog'][$prog] = $pcpu;
                    }
                }
            }

            $procResult = proc_close($proc);

            if ($procResult == 0) {
                $cpuStats['pcpu'] = $cpuStats['pcpu'] / $this->_cpuCores;

                return $cpuStats;
            }

            return array();
        }
    }
}
