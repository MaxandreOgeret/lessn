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
    public $message = 'This value is not a valid LESSn URL.';
    public $messageEmpty = 'This LESSn URL doesn\'t exist.';
}
