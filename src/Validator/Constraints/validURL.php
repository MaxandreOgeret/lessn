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
class validURL extends Constraint
{
    public $message = 'This value is not a valid URL.';
    public $banMessage = 'This URL is banned.';
}