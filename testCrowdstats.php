<?php
include('./InterfaceBootstrap.php');
include('./Bootstrap.php');

class testCrowdstats extends PHPUnit_Framework_TestCase
{
    public function testInfo()
    {
        \Crowdstats\Bootstrap::getInstance()->init();

        $systemInfo = new \Crowdstats\System\Info();

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getCpuCores()
        );

        $this->assertInternalType(
            'string',
            $systemInfo->getCpuType()
        );

        $this->assertInternalType(
            'string',
            $systemInfo->getOsType()
        );

        $this->assertInternalType(
            'string',
            $systemInfo->getHostname()
        );

        $this->assertLessThanOrEqual(
            time(),
            $systemInfo->getUptime()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getNetStats($sampleTime = 0)
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getNetBytesIn()
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getNetBytesOut()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getCpuStats()
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getCpuUsage()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getMonitor()
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getRamFree()
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getRamTotal()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getMemStats($sampleTime = 0)
        );

        $this->assertGreaterThanOrEqual(
            1,
            $systemInfo->getRamUsed()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getDiskStats()
        );

        $this->assertInternalType(
            'array',
            $systemInfo->getDiskUsage()
        );
    }

    public function testMonitor()
    {
        \Crowdstats\Bootstrap::getInstance()->init();
        $systemMonitor = new \Crowdstats\System\Monitor($profiling = true);

        $this->assertSame(
            $systemMonitor,
            $systemMonitor->update($refresh = true)
        );

        $this->assertInternalType(
            'array',
            $systemMonitor->getStats()
        );

        $systemMonitor->prStats();
    }

    public function testProfiling()
    {
        \Crowdstats\Bootstrap::getInstance()->init();
        $profiling = new \Crowdstats\System\Profiling();

        $this->assertSame(
            $profiling,
            $profiling->start()
        );

        $this->assertSame(
            $profiling,
            $profiling->stop()
        );

        $this->assertInternalType(
            'array',
            $profilingData = $profiling->getProfilingData()
        );

        $this->assertSame(
            $profiling,
            $profiling->prData($profilingData)
        );
    }
}
//EOF