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
class ValidLessnLink extends Constraint
{
    public $message = 'app.validator.lessn.notvalid';
    public $messageEmpty = 'app.validator.lessn.nolink';
}
