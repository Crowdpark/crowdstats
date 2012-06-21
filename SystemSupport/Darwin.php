<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\SystemSupport {
    class Darwin implements \Crowdstats\InterfaceSystemInfo
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
            return (int)exec('sysctl -n hw.ncpu');
        }

        /**
         * @return mixed
         */
        public function getUptime()
        {
            $btime = (int)exec('sysctl -n kern.boottime | sed -E \'s/\{ sec = ([0-9]+).*/\1/\'');
            $ctime = time();

            return $ctime - $btime;
        }

        /**
         * @return array|mixed
         */
        public function getNetStats()
        {
            $data      = array('interfaces' => array(), 'traffic' => array('bytes_in' => 0, 'bytes_out' => 0));
            $dataNames = array(
                'interface',
                'mtu',
                'ref',
                'mac_addr',
                'pkts_in',
                'errs_in',
                'bytes_in',
                'pkts_out',
                'errs_out',
                'bytes_out',
                'collision_counter'
            );

            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("file", "/dev/null", "a"),
            );

            $cwd = '/tmp';

            $proc = proc_open(
                'netstat -n -b -i', $descriptorspec, $pipes, $cwd, null
            );

            foreach (preg_split('/\n/', stream_get_contents($pipes[1])) as $line) {
                if (!stristr($line, 'Link')) continue;

                $stats = preg_split('/\s+/', $line);

                if (count($stats) == count($dataNames)) {
                    $temp              = array_combine($dataNames, $stats);
                    $temp['interface'] = preg_replace('/\*/', '', $temp['interface']);
                    $temp['ip_addr']   = exec('ifconfig ' . escapeshellarg($temp['interface']) . ' | grep \'inet \' | sed -E \'s/.*inet ([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}).*/\1/\'');

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
            // TODO: change to 'sysctl hw | grep mem' -- better to fetch memory info in just one call!

            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("file", "/dev/null", "a"),
            );

            $cwd = '/tmp';

            $proc = proc_open(
                'sysctl hw', $descriptorspec, $pipes, $cwd, null
            );

            foreach (preg_split('/\n/', stream_get_contents($pipes[1])) as $line) {
                $match = array();
                if (preg_match('/(hw.physmem|hw.usermem|hw.memsize)\s+=\s+(\d+)/', $line, $match)) {
                    switch ($match[1]) {
                        case 'hw.physmem':
                            $physMem = (int)$match[2];
                            break;
                        case 'hw.usermem':
                            $userMem = (int)$match[2];
                            break;
                        case 'hw.memsize':
                            $totalMem = (int)$match[2];
                            break;
                    }
                }
            }

            $freeMem = $totalMem - ($userMem + $physMem);

            return array(
                'totalBytes' => $totalMem,
                'freeBytes'  => $freeMem,
                'usedBytes'  => $totalMem - $freeMem,
            );
        }
    }
}