<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:09
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\SystemSupport {
    class Linux implements \Crowdstats\InterfaceSystemInfo
    {

        /**
         * @return mixed
         */
        public function getHostname()
        {
            // we really want to get the FQDN system hostname.
            return (string)exec('hostname -f');
        }

        /**
         * @return mixed
         */
        public function getCpuCores()
        {
            return (int)exec('awk \'/^processor/ {++n} END {print n+1}\' /proc/cpuinfo');
        }

        /**
         * @return mixed
         */
        public function getUptime()
        {
            $btime = (int)exec("grep btime /proc/stat | sed 's/^btime\s*//'");
            $ctime = time();

            return $ctime - $btime;
        }

        /**
         * @return mixed
         */
        public function getNetStats()
        {
            $data      = array('interfaces' => array(), 'traffic' => array('bytes_in' => 0, 'bytes_out' => 0));
            $dataNames = array(
                'interface', // 0
                'mtu', // 1
                'ref', // 2
                'mac_addr', // 3
                'pkts_in', // 4
                'errs_in', // 5
                'bytes_in', // 6
                'pkts_out', // 7
                'errs_out', // 8
                'bytes_out', // 9
                'collision_counter', // 10
                'ip_addr' // 11
            );

            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("file", "/dev/null", "a"),
            );

            $cwd = '/tmp';

            $proc = proc_open(
                'ifconfig', $descriptorspec, $pipes, $cwd, null
            );

            $stats     = array();
            $interface = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

            foreach (preg_split('/\n/', stream_get_contents($pipes[1])) as $line) {
                if ($line == '') {
                    $stats[] = $interface;
                    continue;
                }

                if (stristr($line, 'Link encap')) {
                    $match = array();

                    if (preg_match('/^(\w+).*?Link encap:(\w+)\s+.*?HWaddr\s+([0-9a-f:]{17})/', $line, $match)) {
                        $interface[0] = (string)$match[1];
                        $interface[2] = (string)$match[2];
                        $interface[3] = (string)$match[3];
                    }

                    if (preg_match('/^(\w+).*?Link encap:(\w+)\s+/', $line, $match)) {
                        $interface[0] = (string)$match[1];
                        $interface[2] = (string)$match[2];
                        $interface[3] = '';
                    }
                } else {
                    $match = array();

                    if (preg_match('/inet addr:(\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3})/', $line, $match)) {
                        $interface[11] = (string)$match[1];
                    }

                    if (preg_match('/MTU:(\d+)/', $line, $match)) {
                        $interface[1] = (int)$match[1];
                    }

                    if (preg_match('/RX packets:(\d+)\s+errors:(\d+)/', $line, $match)) {
                        $interface[4] = (int)$match[1];
                        $interface[5] = (int)$match[1];
                    }

                    if (preg_match('/TX packets:(\d+)\s+errors:(\d+)/', $line, $match)) {
                        $interface[7] = (int)$match[1];
                        $interface[8] = (int)$match[1];
                    }

                    if (preg_match('/collisions:(\d+)/', $line, $match)) {
                        $interface[10] = (int)$match[1];
                    }

                    if (preg_match('/RX bytes:(\d+)\s.*?TX bytes:(\d+)\s/', $line, $match)) {
                        $interface[6] = (string)$match[1];
                        $interface[9] = (string)$match[2];
                    }
                }
            }

            foreach ($stats as $interface) {

                if (count($interface) == count($dataNames)) {
                    $temp = array_combine($dataNames, $interface);
                    if (isset($data['interfaces'][$temp['interface']])) continue;

                    if ($temp['ip_addr'] != '' && !preg_match('/^127\./', $temp['ip_addr'])) {
                        // we are not interested in loopback device/localhost data...
                        $data['interfaces'][$temp['interface']] = $temp;

                        $data['traffic']['bytes_in'] += (int)$temp['bytes_in'];
                        $data['traffic']['bytes_out'] += (int)$temp['bytes_out'];
                    }
                }
            }

            $procResult = proc_close($proc);

            if ($procResult !== 0) {
                error_log('ERROR: netstat was not OK!');
            }

            return $data;
        }

        /**
         * @return array|mixed
         */
        public function getMemStats()
        {
            $memInfo = (string)exec('free -b | grep Mem');
            preg_match('/Mem\:\s+(\d+)\s+(\d+)\s+(\d+).*/', $memInfo, $match);
            $totalMem = (int)$match[1];
            $usedMem  = (int)$match[2];
            $freeMem  = (int)$match[3];

            return array(
                'totalBytes' => $totalMem,
                'freeBytes'  => $freeMem,
                'usedBytes'  => $usedMem,
            );
        }

    }
}
//EOF