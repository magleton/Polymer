<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-9-28
 * Time: 下午3:04
 */

namespace Polymer\Providers;

use DI\Container;
use Polymer\Validator\BizValidator;

class BizValidatorProvider
{
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            return new BizValidator();
        });
    }
}