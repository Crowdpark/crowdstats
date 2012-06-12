<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats\SystemSupport;
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

}
