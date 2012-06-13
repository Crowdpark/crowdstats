<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:08
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    class BaseInfo implements \Crowdstats\InterfaceSystemSupport
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
         *
         */
        public function __construct()
        {
            $this->_osType  = PHP_OS;
            $this->_cpuType = null;

            try {
                eval("\$this->_systemSupport = new \\Crowdstats\\SystemSupport\\$this->_osType();");
            } catch (\Exception $e) {
                echo('FATAL: SystemSupport Init Failed! (' . $e->getMessage() . ')');
                die('non recoverable...');
            }

            $this->_cpuCores = $this->_systemSupport->getCpuCores();
            $this->_hostname = $this->_systemSupport->getHostname();
            $this->_uptime   = $this->_systemSupport->getUptime();

            $netStats = $this->getNetStats(0);

            $this->_netBytesIn  = $netStats['traffic']['bytes_in'];
            $this->_netBytesOut = $netStats['traffic']['bytes_out'];
        }

        /**
         * @return
         */
        public function getCpuCores()
        {
            return $this->_cpuCores;
        }

        /**
         * @return
         */
        public function getCpuType()
        {
            return $this->_cpuType;
        }

        /**
         * @return
         */
        public function getOsType()
        {
            return $this->_osType;
        }

        /**
         * @return
         */
        public function getHostname()
        {
            $this->_hostname = $this->_systemSupport->getHostname();
            return $this->_hostname;
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
         * @return mixed
         */
        public function getNetStats($sampleTime = 0)
        {
            $data = $this->_systemSupport->getNetStats();
            // TODO: gathering info for $sampleTime and compute in/out per sec...
            return $data;
        }

        /**
         * @return
         */
        public function getNetBytesIn()
        {
            return $this->_netBytesIn;
        }

        /**
         * @return
         */
        public function getNetBytesOut()
        {
            return $this->_netBytesOut;
        }
    }
}

//EOF