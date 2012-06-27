<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:08
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    class BaseInfo implements \Crowdstats\InterfaceBaseInfo
    {
        /**
         * @var
         */
        private $_systemSupport;

        /**
         * @var
         */
        protected $_osType;

        /**
         * @var
         */
        protected $_cpuType;

        /**
         * @var
         */
        protected $_cpuCores;

        /**
         * @var
         */
        protected $_cpuUsage;

        /**
         * @var
         */
        protected $_ramTotal;

        /**
         * @var
         */
        protected $_ramFree;

        /**
         * @var
         */
        protected $_ramUsed;

        /**
         * @var
         */
        protected $_hostname;

        /**
         * @var
         */
        protected $_uptime;

        /**
         * @var
         */
        protected $_netBytesIn;

        /**
         * @var
         */
        protected $_netBytesOut;

        /**
         * @var
         */
        protected $_monitor;

        /**
         * @var
         */
        protected $_diskUsage;

        /**
         * @var
         */
        protected $_diskStats;

        /**
         *
         */
        public function __construct()
        {
            $this->_systemInit();

            $this->_updateStats();
        }

        /**
         *
         */
        protected function _systemInit()
        {
            $this->_osType = PHP_OS;

            try {
                eval("\$this->_systemSupport = new \\Crowdstats\\SystemSupport\\$this->_osType();");
            } catch (\Exception $e) {
                echo('FATAL: SystemSupport Init Failed! (' . $e->getMessage() . ')');
                die('non recoverable...');
            }

            $this->_monitor = array();
        }

        /**
         *
         */
        protected function _updateStats()
        {
            $this->_cpuType  = null;
            $this->_cpuCores = $this->_systemSupport->getCpuCores();
            $this->_hostname = $this->_systemSupport->getHostname();
            $this->_uptime   = $this->_systemSupport->getUptime();

            $netStats = $this->getNetStats();
            $cpuStats = $this->getCpuStats();
            $memStats = $this->getMemStats();

            $this->_netBytesIn  = $netStats['traffic']['bytes_in'];
            $this->_netBytesOut = $netStats['traffic']['bytes_out'];
            $this->_cpuUsage    = $cpuStats['pcpu'];
            $this->_ramFree     = $memStats['freeBytes'];
            $this->_ramTotal    = $memStats['totalBytes'];
            $this->_ramUsed     = $memStats['usedBytes'];
        }

        /**
         * @return int|mixed
         */
        public function getCpuCores()
        {
            return (int)$this->_cpuCores;
        }

        /**
         * @return string
         */
        public function getCpuType()
        {
            return (string)$this->_cpuType;
        }

        /**
         * @return string
         */
        public function getOsType()
        {
            return (string)$this->_osType;
        }

        /**
         * @return mixed|string
         */
        public function getHostname()
        {
            $this->_hostname = $this->_systemSupport->getHostname();
            return (string)$this->_hostname;
        }

        /**
         * @return int
         */
        public function getUptime()
        {
            $this->_uptime = $this->_systemSupport->getUptime();
            return (int)$this->_uptime;
        }

        /**
         * @param int $sampleTime
         *
         * @return array|mixed
         */
        public function getNetStats($sampleTime = 0)
        {
            $data = $this->_systemSupport->getNetStats();

            if ($sampleTime == 0) {
                return (array)$data;
            }

            sleep((int)$sampleTime);

            //TODO: implement sampling via $sampleTime...
            return (array)$data;
        }

        /**
         * @return int
         */
        public function getNetBytesIn()
        {
            return (int)$this->_netBytesIn;
        }

        /**
         * @return int
         */
        public function getNetBytesOut()
        {
            return (int)$this->_netBytesOut;
        }

        /**
         * @return array|mixed
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
             * There is mostly not much difference in ps output between OSX (Darwin) and Linux.
             * That's why we don't need low level routines in SystemSupport for this.
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

            if (proc_close($proc) !== 0) {
                error_log('ERROR: Possible problem with ps info @ getCpuStats...');
            }

            return (array)$cpuStats;
        }

        /**
         * @return float
         */
        public function getCpuUsage()
        {
            return (float)$this->_cpuUsage;
        }

        /**
         * @return array
         */
        public function getMonitor()
        {
            return (array)$this->_monitor;
        }

        /**
         * @return int
         */
        public function getRamFree()
        {
            return (int)$this->_ramFree;
        }

        /**
         * @return int
         */
        public function getRamTotal()
        {
            return (int)$this->_ramTotal;
        }

        /**
         * @return mixed
         */
        public function getMemStats($sampleTime = 0)
        {
            $data = $this->_systemSupport->getMemStats();

            if ($sampleTime == 0) {
                return (array)$data;
            }

            sleep((int)$sampleTime);

            //TODO: implement sampling via $sampleTime...
            return (array)$data;
        }

        /**
         * @return int
         */
        public function getRamUsed()
        {
            return (int)$this->_ramUsed;
        }

        /**
         * @return array|mixed
         */
        public function getDiskStats()
        {
            $this->_diskStats = $this->_systemSupport->getDiskStats();
            return (array)$this->_diskStats;
        }

        /**
         * @return array|mixed
         */
        public function getDiskUsage()
        {
            $this->_diskUsage = $this->_systemSupport->getDiskUsage();
            return (array)$this->_diskUsage;
        }
    }
}

//EOF