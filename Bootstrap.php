<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 *
 * You need this class only if you use crowdstats stand alone.
 *
 */
namespace crowdstats;
class Bootstrap
{
    /**
     * @param $className
     *
     * @throws \Exception
     */
    private function __autoload($className)
    {
        $rootArray = explode('/', APPLICATION_ROOT);
        $testRoot  = end($rootArray);
        $pathArray = explode('\\', $className);

        if ($pathArray[0] == $testRoot) {
            array_shift($pathArray);
        }

        $classFile = APPLICATION_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathArray) . '.php';

        if (!file_exists($classFile)) {
            throw new \Exception("[[$className] = [$classFile]] not found, Bootstrap::__autoload failed!");
        }

        require $classFile;
    }

    /**
     *
     */
    public function __construct()
    {
        ini_set('display_errors', '1');
        error_reporting(E_ALL | E_STRICT);

        define('APPLICATION_ROOT', dirname(__FILE__));

        spl_autoload_register(__NAMESPACE__ . '\Bootstrap::__autoload');
    }

    /**
     *
     */
    public function __destruct()
    {
    }
}
//EOF