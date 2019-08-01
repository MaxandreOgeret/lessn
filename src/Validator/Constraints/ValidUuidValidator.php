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
    const FORBIDDEN = [
        'app',
        'security',
        'link',
        'form',
        'api',
        'changelocale',
        'register',
        'js',
        '_error',
        '_wdt',
        '_profiler'
    ];
    const REGEX_CHAR = '/^[A-z0-9-_~]+$/';

    private $locales;

    public function __construct($locales)
    {
        $this->locales = explode('|', $locales);
    }

    public function validate($value, Constraint $constraint)
    {
        if (!is_null($value)) {
            if (!preg_match(self::REGEX_CHAR, $value, $matches)) {
                $this->context->buildViolation($constraint->messageChar)->addViolation();
            }

            if (in_array($value, self::FORBIDDEN) ||
                in_array($value, $this->locales)
            ) {
                $this->context->buildViolation($constraint->messageForbidden)->addViolation();
            }
        }
    }
}
