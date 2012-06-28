<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 14.06.12
 * Time: 12:08
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\System {
    class Monitor extends \Crowdstats\System\Info implements \Crowdstats\InterfaceSystemInfo
    {
        /**
         * @var bool
         */
        private $_xhprof_on = false;

        /**
         * @param bool $profiling
         */
        public function __construct($profiling = false)
        {
            if (is_callable('xhprof_enable') && $profiling === true) {
                xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
                $this->_xhprof_on = true;
            }
            parent::__construct();
            $this->update(false);
        }

        /**
         * @param bool $refresh
         */
        public function update($refresh = true)
        {
            if ($refresh === true) $this->_updateStats();

            $timestamp = microtime(true);

            $this->_monitor["{$timestamp}"] = array(
                'networkBytesIn'  => $this->_netBytesIn,
                'networkBytesOut' => $this->_netBytesOut,
                'cpuUsage'        => $this->_cpuUsage,
                'ramFree'         => $this->_ramFree,
                'ramTotal'        => $this->_ramTotal,
                'ramUsed'         => $this->_ramUsed,
            );

            return $this;
        }

        /**
         * Yeah... bad thing is when profiling is running it will stop the profiling.
         *
         * So use this function only at the very very end of your script.
         */
        public function prStats()
        {
            $prHead = true;
            foreach ($this->_monitor as $timestamp => $data) {
                if ($prHead) {
                    $statNames = array_keys($data);
                    printf('%s = %s' . PHP_EOL, 'timestamp', implode(':', $statNames));
                    $prHead = false;
                }
                printf('%s = %s' . PHP_EOL, date('Y-m-d, H:i:s', $timestamp), implode(':', $data));
            }

            if (is_callable('xhprof_disable') && $this->_xhprof_on === true) {
                $xhprof_data      = xhprof_disable();
                $this->_xhprof_on = false;

                printf(
                    '%50s %4s %7s %7s %7s %7s' . PHP_EOL,
                    'Function/Method', 'ct', 'wt (s)', 'cpu (s)', 'mu', 'pmu'
                );

                foreach ($xhprof_data as $fName => $fData) {
                    $pfName = preg_replace('/.*?==>(.*)/', '$1', $fName);
                    printf(
                        '%50s %4d %7.4f %7.4f %7d %7d' . PHP_EOL,
                        substr($pfName, 0, 50), $fData['ct'], $fData['wt'] / 1000000, $fData['cpu'] / 1000000, $fData['mu'], $fData['pmu']
                    );
                }
            }
        }

        /**
         * @return array
         */
        public function getStats()
        {
            return (array)$this->_monitor;
        }
    }
}
//EOF