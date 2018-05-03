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
class validUuid extends Constraint
{
    public $messageChar = "UUID should contain only letters and '-', '_', '~'";
    public $messageForbidden = 'This value is forbidden.';

}