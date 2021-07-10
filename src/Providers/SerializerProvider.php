<?php
/**
 * User: macro chen <chen_macro@163.com>
 *
 * Date: 2017/4/17
 * Time: 18:47
 */

namespace Polymer\Providers;

use DI\Container;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $diContainer A container instance
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            return new Serializer($normalizers, $encoders);
        });
    }
}
