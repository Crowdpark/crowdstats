<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:02
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    interface InterfaceSystemInfo
    {
        /**
         * @abstract
         * @return mixed
         */
        public function getHostname();

        /**
         * @abstract
         * @return mixed
         */
        public function getCpuCores();

        /**
         * @abstract
         * @return mixed
         */
        public function getUptime();

        /**
         * @abstract
         * @return mixed
         */
        public function getNetStats();
    }
}
