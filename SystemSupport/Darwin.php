<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\SystemSupport {
    class Darwin implements \Crowdstats\InterfaceSystemSupport
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
            $data      = array('interfaces' => array(), 'traffic' => array( 'in' => 0, 'out' => 0));
            $dataNames = array(
                'interface',
                'mtu',
                'ref',
                'mac_addr',
                'Ipkts',
                'Ierrs',
                'Ibytes',
                'Opkts',
                'Oerrs',
                'Obytes',
                'Coll'
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
                if (!stristr($line, 'Link')) continue; // filter out non device stats lines...
                $stats = preg_split('/\s+/', $line);
                if (count($stats) == count($dataNames)) {
                    $temp            = array_combine($dataNames, $stats);
                    $temp['ip_addr'] = exec('ifconfig ' . escapeshellarg($temp['interface']) . ' | grep \'inet \' | sed -E \'s/.*inet ([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}).*/\1/\'');

                    if ($temp['ip_addr'] != '' && !stristr($temp['ip_addr'], '127.')) {
                        $data['interfaces'][$temp['interface']] = $temp;
                    }
                }
            }

            $procResult = proc_close($proc);

            return $data;
        }
    }
}