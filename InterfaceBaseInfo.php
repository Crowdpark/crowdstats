<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 14.06.12
 * Time: 13:17
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    interface InterfaceBaseInfo extends InterfaceSystemInfo
    {
        /**
         * @abstract
         * @return mixed
         */
        public function getCpuStats();
    }
}
//EOF