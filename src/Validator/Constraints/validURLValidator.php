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

class validURLValidator extends ConstraintValidator
{
    const VALIDATOR_URL_REGEX = '_^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$_iuS';

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match(self::VALIDATOR_URL_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}