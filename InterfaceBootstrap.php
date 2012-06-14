<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 13.06.12
 * Time: 10:26
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    interface InterfaceBootstrap
    {
        public function init();

        public static function getInstance();
    }
}
