<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:08
 * To change this template use File | Settings | File Templates.
 */
namespace crowdstats;
class BaseInfo
{
    /**
     * @var
     */
    private $_osType;
    /**
     * @var
     */
    private $_cpuType;
    /**
     * @var
     */
    private $_cpuCores;


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
}
//EOF