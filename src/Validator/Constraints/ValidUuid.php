<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 17:10
 */

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidUuid extends Constraint
{
    public $messageChar = "app.validator.uuid.chars";
    public $messageForbidden = 'app.validator.uuid.forbidden';
}
