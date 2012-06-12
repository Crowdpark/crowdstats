<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 11.06.12
 * Time: 10:01
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats;
interface InterfaceSystemSupport
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
}
