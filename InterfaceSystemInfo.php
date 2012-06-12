<?php
/**
 * Created by JetBrains PhpStorm.
 * User: finger
 * Date: 08.06.12
 * Time: 14:02
 * To change this template use File | Settings | File Templates.
 */
namespace Crowdstats {
    interface InterfaceSystemInfo extends InterfaceSystemSupport
    {
        /**
         * @abstract
         * @return mixed
         */
        public function getCpuStats();
    }
}
