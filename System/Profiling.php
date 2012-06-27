<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 27.06.12
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\System {
    class Profiling extends \Crowdstats\System\Info implements \Crowdstats\InterfaceSystemInfo
    {
        /**
         * @var bool
         */
        private $_xhprof_on = false;

        /**
         * @var array
         */
        private $_profilingData = array();

        /**
         * @param bool $do_not_start_profiling_now
         */
        function __construct($do_not_start_profiling_now = false)
        {
            $do_not_start_profiling_now = is_bool($do_not_start_profiling_now) ? $do_not_start_profiling_now : false;

            if (is_callable('xhprof_enable') && $do_not_start_profiling_now === false) {
                xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
                $this->_xhprof_on = true;
            }

            $this->_systemInit();
        }

        /**
         * @return Profiling
         */
        function start()
        {
            if (is_callable('xhprof_enable') && $this->_xhprof_on === false) {
                xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
                $this->_xhprof_on = true;
            }

            return $this;
        }

        /**
         * @return Profiling
         */
        function stop()
        {
            if (is_callable('xhprof_disable') && $this->_xhprof_on === true) {
                $this->_profilingData = xhprof_disable();
                $this->_xhprof_on  = false;
            }

            return $this;
        }

        /**
         * @param array $profilingData
         *
         * @return Profiling
         */
        function prData($profilingData = array())
        {
            $profilingData = (is_array($profilingData) && count($profilingData) >= 1) ? $profilingData : $this->_profilingData;

            printf(
                '%50s %4s %7s %7s %7s %7s' . PHP_EOL,
                'Function/Method', 'ct', 'wt (s)', 'cpu (s)', 'mu', 'pmu'
            );

            foreach ($profilingData as $fName => $fData) {
                $pfName = preg_replace('/.*?==>(.*)/', '$1', $fName);
                printf(
                    '%50s %4d %7.4f %7.4f %7d %7d' . PHP_EOL,
                    substr($pfName, 0, 50), $fData['ct'], $fData['wt'] / 1000000, $fData['cpu'] / 1000000, $fData['mu'], $fData['pmu']
                );
            }

            return $this;
        }

        /**
         * @return array
         *
         * ct  = call times
         * wt  = wall/wait time - time spent for waiting on external resources or in sleep
         * cpu = time cpu was used
         * mu  = memory usage
         * pmu = peak memory usage
         */
        public function getProfilingData()
        {
            return $this->_profilingData;
        }
    }
}
