<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:09
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\SystemSupport {
    class Linux implements \Crowdstats\InterfaceSystemSupport
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
            // TODO: Implement getNetStats() method.
        }

    }
}
