<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:09
 * To change this template use File | Settings | File Templates.
 */
namespace crowdstats\SystemSupport;
class Linux implements \crowdstats\InterfaceSystemSupport
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
}
