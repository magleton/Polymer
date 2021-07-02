<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:24
 */

namespace Polymer\Providers;

use DI\Container;

class MqSerializerProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer)
    {
        $pimpleContainer['mq_serializer'] = function (Container $container) {
            // return new SimpleSerializer();  //0.x版本
            return new Serializer();  //1.x 版本
        };
    }
}
