<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/10/26
 * Time: 21:05
 */

namespace Polymer\Constraints;

use Symfony\Component\Validator\Constraint;

class ContainsAlphanumeric extends Constraint
{
    public string $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';
}