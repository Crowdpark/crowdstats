<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:08
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats;
class BaseInfo
{
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
     *
     */
    public function __construct()
    {
        $this->_osType = PHP_OS;
        $systemSupport = null;

        try {
            eval("\$systemSupport = new \\Crowdstats\\SystemSupport\\$this->_osType();");
        } catch (\Exception $e) {
            echo('FATAL: SystemSupport Init Failed! (' . $e->getMessage() . ')');
            die('non recoverable...');
        }

        $this->_cpuCores = $systemSupport->getCpuCores();
        $this->_hostname = $systemSupport->getHostname();
        $this->_cpuType  = null;
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
        return $this->_hostname;
    }
}

//EOF