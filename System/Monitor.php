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
         *
         */
        public function __construct()
        {
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

            echo('update @ ' . $timestamp . PHP_EOL);

            $this->_monitor["{$timestamp}"] = array(
                'networkBytesIn'  => $this->_netBytesIn,
                'networkBytesOut' => $this->_netBytesOut,
                'cpuUsage'        => $this->_cpuUsage,
                'ramFree'         => $this->_ramFree,
                'ramTotal'        => $this->_ramTotal,
                'ramUsed'         => $this->_ramUsed,
            );
        }

        public function results()
        {
            foreach ($this->_monitor as $timestamp => $data) {
                printf('%s = %s' . PHP_EOL, date('Y-m-d, H:i:s', $timestamp), implode(':', $data));
            }
        }
    }
}
//EOF