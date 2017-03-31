<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2017/3/31
 * Time: 20:43
 */

namespace Polymer\Tests\Validators;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AddressValidator
{
    /**
     * 验证地址
     *
     * @param $object
     * @param ExecutionContextInterface $context
     * @param $payload
     * @return bool
     */
    public static function validate($object, ExecutionContextInterface $context, $payload)
    {
        if (strlen($object) < 10) {
            $context->buildViolation('长度必须大于10')
                ->addViolation();
        }
    }
}