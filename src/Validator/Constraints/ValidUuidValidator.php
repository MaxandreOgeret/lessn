<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 17:10
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidUuidValidator extends ConstraintValidator
{
    const FORBIDDEN = ['app', 'security', 'link', 'form'];
    const REGEX_CHAR = '/^[A-z0-9-_~]+$/';
    const REGEX_FORBIDDEN = '/^(app|security|link|form)$/';

    public function validate($value, Constraint $constraint)
    {
        if (!is_null($value)) {
            if (!preg_match(self::REGEX_CHAR, $value, $matches)) {
                $this->context->buildViolation($constraint->messageChar)->addViolation();
            }

            if (preg_match(self::REGEX_FORBIDDEN, $value, $matches)) {
                $this->context->buildViolation($constraint->messageForbidden)->addViolation();
            }
        }
    }
}
