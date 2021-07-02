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
    public function register(Container $pimpleContainer): void
    {
        $pimpleContainer['biz_validator'] = static function (Container $container) {
            return new BizValidator();
        };
    }
}